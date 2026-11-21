<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /** Izinkan request masuk ke tahap validasi dan autentikasi. */
    public function authorize(): bool
    {
        return true;
    }

    /** Atur kolom login yang wajib diisi; pengguna dapat memakai email atau nomor HP. */
    public function rules(): array
    {
        return [
            'login'    => ['required', 'string'],   // Email ATAU No. HP
            'password' => ['required', 'string'],
        ];
    }

    /** Autentikasi berdasarkan email/nomor HP serta batasi percobaan login berulang. */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = trim($this->input('login'));
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $login];
        } else {
            $credentials = ['phone' => User::normalizePhone($login)];
        }
        $credentials['password'] = $this->input('password');

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /** Tolak login sementara jika batas percobaan untuk akun dan alamat IP tercapai. */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /** Buat kunci pembatasan percobaan yang unik untuk login dan alamat IP pemohon. */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('login')) . '|' . $this->ip());
    }
}
