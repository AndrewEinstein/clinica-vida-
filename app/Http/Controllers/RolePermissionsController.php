<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class RolePermissionsController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('role-permissions.manage');

        $roles = User::roleOptions();
        unset($roles[User::ROLE_SUPER_ADMIN]);

        $selectedRole = (string) ($request->query('role') ?: array_key_first($roles));

        if (! Schema::hasTable('permissions') || ! Schema::hasTable('role_permissions')) {
            return view('settings.role-permissions.index', [
                'roles' => $roles,
                'permissions' => collect(),
                'assigned' => [],
                'selectedRole' => $selectedRole,
                'setupError' => 'Modulo de permissoes ainda nao foi instalado no banco. Rode as migrations e seed no Render (RUN_MIGRATIONS=1 e RUN_SEEDERS=1 em um deploy).',
            ]);
        }

        $permissions = Permission::query()->orderBy('group')->orderBy('name')->get();

        $assigned = RolePermission::query()->with('permission')->get()
            ->groupBy('role')
            ->map(fn ($items) => $items->pluck('permission.key')->all())
            ->all();

        $actions = ['view' => 'Visualizar', 'create' => 'Criar', 'edit' => 'Editar', 'delete' => 'Excluir', 'approve' => 'Aprovar', 'export' => 'Exportar'];

        $matrix = [];
        foreach ($permissions as $perm) {
            $parts = explode('.', (string) $perm->key, 2);
            $module = $parts[0] ?? 'outros';
            $action = $parts[1] ?? 'access';
            if (! array_key_exists($action, $actions)) {
                continue;
            }
            $matrix[$module][$action] = $perm;
        }

        return view('settings.role-permissions.index', [
            'roles' => $roles,
            'permissions' => $permissions,
            'assigned' => $assigned,
            'selectedRole' => $selectedRole,
            'actions' => $actions,
            'matrix' => $matrix,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('role-permissions.manage');

        if (! Schema::hasTable('permissions') || ! Schema::hasTable('role_permissions')) {
            return back()->withErrors(['permissions' => 'Tabelas de permissoes nao existem no banco ainda. Rode migrations/seed no Render.']);
        }

        $roles = array_keys(User::roleOptions());
        $rules = [
            'role' => ['required', 'string', 'in:'.implode(',', $roles)],
            'permissions' => ['array'],
            'permissions.*' => ['string'],
        ];

        $data = $request->validate($rules);

        if ($data['role'] === User::ROLE_SUPER_ADMIN) {
            return back()->withErrors(['role' => 'Permissoes do Super Admin nao sao configuraveis.']);
        }

        $permissionKeys = $data['permissions'] ?? [];
        $permissionIds = Permission::query()
            ->whereIn('key', $permissionKeys)
            ->pluck('id')
            ->all();

        RolePermission::query()->where('role', $data['role'])->delete();
        foreach ($permissionIds as $permissionId) {
            RolePermission::create([
                'role' => $data['role'],
                'permission_id' => $permissionId,
            ]);
        }

        return redirect()
            ->route('settings.role-permissions.index', ['role' => $data['role']])
            ->with('success', 'Permissoes atualizadas com sucesso.');
    }
}
