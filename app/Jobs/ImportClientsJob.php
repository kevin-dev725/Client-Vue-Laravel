<?php

namespace App\Jobs;

use App\Client;
use App\ClientImport;
use App\Country;
use App\Notifications\User\InvalidImportNotification;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use League\Csv\Reader;

class ImportClientsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var ClientImport
     */
    private $clientImport;

    private $columnNames = ['Client type', 'Organization name', 'First name', 'Middle name', 'Last name', 'Phone number', 'Phone number extension', 'Alternate phone number', 'Alternate phone number extension', 'Country iso alpha-2 code', 'Street address', 'Street address 2', 'City', 'State', 'Postal code', 'Email address', 'Billing first name', 'Billing middle name', 'Billing last name', 'Billing phone number', 'Billing phone number extension', 'Billing street address', 'Billing street address 2', 'Billing city', 'Billing state', 'Billing postal code', 'Billing email address', 'Initial star rating', 'Zip code'];

    private $formattedColumnNames = ['client_type', 'organization_name', 'first_name', 'middle_name', 'last_name', 'phone_number', 'phone_number_ext', 'alt_phone_number', 'alt_phone_number_ext', 'country', 'street_address', 'street_address2', 'city', 'state', 'postal_code', 'email', 'billing_first_name', 'billing_middle_name', 'billing_last_name', 'billing_phone_number', 'billing_phone_number_ext', 'billing_street_address', 'billing_street_address2', 'billing_city', 'billing_state', 'billing_postal_code', 'billing_email', 'initial_star_rating', 'postal_code'];

    /**
     * Create a new job instance.
     *
     * @param ClientImport $clientImport
     */
    public function __construct(ClientImport $clientImport)
    {
        $this->clientImport = $clientImport;
        $this->columnNames = array_map(function ($column) {
            return strtolower($column);
        }, $this->columnNames);
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        if (!ini_get("auto_detect_line_endings")) {
            ini_set("auto_detect_line_endings", '1');
        }
        $this->clientImport->update([
            'status' => ClientImport::STATUS_STARTED
        ]);
        try {
            $csv = Reader::createFromString(disk_s3()->get($this->clientImport->csv));
            $csv->setHeaderOffset(0);
            $records = $csv->getRecords();
        } catch (Exception $e) {
            $this->failed($e);
            echo 'Error reading csv file.';
            return;
        }
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
            'middle_name' => 'nullable|string|max:191',
            'last_name' => 'required|string|max:191',
            'phone_number' => 'required|phone:AUTO,US',
            'phone_number_ext' => 'nullable|string|max:10',
            'alt_phone_number' => 'nullable|phone:AUTO,US',
            'alt_phone_number_ext' => 'nullable|string|max:10',
            'street_address' => 'required|string|max:80',
            'street_address2' => 'nullable|string|max:80',
            'city' => 'required|string|max:40',
            'state' => 'required|string|max:3',
            'postal_code' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:191',
            'billing_first_name' => [
                'nullable',
                'string',
                'max:191',
            ],
            'billing_middle_name' => 'nullable|string|max:20',
            'billing_last_name' => [
                'nullable',
                'string',
                'max:191',
            ],
            'billing_phone_number' => [
                'nullable',
                'phone:AUTO,US'
            ],
            'billing_phone_number_ext' => 'nullable|string|max:10',
            'billing_street_address' => [
                'nullable',
                'string',
                'max:80',
            ],
            'billing_street_address2' => 'nullable|string|max:80',
            'billing_city' => [
                'nullable',
                'string',
                'max:40',
            ],
            'billing_state' => [
                'nullable',
                'string',
                'max:3',
            ],
            'billing_postal_code' => 'nullable|string|max:20',
            'billing_email' => 'nullable|email|max:191',
            'initial_star_rating' => 'nullable|numeric|min:1|max:5',
            'country' => 'nullable|exists:countries,iso_3166_2'
        ];
        beginTransaction();
        try {
            foreach ($records as $offset => $record) {
                $record = $this->formatRecord($record);
                $record['client_type'] = strtolower($record['client_type']);
                $validator = Validator::make($record, $rules);
                if ($validator->fails()) {
                    rollback();
                    $this->clientImport->update([
                        'status' => ClientImport::STATUS_ERROR,
                        'errors' => $validator->errors()->jsonSerialize(),
                        'invalid_row' => [
                            'row_index' => $offset - 1,
                            'row' => $record,
                        ]
                    ]);
                    $this->clientImport->user->notify(new InvalidImportNotification($this->clientImport));
                    dispatch(new DeleteClientImportJobFile($this->clientImport));
                    return;
                }
                /**
                 * @var Client $client
                 */
                $client = Client::query()
                    ->create([
                        'client_type' => array_get($record, 'client_type'),
                        'organization_name' => array_get($record, 'organization_name'),
                        'first_name' => array_get($record, 'first_name'),
                        'middle_name' => array_get($record, 'middle_name'),
                        'last_name' => array_get($record, 'last_name'),
                        'phone_number' => phone(array_get($record, 'phone_number'), array_get($record, 'country', config('settings.default_country'))),
                        'phone_number_ext' => array_get($record, 'phone_number_ext'),
                        'alt_phone_number' => (!empty(array_get($record, 'alt_phone_number'))) ? phone(array_get($record, 'alt_phone_number'), array_get($record, 'country')) : '',
                        'alt_phone_number_ext' => array_get($record, 'alt_phone_number_ext'),
                        'street_address' => array_get($record, 'street_address'),
                        'street_address2' => array_get($record, 'street_address2'),
                        'city' => array_get($record, 'city'),
                        'state' => array_get($record, 'state'),
                        'postal_code' => array_get($record, 'postal_code'),
                        'email' => array_get($record, 'email'),
                        'billing_first_name' => array_get($record, 'billing_first_name'),
                        'billing_middle_name' => array_get($record, 'billing_middle_name'),
                        'billing_last_name' => array_get($record, 'billing_last_name'),
                        'billing_phone_number' => (!empty(array_get($record, 'billing_phone_number'))) ? phone(array_get($record, 'billing_phone_number'), array_get($record, 'country')) : '',
                        'billing_phone_number_ext' => array_get($record, 'billing_phone_number_ext'),
                        'billing_street_address' => array_get($record, 'billing_street_address'),
                        'billing_street_address2' => array_get($record, 'billing_street_address2'),
                        'billing_city' => array_get($record, 'billing_city'),
                        'billing_state' => array_get($record, 'billing_state'),
                        'billing_postal_code' => array_get($record, 'billing_postal_code'),
                        'billing_email' => array_get($record, 'billing_email'),
                        'initial_star_rating' => empty(array_get($record, 'initial_star_rating')) ? config('settings.import.default_initial_star_rating') : array_get($record, 'initial_star_rating'),
                        'user_id' => $this->clientImport->user_id,
                        'company_id' => $this->clientImport->user->isAccountCompany() ? $this->clientImport->user->company->id : null,
                        'country_id' => array_get($record, 'country') ? Country::query()->where('iso_3166_2', $record['country'])->first()->id : null,
                    ]);
                $client->createInitialReview();
            }
            commit();
            $this->clientImport->update([
                'status' => ClientImport::STATUS_FINISHED
            ]);
            dispatch(new DeleteClientImportJobFile($this->clientImport));
        } catch (Exception $e) {
            rollback();
            throw $e;
        }
    }

    public function failed(Exception $exception)
    {
        $this->clientImport->update([
            'status' => ClientImport::STATUS_ERROR,
            'errors' => ['Error importing CSV. This is probably due to invalid names in the header row.'],
            'exception' => [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
            ]
        ]);
        $this->clientImport->user->notify(new InvalidImportNotification($this->clientImport));
    }

    public function formatRecord(array $record)
    {
        $formatted_record = [];
        foreach ($record as $column => $value) {
            if (($index = array_search(strtolower($column), $this->columnNames)) !== false) {
                $formatted_record[$this->formattedColumnNames[$index]] = trim($value);
            }
        }
        return $formatted_record;
    }
}
