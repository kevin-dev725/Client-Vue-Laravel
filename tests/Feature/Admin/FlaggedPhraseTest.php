<?php

namespace Tests\Feature\Admin;

use App\FlaggedPhrase;
use App\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ApiTestCase;

class FlaggedPhraseTest extends ApiTestCase
{
    use DatabaseTransactions, WithFaker;

    protected $role = Role::ROLE_ADMIN;

    public function setUp()
    {
        parent::setUp();
        $this->actingAs($this->user);
    }

    public function testCreate()
    {
        $this->authJson('post', '/web-api/flagged-phrase', $data = [
            'phrase' => $this->faker->words(3, true)
        ])
            ->assertSuccessful()
            ->assertJson([
                'phrase' => $data['phrase']
            ]);
        $this->assertDatabaseHas('flagged_phrases', $data);
    }

    public function testUpdate()
    {
        $flagged_phrase = factory(FlaggedPhrase::class)->create();
        $this->authJson('put', '/web-api/flagged-phrase/' . $flagged_phrase->id, $data = [
            'phrase' => $this->faker->words(3, true)
        ])
            ->assertSuccessful()
            ->assertJson([
                'phrase' => $data['phrase']
            ]);
        $this->assertDatabaseHas('flagged_phrases', array_merge(
            $data,
            [
                'id' => $flagged_phrase->id,
            ]
        ));
    }

    public function testDelete()
    {
        $flagged_phrase = factory(FlaggedPhrase::class)->create();
        $this->authJson('delete', '/web-api/flagged-phrase/' . $flagged_phrase->id)
            ->assertSuccessful();
        $this->assertDatabaseMissing('flagged_phrases', [
            'id' => $flagged_phrase->id,
        ]);
    }

    public function testGetList()
    {
        factory(FlaggedPhrase::class, 5)->create();
        $this->authJson('get', '/web-api/flagged-phrase')
            ->assertSuccessful()
            ->assertJsonCount(5, 'data');
    }
}
