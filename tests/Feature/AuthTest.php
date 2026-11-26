<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_guest_is_redirected_to_login_from_buku(): void
    {
        $this->get(route('buku.index'))
            ->assertRedirect(route('login'));
    }

    public function test_login_page_has_a_post_form_with_csrf(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Login Praktikum')
            ->assertSee('name="_token"', false)
            ->assertSee('action="'.route('login.proses').'"', false);
    }

    public function test_failed_login_keeps_email_but_not_password(): void
    {
        $email = 'mahasiswa@example.test';

        $this->from(route('login'))
            ->followingRedirects()
            ->post(route('login.proses'), [
                'email' => $email,
                'password' => 'wrong-password',
            ])
            ->assertOk()
            ->assertSee('Email atau kata sandi salah.')
            ->assertSee('value="'.$email.'"', false)
            ->assertSee('<input type="password" name="password">', false)
            ->assertDontSee('value="wrong-password"', false);
    }

    public function test_valid_login_redirects_to_buku_and_displays_logged_in_user(): void
    {
        $password = 'LatihanWeb123!';
        $user = User::factory()->create([
            'name' => 'Mahasiswa Praktikum',
            'email' => 'mahasiswa@example.test',
            'password' => Hash::make($password),
        ]);

        $this->post(route('login.proses'), [
            'email' => $user->email,
            'password' => $password,
        ])
            ->assertRedirect(route('buku.index'))
            ->assertSessionHas('_token');

        $this->assertAuthenticatedAs($user);

        $this->get(route('buku.index'))
            ->assertOk()
            ->assertSee('Masuk sebagai Mahasiswa Praktikum')
            ->assertSee('(pembaca)')
            ->assertSee('Keluar');
    }

    public function test_logout_invalidates_authentication_and_redirects_to_login(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();

        $this->get(route('buku.index'))
            ->assertRedirect(route('login'));
    }

    public function test_logout_form_posts_to_logout_with_csrf(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('buku.index'))
            ->assertOk()
            ->assertSee('name="_token"', false)
            ->assertSee('action="'.route('logout').'"', false)
            ->assertSee('method="POST"', false);
    }
}
