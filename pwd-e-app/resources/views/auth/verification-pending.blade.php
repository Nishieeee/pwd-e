<x-layouts::auth :title="__('Account Pending Verification')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Account under review')" :description="__('Your account is pending verification by the PESO/LGU administrator.')" />

        <div class="rounded-lg border border-primary-border bg-primary-tint px-4 py-3 text-sm text-primary-dark" role="status" aria-live="polite">
            <p class="font-semibold">Status: Pending</p>
            <p class="mt-1">You can sign in, but cannot submit applications or post jobs until your account is approved.</p>
        </div>

        <div class="flex flex-col gap-3">
            <a href="{{ route('home') }}" class="inline-flex h-11 items-center justify-center rounded-lg bg-primary-dark text-white font-semibold hover:bg-primary">Back to Home</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex h-11 w-full items-center justify-center rounded-lg border border-primary-border text-primary-dark font-semibold hover:bg-primary-tint">Sign out</button>
            </form>
        </div>
    </div>
</x-layouts::auth>