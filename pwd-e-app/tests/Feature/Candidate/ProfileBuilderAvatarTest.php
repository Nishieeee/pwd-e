<?php

namespace Tests\Feature\Candidate;

use App\Livewire\Candidate\ProfileBuilder;
use App\Livewire\Candidate\ShowProfile;
use App\Models\User;
use Database\Seeders\CandidateUserSeeder;
use Database\Seeders\PsgcLocationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileBuilderAvatarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['candidate', 'employer', 'admin'] as $role) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
        $this->seed(PsgcLocationSeeder::class);
        $this->seed(CandidateUserSeeder::class);
    }

    public function test_uploading_avatar_in_profile_builder(): void
    {
        Storage::fake('public');

        $user = User::where('email', 'candidate2@pwd-e.local')->first();
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('photo.jpg', 150, 'image/jpeg');

        // Test Livewire component directly
        $component = Livewire::test(ProfileBuilder::class)
            ->set('currentStep', 2)
            ->set('avatarUpload', $file);

        $component->assertHasNoErrors('avatarUpload');
        $this->assertNotNull($component->get('avatarPath'));
        Storage::disk('public')->assertExists($component->get('avatarPath'));

        $html = $component->html();
        $this->assertStringContainsString('Personal Identification', $html);
        $this->assertStringContainsString('Searchable Address', $html);
        $this->assertStringContainsString('Spoken Languages', $html);
        $this->assertStringContainsString('Save draft', $html);
    }

    public function test_removing_avatar_in_profile_builder(): void
    {
        Storage::fake('public');

        $user = User::where('email', 'candidate2@pwd-e.local')->first();
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('photo.jpg', 150, 'image/jpeg');

        $component = Livewire::test(ProfileBuilder::class)
            ->set('currentStep', 2)
            ->set('avatarUpload', $file);

        $this->assertNotNull($component->get('avatarPath'));

        $component->call('removeAvatar');
        $this->assertNull($component->get('avatarPath'));
        $this->assertNull($component->get('avatarUpload'));
    }
}
