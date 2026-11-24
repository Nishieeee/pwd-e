<?php

namespace Tests\Feature\Candidate;

use App\Livewire\Candidate\ProfileBuilder;
use App\Models\User;
use Database\Seeders\PsgcLocationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileBuilderStepTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['candidate', 'employer', 'admin'] as $role) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
        $this->seed(PsgcLocationSeeder::class);
    }

    public function test_candidate_step_navigation_with_validation_feedback(): void
    {
        Storage::fake('public');

        $user = User::factory()->verified()->create([
            'role' => 'candidate',
            'name' => 'Juan Dela Cruz',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'pwd_id_number' => '12-3456-7890',
        ]);
        $user->assignRole('candidate');

        $this->actingAs($user);

        $component = Livewire::test(ProfileBuilder::class);
        $this->assertEquals(1, $component->get('currentStep'));

        // Advance from Step 1 to Step 2
        $component->call('nextStep');
        $this->assertEquals(2, $component->get('currentStep'));

        // On Step 2, missing phone should prevent moving to step 3 and display validation error
        $component->call('nextStep');
        $this->assertEquals(2, $component->get('currentStep'));
        $component->assertHasErrors(['phone']);

        // Now provide phone and city
        $component->set('phone', '+63 917 123 4567');
        $component->set('city', 'Zamboanga City');

        // Now step 2 -> step 3 succeeds
        $component->call('nextStep');
        $this->assertEquals(3, $component->get('currentStep'));

        // From Step 3, jump to Step 4
        $component->call('goToStep', 4);
        $this->assertEquals(4, $component->get('currentStep'));

        // Jump to Step 5
        $component->call('goToStep', 5);
        $this->assertEquals(5, $component->get('currentStep'));

        // Back to Step 1
        $component->call('goToStep', 1);
        $this->assertEquals(1, $component->get('currentStep'));

        // Jump from Step 1 back to Step 5 (since step 2 details are already valid)
        $component->call('goToStep', 5);
        $this->assertEquals(5, $component->get('currentStep'));
    }

    public function test_candidate_can_proceed_step_by_step_and_save(): void
    {
        Storage::fake('public');

        $user = User::factory()->verified()->create([
            'role' => 'candidate',
            'name' => 'Juan Dela Cruz',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'pwd_id_number' => '12-3456-7890',
        ]);
        $user->assignRole('candidate');

        $this->actingAs($user);

        Livewire::test(ProfileBuilder::class)
            // Step 1: Input mode
            ->assertSet('currentStep', 1)
            ->call('nextStep')
            // Step 2: Professional details
            ->assertSet('currentStep', 2)
            ->set('phone', '+63 917 987 6543')
            ->set('city', 'Zamboanga City')
            ->call('nextStep')
            // Step 3: Accommodations
            ->assertSet('currentStep', 3)
            ->call('nextStep')
            // Step 4: Attachments
            ->assertSet('currentStep', 4)
            ->call('nextStep')
            // Step 5: Review & save
            ->assertSet('currentStep', 5)
            ->call('saveProfileAndRedirect')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('candidate_profiles', [
            'user_id' => $user->id,
            'phone' => '+63 917 987 6543',
        ]);
    }
}
