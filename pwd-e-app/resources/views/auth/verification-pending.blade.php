<x-layouts::auth :title="__('Account Verification Status')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Account Verification Status')" :description="__('Your account credentials and statutory accreditation documents are reviewed by the PESO / LGU administrator under RA 10524.')" />

        @livewire('auth.resubmit-verification')

        <div class="flex flex-col gap-3">
            <a href="{{ route('home') }}" class="inline-flex h-11 items-center justify-center rounded-lg bg-primary-dark text-white font-semibold hover:bg-primary transition-colors">Back to Home</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex h-11 w-full items-center justify-center rounded-lg border border-primary-border text-primary-dark font-semibold hover:bg-primary-tint transition-colors cursor-pointer">Sign out</button>
            </form>
        </div>
    </div>
</x-layouts::auth>
