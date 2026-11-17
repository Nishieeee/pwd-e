<div class="max-w-7xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Verification Queue</h1>

    <div class="flex flex-wrap gap-2 mb-4" role="tablist" aria-label="Status filter">
        @foreach (['pending','needs_more_docs','approved','rejected','all'] as $s)
            <button type="button"
                wire:click="setStatusFilter('{{ $s }}')"
                class="px-3 py-2 min-h-[44px] rounded border {{ $status === $s ? 'bg-blue-600 text-white' : 'bg-white text-gray-700' }}"
                aria-pressed="{{ $status === $s ? 'true' : 'false' }}">
                {{ ucfirst(str_replace('_',' ',$s)) }} ({{ $counts[$s] ?? 0 }})
            </button>
        @endforeach
    </div>

    @if ($requests->isEmpty())
        <p class="text-gray-600">No verification requests.</p>
    @else
        <div class="space-y-4">
            @foreach ($requests as $vr)
                <article class="border rounded p-4 bg-white">
                    <header class="flex flex-wrap justify-between gap-2 mb-2">
                        <div>
                            <h2 class="font-semibold">{{ $vr->user->name ?? 'User #'.$vr->user_id }}</h2>
                            <p class="text-sm text-gray-600">{{ $vr->user->email ?? '' }}</p>
                        </div>
                        <span class="text-sm font-medium px-2 py-1 rounded bg-gray-100">{{ str_replace('_',' ',$vr->status) }}</span>
                    </header>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm mb-3">
                        <div><dt class="font-medium">Type</dt><dd>{{ $vr->type }}</dd></div>
                        <div><dt class="font-medium">ID number</dt><dd>{{ $vr->id_number }}</dd></div>
                        <div><dt class="font-medium">Submitted</dt><dd>{{ $vr->created_at?->format('Y-m-d H:i') }}</dd></div>
                        @if ($vr->reviewed_at)
                            <div><dt class="font-medium">Reviewed</dt><dd>{{ $vr->reviewed_at->format('Y-m-d H:i') }}</dd></div>
                        @endif
                    </dl>

                    @if ($vr->document_path)
                        <div class="mb-3">
                            <a href="{{ route('admin.verifications.document', $vr) }}" target="_blank" rel="noopener" class="text-blue-600 underline">View document</a>
                        </div>
                    @endif

                    @if ($vr->rejection_reason)
                        <p class="text-sm text-red-700 mb-3">Note: {{ $vr->rejection_reason }}</p>
                    @endif

                    @if (in_array($vr->status, ['pending','needs_more_docs']))
                        <div class="flex flex-wrap gap-2">
                            <button type="button" wire:click="approve({{ $vr->id }})" class="px-3 py-2 min-h-[44px] rounded bg-green-600 text-white">Approve</button>
                            <button type="button" wire:click="openReview({{ $vr->id }})" class="px-3 py-2 min-h-[44px] rounded bg-red-600 text-white">Reject / Request docs</button>
                        </div>

                        @if ($reviewingId === $vr->id)
                            <div class="mt-3 border-t pt-3">
                                <label for="note-{{ $vr->id }}" class="block text-sm font-medium mb-1">Review note</label>
                                <textarea id="note-{{ $vr->id }}" wire:model="reviewNote" rows="3" class="w-full border rounded p-2"></textarea>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <button type="button" wire:click="reject({{ $vr->id }})" class="px-3 py-2 min-h-[44px] rounded bg-red-600 text-white">Reject</button>
                                    <button type="button" wire:click="requestMoreDocs({{ $vr->id }})" class="px-3 py-2 min-h-[44px] rounded bg-yellow-600 text-white">Request more docs</button>
                                    <button type="button" wire:click="closeReview" class="px-3 py-2 min-h-[44px] rounded bg-gray-200">Cancel</button>
                                </div>
                            </div>
                        @endif
                    @endif
                </article>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    @endif
</div>