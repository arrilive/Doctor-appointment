<?php
/** @var Tests\TestCase $this */

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Speciality;
use App\Models\User;
use App\Services\WhatsAppService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\seed;

uses(RefreshDatabase::class);

beforeEach(function () {
    seed(RoleSeeder::class);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrador');

    $this->doctorUser = User::factory()->create(['name' => 'Dr. García']);
    $this->doctor = Doctor::create([
        'user_id' => $this->doctorUser->id,
        'speciality_id' => Speciality::create(['name' => 'General'])->id,
        'medical_license_number' => '123456',
    ]);

    $this->patientUser = User::factory()->create(['phone' => '6621234567']);
    $this->patient = Patient::create([
        'user_id' => $this->patientUser->id,
    ]);

    // Create schedule for tomorrow
    $tomorrow = now()->addDay();
    $dayOfWeek = (int) $tomorrow->format('N') - 1;

    // Create enough slots for 60 min (2 slots of 30 min)
    DoctorSchedule::create([
        'doctor_id' => $this->doctor->id,
        'day_of_week' => $dayOfWeek,
        'start_time' => '10:00:00',
        'end_time'   => '10:30:00',
    ]);
    DoctorSchedule::create([
        'doctor_id' => $this->doctor->id,
        'day_of_week' => $dayOfWeek,
        'start_time' => '10:30:00',
        'end_time'   => '11:00:00',
    ]);
});

afterEach(function () {
    Mockery::close();
});

it('whatsapp confirmation is sent when appointment is created', function () {
    $mock = Mockery::mock(WhatsAppService::class);
    $mock->shouldReceive('sendConfirmation')
        ->once()
        ->withArgs(function($phone, $fecha, $hora, $doctor) {
            return str_contains($phone, '662')
                && strlen($fecha) > 0
                && strlen($hora) > 0
                && str_contains($doctor, 'García');
        });
    app()->instance(WhatsAppService::class, $mock);

    actingAs($this->admin)
        ->post(route('admin.appointments.store'), [
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'duration' => 60,
            'notes' => 'Test appointment',
        ])
        ->assertRedirect(route('admin.appointments.index'));

    $this->assertDatabaseHas('appointments', [
        'patient_id' => $this->patient->id,
        'doctor_id' => $this->doctor->id,
        'start_time' => '10:00',
    ]);
});

it('appointment is saved even if whatsapp service throws exception', function () {
    $mock = Mockery::mock(WhatsAppService::class);
    $mock->shouldReceive('sendConfirmation')
        ->andThrow(new \Exception('Twilio down'));
    app()->instance(WhatsAppService::class, $mock);

    actingAs($this->admin)
        ->post(route('admin.appointments.store'), [
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'duration' => 60,
        ])
        ->assertRedirect(route('admin.appointments.index'));

    $this->assertDatabaseHas('appointments', [
        'patient_id' => $this->patient->id,
        'doctor_id' => $this->doctor->id,
    ]);
});

it('whatsapp confirmation receives correct phone from patient user', function () {
    $capturedPhone = '';
    $mock = Mockery::mock(WhatsAppService::class);
    $mock->shouldReceive('sendConfirmation')
        ->once()
        ->withArgs(function($phone) use (&$capturedPhone) {
            $capturedPhone = $phone;
            return true;
        });
    app()->instance(WhatsAppService::class, $mock);

    actingAs($this->admin)
        ->post(route('admin.appointments.store'), [
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'duration' => 60,
        ]);

    expect($capturedPhone)->toBe('6621234567');
});
