<?php

namespace Tests\Feature;

use App\Client;
use App\ClientImport;
use App\Notifications\User\InvalidImportNotification;
use App\Review;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use League\Csv\Exception;
use League\Csv\Reader;
use Tests\ApiTestCase;

class CsvImportTest extends ApiTestCase
{
    use DatabaseTransactions;

    /**
     * @throws Exception
     */
    public function testImportingClientsUsingCsvFile()
    {
        $csv = Reader::createFromPath(storage_path('clients-csv-template.csv'), 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords(['client_type', 'organization_name', 'first_name', 'middle_name', 'last_name', 'phone_number', 'phone_number_ext', 'alt_phone_number', 'alt_phone_number_ext', 'country_iso_code', 'street_address', 'street_address2', 'city', 'state', 'postal_code', 'email', 'billing_fname', 'billing_mname', 'billing_lname', 'billing_phone_number', 'billing_phone_number_ext', 'billing_street_address', 'billing_street_address2', 'billing_city', 'billing_state', 'billing_postal_code', 'billing_email', 'initial_star_rating']);

        $response = $this->authJson('post', '/api/v1/client/import-csv', [
            'file' => new UploadedFile(
                storage_path('clients-csv-template.csv'),
                'clients-csv-template.csv', 'text/csv',
                filesize(storage_path('clients-csv-template.csv')),
                null,
                true
            ),
        ]);

        $response->assertStatus(200);
        foreach ($records as $offset => $record) {
            $this->assertDatabaseHas('clients', [
                'client_type' => $record['client_type'],
                'organization_name' => $record['organization_name'],
                'first_name' => $record['first_name'],
                'middle_name' => $record['middle_name'],
                'last_name' => $record['last_name'],
                'phone_number' => phone($record['phone_number'], $record['country_iso_code']),
                'phone_number_ext' => $record['phone_number_ext'],
                'alt_phone_number' => (!empty($record['alt_phone_number'])) ? phone($record['alt_phone_number'], $record['country_iso_code']) : '',
                'alt_phone_number_ext' => $record['alt_phone_number_ext'],
                'street_address' => $record['street_address'],
                'street_address2' => $record['street_address2'],
                'city' => $record['city'],
                'state' => $record['state'],
                'postal_code' => $record['postal_code'],
                'email' => $record['email'],
                'billing_first_name' => $record['billing_fname'],
                'billing_middle_name' => $record['billing_mname'],
                'billing_last_name' => $record['billing_lname'],
                'billing_phone_number' => (!empty($record['billing_phone_number'])) ? phone($record['billing_phone_number'], $record['country_iso_code']) : '',
                'billing_phone_number_ext' => $record['billing_phone_number_ext'],
                'billing_street_address' => $record['billing_street_address'],
                'billing_street_address2' => $record['billing_street_address2'],
                'billing_city' => $record['billing_city'],
                'billing_state' => $record['billing_state'],
                'billing_postal_code' => $record['billing_postal_code'],
                'billing_email' => $record['billing_email'],
                'initial_star_rating' => empty(trim($record['initial_star_rating'])) ? config('settings.import.default_initial_star_rating') : $record['initial_star_rating'],
            ]);
        }
    }

    /**
     * @throws Exception
     */
    public function testImportingClientsUsingCsvFileCreatesDefaultReview()
    {
        $csv = Reader::createFromPath(storage_path('clients-csv-template.csv'), 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords(['client_type', 'organization_name', 'first_name', 'middle_name', 'last_name', 'phone_number', 'phone_number_ext', 'alt_phone_number', 'alt_phone_number_ext', 'country_iso_code', 'street_address', 'street_address2', 'city', 'state', 'postal_code', 'email', 'billing_fname', 'billing_mname', 'billing_lname', 'billing_phone_number', 'billing_phone_number_ext', 'billing_street_address', 'billing_street_address2', 'billing_city', 'billing_state', 'billing_postal_code', 'billing_email', 'initial_star_rating']);

        $response = $this->authJson('post', '/api/v1/client/import-csv', [
            'file' => new UploadedFile(
                storage_path('clients-csv-template.csv'),
                'clients-csv-template.csv', 'text/csv',
                filesize(storage_path('clients-csv-template.csv')),
                null,
                true
            ),
        ]);

        $response->assertStatus(200);
        foreach ($records as $offset => $record) {
            $this->assertDatabaseHas('clients', [
                'email' => $record['email'],
            ]);
            $client = Client::query()
                ->where('email', $record['email'])
                ->where('user_id', $this->user->id)
                ->first();
            $this->assertNotNull($client, 'Client does not exist.');
            $this->assertDatabaseHas('reviews', [
                'user_id' => $this->user->id,
                'client_id' => $client->id,
                'service_date' => today()->toDateString(),
                'star_rating' => 4,
                'comment' => null,
                'payment_rating' => Review::REVIEW_RATING_NO_OPINION,
                'character_rating' => Review::REVIEW_RATING_NO_OPINION,
                'repeat_rating' => Review::REVIEW_RATING_NO_OPINION,
            ]);
        }
    }

    /**
     * @throws Exception
     */
    public function testImportOnlyRequiresSomeFields()
    {
        $csv = Reader::createFromPath($path = disk_local()->path('testing/revised-clients-csv-template.csv'), 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords(['client_type', 'organization_name', 'first_name', 'last_name', 'phone_number', 'phone_number_ext', 'street_address', 'street_address2', 'city', 'state', 'postal_code', 'email']);

        $response = $this->authJson('post', '/api/v1/client/import-csv', [
            'file' => new UploadedFile(
                $path,
                'revised-clients-csv-template.csv', 'text/csv',
                filesize($path),
                null,
                true
            ),
        ]);

        $response->assertStatus(200);
        foreach ($records as $offset => $record) {
            $this->assertDatabaseHas('clients', [
                'client_type' => $record['client_type'],
                'organization_name' => $record['organization_name'],
                'first_name' => $record['first_name'],
                'last_name' => $record['last_name'],
                'phone_number' => phone($record['phone_number'], array_get($record, 'country_iso_code', config('settings.default_country'))),
                'phone_number_ext' => $record['phone_number_ext'],
                'street_address' => $record['street_address'],
                'street_address2' => $record['street_address2'],
                'city' => $record['city'],
                'state' => $record['state'],
                'postal_code' => $record['postal_code'],
                'email' => $record['email'],
            ]);
        }
    }

    public function testValidatesCsvContent()
    {
        Notification::fake();

        $path = disk_local()->path('testing/csv-invalid-template.csv');
        $response = $this->authJson('post', '/api/v1/client/import-csv', [
            'file' => new UploadedFile(
                $path,
                'csv-invalid-template.csv', 'text/csv',
                filesize($path),
                null,
                true
            ),
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('client_imports', [
            'user_id' => $this->user->id,
            'status' => ClientImport::STATUS_ERROR,
            'invalid_row' => null,
        ]);
        /** @var ClientImport $client_import */
        $this->assertNotNull(
            $client_import = ClientImport::query()->where('user_id', $this->user->id)
                ->whereNotNull('errors')
                ->whereNotNull('exception')
                ->first()
        );
        $this->assertTrue(['Error importing CSV. This is probably due to invalid names in the header row.'] === $client_import->errors);
        Notification::assertSentTo(
            $this->user,
            InvalidImportNotification::class,
            function ($notification, $channels) use ($client_import) {
                return $notification->clientImport->id === $client_import->id;
            }
        );
    }
}
