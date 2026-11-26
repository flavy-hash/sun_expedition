<?php

namespace Tests\Feature;

use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ExpiredFormTokenTest extends TestCase
{
    public function test_csrf_token_endpoint_returns_the_session_token_uncached(): void
    {
        $response = $this->get(route('csrf-token'))->assertOk()->assertHeader('Content-Type', 'application/json');

        $this->assertSame(session()->token(), $response->json('token'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_an_expired_token_sends_people_back_to_the_form_with_their_input(): void
    {
        // CSRF checks are off in tests, so throw the same exception the middleware would.
        Route::middleware('web')->post('/_expired-form', fn () => throw new TokenMismatchException('CSRF token mismatch.'));

        $this->from('/tours/some-package')
            ->post('/_expired-form', ['_token' => 'stale', 'name' => 'Asha', 'email' => 'asha@example.com'])
            ->assertRedirect('/tours/some-package')
            ->assertSessionHasErrors('session')
            ->assertSessionHasInput('name', 'Asha')
            ->assertSessionMissing('_old_input._token');
    }

    public function test_json_requests_still_get_a_419(): void
    {
        Route::middleware('web')->post('/_expired-json', fn () => throw new TokenMismatchException('CSRF token mismatch.'));

        $this->postJson('/_expired-json')->assertStatus(419);
    }
}
