<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Support\Facades\Session;

class CampaignControllerTest extends TestCase
{
    public function testStoreMethodDispatchesJobs()
    {
        Session::start();
        $token = csrf_token();
        $user = User::factory()->create();
        $file = UploadedFile::fake()->createWithContent('campaign.csv', "name,email\nUser One,user1@example.com\nUser Two,user2@example.com");

        $response = $this->actingAs($user)
            ->postJson(route('upload-campaign'), [
                'file' => $file,
                'campaignName' => 'Test Campaign',
            ], ['X-CSRF-TOKEN' => $token]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Jobs dispatched successfully']);
    }
}
