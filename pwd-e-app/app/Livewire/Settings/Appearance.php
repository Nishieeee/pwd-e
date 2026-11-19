<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Appearance settings')]
class Appearance extends Component
{
    public function render()
    {
        $user = Auth::user();
        $layout = $user?->isEmployer() ? 'layouts.employer' : ($user?->isCandidate() ? 'layouts.candidate' : 'layouts.app');

        return view('livewire.settings.appearance')->layout($layout);
    }
}
