<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Plan\AlreadySubscribedToPlan;
use App\Http\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Plan\StoreRequest;
use App\Http\Requests\Api\Plan\SubscribeRequest;
use App\Http\Requests\Api\Plan\UpdateRequest;
use App\Plan;
use App\Transformers\PlanTransformer;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlanController extends Controller
{
    /**
     * @var PlanTransformer
     */
    private $transformer;

    /**
     * PlanController constructor.
     * @param PlanTransformer $transformer
     */
    public function __construct(PlanTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return fractal()
            ->collection(Plan::all(), $this->transformer)
            ->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return Response
     * @throws Exception
     */
    public function store(StoreRequest $request)
    {
        beginTransaction();
        try {
            $plan = Plan::query()
                ->create($request->only(['name', 'price', 'stripe_id']));
            commit();
            return $plan->getSerializedData($request);
        } catch (Exception $e) {
            rollback();
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Plan $plan
     * @return array
     * @throws AuthorizationException
     */
    public function show(Request $request, Plan $plan)
    {
        $this->authorize('view', $plan);
        return $plan
            ->getSerializedData($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Plan $plan
     * @return array
     * @throws Exception
     */
    public function update(UpdateRequest $request, Plan $plan)
    {
        beginTransaction();
        try {
            $plan->update($request->only(['name', 'price', 'stripe_id']));
            commit();
            return $plan
                ->refresh()
                ->getSerializedData($request);
        } catch (Exception $e) {
            rollback();
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Plan $plan
     * @return JsonResponse
     */
    public function destroy(Plan $plan)
    {
        transaction(function () use ($plan) {
            $plan->delete();
        });

        return ApiResponse::success();
    }

    /**
     * @param SubscribeRequest $request
     * @param Plan $plan
     * @return JsonResponse
     * @throws AlreadySubscribedToPlan
     * @throws Exception
     */
    public function subscribe(SubscribeRequest $request, Plan $plan)
    {
        $user = $request->user();
        $token = $request->get('card_token');
        if ($user->subscribedToPlan($plan->stripe_id, Plan::SUBSCRIPTION_MAIN)) {
            throw new AlreadySubscribedToPlan();
        }

        beginTransaction();
        try {
            if (!$user->hasStripeId()) {
                $user->createAsStripeCustomer($request->get('token'));
            }

            if ($request->filled('card_token')) {
                $user->updateCard($token);
            }

            if ($user->subscribed(Plan::SUBSCRIPTION_MAIN)) {
                $user->subscription(Plan::SUBSCRIPTION_MAIN)
                    ->swap($plan->stripe_id);
            } else {
                $user->newSubscription(Plan::SUBSCRIPTION_MAIN, $plan->stripe_id)
                    ->create($token, [
                        'email' => $user->email
                    ]);
            }
//			$user->notify(new SuccessfulSubscription());

            commit();

            return ApiResponse::success();
        } catch (Exception $e) {
            rollback();
            throw $e;
        }
    }
}
