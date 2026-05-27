<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class RoleDefaultsSeeder extends Seeder
{
    public function run(): void
    {
        // System roles (global) for TI
        $roles = [
            ['key' => 'ti_tecnico', 'name' => 'Tecnico de TI'],
            ['key' => 'ti_coordenador', 'name' => 'Coordenador de TI'],
        ];

        foreach ($roles as $r) {
            Role::updateOrCreate(
                ['clinic_id' => null, 'key' => $r['key']],
                ['name' => $r['name'], 'description' => null, 'is_system' => true, 'status' => 'active']
            );
        }

        $permIds = Permission::query()->pluck('id', 'key')->toArray();

        $grant = function (string $roleKey, array $permissionKeys) use ($permIds): void {
            RolePermission::query()->where('role', $roleKey)->delete();
            foreach ($permissionKeys as $key) {
                if (! isset($permIds[$key])) {
                    continue;
                }
                RolePermission::create([
                    'role' => $roleKey,
                    'permission_id' => $permIds[$key],
                ]);
            }
        };

        // Tecnico: atua em chamados, sem acesso administrativo total.
        $grant('ti_tecnico', [
            'dashboard.view',
            'it-tickets.view',
            'it-tickets.create',
            'it-tickets.edit',
            // pode anexar/comentar (comentarios internos dependem de it-tickets.edit na UI)
            // dashboard TI: usa it-tickets.view e mostra recorte operacional quando nao tem admin.
        ]);

        // Coordenador: visao gerencial + export, sem perfis/config globais por padrao.
        $grant('ti_coordenador', [
            'dashboard.view',
            'it-tickets.view',
            'it-tickets.create',
            'it-tickets.edit',
            'it-tickets.export',
            'reports.view',
            'reports.export',
        ]);
    }
}

