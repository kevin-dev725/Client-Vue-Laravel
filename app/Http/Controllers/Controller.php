<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\TransformerAbstract;

class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    use AuthorizesRequests {
        resourceAbilityMap as protected resourceAbilityMapTrait;
    }

    /**
     * Get the map of resource methods to ability names.
     *
     * @return array
     */
    protected function resourceAbilityMap()
    {
        return array_merge($this->resourceAbilityMapTrait(), ['index' => 'index']);
    }

    /**
     * @param Request|FormRequest $request
     * @param Builder $query
     * @param TransformerAbstract $transformer
     * @param bool $allow_all
     * @return JsonResponse
     */
    protected function respondIndex($request, Builder $query, TransformerAbstract $transformer, $allow_all = true)
    {
        if ($allow_all && $request->get('all')) {
            return fractal($query->get(), $transformer)
                ->respond();
        }
        $pagination = $query->paginate(request('per_page', 15));
        return fractal($pagination->items(), $transformer)
            ->paginateWith(new IlluminatePaginatorAdapter($pagination))
            ->respond();
    }
}
