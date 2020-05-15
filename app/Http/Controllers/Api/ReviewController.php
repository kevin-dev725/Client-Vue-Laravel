<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\Http\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Review\UpdateRequest;
use App\Review;
use App\Transformers\ClientTransformer;
use App\Transformers\ReviewTransformer;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class ReviewController extends Controller
{
    const SEARCH_BY_PHONE_NUMBER = 'phone number', SEARCH_BY_EMAIL = 'email', SEARCH_BY_NAME_AND_ADDRESS = 'name and address', SEARCH_BY_SPECIFIC_CLIENT = 'specific client';
    private $transformer;

    /**
     * ReviewController constructor.
     * @param $transformer
     */
    public function __construct(ReviewTransformer $transformer)
    {
        $this->transformer = $transformer;
    }


    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'search_by' => 'nullable|in:' . implode(',', [self::SEARCH_BY_EMAIL, self::SEARCH_BY_NAME_AND_ADDRESS, self::SEARCH_BY_PHONE_NUMBER, self::SEARCH_BY_SPECIFIC_CLIENT]),
            'phone_number' => 'nullable|required_if:search_by,' . self::SEARCH_BY_PHONE_NUMBER . '|string',
            'phone_number_ext' => 'nullable|string',
            'email' => 'nullable|required_if:search_by,' . self::SEARCH_BY_EMAIL . '|email',
            'first_name' => 'nullable|string|required_if:search_by,' . self::SEARCH_BY_NAME_AND_ADDRESS,
            'last_name' => 'nullable|string|required_if:search_by,' . self::SEARCH_BY_NAME_AND_ADDRESS,
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'street_address' => 'nullable|string',
            'client_id' => 'nullable|required_if:search_by,' . self::SEARCH_BY_SPECIFIC_CLIENT . '|exists:clients,id',
        ]);
        $query = Review::query();
        $clients_with_no_reviews = Client::query()
            ->whereDoesntHave('reviews');
        // $clients_with_no_reviews = Client::query();

        if ($request->filled('search_by')) {
            $query->leftJoin('clients as c', 'c.id', '=', 'reviews.client_id');
            switch ($request->get('search_by')) {
                case self::SEARCH_BY_EMAIL:
                    $query->where('c.email', $request->get('email'));
                    $clients_with_no_reviews->where('email', $request->get('email'));
                    break;
                case self::SEARCH_BY_PHONE_NUMBER:
                    $phone_number = phone_clean($request->get('phone_number'));
                    $query->where(function (Builder $query) use ($request, $phone_number) {
                        $query->where(function (Builder $query) use ($request, $phone_number) {
                            $query->whereRaw("phone_clean(phone_number) like '%$phone_number%'");
                            if ($request->filled('phone_number_ext')) {
                                $query->where('phone_number_ext', 'like', '%' . $request->get('phone_number_ext') . '%');
                            }
                        })
                            ->orWhere(function (Builder $query) use ($request, $phone_number) {
                                $query->whereRaw("phone_clean(alt_phone_number) like '%$phone_number%'");
                                if ($request->filled('phone_number_ext')) {
                                    $query->where('alt_phone_number_ext', 'like', '%' . $request->get('phone_number_ext') . '%');
                                }
                            });
                    });
                    $clients_with_no_reviews->where(function (Builder $query) use ($request, $phone_number) {
                        $query->where(function (Builder $query) use ($request, $phone_number) {
                            $query->whereRaw("phone_clean(phone_number) like '%$phone_number%'");
                            if ($request->filled('phone_number_ext')) {
                                $query->where('phone_number_ext', 'like', '%' . $request->get('phone_number_ext') . '%');
                            }
                        })
                            ->orWhere(function (Builder $query) use ($request, $phone_number) {
                                $query->whereRaw("phone_clean(alt_phone_number) like '%$phone_number%'");
                                if ($request->filled('phone_number_ext')) {
                                    $query->where('alt_phone_number_ext', 'like', '%' . $request->get('phone_number_ext') . '%');
                                }
                            });
                    });
                    break;
                case self::SEARCH_BY_NAME_AND_ADDRESS:
                    $query->where(function (Builder $query) use ($request) {
                        $query->where('first_name', 'like', '%' . $request->get('first_name') . '%')
                            ->where('last_name', 'like', '%' . $request->get('last_name') . '%');
                        if ($request->filled('city')) {
                            $query->where('city', 'like', '%' . $request->get('city') . '%');
                        }
                        if ($request->filled('state')) {
                            $query->where(function (Builder $query) use ($request) {
                                $query->where('state', $request->get('state'))
                                    ->orWhere('postal_code', $request->get('state'));
                            });
                        }
                        if ($request->filled('street_address')) {
                            $query->where('street_address', 'like', '%' . $request->get('street_address') . '%');
                        }
                    });
                    $clients_with_no_reviews->where(function (Builder $query) use ($request) {
                        $query->where('first_name', 'like', '%' . $request->get('first_name') . '%')
                            ->where('last_name', 'like', '%' . $request->get('last_name') . '%');
                        if ($request->filled('city')) {
                            $query->where('city', 'like', '%' . $request->get('city') . '%');
                        }
                        if ($request->filled('state')) {
                            $query->where(function (Builder $query) use ($request) {
                                $query->where('state', $request->get('state'))
                                    ->orWhere('postal_code', $request->get('state'));
                            });
                        }
                        if ($request->filled('street_address')) {
                            $query->where('street_address', 'like', '%' . $request->get('street_address') . '%');
                        }
                    });
                    break;
                case self::SEARCH_BY_SPECIFIC_CLIENT:
                    /**
                     * @var Client $client
                     */
                    $client = Client::query()->find($request->get('client_id'));
                    $query->where('c.first_name', $client->first_name)
                        ->where('c.last_name', $client->last_name)
                        ->where('c.phone_number', $client->phone_number)
                        ->where('c.email', $client->email);
                    $clients_with_no_reviews->where('first_name', $client->first_name)
                        ->where('last_name', $client->last_name)
                        ->where('phone_number', $client->phone_number)
                        ->where('email', $client->email);
                    break;
            }
        }
        $query->select('reviews.*');
        $query->orderBy('reviews.created_at', 'desc');
        $pagination = $query->paginate($request->get('per_page', 15));
        $clients_with_no_reviews->orderByDesc('created_at');

        return fractal()
            ->addMeta([
                'clients_with_no_reviews' => fractal($clients_with_no_reviews->get(), new ClientTransformer())
                    ->parseIncludes($request->get('clients_with_no_reviews_include', ''))
                    ->toArray()['data']
            ])
            ->collection($pagination->items(), $this->transformer)
            ->paginateWith(new IlluminatePaginatorAdapter($pagination))
            ->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Review $review
     * @return array
     */
    public function show(Request $request, Review $review)
    {
        return $review->getSerializedData($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Review $review
     * @return array
     * @throws Exception
     */
    public function update(UpdateRequest $request, Review $review)
    {
        beginTransaction();
        try {
            $service_date = Carbon::parse($request->get('service_date'))->gt(now()) ? now() : $request->get('service_date');
            $review->update([
                'service_date' => $service_date,
                'star_rating' => $request->get('star_rating'),
                'payment_rating' => $request->filled('payment_rating') ? $request->get('payment_rating') : Review::REVIEW_RATING_NO_OPINION,
                'character_rating' => $request->filled('character_rating') ? $request->get('character_rating') : Review::REVIEW_RATING_NO_OPINION,
                'repeat_rating' => $request->filled('repeat_rating') ? $request->get('repeat_rating') : Review::REVIEW_RATING_NO_OPINION,
                'comment' => $request->get('comment'),
            ]);
            if ($review->isFlagged()) {
                if (!$review->hasFlaggedPhrases()) {
                    $review->unFlag();
                }
            } else {
                $review->flagIfHasFlaggedPhrases();
            }
            commit();
            return $review->refresh()->getSerializedData($request);

        } catch (Exception $e) {
            rollback();
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Review $review
     * @return JsonResponse
     */
    public function destroy(Review $review)
    {
        transaction(function () use ($review) {
            $review->delete();
        });

        return ApiResponse::success();
    }

    public function getFlaggedReviews(Request $request)
    {
        $reviews = Review::query()
            ->flagged()
            ->get();
        return fractal($reviews, $this->transformer)
            ->respond();
    }
}
