<?php

namespace Tests\Feature;

use App\Media;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\ApiTestCase;

class GalleryTest extends ApiTestCase
{
    use DatabaseTransactions;

    public function testGetGalleryPhotos()
    {
        $this->actingAs($this->user, 'api');

        /** @var Collection|Media[] $photos */
        $photos = factory(Media::class, 3)
            ->create([
                'model_type' => User::class,
                'model_id' => $this->user->id,
                'collection_name' => User::MEDIA_COLLECTION_GALLERY
            ]);

        $this->getJson('/api/v1/gallery')
            ->assertOk()
            ->assertJsonCount($photos->count(), 'data')
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'url',
                        'name',
                        'file_name',
                        'mime_type'
                    ]
                ]
            ])
            ->assertJson(
                $photos->only(['id', 'name', 'file_name', 'mime_type', 'collection_name'])
                    ->values()
                    ->toArray()
            );
    }

    public function testGetGalleryPhotosOfSpecificUser()
    {
        $this->actingAs($this->user, 'api');

        factory(Media::class, 1)
            ->create([
                'model_type' => User::class,
                'model_id' => $this->user->id,
                'collection_name' => User::MEDIA_COLLECTION_GALLERY
            ]);

        /** @var User $other_user */
        $other_user = factory(User::class)
            ->create();

        /** @var Collection|Media[] $photos */
        $photos = factory(Media::class, 3)
            ->create([
                'model_type' => User::class,
                'model_id' => $other_user->id,
                'collection_name' => User::MEDIA_COLLECTION_GALLERY
            ]);

        $this->getJson('/api/v1/gallery?user_id=' . $other_user->id)
            ->assertOk()
            ->assertJsonCount($photos->count(), 'data')
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'url',
                        'name',
                        'file_name',
                        'mime_type'
                    ]
                ]
            ])
            ->assertJson(
                $photos->only(['id', 'name', 'file_name', 'mime_type', 'collection_name'])
                    ->values()
                    ->toArray()
            );
    }

    public function testGetGalleryPhotosPaginates()
    {
        $this->actingAs($this->user, 'api');

        factory(Media::class, 3)
            ->create([
                'model_type' => User::class,
                'model_id' => $this->user->id,
                'collection_name' => User::MEDIA_COLLECTION_GALLERY
            ]);
        $this->assertApiPaginatesData('/api/v1/gallery', 3, 1);
    }

    public function testUploadMultipleGalleryPhotos()
    {
        $this->actingAs($this->user, 'api');
        $this->postJson('/api/v1/gallery', $payload = [
            'photos' => [
                UploadedFile::fake()->image('photo1.png'),
                UploadedFile::fake()->image('photo2.png'),
            ],
        ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'name' => 'photo1',
                        'file_name' => 'photo1.png',
                        'mime_type' => 'image/png',
                    ],
                    [
                        'name' => 'photo2',
                        'file_name' => 'photo2.png',
                        'mime_type' => 'image/png',
                    ]
                ]
            ]);
        /** @var UploadedFile $photo */
        foreach ($payload['photos'] as $photo) {
            $this->assertDatabaseHas('media', [
                'model_type' => User::class,
                'model_id' => $this->user->id,
                'name' => explode('.', $photo->getClientOriginalName())[0],
                'file_name' => $photo->getClientOriginalName(),
                'mime_type' => 'image/png',
                'collection_name' => 'gallery'
            ]);
        }
    }

    public function testDeleteGalleryPhoto()
    {
        $this->actingAs($this->user, 'api');

        /** @var Media $photo */
        $photo = factory(Media::class)
            ->create([
                'model_type' => User::class,
                'model_id' => $this->user->id,
                'collection_name' => User::MEDIA_COLLECTION_GALLERY
            ]);

        $this->deleteJson('/api/v1/gallery/' . $photo->id)
            ->assertOk();

        $this->assertDatabaseMissing('media', $photo->only('id'));
    }

    public function testDeleteMultipleGalleryPhotos()
    {
        $this->actingAs($this->user, 'api');

        /** @var Collection|Media[] $photos */
        $photos = factory(Media::class, 3)
            ->create([
                'model_type' => User::class,
                'model_id' => $this->user->id,
                'collection_name' => User::MEDIA_COLLECTION_GALLERY
            ]);

        $this->deleteJson('/api/v1/gallery', [
            'id' => $photos->pluck('id')->all()
        ])
            ->assertSuccessful();

        foreach ($photos as $photo) {
            $this->assertDatabaseMissing('media', $photo->only('id'));
        }
    }

    public function testCannotDeleteOtherUserGalleryPhotos()
    {
        $this->actingAs($this->user, 'api');

        /** @var Collection|Media[] $photos */
        $photos = factory(Media::class, 3)
            ->create([
                'model_type' => User::class,
                'model_id' => factory(User::class)->create()->id,
                'collection_name' => User::MEDIA_COLLECTION_GALLERY
            ]);

        $this->deleteJson('/api/v1/gallery', [
            'id' => $photos->pluck('id')->all()
        ])
            ->assertForbidden();
    }
}
