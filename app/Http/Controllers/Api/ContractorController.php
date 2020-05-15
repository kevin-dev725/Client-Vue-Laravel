<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Contractor\IndexRequest;
use App\Transformers\UserTransformer;
use App\User;
use App\UserLocation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ContractorController extends Controller
{
    /**
     * @param Builder|User $query
     * @param Request $request
     */
    protected function applySearchFilters(Builder $query, $request)
    {
        $query->whereHas('user_skills');
        if ($skills = $request->get('skills')) {
            $query->where(function (Builder $builder) use ($skills) {
                foreach (explode(',', $skills) as $skill) {
                    $builder->orWhereHas('user_skills', function (Builder $builder) use ($skill) {
                        $builder->where('name', $skill);
                    });
                }
            });
        }
        if ($city = $request->get('city')) {
            $query->where('city', $city);
        }
        $state = $request->get('state');
        $query->where('state', $state);
    }

    public function index(IndexRequest $request)
    {
        $query = User::query();
        $this->applySearchFilters($query, $request);
        if ($request->has('nearby')) {
            $radius = $request->input('nearby.radius');
            $lat = null;
            $lng = null;
            if ($request->has('nearby.lat')) {
                $lat = $request->input('nearby.lat');
                $lng = $request->input('nearby.lng');
            } else {
                /** @var UserLocation $location */
                $location = $request->user()
                    ->location;
                $lat = $location->lat;
                $lng = $location->lng;
            }
            $user_ids = UserLocation::withinRadius($lat, $lng, $radius)
                ->where('user_id', '!=', $request->user()->id)
                ->limit(50)
                ->whereHas('user', function (Builder $builder) use ($request) {
                    $this->applySearchFilters($builder, $request);
                })
                ->pluck('user_id')
                ->all();
            $users = collect();
            foreach ($user_ids as $id) {
                $users[] = User::find($id);
            }
            return fractal($users, new UserTransformer())
                ->respond();
        }
        return $this->respondIndex($request, $query, new UserTransformer());
    }
}
