<div class="max-w-5xl mx-auto mb-6">
    <nav class="flex items-center text-xs text-gray-500 gap-1.5 mb-3" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span aria-hidden="true">/</span>
        <span class="text-gray-900 font-medium" aria-current="page">{{ __('Settings') }}</span>
    </nav>
    <flux:heading size="xl" level="1">{{ __('Account Settings') }}</flux:heading>
    <flux:subheading size="lg" class="mb-2">{{ __('Manage your profile and account preferences') }}</flux:subheading>
</div>
