# Clinica Vida+

Sistema SaaS multi-clinica para gestao de clinica medica, construido com Laravel 11, Blade, Bootstrap 5 e MySQL.

## Modulos incluidos

- Autenticacao e controle de perfis
- Dashboard operacional
- Clinicas, usuarios, medicos e pacientes
- Agenda medica com confirmacao, cancelamento, finalizacao, triagem e encaminhamento
- Triagem completa com IMC calculado automaticamente
- Atendimento medico com dados do paciente, consulta e sinais vitais
- Prontuario, receita medica, atestados e solicitacoes de exames
- Financeiro, convenios, relatorios e configuracoes
- Policies, Form Requests, relacionamentos Eloquent, seeders e isolamento por `clinic_id`

## Requisitos

- PHP 8.2+
- Composer
- MySQL 8+

## Instalacao

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure o banco no `.env`:

```env
DB_DATABASE=clinic_saas
DB_USERNAME=root
DB_PASSWORD=
```

Depois rode:

```bash
php artisan migrate --seed
php artisan serve
```

## Acessos demo

- Super Admin: `super@clinicavida.test` / `password`
- Administrador: `admin@clinicavida.test` / `password`
- Medico: `medico@clinicavida.test` / `password`
- Triagem: `triagem@clinicavida.test` / `password`
- Recepcao: `recepcao@clinicavida.test` / `password`
- Financeiro: `financeiro@clinicavida.test` / `password`

## Multi-clinica

Os modelos operacionais usam `clinic_id` e o trait `BelongsToClinic`, que aplica escopo global para usuarios comuns. O Super Admin ignora esse escopo via policies e pode acessar todas as clinicas.
