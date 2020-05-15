<?php

namespace App\Jobs;

use App\Client;
use App\Notifications\User\QuickbooksImportFinishedNotification;
use App\QuickbooksImport;
use App\Rules\StateValidator;
use App\Services\HumanNameParser;
use App\Services\States;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ImportQuickbooksClientsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var QuickbooksImport
     */
    private $import;

    private $errors = [];

    /**
     * @var HumanNameParser
     */
    private $nameParser;

    /**
     * @var array
     */
    private $stateValidValues;

    /**
     * Create a new job instance.
     *
     * @param QuickbooksImport $import
     */
    public function __construct(QuickbooksImport $import)
    {
        $this->import = $import;
        $this->nameParser = app(HumanNameParser::class);
        $this->stateValidValues = States::validValues();
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function handle()
    {
        beginTransaction();
        $data = json_decode(disk_s3()->get($this->import->path));
        try {
            foreach ($data->QueryResponse->Customer as $offset => $customer) {
                if ($data = $this->validate($customer)) {
                    /**
                     * @var Client $client
                     */
                    $client = Client::query()
                        ->updateOrCreate([
                            'email' => $data->email,
                            'client_type' => $data->client_type,
                            'first_name' => $data->first_name,
                            'last_name' => $data->last_name,
                        ], [
                            'client_type' => $data->client_type,
                            'organization_name' => $data->organization_name,
                            'first_name' => $data->first_name,
                            'last_name' => $data->last_name,
                            'phone_number' => $customerPhone = phone($data->phone_number, !is_null($this->import->user->country) ? $this->import->user->country->iso_3166_2 : 'US'),
                            'street_address' => $data->street_address,
                            'city' => $data->city,
                            'state' => $data->state,
                            'postal_code' => $data->postal_code,
                            'email' => $data->email,
                            'billing_first_name' => $data->billing_first_name,
                            'billing_last_name' => $data->billing_last_name,
                            'billing_phone_number' => $customerPhone,
                            'billing_street_address' => $data->billing_street_address,
                            'billing_city' => $data->billing_city,
                            'billing_state' => $data->billing_state,
                            'billing_postal_code' => $data->billing_postal_code,
                            'billing_email' => $data->billing_email,
                            'user_id' => $this->import->user_id,
                            'company_id' => $this->import->user->isAccountCompany() ? $this->import->user->company->id : null,
                            'country_id' => !empty($this->import->user->country) ? $this->import->user->country->id : null,
                        ]);
                    if ($client->wasRecentlyCreated) {
                        $client->update([
                            'initial_star_rating' => config('settings.import.default_initial_star_rating')
                        ]);
                        $client->createInitialReview();
                    }
                } else {
                    continue;
                }
            }
            commit();
            $this->import->update([
                'status' => count($this->errors) > 0 ? QuickbooksImport::STATUS_FINISHED_WITH_ERROR : QuickbooksImport::STATUS_FINISHED,
                'errors' => $this->errors,
            ]);
            dispatch(new DeleteQuickbooksImportJobFile($this->import));
            $this->import->user->notify(new QuickbooksImportFinishedNotification($this->import));
        } catch (Exception $e) {
            rollback();
            throw $e;
        }
    }

    /**
     * @param $customer
     * @return bool|object
     */
    private function validate($customer)
    {
        $isClientTypeOrg = !empty($customer->CompanyName);
        $rules = [
            'client_type' => [
                'required',
                Rule::in([Client::CLIENT_TYPE_INDIVIDUAL, Client::CLIENT_TYPE_ORGANIZATION]),
            ],
            'organization_name' => [
                'required_if:client_type,==,' . Client::CLIENT_TYPE_ORGANIZATION,
                'nullable',
                'string',
                'max:191',
            ],
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'phone_number' => 'required|phone:AUTO,US',
            'street_address' => 'nullable|string|max:80',
            'city' => 'nullable|string|max:40',
            'state' => [
                'nullable',
                'string',
                new StateValidator(),
            ],
            'postal_code' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:191',
            'billing_first_name' => [
                'nullable',
                'required_if:client_type,==,' . Client::CLIENT_TYPE_ORGANIZATION,
                'string',
                'max:191',
            ],
            'billing_last_name' => [
                'nullable',
                'required_if:client_type,==,' . Client::CLIENT_TYPE_ORGANIZATION,
                'string',
                'max:191',
            ],
            'billing_phone_number' => [
                'nullable',
                'required_if:client_type,==,' . Client::CLIENT_TYPE_ORGANIZATION,
                'phone:AUTO,US'
            ],
            'billing_street_address' => [
                'nullable',
                'required_if:client_type,==,' . Client::CLIENT_TYPE_ORGANIZATION,
                'string',
                'max:80',
            ],
            'billing_city' => [
                'nullable',
                'required_if:client_type,==,' . Client::CLIENT_TYPE_ORGANIZATION,
                'string',
                'max:40',
            ],
            'billing_state' => [
                'nullable',
//				'required_if:client_type,==,' . Client::CLIENT_TYPE_ORGANIZATION,
                'string',
                new StateValidator(),
            ],
            'billing_postal_code' => 'nullable|string|max:20',
            'billing_email' => 'nullable|email|max:191',
        ];

        $first_name = data_get($customer, 'GivenName');
        $last_name = data_get($customer, 'FamilyName');
        if (empty($first_name) || empty($last_name)) {
            if (!empty($name = data_get($customer, 'DisplayName'))) {
                $parts = $this->nameParser->splitFullName($name);
                $first_name = $parts['first_name'];
                $last_name = $parts['last_name'];
            }
        }

        $data = [
            'client_type' => $clientType = !empty($customer->CompanyName) ? Client::CLIENT_TYPE_ORGANIZATION : Client::CLIENT_TYPE_INDIVIDUAL,
            'organization_name' => $clientType === Client::CLIENT_TYPE_ORGANIZATION ? $customer->CompanyName : null,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'phone_number' => $phone = data_get($customer, 'PrimaryPhone.FreeFormNumber'),
            'street_address' => $street = data_get($customer, 'BillAddr.Line1'),
            'city' => $city = data_get($customer, 'BillAddr.City'),
            'state' => $state = States::getState(data_get($customer, 'BillAddr.CountrySubDivisionCode')),
            'postal_code' => $postal = data_get($customer, 'BillAddr.PostalCode'),
            'email' => $customerEmail = data_get($customer, 'PrimaryEmailAddr.Address'),
            'billing_first_name' => $isClientTypeOrg ? $first_name : null,
            'billing_last_name' => $isClientTypeOrg ? $last_name : null,
            'billing_phone_number' => $isClientTypeOrg ? $phone : null,
            'billing_street_address' => $isClientTypeOrg ? $street : null,
            'billing_city' => $isClientTypeOrg ? $city : null,
            'billing_state' => $isClientTypeOrg ? $state : null,
            'billing_postal_code' => $isClientTypeOrg ? $postal : null,
            'billing_email' => $isClientTypeOrg ? $customerEmail : null,
        ];

        $validator = Validator::make($data, $rules, [
            'state.in' => 'State name ' . $state . ' unrecognized.',
            'billing_state.in' => 'State name ' . $state . ' unrecognized.',
        ]);
        if ($validator->fails()) {
            array_push($this->errors, [
                'customer' => $customer->FullyQualifiedName,
                'error' => $validator->errors()->jsonSerialize()
            ]);
            return false;
        }

        return (object)$data;
    }

    /**
     * @param Exception $exception
     */
    public function failed(Exception $exception)
    {
        $this->import->update([
            'status' => QuickbooksImport::STATUS_ERROR,
            'errors' => $exception->getMessage(),
        ]);
    }
}
