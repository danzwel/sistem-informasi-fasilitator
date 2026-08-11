<?php

namespace Tests\Feature;

use App\Imports\PelatihanImport;
use App\Models\ActivityLog;
use App\Models\Fasilitator;
use App\Models\Kegiatan;
use App\Models\Materi;
use App\Models\Pengajuan;
use App\Models\Penyelenggara;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class PhaseTwoFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_role_middleware_protect_domain_routes(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer']);

        $this->post(route('login.store'), [
            'email' => $viewer->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($viewer);
        $this->assertDatabaseHas('activity_logs', ['user_id' => $viewer->id, 'action' => 'login']);

        $this->postJson(route('materi.store'), [
            'nama' => 'Manajemen Kelas',
            'status' => 'aktif',
        ])->assertForbidden();
    }

    public function test_operator_can_create_master_kegiatan_and_fasilitator_relations(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $fasilitator = Fasilitator::create(['nama' => 'Fasilitator Uji', 'status' => 'aktif']);
        $penyelenggara = Penyelenggara::create(['nama' => 'Penyelenggara Uji', 'status' => 'aktif']);

        $this->actingAs($operator)->postJson(route('materi.store'), [
            'nama' => 'Manajemen Kelas',
            'status' => 'aktif',
        ])->assertCreated();

        $materi = Materi::firstOrFail();
        $fasilitator->materis()->attach($materi);

        $this->postJson(route('kegiatan.store'), [
            'nama' => 'Pelatihan Uji',
            'tanggal_mulai' => '2026-08-11',
            'penyelenggara_id' => $penyelenggara->id,
            'status' => 'upcoming',
            'fasilitators' => [['id' => $fasilitator->id, 'peran' => 'Narasumber']],
        ])->assertCreated()->assertJsonPath('nama', 'Pelatihan Uji');

        $kegiatan = Kegiatan::firstOrFail();
        $this->assertTrue($kegiatan->fasilitators->contains($fasilitator));
        $this->assertTrue($fasilitator->fresh()->materis->contains($materi));
        $this->assertDatabaseHas('activity_logs', ['user_id' => $operator->id, 'action' => 'create']);
    }

    public function test_admin_reviews_pengajuan_and_operator_records_one_rating_with_review(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $admin = User::factory()->create(['role' => 'admin']);
        $fasilitator = Fasilitator::create(['nama' => 'Fasilitator Penilaian', 'status' => 'aktif']);
        $kegiatan = Kegiatan::create(['nama' => 'Kegiatan Penilaian', 'tanggal_mulai' => '2026-08-11', 'status' => 'ongoing']);

        $this->actingAs($operator)->postJson(route('pengajuan.store'), [
            'fasilitator_id' => $fasilitator->id,
            'kegiatan_id' => $kegiatan->id,
            'nama_kegiatan' => $kegiatan->nama,
        ])->assertCreated();

        $pengajuan = Pengajuan::firstOrFail();
        $this->actingAs($admin)->patchJson(route('pengajuan.review', $pengajuan), [
            'status' => 'approved',
            'catatan_admin' => 'Disetujui untuk kegiatan uji.',
        ])->assertOk()->assertJsonPath('status', 'approved');

        $this->actingAs($operator)->postJson(route('rating.store'), [
            'kegiatan_id' => $kegiatan->id,
            'fasilitator_id' => $fasilitator->id,
            'rating' => 5,
            'review' => 'Sangat baik.',
        ])->assertCreated();

        $this->assertDatabaseHas('ratings', [
            'kegiatan_id' => $kegiatan->id,
            'fasilitator_id' => $fasilitator->id,
            'reviewer_id' => $operator->id,
            'rating' => 5,
            'review' => 'Sangat baik.',
        ]);
        $this->assertSame($admin->id, $pengajuan->fresh()->reviewed_by);
    }

    public function test_dashboard_monitors_domain_status_and_cv_includes_kegiatan(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer']);
        $fasilitator = Fasilitator::create(['nama' => 'Fasilitator CV', 'status' => 'aktif']);
        $kegiatan = Kegiatan::create(['nama' => 'Kegiatan CV', 'tanggal_mulai' => '2026-08-11', 'status' => 'ongoing']);
        $kegiatan->fasilitators()->attach($fasilitator, ['peran' => 'Fasilitator']);
        Pengajuan::create([
            'fasilitator_id' => $fasilitator->id,
            'nama_kegiatan' => 'Pengajuan CV',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
        ActivityLog::create(['user_id' => $viewer->id, 'action' => 'create', 'description' => 'Aktivitas uji.']);

        $this->actingAs($viewer)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pengajuan Pending')
            ->assertSee('Aktivitas uji.');

        $this->get(route('fasilitator.cv', $fasilitator))
            ->assertOk()
            ->assertSee('Kegiatan CV');
    }

    public function test_import_preview_transform_does_not_write_data(): void
    {
        $import = new PelatihanImport(true);
        $import->collection(new Collection([
            [null, null, null, null, null, null],
            [null, null, null, null, null, null],
            [null, '2026', 'Agustus', 'Pelatihan Uji', 'Materi Uji', '  Nama   Uji  '],
        ]));

        $this->assertSame(1, $import->ringkasan['baris_diproses']);
        $this->assertSame('Nama Uji', $import->ringkasan['valid'][0]['nama_fasilitator']);
        $this->assertDatabaseCount('fasilitators', 0);
        $this->assertDatabaseCount('riwayat_pelatihans', 0);
    }
}
