<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\EmailRequest;
use App\Http\Requests\Api\User\SearchRequest;
use App\Http\Requests\Api\User\UpdateRequest;
use App\Notifications\User\AdminEmail;
use App\Transformers\ClientTransformer;
use App\Transformers\UserTransformer;
use App\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Serializer\ArraySerializer;

class UserController extends Controller
{
    /**
     * @var UserTransformer
     */
    private $transformer;

    /**
     * UserController constructor.
     * @param UserTransformer $transformer
     */
    public function __construct(UserTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'keyword' => 'nullable|string'
        ]);
        $query = User::query();

        if ($request->filled('keyword')) {
            $keyword = $request->get('keyword');
            $query->where('name', 'LIKE', '%' . $keyword . '%')
                ->orWhere('email', 'LIKE', '%' . $keyword . '%');
        }

        $pagination = $query->paginate(15);

        return fractal()
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param User $user
     * @return array
     */
    public function show(Request $request, User $user)
    {
        return $user->getSerializedData($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param User $user
     * @return array
     * @throws Exception
     */
    public function update(UpdateRequest $request, User $user)
    {
        beginTransaction();
        try {
            $user->update(array_merge(
                $request->only([
                    'first_name',
                    'middle_name',
                    'last_name',
                    'email',
                    'account_type',
                    'description',
                    'phone_number',
                    'alt_phone_number',
                    'phone_number_ext',
                    'alt_phone_number_ext',
                    'street_address',
                    'street_address2',
                    'city',
                    'state',
                    'postal_code',
                    'business_url',
                    'facebook_url',
                    'twitter_url',
                    'account_status',
                ],
                    [
                        'name' => $request->getFullName(),
                    ]
                )));

            if ($request->company()) {
                $user->update([
                    'company_name' => $request->company()->name,
                    'company_id' => $request->company()->id,
                ]);
            }

            if ($request->filled('password')) {
                $user->update([
                    'password' => bcrypt($request->get('password')),
                ]);
            }
            commit();

            return $user->refresh()
                ->getSerializedData($request);
        } catch (Exception $e) {
            rollback();
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return Response
     */
    public function destroy(User $user)
    {
        //
        // echo 11;
        $user = User::find($user->id);
        $user->delete();
    }

    /**
     * @param EmailRequest $request
     * @param User $user
     */
    public function email(EmailRequest $request, User $user)
    {
        $user->notify(new AdminEmail($request->get('subject'), $request->get('message')));
    }

    /**
     * @param Request $request
     * @param User $user
     * @return mixed
     * @throws AuthorizationException
     */
    public function getClients(Request $request, User $user)
    {
        $this->authorize('getClients', $user);
        $query = Client::query()
            ->where('user_id', $user->id);

        $paginate = $query->paginate($request->get('per_page', 15));

        return fractal()
            ->collection($paginate->items(), new ClientTransformer())
            ->serializeWith(new ArraySerializer())
            ->paginateWith(new IlluminatePaginatorAdapter($paginate))
            ->toArray();
    }
}
