<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\QuickbooksImport;
use App\Transformers\QuickbooksImportTransformer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class QuickbooksController extends Controller
{
    /**
     * @var QuickbooksImportTransformer
     */
    private $transformer;

    public function __construct(QuickbooksImportTransformer $transformer)
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
        /**
         * @var User $user
         */
        $user = $request->user();
        $query = $user->quickbooks_imports()->latest();
        $pagination = $query->paginate(5);
        return fractal($pagination->items(), new QuickbooksImportTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($pagination));
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
     * @param QuickbooksImport $quickbooksImport
     * @return Response
     */
    public function show(QuickbooksImport $quickbooksImport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param QuickbooksImport $quickbooksImport
     * @return Response
     */
    public function update(Request $request, QuickbooksImport $quickbooksImport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param QuickbooksImport $quickbooksImport
     * @return Response
     */
    public function destroy(QuickbooksImport $quickbooksImport)
    {
        //
    }
}
