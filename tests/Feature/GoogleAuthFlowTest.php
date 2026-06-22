<?php

use App\Services\GoogleAccountResolver;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

it('resolves siswa google account as student using UPSI source matric number', function () {
    config([
        'upsi.connection' => config('database.default'),
        'upsi.student_view' => 'h2u_student',
    ]);

    Schema::create('h2u_student', function (Blueprint $table) {
        $table->string('sm_student_id')->nullable();
        $table->string('sm_student_name')->nullable();
        $table->string('sm_siswa_email')->nullable();
        $table->string('ss_status_desc')->nullable();
        $table->string('sm_level')->nullable();
        $table->string('sm_program')->nullable();
        $table->string('pm_program_desc')->nullable();
        $table->date('sm_graduation_date')->nullable();
    });

    DB::table('h2u_student')->insert([
        'sm_student_id' => 'D2023999123',
        'sm_student_name' => 'Aina Google',
        'sm_siswa_email' => 'aina.google@siswa.upsi.edu.my',
        'ss_status_desc' => 'Aktif',
        'sm_level' => '5',
        'sm_program' => 'AT48',
        'pm_program_desc' => 'Software Engineering',
        'sm_graduation_date' => now()->addYears(2)->toDateString(),
    ]);

    $resolved = app(GoogleAccountResolver::class)->resolve(
        'google-123',
        'Aina Google',
        'Aina.Google@siswa.upsi.edu.my'
    );

    expect($resolved->role)->toBe('student')
        ->and($resolved->studentId)->toBe('D2023999123')
        ->and($resolved->staffEmail)->toBeNull()
        ->and($resolved->publicVerifiedAt)->toBeNull();
});

it('blocks siswa google account when student id cannot be detected', function () {
    config([
        'upsi.connection' => config('database.default'),
        'upsi.student_view' => 'h2u_student',
    ]);

    app(GoogleAccountResolver::class)->resolve(
        'google-456',
        'Unknown Student',
        'unknown.student@siswa.upsi.edu.my'
    );
})->throws(RuntimeException::class, 'Student ID could not be verified');

it('resolves staff google account as verified community staff', function () {
    $resolved = app(GoogleAccountResolver::class)->resolve(
        'google-789',
        'UPSI Staff',
        'staff.member@upsi.edu.my'
    );

    expect($resolved->role)->toBe('community')
        ->and($resolved->staffEmail)->toBe('staff.member@upsi.edu.my')
        ->and($resolved->staffVerifiedAt)->not->toBeNull()
        ->and($resolved->verificationStatus)->toBe('approved');
});

it('resolves public google account as community requiring document verification', function () {
    $resolved = app(GoogleAccountResolver::class)->resolve(
        'google-999',
        'Public User',
        'public.user@gmail.com'
    );

    expect($resolved->role)->toBe('community')
        ->and($resolved->staffEmail)->toBeNull()
        ->and($resolved->publicVerifiedAt)->toBeNull()
        ->and($resolved->verificationStatus)->toBe('pending');
});

it('requires phone and terms before completing google registration', function () {
    $this->withSession([
        'google_auth_user' => [
            'id' => 'google-complete-1',
            'name' => 'Public Complete',
            'email' => 'public.complete@gmail.com',
            'avatar' => null,
        ],
    ]);

    $response = $this->post(route('auth.google.complete'), [
        'phone' => '',
    ]);

    $response->assertSessionHasErrors(['phone', 'terms']);
    expect(DB::table('h2u_users')->where('hu_email', 'public.complete@gmail.com')->exists())->toBeFalse();
});

it('creates verified google community user after phone and terms are submitted', function () {
    $this->withSession([
        'google_auth_user' => [
            'id' => 'google-complete-2',
            'name' => 'Public Complete',
            'email' => 'public.complete@gmail.com',
            'avatar' => 'https://example.test/avatar.png',
        ],
    ]);

    $response = $this->post(route('auth.google.complete'), [
        'phone' => '0123456789',
        'terms' => '1',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));

    $user = DB::table('h2u_users')->where('hu_email', 'public.complete@gmail.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->hu_role)->toBe('community')
        ->and($user->hu_phone)->toBe('0123456789')
        ->and($user->hu_email_verified_at)->not->toBeNull()
        ->and($user->hu_google_id)->toBe('google-complete-2')
        ->and($user->hu_terms_accepted_at)->not->toBeNull();
});

it('creates google student user with detected matric number and student status record', function () {
    config([
        'upsi.connection' => config('database.default'),
        'upsi.student_view' => 'h2u_student',
    ]);

    Schema::create('h2u_student', function (Blueprint $table) {
        $table->string('sm_student_id')->nullable();
        $table->string('sm_student_name')->nullable();
        $table->string('sm_siswa_email')->nullable();
        $table->string('ss_status_desc')->nullable();
        $table->string('sm_level')->nullable();
        $table->string('sm_program')->nullable();
        $table->string('pm_program_desc')->nullable();
        $table->date('sm_graduation_date')->nullable();
    });

    DB::table('h2u_student')->insert([
        'sm_student_id' => 'D2023999001',
        'sm_student_name' => 'Student Google',
        'sm_siswa_email' => 'student.google@siswa.upsi.edu.my',
        'ss_status_desc' => 'Aktif',
        'sm_level' => '6',
        'sm_program' => 'AT48',
        'pm_program_desc' => 'Software Engineering',
        'sm_graduation_date' => now()->addYears(2)->toDateString(),
    ]);

    $this->withSession([
        'google_auth_user' => [
            'id' => 'google-student-1',
            'name' => 'Student Google',
            'email' => 'student.google@siswa.upsi.edu.my',
            'avatar' => null,
        ],
    ]);

    $response = $this->post(route('auth.google.complete'), [
        'phone' => '01123456789',
        'terms' => '1',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));

    $user = DB::table('h2u_users')->where('hu_email', 'student.google@siswa.upsi.edu.my')->first();

    expect($user)->not->toBeNull()
        ->and($user->hu_role)->toBe('student')
        ->and($user->hu_student_id)->toBe('D2023999001')
        ->and($user->hu_email_verified_at)->not->toBeNull();

    $status = DB::table('h2u_student_statuses')->where('hss_student_id', $user->hu_id)->first();

    expect($status)->not->toBeNull()
        ->and($status->hss_matric_no)->toBe('D2023999001')
        ->and($status->hss_status)->toBe('Active');
});
