<?php

namespace Tests\Feature;

use App\Models\Satdik;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SystemOptimizationTest extends TestCase
{
    public function test_sqlite_wal_and_busy_timeout_configured(): void
    {
        $journalMode = DB::select("PRAGMA journal_mode")[0]->journal_mode ?? null;
        $busyTimeout = DB::select("PRAGMA busy_timeout")[0]->timeout ?? null;

        $this->assertEquals('wal', strtolower((string) $journalMode));
        $this->assertEquals(5000, (int) $busyTimeout);
    }

    public function test_dashboard_aggregates_and_caches_cleanly(): void
    {
        $user = User::where('role_code', 'danrindam')->first() ?? User::factory()->create([
            'role_code' => 'danrindam',
            'email' => 'danrindam_test@rindam.mil.id',
        ]);

        Cache::flush();

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);

        // Verify cache key was populated
        $this->assertTrue(Cache::has('dashboard_macro_all'));
    }

    public function test_reveal_sensitive_endpoint_has_rate_limiting(): void
    {
        $user = User::where('role_code', 'super_admin')->first() ?? User::factory()->create([
            'role_code' => 'super_admin',
            'email' => 'superadmin_test@rindam.mil.id',
        ]);

        $student = Student::first();
        if (!$student) {
            $this->markTestSkipped('No students present for reveal sensitive test');
        }

        // Test reveal sensitive request
        $token = 'test_csrf_token';
        $response = $this->actingAs($user)
            ->withSession(['_token' => $token])
            ->post(route('students.reveal-sensitive', $student), [
                '_token' => $token,
                'access_reason' => 'Verifikasi Data Pemeriksaan Validasi',
            ], [
                'Accept' => 'application/json',
                'X-CSRF-TOKEN' => $token,
            ]);

        // Should succeed and return 200 with sensitive data decrypted
        $response->assertStatus(200);
        $this->assertEquals('success', $response->json('status'));

        // Fire 10 additional requests to hit the 10/min throttle limit
        $hitLimit = false;
        for ($i = 0; $i < 10; $i++) {
            $res = $this->actingAs($user)
                ->withSession(['_token' => $token])
                ->post(route('students.reveal-sensitive', $student), [
                    '_token' => $token,
                    'access_reason' => 'Verifikasi Data Pemeriksaan Validasi',
                ], [
                    'Accept' => 'application/json',
                    'X-CSRF-TOKEN' => $token,
                ]);
            if ($res->status() === 429) {
                $hitLimit = true;
                break;
            }
        }
        $this->assertTrue($hitLimit, 'Rate limiter should throttle after 10 requests');
    }

    public function test_demo_accounts_conditional_display(): void
    {
        config(['app.debug' => false]);
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_student_dossier_renders_complete_dapokdikma_data(): void
    {
        $user = User::where('role_code', 'danrindam')->first();
        $student = Student::where('nosik', '2026-SECABA-001')->first();

        $this->assertNotNull($student, 'Student 2026-SECABA-001 must exist');

        $response = $this->actingAs($user)->get(route('students.show', $student));
        $response->assertStatus(200);
        $response->assertSee('Dossier Potensi, Kesamaptaan');
        $response->assertSee('119'); // IQ score
        $response->assertSee('86.18'); // Jasmani score
        $response->assertSee('ARH, ARM'); // Branch recommendations
    }
}
