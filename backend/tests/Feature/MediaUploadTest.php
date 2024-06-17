<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use App\Services\FileSystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class MediaUploadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function store_media_file(): void
    {
        $filesystem = new FileSystem();
        Storage::fake($filesystem->disk->value);

        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('avatar.png');
        $response = $this->actingAs($user, 'api')
            ->postJson(route('v1.media.post'), [
                'media' => $file,
            ]);

        // Assert one or more files were stored...
        Storage::disk($filesystem->disk->value)->assertExists('media');
        $this->assertDatabaseHas('media', [
            'disk' => $filesystem->disk->value,
        ]);
    }

    /** @test */
    public function store_media_url(): void
    {
        $this->markTestSkipped();
        $filesystem = new FileSystem();
        Storage::fake($filesystem->disk->value);

        $user = User::factory()->create();

        $file = config('app.url').'/storage/'.config('backend.media.image.path');
        $response = $this->actingAs($user, 'api')
            ->postJson(route('v1.media.post'), [
                'media' => 'https://www.hardeverse.org/images/logo/logo-bs.png',
            ]);

        // Assert one or more files were stored...
        Storage::disk($filesystem->disk->value)->assertExists('media');
        $this->assertDatabaseHas('media', [
            'disk' => $filesystem->disk->value,
        ]);
    }

    /** @test */
    public function a_media_resource_may_be_viewed(): void
    {
        $media = Media::factory()->create();
        $response = $this->getJson(route('v1.media.show', [
            'media' => $media->uuid,
        ]))
            ->assertStatus(200);

        $response->assertJson(fn (AssertableJson $json) => $json
            ->where('status', true)
            ->where('message', 'success')
            ->where('data.uuid', $media->uuid)
            ->where('data.id', $media->id)
            ->etc()
        );

    }
}
