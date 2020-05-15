<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\State;
use App\Transformers\StateTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return fractal(State::where('country_code', 'US')->get(), new StateTransformer())
            ->respond();
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
     * @param State $state
     * @return Response
     */
    public function show(State $state)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param State $state
     * @return Response
     */
    public function update(Request $request, State $state)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param State $state
     * @return Response
     */
    public function destroy(State $state)
    {
        //
    }
}
