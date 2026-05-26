<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RolePermissionsController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('role-permissions.manage');

        $roles = User::roleOptions();
        unset($roles[User::ROLE_SUPER_ADMIN]);

        $permissions = Permission::query()
            ->orderBy('group')
            ->orderBy('name')
            ->get();

        $assigned = RolePermission::query()
            ->with('permission')
            ->get()
            ->groupBy('role')
            ->map(fn ($items) => $items->pluck('permission.key')->all())
            ->all();

        return view('settings.role-permissions.index', [
            'roles' => $roles,
            'permissions' => $permissions,
            'assigned' => $assigned,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('role-permissions.manage');

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

        return back()->with('success', 'Permissoes atualizadas com sucesso.');
    }
}
