<?php

namespace App\Transformers;

use App\Company;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class CompanyTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $availableIncludes = ['users', 'clients'];

    /**
     * @param Company $company
     * @return array
     */
    public function transform(Company $company)
    {
        return $company->attributesToArray();
    }

    /**
     * @param Company $company
     * @return Collection
     */
    public function includeUsers(Company $company)
    {
        return $this->collection($company->users, new UserTransformer());
    }

    /**
     * @param Company $company
     * @return Collection
     */
    public function includeClients(Company $company)
    {
        return $this->collection($company->clients, new ClientTransformer());
    }
}