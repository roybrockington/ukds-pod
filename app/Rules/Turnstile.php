<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Translation\PotentiallyTranslatedString;
use Throwable;

class Turnstile implements ValidationRule
{
    /**
     * Verify a Cloudflare Turnstile token with Cloudflare's siteverify API.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => config('services.turnstile.secret'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);

            $verified = $response->successful() && $response->json('success') === true;
        } catch (Throwable $e) {
            report($e);

            $verified = false;
        }

        if (! $verified) {
            $fail('We couldn\'t verify you\'re human. Please try the check again.');
        }
    }
}
