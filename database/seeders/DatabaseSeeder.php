<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\ClinicSetting;
use App\Models\Doctor;
use App\Models\ExamRequest;
use App\Models\FinancialTransaction;
use App\Models\InsuranceProvider;
use App\Models\MedicalCertificate;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Triage;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(RoleDefaultsSeeder::class);

        $clinicA = Clinic::create([
            'name' => 'Clinica Vida+',
            'cnpj' => '12.345.678/0001-90',
            'email' => 'contato@clinicavida.test',
            'phone' => '(65) 3333-1000',
            'whatsapp' => '(65) 99999-1000',
            'address' => 'Av. Central, 1200',
            'city' => 'Cuiaba',
            'state' => 'MT',
            'status' => 'active',
        ]);

        $clinicB = Clinic::create([
            'name' => 'Clinica Norte Saude',
            'cnpj' => '98.765.432/0001-10',
            'email' => 'contato@nortesaude.test',
            'phone' => '(65) 3333-2000',
            'whatsapp' => '(65) 99999-2000',
            'address' => 'Rua das Flores, 455',
            'city' => 'Varzea Grande',
            'state' => 'MT',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Super Admin',
            'email' => 'super@clinicavida.test',
            'password' => 'password',
            'role' => User::ROLE_SUPER_ADMIN,
            'status' => 'active',
        ]);

        User::create([
            'clinic_id' => $clinicA->id,
            'name' => 'Admin Clinica',
            'email' => 'admin@clinicavida.test',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'status' => 'active',
        ]);

        $doctorUser = User::create([
            'clinic_id' => $clinicA->id,
            'name' => 'Dra. Helena Matos',
            'email' => 'medico@clinicavida.test',
            'password' => 'password',
            'role' => User::ROLE_DOCTOR,
            'status' => 'active',
        ]);

        $nurse = User::create([
            'clinic_id' => $clinicA->id,
            'name' => 'Enf. Camila Torres',
            'email' => 'triagem@clinicavida.test',
            'password' => 'password',
            'role' => User::ROLE_NURSE,
            'status' => 'active',
        ]);

        User::create([
            'clinic_id' => $clinicA->id,
            'name' => 'Recepcao',
            'email' => 'recepcao@clinicavida.test',
            'password' => 'password',
            'role' => User::ROLE_RECEPTIONIST,
            'status' => 'active',
        ]);

        User::create([
            'clinic_id' => $clinicA->id,
            'name' => 'Financeiro',
            'email' => 'financeiro@clinicavida.test',
            'password' => 'password',
            'role' => User::ROLE_FINANCE,
            'status' => 'active',
        ]);

        User::create([
            'clinic_id' => $clinicA->id,
            'name' => 'Tecnico de TI',
            'email' => 'ti.tecnico@clinicavida.test',
            'password' => 'password',
            'role' => 'ti_tecnico',
            'status' => 'active',
        ]);

        User::create([
            'clinic_id' => $clinicA->id,
            'name' => 'Coordenador de TI',
            'email' => 'ti.coordenador@clinicavida.test',
            'password' => 'password',
            'role' => 'ti_coordenador',
            'status' => 'active',
        ]);

        User::create([
            'clinic_id' => $clinicB->id,
            'name' => 'Admin Norte',
            'email' => 'admin.norte@clinicavida.test',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'status' => 'active',
        ]);

        $insurance = InsuranceProvider::create([
            'clinic_id' => $clinicA->id,
            'name' => 'Saude Total',
            'ans_code' => 'ANS-45890',
            'contact_name' => 'Mariana Costa',
            'phone' => '(65) 3222-1200',
            'email' => 'credenciamento@saudetotal.test',
            'coverage_notes' => 'Consultas clinicas e exames laboratoriais.',
            'status' => 'active',
        ]);

        $doctor = Doctor::create([
            'clinic_id' => $clinicA->id,
            'user_id' => $doctorUser->id,
            'name' => 'Dra. Helena Matos',
            'cpf' => '111.222.333-44',
            'rg' => '1234567-8',
            'crm' => '85641',
            'crm_uf' => 'MT',
            'specialty' => 'Clinica medica',
            'phone' => '(65) 3333-1010',
            'whatsapp' => '(65) 99999-1010',
            'email' => 'helena@clinicavida.test',
            'address' => 'Av. Central, 1200',
            'consultation_fee' => 220,
            'working_hours' => ['description' => 'Segunda a sexta, 08:00-12:00 e 14:00-18:00'],
            'status' => 'active',
        ]);

        $patient = Patient::create([
            'clinic_id' => $clinicA->id,
            'insurance_provider_id' => $insurance->id,
            'name' => 'Joao Pereira',
            'cpf' => '222.333.444-55',
            'rg' => '7654321-0',
            'birth_date' => now()->subYears(42)->toDateString(),
            'sex' => 'male',
            'phone' => '(65) 98888-1111',
            'whatsapp' => '(65) 98888-1111',
            'email' => 'joao.pereira@test.local',
            'address' => 'Rua A, 45',
            'notes' => 'Paciente hipertenso em acompanhamento.',
            'status' => 'active',
        ]);

        $appointment = Appointment::create([
            'clinic_id' => $clinicA->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'insurance_provider_id' => $insurance->id,
            'scheduled_at' => now()->setTime(10, 30),
            'duration_minutes' => 30,
            'type' => 'Consulta',
            'reason' => 'Dor no peito e cansaco',
            'status' => Appointment::STATUS_WAITING_DOCTOR,
            'notes' => 'Paciente chegou com antecedencia.',
        ]);

        Triage::create([
            'clinic_id' => $clinicA->id,
            'patient_id' => $patient->id,
            'appointment_id' => $appointment->id,
            'professional_id' => $nurse->id,
            'triaged_at' => now()->setTime(10, 5),
            'chief_complaint' => 'Dor no peito ha 2 horas.',
            'symptoms' => 'Cansaco, sudorese e desconforto ao respirar.',
            'blood_pressure' => '150/95',
            'heart_rate' => 104,
            'respiratory_rate' => 24,
            'temperature' => 36.8,
            'oxygen_saturation' => 94,
            'weight' => 82.5,
            'height' => 1.75,
            'bmi' => 26.94,
            'blood_glucose' => 118,
            'pain_level' => 8,
            'allergies' => 'Dipirona',
            'current_medications' => 'Losartana 50mg',
            'pre_existing_conditions' => 'Hipertensao arterial',
            'notes' => 'Encaminhar com prioridade.',
            'risk_classification' => Triage::RISK_EMERGENCY,
            'status' => Triage::STATUS_FORWARDED,
        ]);

        MedicalRecord::create([
            'clinic_id' => $clinicA->id,
            'patient_id' => $patient->id,
            'appointment_id' => $appointment->id,
            'doctor_id' => $doctor->id,
            'subjective' => 'Paciente relata dor toracica intensa.',
            'objective' => 'PA elevada, FC 104 bpm, SpO2 94%.',
            'assessment' => 'Dor toracica a esclarecer.',
            'plan' => 'Solicitar ECG e enzimas cardiacas. Monitorar sinais vitais.',
            'diagnosis' => 'Dor toracica',
            'status' => 'draft',
        ]);

        Prescription::create([
            'clinic_id' => $clinicA->id,
            'patient_id' => $patient->id,
            'appointment_id' => $appointment->id,
            'doctor_id' => $doctor->id,
            'medications' => 'AAS 100mg - tomar 1 comprimido apos avaliacao medica.',
            'instructions' => 'Retornar imediatamente em caso de piora dos sintomas.',
            'issued_at' => now(),
            'status' => 'issued',
        ]);

        MedicalCertificate::create([
            'clinic_id' => $clinicA->id,
            'patient_id' => $patient->id,
            'appointment_id' => $appointment->id,
            'doctor_id' => $doctor->id,
            'title' => 'Atestado medico',
            'content' => 'Atesto, para os devidos fins, que o paciente Joao Pereira necessita de afastamento de suas atividades por 2 dias.',
            'rest_days' => 2,
            'issued_at' => now(),
            'status' => 'issued',
        ]);

        ExamRequest::create([
            'clinic_id' => $clinicA->id,
            'patient_id' => $patient->id,
            'appointment_id' => $appointment->id,
            'doctor_id' => $doctor->id,
            'exam_name' => 'Eletrocardiograma',
            'indication' => 'Investigacao de dor toracica.',
            'priority' => 'emergency',
            'status' => 'requested',
            'requested_at' => now(),
        ]);

        foreach ([
            ['Consulta clinica medica - Pix', 'pix', 220],
            ['Consulta retorno - Cartao credito', 'credit_card', 180],
            ['Exame laboratorial - Boleto', 'boleto', 95],
            ['Procedimento ambulatorial - Cartao debito', 'debit_card', 140],
        ] as [$description, $paymentMethod, $amount]) {
            FinancialTransaction::create([
                'clinic_id' => $clinicA->id,
                'patient_id' => $patient->id,
                'appointment_id' => $appointment->id,
                'description' => $description,
                'type' => 'revenue',
                'category' => 'Consultas',
                'amount' => $amount,
                'status' => 'paid',
                'due_date' => now()->toDateString(),
                'paid_at' => now(),
                'payment_method' => $paymentMethod,
            ]);
        }

        foreach ([
            'tempo_padrao_consulta' => '30',
            'alerta_emergencia_dashboard' => 'ativo',
            'nome_fantasia' => 'Clinica Vida+',
        ] as $key => $value) {
            ClinicSetting::create([
                'clinic_id' => $clinicA->id,
                'group' => 'geral',
                'key' => $key,
                'value' => $value,
            ]);
        }
    }
}
