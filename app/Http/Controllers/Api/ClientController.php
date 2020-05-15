<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\ClientImport;
use App\Country;
use App\Http\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Client\ImportRequest;
use App\Http\Requests\Api\Client\ReviewRequest;
use App\Http\Requests\Api\Client\SearchRequest;
use App\Http\Requests\Api\Client\StoreRequest;
use App\Http\Requests\Api\Client\StoreWithReviewRequest;
use App\Http\Requests\Api\Client\UpdateRequest;
use App\Jobs\ImportClientsJob;
use App\Review;
use App\Transformers\ClientTransformer;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class ClientController extends Controller
{
    /**
     * @var ClientTransformer
     */
    private $transformer;

    /**
     * ClientController constructor.
     * @param ClientTransformer $transformer
     */
    public function __construct(ClientTransformer $transformer)
    {
        $this->transformer = $transformer;
    }


    /**
     * @param Request $request
     * @return Client
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'keyword' => 'nullable|string'
        ]);
        $query = Client::query();
        if ($request->user()->isUser()) {
            $query->where('user_id', $request->user()->id);
        }
        if ($request->filled('keyword')) {
            $keyword = $request->get('keyword');
            $query->where(function (Builder $query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('street_address', 'like', '%' . $keyword . '%')
                    ->orWhere('city', 'like', '%' . $keyword . '%')
                    ->orWhere('organization_name', 'like', '%' . $keyword . '%');
            });
        }
        if ($request->has('filter')) {
            switch ($request->get('filter')) {
                case 'unreviewed':
                    $query->unReviewed();
                    break;
            }
        }
        $query->orderByRaw("if(client_type = '" . Client::CLIENT_TYPE_INDIVIDUAL . "', last_name, organization_name)");
        $paginate = $query->paginate($request->get('per_page', 15));
        return fractal()
            ->collection($paginate->items(), $this->transformer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginate))
            ->toArray();
            
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return Client
     * @throws Exception
     */
    public function store(StoreRequest $request)
    {
        beginTransaction();
        try {
            /**
             * @var Client $client
             */
            $client = Client::query()
                ->create(array_merge($request->only([
                    'client_type',
                    'organization_name',
                    'first_name',
                    'middle_name',
                    'last_name',
                    'phone_number',
                    'phone_number_ext',
                    'alt_phone_number',
                    'alt_phone_number_ext',
                    'street_address',
                    'street_address2',
                    'city',
                    'state',
                    'postal_code',
                    'email',
                    'billing_first_name',
                    'billing_middle_name',
                    'billing_last_name',
                    'billing_phone_number',
                    'billing_phone_number_ext',
                    'billing_street_address',
                    'billing_street_address2',
                    'billing_city',
                    'billing_state',
                    'billing_postal_code',
                    'billing_email',
                    'initial_star_rating',
                ]), [
                    'country_id' => Country::getDefaultCountry()->id,
                    'user_id' => $request->user()->id,
                    'phone_number' => (string)phone($request->get('phone_number'), config('settings.default_country')),
                    'alt_phone_number' => $request->filled('alt_phone_number') ? (string)phone($request->get('alt_phone_number'), config('settings.default_country')) : null,
                    'company_id' => $request->user()
                        ->isAccountCompany() ? $request->user()->company->id : null,
                ]));
            $client->reviews()
                ->create([
                    'user_id' => $request->user()->id,
                    'service_date' => $request->input('review.service_date', today()->toDateString()),
                    'star_rating' => $request->input('review.star_rating', config('settings.import.default_initial_star_rating')),
                    'payment_rating' => $request->filled('review.payment_rating') ? $request->input('review.payment_rating') : Review::REVIEW_RATING_NO_OPINION,
                    'character_rating' => $request->filled('review.character_rating') ? $request->input('review.character_rating') : Review::REVIEW_RATING_NO_OPINION,
                    'repeat_rating' => $request->filled('review.repeat_rating') ? $request->input('review.repeat_rating') : Review::REVIEW_RATING_NO_OPINION,
                    'comment' => $request->input('review.comment'),
                ]);
            commit();
            return $client->getSerializedData($request);
        } catch (Exception $e) {
            rollback();
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Client $client
     * @return array
     */
    public function show(Request $request, Client $client)
    {
        return $client->getSerializedData($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Client $client
     * @return array
     * @throws Exception
     */
    public function update(UpdateRequest $request, Client $client)
    {
        beginTransaction();
        try {
            $array = [
                'client_type',
                'organization_name',
                'first_name',
                'middle_name',
                'last_name',
                'phone_number',
                'phone_number_ext',
                'alt_phone_number',
                'alt_phone_number_ext',
                'street_address',
                'street_address2',
                'city',
                'state',
                'postal_code',
                'email',
                'billing_first_name',
                'billing_middle_name',
                'billing_last_name',
                'billing_phone_number',
                'billing_phone_number_ext',
                'billing_street_address',
                'billing_street_address2',
                'billing_city',
                'billing_state',
                'billing_postal_code',
                'billing_email',
                'initial_star_rating',
            ];

            if ($request->basicInfoOnly()) {
                $array = [
                    'first_name',
                    'middle_name',
                    'last_name',
                    'phone_number',
                    'phone_number_ext',
                    'street_address',
                    'city',
                    'state',
                    'postal_code',
                    'email',
                    'organization_name'
                ];
            }
            $data = array_merge($request->only($array), [
                'country_id' => Country::getDefaultCountry()->id,
            ]);
            $data['phone_number'] = phone($request->get('phone_number'), config('settings.default_country'));
            if (!$request->basicInfoOnly()) {
                if ($request->filled('alt_phone_number')) {
                    $data['alt_phone_number'] = phone($request->get('alt_phone_number'), config('settings.default_country'));
                }
            }
            $client->update($data);

            commit();
            return $client->refresh()
                ->getSerializedData($request);
        } catch (Exception $e) {
            rollback();
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Client $client
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);
        transaction(function () use ($client) {
            $client->delete();
        });
        return ApiResponse::success();
    }

    /**
     * @param ReviewRequest $request
     * @param Client $client
     * @return array
     * @throws Exception
     */
    public function review(ReviewRequest $request, Client $client)
    {
        beginTransaction();
        try {
            $service_date = Carbon::parse($request->get('service_date'))->gt(now()) ? now() : $request->get('service_date');
            /**
             * @var Review $review
             */
            $review = $client->reviews()
                ->create([
                    'user_id' => $request->user()->id,
                    'service_date' => $service_date,
                    'star_rating' => $request->get('star_rating'),
                    'payment_rating' => $request->filled('payment_rating') ? $request->get('payment_rating') : Review::REVIEW_RATING_NO_OPINION,
                    'character_rating' => $request->filled('character_rating') ? $request->get('character_rating') : Review::REVIEW_RATING_NO_OPINION,
                    'repeat_rating' => $request->filled('repeat_rating') ? $request->get('repeat_rating') : Review::REVIEW_RATING_NO_OPINION,
                    'comment' => $request->get('comment'),
                ]);
            commit();
            return $review->getSerializedData($request);
        } catch (Exception $e) {
            rollback();
            throw $e;
        }
    }

    /**
     * @param ImportRequest $request
     * @return void
     */
    public function import(ImportRequest $request)
    {
        /**
         * @var ClientImport $import
         */
        $import = ClientImport::query()
            ->create([
                'user_id' => $request->user()->id,
                'csv' => disk_s3()->putFile(storage_prefix_dir('imports'), $request->file('file'))
            ]);
        dispatch(new ImportClientsJob($import));
    }

    /**
     * Store a client along with review
     * @param StoreWithReviewRequest $request
     * @return mixed
     * @throws Exception
     */
    public function storeWithReview(StoreWithReviewRequest $request)
    {
        beginTransaction();
        try {
            $client = Client::query()
                ->create(array_merge($request->only([
                    'client_type',
                    'organization_name',
                    'first_name',
                    'middle_name',
                    'last_name',
                    'phone_number',
                    'phone_number_ext',
                    'alt_phone_number_ext',
                    'street_address',
                    'street_address2',
                    'city',
                    'state',
                    'postal_code',
                    'email',
                    'billing_first_name',
                    'billing_middle_name',
                    'billing_last_name',
                    'billing_phone_number',
                    'billing_phone_number_ext',
                    'billing_street_address',
                    'billing_street_address2',
                    'billing_city',
                    'billing_state',
                    'billing_postal_code',
                    'billing_email',
                    'initial_star_rating',
                ]), [
                    'name' => $request->getFullName(),
                ]));

            $this->authorize('review', $client);
            $service_date = Carbon::parse($request->get('service_date'))->gt(now()) ? now() : $request->get('service_date');

            $client->reviews()
                ->create([
                    'user_id' => $request->user()->id,
                    'service_date' => $service_date,
                    'star_rating' => $request->get('star_rating'),
                    'payment_rating' => $request->filled('payment_rating') ? $request->get('payment_rating') : Review::REVIEW_RATING_NO_OPINION,
                    'character_rating' => $request->filled('character_rating') ? $request->get('character_rating') : Review::REVIEW_RATING_NO_OPINION,
                    'repeat_rating' => $request->filled('repeat_rating') ? $request->get('repeat_rating') : Review::REVIEW_RATING_NO_OPINION,
                    'comment' => $request->get('comment'),
                ]);

            commit();

            return $client->getSerializedData($request);
        } catch (Exception $e) {
            rollback();
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getOrganizations(Request $request)
    {
        $this->validate($request, [
            'keyword' => 'nullable|string'
        ]);
        return $request->user()->clients()
            ->whereNotNull('organization_name')
            ->where('organization_name', 'like', '%' . $request->get('keyword') . '%')
            ->selectRaw('distinct(organization_name) as org')
            ->pluck('org')
            ->filter(function ($org) {
                return trim($org) !== '';
            })
            ->values()
            ->toArray();
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getSummary(Request $request)
    {
        return [
            'total' => $request->user()->clients()->count()
        ];
    }
}
