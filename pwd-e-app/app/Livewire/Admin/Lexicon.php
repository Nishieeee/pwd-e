<?php

namespace App\Livewire\Admin;

use App\Models\ExclusionaryTerm;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Lexicon Management')]
class Lexicon extends Component
{
    use WithPagination;

    public string $phrase = '';

    public string $category = '';

    public string $suggestion = '';

    public bool $isActive = true;

    public ?int $editingId = null;

    public function mount(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }
    }

    public function save(): void
    {
        $data = $this->validate([
            'phrase' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'suggestion' => ['nullable', 'string', 'max:255'],
            'isActive' => ['boolean'],
        ]);

        ExclusionaryTerm::updateOrCreate(
            ['id' => $this->editingId],
            [
                'phrase' => $data['phrase'],
                'category' => $data['category'] ?: null,
                'suggestion' => $data['suggestion'] ?: null,
                'is_active' => $this->isActive,
            ],
        );

        activity()
            ->causedBy(Auth::user())
            ->event($this->editingId ? 'updated' : 'created')
            ->log(($this->editingId ? 'Updated' : 'Added').' lexicon term: '.$data['phrase']);
        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $term = ExclusionaryTerm::findOrFail($id);
        $this->editingId = $term->id;
        $this->phrase = $term->phrase;
        $this->category = $term->category ?? '';
        $this->suggestion = $term->suggestion ?? '';
        $this->isActive = $term->is_active;
    }

    public function toggle(int $id): void
    {
        $term = ExclusionaryTerm::findOrFail($id);
        $term->update(['is_active' => ! $term->is_active]);
        Flux::toast(variant: 'success', text: $term->is_active ? __('Term activated.') : __('Term deactivated.'));
    }

    public function delete(int $id): void
    {
        $term = ExclusionaryTerm::findOrFail($id);
        $term->delete();
        activity()
            ->causedBy(Auth::user())
            ->performedOn($term)
            ->event('deleted')
            ->log('Removed lexicon term: '.$term->phrase);
        Flux::toast(variant: 'success', text: __('Term removed.'));
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->phrase = '';
        $this->category = '';
        $this->suggestion = '';
        $this->isActive = true;
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.admin.lexicon', [
            'terms' => ExclusionaryTerm::orderBy('phrase')->paginate(15),
        ]);
    }
}
