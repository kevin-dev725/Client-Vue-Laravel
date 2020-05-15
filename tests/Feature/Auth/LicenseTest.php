<?php

namespace Tests\Feature\Auth;

use App\License;
use App\Media;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\ApiTestCase;

class LicenseTest extends ApiTestCase
{
    use DatabaseTransactions;

    public function testAddLicense()
    {
        $this->actingAs($this->user, 'api');

        $response = $this->postJson('/api/v1/license', $payload = [
            'name' => $this->faker->name,
            'number' => $this->faker->uuid,
            'expiration' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'is_insured' => $this->faker->boolean,
            'photos' => [
                [
                    'photo' => UploadedFile::fake()->image('front.png'),
                ],
                [
                    'photo' => UploadedFile::fake()->image('back.png'),
                ],
            ],
            'certs' => [
                [
                    'photo' => UploadedFile::fake()->image('cert1.png'),
                ],
                [
                    'photo' => UploadedFile::fake()->image('cert2.png'),
                ],
                [
                    'photo' => UploadedFile::fake()->image('cert3.png'),
                ],
            ],
        ])
            ->assertOk()
            ->assertJsonStructure([
                'photos' => [
                    'data' => [
                        [
                            'id',
                            'url',
                            'name',
                            'file_name',
                            'mime_type',
                        ]
                    ]
                ],
                'certs' => [
                    'data' => [
                        [
                            'id',
                            'url',
                            'name',
                            'file_name',
                            'mime_type',
                        ]
                    ]
                ],
            ])
            ->assertJson(array_merge(
                array_only($payload, ['name', 'number', 'expiration', 'is_insured']),
                [
                    'photos' => [
                        'data' => array_map(function ($item) {
                            /** @var UploadedFile $photo */
                            $photo = $item['photo'];
                            return [
                                'name' => explode('.', $photo->getClientOriginalName())[0],
                                'file_name' => $photo->getClientOriginalName(),
                                'mime_type' => 'image/png'
                            ];
                        }, $payload['photos'])
                    ],
                    'certs' => [
                        'data' => array_map(function ($item) {
                            /** @var UploadedFile $photo */
                            $photo = $item['photo'];
                            return [
                                'name' => explode('.', $photo->getClientOriginalName())[0],
                                'file_name' => $photo->getClientOriginalName(),
                                'mime_type' => 'image/png'
                            ];
                        }, $payload['certs'])
                    ],
                ]
            ));

        $this->assertDatabaseHas('licenses', array_merge(
            array_only($payload, ['name', 'number', 'expiration', 'is_insured']),
            ['user_id' => $this->user->id]
        ));

        foreach ($payload['photos'] as $item) {
            /** @var UploadedFile $photo */
            $photo = $item['photo'];
            $this->assertDatabaseHas('media', [
                'model_type' => License::class,
                'model_id' => $response->json('id'),
                'collection_name' => License::MEDIA_COLLECTION_PHOTOS,
                'name' => explode('.', $photo->getClientOriginalName())[0],
                'file_name' => $photo->getClientOriginalName(),
                'mime_type' => 'image/png'
            ]);
        }

        foreach ($payload['certs'] as $item) {
            /** @var UploadedFile $cert */
            $cert = $item['photo'];
            $this->assertDatabaseHas('media', [
                'model_type' => License::class,
                'model_id' => $response->json('id'),
                'collection_name' => License::MEDIA_COLLECTION_CERTS,
                'name' => explode('.', $cert->getClientOriginalName())[0],
                'file_name' => $cert->getClientOriginalName(),
                'mime_type' => 'image/png'
            ]);
        }
    }

    public function testUpdateLicense()
    {
        $this->actingAs($this->user, 'api');

        /** @var License $license */
        $license = factory(License::class)
            ->states(['with photos', 'with certs'])
            ->create([
                'user_id' => $this->user->id,
            ]);

        $this->putJson('/api/v1/license/' . $license->id, $payload = [
            'name' => $this->faker->name,
            'number' => $this->faker->uuid,
            'expiration' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'is_insured' => $this->faker->boolean,
            'photos' => [
                [
                    'id' => $license->photos()->first()->id,
                    'photo' => null,
                ],
                [
                    'id' => null,
                    'photo' => UploadedFile::fake()->image('back.png'),
                ],
            ],
            'certs' => [
                [
                    'id' => $license->certs()->first()->id,
                    'photo' => null,
                ],
                [
                    'id' => null,
                    'photo' => UploadedFile::fake()->image('cert2.png'),
                ],
            ],
        ])
            ->assertOk()
            ->assertJsonStructure([
                'photos' => [
                    'data' => [
                        [
                            'id',
                            'url',
                            'name',
                            'file_name',
                            'mime_type',
                        ]
                    ]
                ],
                'certs' => [
                    'data' => [
                        [
                            'id',
                            'url',
                            'name',
                            'file_name',
                            'mime_type',
                        ]
                    ]
                ],
            ])
            ->assertJsonCount(2, 'photos.data')
            ->assertJsonCount(2, 'certs.data')
            ->assertJson(array_merge(
                array_only($payload, ['name', 'number', 'expiration', 'is_insured']),
                [
                    'photos' => [
                        'data' => array_map(function ($item) {
                            /** @var Media $media */
                            $media = null;
                            /** @var UploadedFile $photo */
                            $photo = array_get($item, 'photo');
                            if ($item['id']) {
                                $media = Media::find($item['id']);
                            }
                            return [
                                'name' => $photo ? explode('.', $photo->getClientOriginalName())[0] : $media->name,
                                'file_name' => $photo ? $photo->getClientOriginalName() : $media->file_name,
                                'mime_type' => 'image/png'
                            ];
                        }, $payload['photos'])
                    ],
                    'certs' => [
                        'data' => array_map(function ($item) {
                            /** @var Media $media */
                            $media = null;
                            /** @var UploadedFile $photo */
                            $photo = array_get($item, 'photo');
                            if ($item['id']) {
                                $media = Media::find($item['id']);
                            }
                            return [
                                'name' => $photo ? explode('.', $photo->getClientOriginalName())[0] : $media->name,
                                'file_name' => $photo ? $photo->getClientOriginalName() : $media->file_name,
                                'mime_type' => 'image/png'
                            ];
                        }, $payload['certs'])
                    ],
                ]
            ));

        $this->assertDatabaseHas('licenses', array_merge(
            array_only($payload, ['name', 'number', 'expiration', 'is_insured']),
            $license->only('id')
        ));

        foreach ($payload['photos'] as $item) {
            /** @var Media $media */
            $media = Media::find(array_get($item, 'id'));
            /** @var UploadedFile $photo */
            $photo = array_get($item, 'photo');
            $data = [
                'model_type' => License::class,
                'model_id' => $license->id,
                'collection_name' => License::MEDIA_COLLECTION_PHOTOS,
                'mime_type' => 'image/png'
            ];
            if ($media) {
                $data = array_merge(
                    $data,
                    $media->only('id', 'name', 'file_name')
                );
            }
            if ($photo) {
                $data = array_merge(
                    $data,
                    [
                        'name' => explode('.', $photo->getClientOriginalName())[0],
                        'file_name' => $photo->getClientOriginalName(),
                    ]
                );
            }
            $this->assertDatabaseHas('media', $data);
        }

        foreach ($payload['certs'] as $item) {
            /** @var Media $media */
            $media = Media::find(array_get($item, 'id'));
            /** @var UploadedFile $photo */
            $photo = array_get($item, 'photo');
            $data = [
                'model_type' => License::class,
                'model_id' => $license->id,
                'collection_name' => License::MEDIA_COLLECTION_CERTS,
                'mime_type' => 'image/png'
            ];
            if ($media) {
                $data = array_merge(
                    $data,
                    $media->only('id', 'name', 'file_name')
                );
            }
            if ($photo) {
                $data = array_merge(
                    $data,
                    [
                        'name' => explode('.', $photo->getClientOriginalName())[0],
                        'file_name' => $photo->getClientOriginalName(),
                    ]
                );
            }
            $this->assertDatabaseHas('media', $data);
        }
    }

    public function testGetLicenses()
    {
        $this->actingAs($this->user, 'api');

        /** @var Collection|License[] $licenses */
        $licenses = factory(License::class, 3)
            ->states(['with photos', 'with certs'])
            ->create([
                'user_id' => $this->user->id,
            ]);

        // create license for other user
        factory(License::class)
            ->states(['with photos', 'with certs'])
            ->create();

        $response = $this->getJson('/api/v1/license')
            ->assertOk()
            ->assertJsonCount($licenses->count(), 'data');

        $response_ids = array_pluck($response->json('data'), 'id');

        foreach ($licenses as $license) {
            $this->assertInArray($license->id, $response_ids);
        }
    }

    public function testGetLicensesOfSpecificUser()
    {
        $this->actingAs($this->user, 'api');

        /** @var User $other_user */
        $other_user = factory(User::class)->create();

        /** @var Collection|License[] $licenses */
        $licenses = factory(License::class, 3)
            ->states(['with photos', 'with certs'])
            ->create([
                'user_id' => $other_user->id,
            ]);

        // create license for other user
        factory(License::class)
            ->states(['with photos', 'with certs'])
            ->create([
                'user_id' => $this->user->id,
            ]);

        $response = $this->getJson('/api/v1/license?user_id=' . $other_user->id)
            ->assertOk()
            ->assertJsonCount($licenses->count(), 'data');

        $response_ids = array_pluck($response->json('data'), 'id');

        foreach ($licenses as $license) {
            $this->assertInArray($license->id, $response_ids);
        }
    }

    public function testDeleteLicense()
    {
        $this->actingAs($this->user, 'api');

        /** @var License $license */
        $license = factory(License::class)
            ->states(['with photos', 'with certs'])
            ->create([
                'user_id' => $this->user->id,
            ]);

        $this->deleteJson('/api/v1/license/' . $license->id)
            ->assertOk();

        $this->assertDatabaseMissing('licenses', $license->only('id'));
    }

    public function testClearLicensePhotos()
    {
        $this->actingAs($this->user, 'api');

        /** @var License $license */
        $license = factory(License::class)
            ->states(['with photos'])
            ->create([
                'user_id' => $this->user->id,
            ]);

        $this->putJson('/api/v1/license/' . $license->id, [
            'clear_photos' => true
        ])
            ->assertOk();

        $this->assertDatabaseMissing('media', [
            'model_type' => License::class,
            'model_id' => $license->id,
            'collection_name' => License::MEDIA_COLLECTION_PHOTOS,
        ]);
    }

    public function testClearLicenseCerts()
    {
        $this->actingAs($this->user, 'api');

        /** @var License $license */
        $license = factory(License::class)
            ->states(['with certs'])
            ->create([
                'user_id' => $this->user->id,
            ]);

        $this->putJson('/api/v1/license/' . $license->id, [
            'clear_certs' => true
        ])
            ->assertOk();

        $this->assertDatabaseMissing('media', [
            'model_type' => License::class,
            'model_id' => $license->id,
            'collection_name' => License::MEDIA_COLLECTION_CERTS,
        ]);
    }
}
