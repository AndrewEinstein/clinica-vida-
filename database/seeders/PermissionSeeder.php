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
            ['key' => 'dashboard.view', 'name' => 'Dashboard', 'group' => 'Dashboard'],

            // Cadastros / Admin
            ['key' => 'clinics.view', 'name' => 'Clinicas - Visualizar', 'group' => 'Cadastros'],
            ['key' => 'clinics.create', 'name' => 'Clinicas - Criar', 'group' => 'Cadastros'],
            ['key' => 'clinics.edit', 'name' => 'Clinicas - Editar', 'group' => 'Cadastros'],
            ['key' => 'clinics.delete', 'name' => 'Clinicas - Excluir', 'group' => 'Cadastros'],

            ['key' => 'users.view', 'name' => 'Usuarios - Visualizar', 'group' => 'Cadastros'],
            ['key' => 'users.create', 'name' => 'Usuarios - Criar', 'group' => 'Cadastros'],
            ['key' => 'users.edit', 'name' => 'Usuarios - Editar', 'group' => 'Cadastros'],
            ['key' => 'users.delete', 'name' => 'Usuarios - Excluir', 'group' => 'Cadastros'],

            ['key' => 'doctors.view', 'name' => 'Medicos - Visualizar', 'group' => 'Cadastros'],
            ['key' => 'doctors.create', 'name' => 'Medicos - Criar', 'group' => 'Cadastros'],
            ['key' => 'doctors.edit', 'name' => 'Medicos - Editar', 'group' => 'Cadastros'],
            ['key' => 'doctors.delete', 'name' => 'Medicos - Excluir', 'group' => 'Cadastros'],

            ['key' => 'patients.view', 'name' => 'Pacientes - Visualizar', 'group' => 'Cadastros'],
            ['key' => 'patients.create', 'name' => 'Pacientes - Criar', 'group' => 'Cadastros'],
            ['key' => 'patients.edit', 'name' => 'Pacientes - Editar', 'group' => 'Cadastros'],
            ['key' => 'patients.delete', 'name' => 'Pacientes - Excluir', 'group' => 'Cadastros'],

            ['key' => 'insurance.view', 'name' => 'Convenios - Visualizar', 'group' => 'Cadastros'],
            ['key' => 'insurance.create', 'name' => 'Convenios - Criar', 'group' => 'Cadastros'],
            ['key' => 'insurance.edit', 'name' => 'Convenios - Editar', 'group' => 'Cadastros'],
            ['key' => 'insurance.delete', 'name' => 'Convenios - Excluir', 'group' => 'Cadastros'],

            // Operacao
            ['key' => 'appointments.view', 'name' => 'Agenda/Consultas - Visualizar', 'group' => 'Operacao'],
            ['key' => 'appointments.create', 'name' => 'Agenda/Consultas - Criar', 'group' => 'Operacao'],
            ['key' => 'appointments.edit', 'name' => 'Agenda/Consultas - Editar', 'group' => 'Operacao'],
            ['key' => 'appointments.delete', 'name' => 'Agenda/Consultas - Excluir', 'group' => 'Operacao'],

            ['key' => 'triages.view', 'name' => 'Triagem - Visualizar', 'group' => 'Operacao'],
            ['key' => 'triages.create', 'name' => 'Triagem - Criar', 'group' => 'Operacao'],
            ['key' => 'triages.edit', 'name' => 'Triagem - Editar', 'group' => 'Operacao'],
            ['key' => 'triages.delete', 'name' => 'Triagem - Excluir', 'group' => 'Operacao'],

            // Clinico
            ['key' => 'medical-records.view', 'name' => 'Prontuarios - Visualizar', 'group' => 'Clinico'],
            ['key' => 'medical-records.create', 'name' => 'Prontuarios - Criar', 'group' => 'Clinico'],
            ['key' => 'medical-records.edit', 'name' => 'Prontuarios - Editar', 'group' => 'Clinico'],
            ['key' => 'medical-records.delete', 'name' => 'Prontuarios - Excluir', 'group' => 'Clinico'],

            ['key' => 'prescriptions.view', 'name' => 'Prescricoes - Visualizar', 'group' => 'Clinico'],
            ['key' => 'prescriptions.create', 'name' => 'Prescricoes - Criar', 'group' => 'Clinico'],
            ['key' => 'prescriptions.edit', 'name' => 'Prescricoes - Editar', 'group' => 'Clinico'],
            ['key' => 'prescriptions.delete', 'name' => 'Prescricoes - Excluir', 'group' => 'Clinico'],
            ['key' => 'prescriptions.export', 'name' => 'Prescricoes - Exportar', 'group' => 'Clinico'],

            ['key' => 'exam-requests.view', 'name' => 'Exames - Visualizar', 'group' => 'Clinico'],
            ['key' => 'exam-requests.create', 'name' => 'Exames - Criar', 'group' => 'Clinico'],
            ['key' => 'exam-requests.edit', 'name' => 'Exames - Editar', 'group' => 'Clinico'],
            ['key' => 'exam-requests.delete', 'name' => 'Exames - Excluir', 'group' => 'Clinico'],

            ['key' => 'medical-certificates.view', 'name' => 'Atestados - Visualizar', 'group' => 'Clinico'],
            ['key' => 'medical-certificates.create', 'name' => 'Atestados - Criar', 'group' => 'Clinico'],
            ['key' => 'medical-certificates.edit', 'name' => 'Atestados - Editar', 'group' => 'Clinico'],
            ['key' => 'medical-certificates.delete', 'name' => 'Atestados - Excluir', 'group' => 'Clinico'],
            ['key' => 'medical-certificates.export', 'name' => 'Atestados - Exportar', 'group' => 'Clinico'],

            // Financeiro
            ['key' => 'finance.view', 'name' => 'Financeiro - Visualizar', 'group' => 'Financeiro'],
            ['key' => 'finance.create', 'name' => 'Financeiro - Criar', 'group' => 'Financeiro'],
            ['key' => 'finance.edit', 'name' => 'Financeiro - Editar', 'group' => 'Financeiro'],
            ['key' => 'finance.delete', 'name' => 'Financeiro - Excluir', 'group' => 'Financeiro'],
            ['key' => 'finance.export', 'name' => 'Financeiro - Exportar', 'group' => 'Financeiro'],
            ['key' => 'finance.approve', 'name' => 'Financeiro - Aprovar', 'group' => 'Financeiro'],

            // Relatorios
            ['key' => 'reports.view', 'name' => 'Relatorios - Visualizar', 'group' => 'Relatorios'],
            ['key' => 'reports.export', 'name' => 'Relatorios - Exportar', 'group' => 'Relatorios'],

            // Configuracoes
            ['key' => 'settings.view', 'name' => 'Configuracoes - Visualizar', 'group' => 'Configuracoes'],
            ['key' => 'settings.edit', 'name' => 'Configuracoes - Editar', 'group' => 'Configuracoes'],

            ['key' => 'roles.view', 'name' => 'Perfis - Visualizar', 'group' => 'Configuracoes'],
            ['key' => 'roles.create', 'name' => 'Perfis - Criar', 'group' => 'Configuracoes'],
            ['key' => 'roles.edit', 'name' => 'Perfis - Editar', 'group' => 'Configuracoes'],
            ['key' => 'roles.delete', 'name' => 'Perfis - Excluir', 'group' => 'Configuracoes'],

            ['key' => 'role-permissions.manage', 'name' => 'Perfis e Permissoes - Gerenciar', 'group' => 'Configuracoes'],

            // TI
            ['key' => 'it-tickets.view', 'name' => 'Chamados TI - Visualizar', 'group' => 'TI'],
            ['key' => 'it-tickets.create', 'name' => 'Chamados TI - Criar', 'group' => 'TI'],
            ['key' => 'it-tickets.edit', 'name' => 'Chamados TI - Editar', 'group' => 'TI'],
            ['key' => 'it-tickets.delete', 'name' => 'Chamados TI - Excluir', 'group' => 'TI'],
            ['key' => 'it-tickets.export', 'name' => 'Chamados TI - Exportar', 'group' => 'TI'],
            ['key' => 'it-tickets.approve', 'name' => 'Chamados TI - Aprovar', 'group' => 'TI'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['key' => $perm['key']],
                ['name' => $perm['name'], 'group' => $perm['group']]
            );
        }
    }
}

