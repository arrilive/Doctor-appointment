<?php
/** @var Tests\TestCase $this */

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Speciality;
use App\Models\User;
use App\Services\WhatsAppService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\seed;
use Carbon\Carbon;

uses(RefreshDatabase::class);

beforeEach(function () {
    seed(RoleSeeder::class);

    $this->doctorUser = User::factory()->create(['name' => 'Dr. García']);
    $this->doctor = Doctor::create([
        'user_id' => $this->doctorUser->id,
        'speciality_id' => Speciality::create(['name' => 'General'])->id,
        'medical_license_number' => '123456',
    ]);

    $this->patientUser = User::factory()->create(['phone' => '9981234567']);
    $this->patient = Patient::create([
        'user_id' => $this->patientUser->id,
    ]);
});

afterEach(function () {
    Mockery::close();
});

it('command sends reminders only for tomorrow\'s programado appointments', function () {
    // Tomorrow, programado -> SHOULD send
    Appointment::create([
        'patient_id' => $this->patient->id,
        'doctor_id' => $this->doctor->id,
        'appointment_date' => Carbon::tomorrow()->toDateString(),
        'start_time' => '10:00',
        'end_time' => '11:00:00',
        'status' => 'programado',
    ]);

    // Tomorrow, cancelado -> should NOT send
    Appointment::create([
        'patient_id' => $this->patient->id,
        'doctor_id' => $this->doctor->id,
        'appointment_date' => Carbon::tomorrow()->toDateString(),
        'start_time' => '11:00:00',
        'end_time' => '12:00:00',
        'status' => 'cancelado',
    ]);

    // Today, programado -> should NOT send
    Appointment::create([
        'patient_id' => $this->patient->id,
        'doctor_id' => $this->doctor->id,
        'appointment_date' => Carbon::today()->toDateString(),
        'start_time' => '12:00:00',
        'end_time' => '13:00:00',
        'status' => 'programado',
    ]);

    // Day after tomorrow, programado -> should NOT send
    Appointment::create([
        'patient_id' => $this->patient->id,
        'doctor_id' => $this->doctor->id,
        'appointment_date' => Carbon::tomorrow()->addDay()->toDateString(),
        'start_time' => '10:00',
        'end_time' => '11:00:00',
        'status' => 'programado',
    ]);

    $mock = Mockery::mock(WhatsAppService::class);
    $mock->shouldReceive('sendDailySummary')->once();
    app()->instance(WhatsAppService::class, $mock);

    $this->artisan('appointments:send-reminders')
        ->assertExitCode(0)
        ->expectsOutput('Reminders sent: 1');
});

it('command outputs zero when no appointments tomorrow', function () {
    $mock = Mockery::mock(WhatsAppService::class);
    $mock->shouldReceive('sendDailySummary')->never();
    app()->instance(WhatsAppService::class, $mock);

    $this->artisan('appointments:send-reminders')
        ->assertExitCode(0)
        ->expectsOutput('Reminders sent: 0');
});

it('command sends reminder with correct patient phone and doctor name', function () {
    Appointment::create([
        'patient_id' => $this->patient->id,
        'doctor_id' => $this->doctor->id,
        'appointment_date' => Carbon::tomorrow()->toDateString(),
        'start_time' => '10:00:00',
        'end_time' => '11:00:00',
        'status' => 'programado',
    ]);

    $mock = Mockery::mock(WhatsAppService::class);
    $mock->shouldReceive('sendDailySummary')
        ->once()
        ->withArgs(function(array $summary) {
            return count($summary) === 1
                && isset($summary[0]['paciente'])
                && str_contains($summary[0]['doctor'], 'García')
                && $summary[0]['hora'] === '10:00';
        });
    app()->instance(WhatsAppService::class, $mock);

    $this->artisan('appointments:send-reminders');
});
