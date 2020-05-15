<?php

namespace App\Http\Controllers\Api;

use App\Company;
use App\Http\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Company\StoreRequest;
use App\Http\Requests\Api\Company\UpdateRequest;
use App\Transformers\CompanyTransformer;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * @var CompanyTransformer
     */
    private $transformer;

    /**
     * CompanyController constructor.
     * @param CompanyTransformer $transformer
     */
    public function __construct(CompanyTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index()
    {
        return fractal()
            ->collection(Company::all(), $this->transformer)
            ->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return array
     * @throws Exception
     */
    public function store(StoreRequest $request)
    {
        beginTransaction();
        try {
            $company = Company::query()
                ->create($request->only('name'));
            commit();

            return $company->getSerializedData($request);
        } catch (Exception $e) {
            rollback();
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Company $company
     * @return array
     * @throws AuthorizationException
     */
    public function show(Request $request, Company $company)
    {
        $this->authorize('show', $company);

        return $company->getSerializedData($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Company $company
     * @return array
     * @throws Exception
     */
    public function update(UpdateRequest $request, Company $company)
    {
        beginTransaction();
        try {
            $company->update($request->only('name'));
            commit();

            return $company->refresh()
                ->getSerializedData($request);
        } catch (Exception $e) {
            rollback();
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Company $company
     * @return void
     * @throws AuthorizationException
     */
    public function destroy(Company $company)
    {
        $this->authorize('delete', $company);
        transaction(function () use ($company) {
            $company->delete();

            return ApiResponse::success();
        });
    }
}
