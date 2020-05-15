<?php

namespace Tests\Feature;

use App\Client;
use App\FlaggedPhrase;
use App\Http\Controllers\Api\ReviewController;
use App\Notifications\Review\ReviewFlaggedNotification;
use App\Review;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Tests\ApiTestCase;

class ReviewTest extends ApiTestCase
{
    use DatabaseTransactions;

    public function testCreateReview()
    {
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->authJson('post', '/api/v1/client/' . $client->id . '/review', $data = [
            'service_date' => $now = today()->toDateString(),
            'star_rating' => $this->faker->numberBetween(1, 5),
            'payment_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'character_rating' => Review::REVIEW_RATING_THUMBS_DOWN,
            'repeat_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'comment' => $this->faker->sentence
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment($data);
        $this->assertDatabaseHas('reviews', array_merge($data, [
            'client_id' => $client->id,
            'user_id' => $this->user->id,
        ]));
    }

    public function testCreateReviewRequiredFieldsValidation()
    {
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->authJson('post', '/api/v1/client/' . $client->id . '/review');
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['service_date', 'star_rating']);
    }

    public function testUpdateReview()
    {
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);
        $review = factory(Review::class)->create([
            'client_id' => $client->id,
            'user_id' => $this->user->id,
            'service_date' => $now = today()->subDays(2)->toDateString(),
            'star_rating' => $this->faker->numberBetween(1, 5),
            'payment_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'character_rating' => Review::REVIEW_RATING_THUMBS_DOWN,
            'repeat_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'comment' => $this->faker->sentence
        ]);
        $response = $this->authJson('put', '/api/v1/review/' . $review->id, $data = [
            'service_date' => $now = today()->toDateString(),
            'star_rating' => $this->faker->numberBetween(1, 5),
            'payment_rating' => Review::REVIEW_RATING_THUMBS_DOWN,
            'character_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'repeat_rating' => Review::REVIEW_RATING_THUMBS_DOWN,
            'comment' => $this->faker->sentence
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment($data);
        $this->assertDatabaseHas('reviews', $data);
    }

    public function testUpdateReviewRequiredFieldsValidation()
    {
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);
        $review = factory(Review::class)->create([
            'client_id' => $client->id,
            'user_id' => $this->user->id,
        ]);
        $response = $this->authJson('put', '/api/v1/review/' . $review->id);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['service_date', 'star_rating']);
    }

    public function testDeleteReview()
    {
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);
        $review = factory(Review::class)->create([
            'client_id' => $client->id,
            'user_id' => $this->user->id
        ]);
        $this->authJson('delete', '/api/v1/review/' . $review->id)
            ->assertStatus(200);
        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id
        ]);
    }

    public function testSearchReview()
    {
        factory(Review::class, 5)->states('complete')->create();
        $response = $this->authJson('get', '/api/v1/review');
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function testSearchReviewsByEmail()
    {
        $reviews = factory(Review::class, 3)->states('complete')->create();
        $first_review = $reviews->first();
        $response = $this->authJson('get', '/api/v1/review', [
            'search_by' => 'email',
            'email' => $first_review->client->email
        ]);
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
        $this->assertArraySubset([
            [
                'id' => $first_review->id,
                'user_id' => $first_review->user_id,
                'comment' => $first_review->comment
            ]
        ], $response->json('data'));
    }

    public function testSearchReviewsByClientPhoneNumber()
    {
        $reviews = factory(Review::class, 3)->states('complete')->create();
        $first_review = $reviews->first();
        $response = $this->authJson('get', '/api/v1/review', [
            'search_by' => 'phone number',
            'phone_number' => $first_review->client->phone_number
        ]);
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
        $this->assertArraySubset([
            [
                'id' => $first_review->id,
                'user_id' => $first_review->user_id,
                'comment' => $first_review->comment
            ]
        ], $response->json('data'));
    }

    public function testSearchReviewsByClientCleanedPhoneNumber()
    {
        $client = factory(Client::class)->states('complete')->create([
            'phone_number' => '(904) 815-8112'
        ]);
        factory(Review::class, 3)->states('complete')->create([
            'client_id' => $client->id,
            'user_id' => $client->user_id,
        ]);
        $response = $this->authJson('get', '/api/v1/review', [
            'search_by' => 'phone number',
            'phone_number' => '9048158112'
        ]);
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function testSearchReviewsByClientNameAndAddress()
    {
        $reviews = factory(Review::class, 3)->states('complete')->create();
        $first_review = $reviews->first();
        $response = $this->authJson('get', '/api/v1/review', [
            'search_by' => 'name and address',
            'first_name' => $first_review->client->first_name,
            'last_name' => $first_review->client->last_name,
            'city' => $first_review->client->city,
            'state' => $first_review->client->state,
            'street_address' => $first_review->client->street_address,
        ]);
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
        $this->assertArraySubset([
            [
                'id' => $first_review->id,
                'user_id' => $first_review->user_id,
                'comment' => $first_review->comment
            ]
        ], $response->json('data'));
    }

    public function testSearchReviewsBySpecificClientReturnsReviewsAcrossUsers()
    {
        /**
         * @var Collection $clients
         */
        $clients = factory(Client::class, 5)->states('complete')->create([
            'first_name' => $first_name = $this->faker->firstName,
            'middle_name' => $middle_name = $this->faker->lastName,
            'last_name' => $last_name = $this->faker->lastName,
            'email' => $email = $this->faker->unique()->safeEmail,
            'phone_number' => $phone_number = $this->testPhoneNumber,
        ]);
        factory(Review::class, 3)->states('complete')->create();
        $reviews = collect();
        $i = 0;
        $clients->each(function (Client $client) use ($reviews, &$i) {
            Carbon::setTestNow(now()->addHours(++$i));
            $review = factory(Review::class)->create([
                'user_id' => $client->user_id,
                'client_id' => $client->id,
            ]);
            $reviews->push($review);
        });
        /**
         * @var Client $client
         */
        $client = $clients->first();
        $response = $this->authJson('get', '/api/v1/review', [
            'search_by' => 'specific client',
            'client_id' => $client->id,
        ]);
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
        $sorted_reviews = $reviews->sortByDesc('created_at')->values();
        $i = 0;
        foreach ($sorted_reviews as $review) {
            $this->assertEquals($review->id, $response->json('data.' . $i++ . '.id'));
        }
    }

    public function testSearchReviewsReturnsClientsWithNoReviews()
    {
        /**
         * @var Collection $clients
         */
        $clients = factory(Client::class, 5)->states('complete')->create();
        /**
         * @var Client $client
         */
        $client = $clients->first();
        $response = $this->authJson('get', '/api/v1/review', [
            'search_by' => ReviewController::SEARCH_BY_EMAIL,
            'email' => $client->email,
        ]);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'clients_with_no_reviews'
                ]
            ])
            ->assertJsonCount(0, 'data')
            ->assertJsonCount(1, 'meta.clients_with_no_reviews');
        $this->assertArraySubset([
            'id' => $client->id,
            'email' => $client->email,
        ], $response->json('meta.clients_with_no_reviews.0'));
    }

    public function testClientsWithNoReviewsIncludes()
    {
        /**
         * @var Collection $clients
         */
        $clients = factory(Client::class, 5)->states('complete')->create();
        /**
         * @var Client $client
         */
        $client = $clients->first();
        $response = $this->authJson('get', '/api/v1/review', [
            'search_by' => ReviewController::SEARCH_BY_EMAIL,
            'email' => $client->email,
            'clients_with_no_reviews_include' => 'reviews'
        ]);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'clients_with_no_reviews'
                ]
            ])
            ->assertJsonCount(0, 'data')
            ->assertJsonCount(1, 'meta.clients_with_no_reviews');
        $this->assertArraySubset([
            'id' => $client->id,
            'email' => $client->email,
        ], $response->json('meta.clients_with_no_reviews.0'));
        $this->assertArrayHasKey('reviews', $response->json('meta.clients_with_no_reviews.0'));
    }

    public function testCreateReviewFlagsReviewsWithFlaggedPhrases()
    {
        $flagged_phrase = factory(FlaggedPhrase::class)->create();
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->authJson('post', '/api/v1/client/' . $client->id . '/review', $data = [
            'service_date' => $now = today()->toDateString(),
            'star_rating' => $this->faker->numberBetween(1, 5),
            'payment_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'character_rating' => Review::REVIEW_RATING_THUMBS_DOWN,
            'repeat_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'comment' => $this->faker->sentence . ' ' . $flagged_phrase->phrase . ' ' . $this->faker->sentence
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment($data);
        $this->assertDatabaseHas('flagged_reviews', [
            'review_id' => $response->json('id'),
            'phrase' => $flagged_phrase->phrase,
            'is_resolved' => false,
        ]);
    }

    public function testFlaggedReviewsNotifyAdministrators()
    {
        Notification::fake();
        /**
         * @var Collection $admin_users
         */
        $admin_users = factory(User::class, 5)->states('admin')->create();
        $flagged_phrase = factory(FlaggedPhrase::class)->create();
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->authJson('post', '/api/v1/client/' . $client->id . '/review', $data = [
            'service_date' => $now = today()->toDateString(),
            'star_rating' => $this->faker->numberBetween(1, 5),
            'payment_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'character_rating' => Review::REVIEW_RATING_THUMBS_DOWN,
            'repeat_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'comment' => $this->faker->sentence . ' ' . $flagged_phrase->phrase . ' ' . $this->faker->sentence
        ])
            ->assertSuccessful();
        $review_id = $response->json('id');

        $admin_users->each(function (User $user) use ($review_id) {
            Notification::assertSentTo(
                $user,
                ReviewFlaggedNotification::class,
                function ($notification, $channels) use ($review_id) {
                    return $notification->review->id === $review_id;
                }
            );
        });
        Notification::assertNotSentTo(
            [$this->user], ReviewFlaggedNotification::class
        );
    }

    public function testCreateReviewDoesNotFlagReviewsWithNoFlaggedPhrases()
    {
        Notification::fake();
        factory(FlaggedPhrase::class)->create([
            'phrase' => 'racist phrase'
        ]);
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->authJson('post', '/api/v1/client/' . $client->id . '/review', $data = [
            'service_date' => $now = today()->toDateString(),
            'star_rating' => $this->faker->numberBetween(1, 5),
            'payment_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'character_rating' => Review::REVIEW_RATING_THUMBS_DOWN,
            'repeat_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'comment' => 'nice comment'
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment($data);
        $this->assertDatabaseMissing('flagged_reviews', [
            'review_id' => $response->json('id'),
            'is_resolved' => false,
        ]);
    }

    public function testUpdateReviewChecksForFlaggedPhrase()
    {
        Notification::fake();
        $admin_users = factory(User::class, 5)->states('admin')->create();
        $flagged_phrase = factory(FlaggedPhrase::class)->create();
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);
        $review = factory(Review::class)->create([
            'client_id' => $client->id,
            'user_id' => $this->user->id,
            'service_date' => $now = today()->subDays(2)->toDateString(),
            'star_rating' => $this->faker->numberBetween(1, 5),
            'payment_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'character_rating' => Review::REVIEW_RATING_THUMBS_DOWN,
            'repeat_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'comment' => $this->faker->sentence
        ]);
        $response = $this->authJson('put', '/api/v1/review/' . $review->id, $data = [
            'service_date' => $now = today()->toDateString(),
            'star_rating' => $this->faker->numberBetween(1, 5),
            'payment_rating' => Review::REVIEW_RATING_THUMBS_DOWN,
            'character_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'repeat_rating' => Review::REVIEW_RATING_THUMBS_DOWN,
            'comment' => $this->faker->sentence . ' ' . $flagged_phrase->phrase . ' ' . $this->faker->sentence
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment($data);
        $this->assertDatabaseHas('reviews', $data);
        $this->assertDatabaseHas('flagged_reviews', [
            'review_id' => $review->id,
            'phrase' => $flagged_phrase->phrase,
            'is_resolved' => false,
        ]);
        $admin_users->each(function (User $user) use ($review) {
            Notification::assertSentTo(
                $user,
                ReviewFlaggedNotification::class,
                function ($notification, $channels) use ($review) {
                    return $notification->review->id === $review->id;
                }
            );
        });
    }

    public function testUpdateReviewUnFlagsReviewsWithUpdatedCommentsThatDoesNotContainFlaggedPhrases()
    {
        Notification::fake();
        $flagged_phrase = factory(FlaggedPhrase::class)->create();
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);
        $review = factory(Review::class)->create([
            'client_id' => $client->id,
            'user_id' => $this->user->id,
            'service_date' => $now = today()->subDays(2)->toDateString(),
            'star_rating' => $this->faker->numberBetween(1, 5),
            'payment_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'character_rating' => Review::REVIEW_RATING_THUMBS_DOWN,
            'repeat_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'comment' => $flagged_phrase->phrase
        ]);
        $response = $this->authJson('put', '/api/v1/review/' . $review->id, $data = [
            'service_date' => $now = today()->toDateString(),
            'star_rating' => $this->faker->numberBetween(1, 5),
            'payment_rating' => Review::REVIEW_RATING_THUMBS_DOWN,
            'character_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'repeat_rating' => Review::REVIEW_RATING_THUMBS_DOWN,
            'comment' => 'Nice Comment'
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment($data);
        $this->assertDatabaseHas('reviews', $data);
        $this->assertDatabaseMissing('flagged_reviews', [
            'review_id' => $review->id,
            'is_resolved' => false,
        ]);
    }

    public function testGetFlaggedReviews()
    {
        Notification::fake();
        $user = factory(User::class)->states('admin')->create();
        $this->actingAs($user);
        $flagged_phrase = factory(FlaggedPhrase::class)->create();
        $client = factory(Client::class)->create([
            'user_id' => $this->user->id,
        ]);
        factory(Review::class, 5)->create([
            'client_id' => $client->id,
            'user_id' => $this->user->id,
            'service_date' => $now = today()->subDays(2)->toDateString(),
            'star_rating' => $this->faker->numberBetween(1, 5),
            'payment_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'character_rating' => Review::REVIEW_RATING_THUMBS_DOWN,
            'repeat_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'comment' => 'Nice Comment'
        ]);
        $flagged_reviews = factory(Review::class, 5)->create([
            'client_id' => $client->id,
            'user_id' => $this->user->id,
            'service_date' => $now = today()->subDays(2)->toDateString(),
            'star_rating' => $this->faker->numberBetween(1, 5),
            'payment_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'character_rating' => Review::REVIEW_RATING_THUMBS_DOWN,
            'repeat_rating' => Review::REVIEW_RATING_THUMBS_UP,
            'comment' => $flagged_phrase->phrase
        ]);
        $this->getJson('/web-api/review/flagged')
            ->assertSuccessful()
            ->assertJsonCount($flagged_reviews->count(), 'data');
    }
}
