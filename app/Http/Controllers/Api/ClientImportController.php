<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Transformers\ClientImportTransformer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class ClientImportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $request->user();
        $query = $user->client_imports()->latest();
        $pagination = $query->paginate(5);
        return fractal($pagination->items(), new ClientImportTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($pagination));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
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
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
