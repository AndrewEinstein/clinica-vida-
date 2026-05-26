<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['key' => 'dashboard.view', 'name' => 'Ver Dashboard', 'group' => 'Dashboard'],

            // Cadastros
            ['key' => 'clinics.manage', 'name' => 'Gerenciar Clinicas', 'group' => 'Cadastros'],
            ['key' => 'users.manage', 'name' => 'Gerenciar Usuarios', 'group' => 'Cadastros'],
            ['key' => 'doctors.manage', 'name' => 'Gerenciar Medicos', 'group' => 'Cadastros'],
            ['key' => 'patients.manage', 'name' => 'Gerenciar Pacientes', 'group' => 'Cadastros'],
            ['key' => 'insurance.manage', 'name' => 'Gerenciar Convenios', 'group' => 'Cadastros'],

            // Operacao
            ['key' => 'appointments.manage', 'name' => 'Gerenciar Agenda/Consultas', 'group' => 'Operacao'],
            ['key' => 'triages.manage', 'name' => 'Gerenciar Triagens', 'group' => 'Operacao'],
            ['key' => 'medical-care.access', 'name' => 'Acessar Atendimento Medico', 'group' => 'Operacao'],
            ['key' => 'medical-records.manage', 'name' => 'Gerenciar Prontuarios', 'group' => 'Operacao'],
            ['key' => 'prescriptions.manage', 'name' => 'Gerenciar Prescricoes', 'group' => 'Operacao'],
            ['key' => 'exam-requests.manage', 'name' => 'Gerenciar Exames', 'group' => 'Operacao'],
            ['key' => 'medical-certificates.manage', 'name' => 'Gerenciar Atestados', 'group' => 'Operacao'],

            // Financeiro
            ['key' => 'finance.manage', 'name' => 'Gerenciar Financeiro', 'group' => 'Financeiro'],

            // Relatorios & Configuracoes
            ['key' => 'reports.view', 'name' => 'Ver Relatorios', 'group' => 'Relatorios'],
            ['key' => 'settings.manage', 'name' => 'Gerenciar Configuracoes da Clinica', 'group' => 'Configuracoes'],
            ['key' => 'role-permissions.manage', 'name' => 'Gerenciar Permissoes por Perfil', 'group' => 'Configuracoes'],
            ['key' => 'it-tickets.manage', 'name' => 'Gerenciar Chamados de TI', 'group' => 'TI'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['key' => $perm['key']],
                ['name' => $perm['name'], 'group' => $perm['group']]
            );
        }
    }
}
