<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Transformers\ActivityTransformer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{

    /**
     * @var ActivityTransformer
     */
    private $activityTransformer;

    /**
     * ActivityController constructor.
     * @param ActivityTransformer $activityTransformer
     */
    public function __construct(ActivityTransformer $activityTransformer)
    {
        $this->activityTransformer = $activityTransformer;
    }

    /**
     * @param Request $request
     * @return array
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', Activity::class);

        $query = Activity::query()->latest();

        $paginate = $query->paginate(15);

        return fractal()
            ->collection($paginate->items(), $this->activityTransformer)
            ->paginateWith(new IlluminatePaginatorAdapter($paginate))
            ->toArray();
    }
}
