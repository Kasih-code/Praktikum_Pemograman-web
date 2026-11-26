<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BukuTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->create();
        $admin->role = 'admin';
        $admin->save();

        $this->actingAs($admin);
    }

    public function test_buku_index_displays_both_sample_books_in_the_expected_table(): void
    {
        $this->get('/buku')
            ->assertOk()
            ->assertSee('BK001')
            ->assertSee('BK002')
            ->assertDontSee('BK003')
            ->assertDontSee('BK004')
            ->assertSee('Belajar Laravel')
            ->assertSee('Pemrograman')
            ->assertSee('<th>Kode</th>', false)
            ->assertSee('<th>Judul</th>', false)
            ->assertSee('<th>Kategori</th>', false)
            ->assertSee('<th>Aksi</th>', false)
            ->assertDontSee('<th>Detail</th>', false)
            ->assertDontSee('Cari judul');
    }

    public function test_buku_index_filters_titles_using_the_cari_query(): void
    {
        $this->get('/buku?cari=lArAvEl')
            ->assertOk()
            ->assertSee('BK002')
            ->assertDontSee('BK001')
            ->assertDontSee('BK003')
            ->assertDontSee('BK004');
    }

    public function test_bk002_can_be_found_by_searching_its_title(): void
    {
        $this->get('/buku?cari=Laravel')
            ->assertOk()
            ->assertSee('BK002')
            ->assertSee('Belajar Laravel')
            ->assertDontSee('BK001');
    }

    public function test_buku_index_shows_an_empty_state_when_no_title_matches(): void
    {
        $this->get('/buku?cari=TidakAda')
            ->assertOk()
            ->assertSee('Data buku tidak ditemukan.');
    }

    public function test_buku_detail_route_is_not_available(): void
    {
        $this->get('/buku/BK002')->assertStatus(405);
    }

    public function test_reader_can_view_buku_without_write_controls(): void
    {
        $reader = User::factory()->create()->refresh();
        $this->assertSame('pembaca', $reader->role);

        $this->actingAs($reader)
            ->get(route('buku.index'))
            ->assertOk()
            ->assertSee('(pembaca)')
            ->assertDontSee('Tambah Buku')
            ->assertDontSee('<th>Aksi</th>', false)
            ->assertDontSee('Ubah');
    }

    public function test_reader_cannot_open_the_book_creation_form(): void
    {
        $reader = User::factory()->create();

        $this->actingAs($reader)
            ->get(route('buku.create'))
            ->assertForbidden();
    }

    public function test_reader_cannot_create_a_book(): void
    {
        $reader = User::factory()->create();
        $kategori = Kategori::where('nama', 'Pemrograman')->firstOrFail();

        $this->actingAs($reader)
            ->post(route('buku.store'), [
                'kode' => 'BK005',
                'judul' => 'Pemrograman Laravel',
                'kategori_id' => $kategori->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('bukus', ['kode' => 'BK005']);
    }

    public function test_reader_cannot_edit_update_or_delete_a_book(): void
    {
        $reader = User::factory()->create()->refresh();
        $buku = Buku::where('kode', 'BK001')->firstOrFail();

        $this->actingAs($reader)
            ->get(route('buku.edit', $buku))
            ->assertForbidden();

        $this->put(route('buku.update', $buku), [
            'kode' => 'BK001',
            'judul' => 'Judul tidak berwenang',
            'kategori_id' => $buku->kategori_id,
        ])->assertForbidden();

        $this->delete(route('buku.destroy', $buku))
            ->assertForbidden();

        $this->assertDatabaseHas('bukus', [
            'id' => $buku->id,
            'judul' => 'Dasar PHP',
        ]);
    }

    public function test_valid_book_is_created_and_persisted(): void
    {
        $kategori = Kategori::where('nama', 'Pemrograman')->firstOrFail();

        $this->post(route('buku.store'), [
            'kode' => 'BK005',
            'judul' => 'Pemrograman Laravel',
            'kategori_id' => $kategori->id,
        ])
            ->assertRedirect(route('buku.index'))
            ->assertSessionHas('status', 'Buku ditambahkan.');

        $this->assertDatabaseHas('bukus', [
            'kode' => 'BK005',
            'judul' => 'Pemrograman Laravel',
            'kategori_id' => $kategori->id,
        ]);
    }

    public function test_book_creation_rejects_duplicate_codes(): void
    {
        $kategori = Kategori::where('nama', 'Pemrograman')->firstOrFail();

        $this->post(route('buku.store'), [
            'kode' => 'BK001',
            'judul' => 'Judul baru',
            'kategori_id' => $kategori->id,
        ])->assertSessionHasErrors('kode');

        $this->assertDatabaseMissing('bukus', ['judul' => 'Judul baru']);
    }

    public function test_book_update_accepts_its_existing_code(): void
    {
        $buku = Buku::where('kode', 'BK001')->firstOrFail();
        $kategori = Kategori::where('nama', 'Pemrograman')->firstOrFail();

        $this->put(route('buku.update', $buku), [
            'kode' => 'BK001',
            'judul' => 'Dasar PHP diperbarui',
            'kategori_id' => $kategori->id,
        ])
            ->assertRedirect(route('buku.index'))
            ->assertSessionHas('status', 'Buku diperbarui.');

        $this->assertDatabaseHas('bukus', [
            'id' => $buku->id,
            'kode' => 'BK001',
            'judul' => 'Dasar PHP diperbarui',
        ]);
    }

    public function test_book_can_be_deleted(): void
    {
        $buku = Buku::where('kode', 'BK001')->firstOrFail();

        $this->delete(route('buku.destroy', $buku))
            ->assertRedirect(route('buku.index'))
            ->assertSessionHas('status', 'Buku dihapus.');

        $this->assertDatabaseMissing('bukus', ['id' => $buku->id]);
    }

    public function test_book_creation_requires_all_fields(): void
    {
        $this->post(route('buku.store'), [])
            ->assertSessionHasErrors(['kode', 'judul', 'kategori_id']);

        $this->assertDatabaseCount('bukus', 2);
    }

    public function test_book_creation_rejects_an_unknown_category(): void
    {
        $this->post(route('buku.store'), [
            'kode' => 'BK005',
            'judul' => 'Pemrograman Laravel',
            'kategori_id' => 99999,
        ])->assertSessionHasErrors('kategori_id');

        $this->assertDatabaseMissing('bukus', ['kode' => 'BK005']);
    }

    public function test_book_form_displays_available_categories(): void
    {
        $this->get(route('buku.create'))
            ->assertOk()
            ->assertSee('<select name="kategori_id">', false)
            ->assertSee('Pemrograman')
            ->assertSee('Basis Data');
    }
}
