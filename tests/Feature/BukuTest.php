<?php

namespace Tests\Feature;

use Tests\TestCase;

class BukuTest extends TestCase
{
    public function test_buku_index_displays_all_four_sample_books(): void
    {
        $this->get('/buku')
            ->assertOk()
            ->assertSee('BK001')
            ->assertSee('BK002')
            ->assertSee('BK003')
            ->assertSee('BK004')
            ->assertSee('Pemrograman Web Lanjutan');
    }

    public function test_buku_index_filters_titles_using_the_cari_query(): void
    {
        $this->get('/buku?cari=lArAvEl')
            ->assertOk()
            ->assertSee('BK002')
            ->assertSee('value="lArAvEl"', false)
            ->assertDontSee('BK001')
            ->assertDontSee('BK003')
            ->assertDontSee('BK004');
    }

    public function test_bk004_can_be_found_by_searching_its_title(): void
    {
        $this->get('/buku?cari=Lanjutan')
            ->assertOk()
            ->assertSee('BK004')
            ->assertSee('Pemrograman Web Lanjutan')
            ->assertDontSee('BK002');
    }

    public function test_buku_index_shows_an_empty_state_when_no_title_matches(): void
    {
        $this->get('/buku?cari=TidakAda')
            ->assertOk()
            ->assertSee('Data buku tidak ditemukan.');
    }

    public function test_buku_detail_displays_bk004(): void
    {
        $this->get('/buku/BK004')
            ->assertOk()
            ->assertSee('BK004')
            ->assertSee('Pemrograman Web Lanjutan');
    }

    public function test_unknown_buku_detail_returns_not_found(): void
    {
        $this->get('/buku/BK999')->assertNotFound();
    }
}
