@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6">
    <h1 class="text-xl font-semibold mb-4">Two-factor instellen</h1>

    @if (session('status') === 'two-factor-authentication-enabled')
        <div class="mb-4 p-4 bg-blue-100 text-blue-700 rounded">
            Rond de instelling hieronder af (scan QR + bevestig code).
        </div>
    @endif

    @if (session('status') === 'two-factor-authentication-confirmed')
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
            2FA is actief. Je kunt nu verder.
        </div>
    @endif

    @php($user = request()->user())

    @if (empty($user->two_factor_secret))
        {{-- Enable --}}
        <form method="POST" action="/user/two-factor-authentication">
            @csrf
            <button type="submit" class="px-4 py-2 border rounded">
                2FA inschakelen
            </button>
        </form>
        <p class="text-sm mt-3">
            Let op: Fortify vereist wachtwoordbevestiging voor deze actie.
        </p>
    @else
        {{-- Show QR --}}
        <div class="my-4 p-4 bg-gray-100 rounded flex justify-center">
            {!! $user->twoFactorQrCodeSvg() !!}
        </div>

        {{-- Confirm --}}
        <form method="POST" action="/user/confirmed-two-factor-authentication" class="mt-4">
            @csrf
            <label class="block mb-2 font-semibold">Code uit je authenticator</label>
            <input name="code" class="border rounded w-full p-2" autocomplete="one-time-code" required />
            @error('code') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
            <button class="mt-3 px-4 py-2 border rounded bg-blue-500 text-white" type="submit">Bevestigen</button>
        </form>

        {{-- Recovery codes --}}
        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded">
            <h2 class="font-semibold mb-2">Recovery codes</h2>
            <p class="text-sm mb-3">Sla deze codes veilig op. Je kunt ze gebruiken om in te loggen als je authenticator verloren gaat.</p>
            <ul class="list-disc pl-6 space-y-1">
                @foreach ($user->recoveryCodes() as $code)
                    <li class="font-mono text-sm">{{ $code }}</li>
                @endforeach
            </ul>

            <form method="POST" action="/user/two-factor-recovery-codes" class="mt-4">
                @csrf
                <button class="px-4 py-2 border rounded text-sm" type="submit">
                    Nieuwe recovery codes genereren
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
