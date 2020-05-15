<?php

namespace Tests\Feature;

use App\Client;
use App\Jobs\ImportQuickbooksClientsJob;
use App\QuickbooksImport;
use App\Review;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\File;
use Tests\TestCase;

class QuickBooksTest extends TestCase
{
    use DatabaseTransactions;

    protected $quickbooksCustomersPath = 'quickbooks/quickbooks-customers.json';

    /**
     * @var User
     */
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);
        if (!disk_s3()->exists(storage_prefix_dir($this->quickbooksCustomersPath))) {
            disk_s3()->putFileAs(storage_prefix_dir('quickbooks'), new File(storage_path('app/testing/quickbooks-customers.json')), 'quickbooks-customers.json');
        }
    }

    public function testImport()
    {
        /**
         * @var QuickbooksImport $import
         */
        $import = QuickbooksImport::query()
            ->create([
                'user_id' => $this->user->id,
                'path' => storage_prefix_dir($this->quickbooksCustomersPath),
                'status' => QuickbooksImport::STATUS_PENDING,
            ]);
        dispatch_now(new ImportQuickbooksClientsJob($import));
        $data = json_decode(file_get_contents(storage_path('app/testing/quickbooks-customers.json')));
        foreach ($data->QueryResponse->Customer as $offset => $customer) {
            $isClientTypeOrg = !empty($customer->CompanyName);
            if (empty($customer->GivenName) || empty($customer->FamilyName) || empty($customer->PrimaryPhone)) {
                $this->assertDatabaseMissing('clients', [
                    'user_id' => $this->user->id,
                    'client_type' => $clientType = !empty($customer->CompanyName) ? Client::CLIENT_TYPE_ORGANIZATION : Client::CLIENT_TYPE_INDIVIDUAL,
                    'first_name' => $first_name = !empty($customer->GivenName) ? $customer->GivenName : null,
                    'last_name' => $last_name = !empty($customer->FamilyName) ? $customer->FamilyName : null,
                    'phone_number' => $phone = !empty($customer->PrimaryPhone) ? phone($customer->PrimaryPhone->FreeFormNumber, config('settings.default_country')) : null,
                    'street_address' => $street = !empty($customer->BillAddr) ? $customer->BillAddr->Line1 : null,
                    'city' => $city = !empty($customer->BillAddr) ? $customer->BillAddr->City : null,
                    'state' => $state = !empty($customer->BillAddr) ? $customer->BillAddr->CountrySubDivisionCode : null,
                    'postal_code' => $postal = data_get($customer, 'BillAddr.PostalCode'),
                    'email' => $customerEmail = !empty($customer->PrimaryEmailAddr->Address) ? $customer->PrimaryEmailAddr->Address : null,
                    'billing_first_name' => $isClientTypeOrg ? $first_name : null,
                    'billing_last_name' => $isClientTypeOrg ? $last_name : null,
                    'billing_phone_number' => $phone,
                    'billing_street_address' => $isClientTypeOrg ? $street : null,
                    'billing_city' => $isClientTypeOrg ? $city : null,
                    'billing_state' => $isClientTypeOrg ? $state : null,
                    'billing_postal_code' => $isClientTypeOrg ? $postal : null,
                    'billing_email' => $isClientTypeOrg ? $customerEmail : null,
                ]);
            } else {
                $this->assertDatabaseHas('clients', [
                    'user_id' => $this->user->id,
                    'client_type' => $clientType = !empty($customer->CompanyName) ? Client::CLIENT_TYPE_ORGANIZATION : Client::CLIENT_TYPE_INDIVIDUAL,
                    'first_name' => $first_name = !empty($customer->GivenName) ? $customer->GivenName : null,
                    'last_name' => $last_name = !empty($customer->FamilyName) ? $customer->FamilyName : null,
                    'phone_number' => $phone = !empty($customer->PrimaryPhone) ? phone($customer->PrimaryPhone->FreeFormNumber, config('settings.default_country')) : null,
                    'street_address' => $street = !empty($customer->BillAddr) ? $customer->BillAddr->Line1 : null,
                    'city' => $city = !empty($customer->BillAddr) ? $customer->BillAddr->City : null,
                    'state' => $state = !empty($customer->BillAddr) ? $customer->BillAddr->CountrySubDivisionCode : null,
                    'postal_code' => $postal = data_get($customer, 'BillAddr.PostalCode'),
                    'email' => $customerEmail = !empty($customer->PrimaryEmailAddr->Address) ? $customer->PrimaryEmailAddr->Address : null,
                    'billing_first_name' => $isClientTypeOrg ? $first_name : null,
                    'billing_last_name' => $isClientTypeOrg ? $last_name : null,
                    'billing_phone_number' => $phone,
                    'billing_street_address' => $isClientTypeOrg ? $street : null,
                    'billing_city' => $isClientTypeOrg ? $city : null,
                    'billing_state' => $isClientTypeOrg ? $state : null,
                    'billing_postal_code' => $isClientTypeOrg ? $postal : null,
                    'billing_email' => $isClientTypeOrg ? $customerEmail : null,
                ]);
            }
        }
    }

    public function testImportFromQuickbooksSetsFourStarToClientInitialRating()
    {
        /**
         * @var QuickbooksImport $import
         */
        $import = QuickbooksImport::query()
            ->create([
                'user_id' => $this->user->id,
                'path' => storage_prefix_dir($this->quickbooksCustomersPath),
                'status' => QuickbooksImport::STATUS_PENDING,
            ]);
        dispatch_now(new ImportQuickbooksClientsJob($import));
        $data = json_decode(file_get_contents(storage_path('app/testing/quickbooks-customers.json')));
        foreach ($data->QueryResponse->Customer as $offset => $customer) {
            if (empty($customer->GivenName) || empty($customer->FamilyName) || empty($customer->PrimaryPhone)) {
                $this->assertDatabaseMissing('clients', [
                    'user_id' => $this->user->id,
                    'client_type' => $clientType = !empty($customer->CompanyName) ? Client::CLIENT_TYPE_ORGANIZATION : Client::CLIENT_TYPE_INDIVIDUAL,
                    'first_name' => $first_name = !empty($customer->GivenName) ? $customer->GivenName : null,
                    'last_name' => $last_name = !empty($customer->FamilyName) ? $customer->FamilyName : null,
                ]);
            } else {
                $this->assertDatabaseHas('clients', [
                    'user_id' => $this->user->id,
                    'client_type' => $clientType = !empty($customer->CompanyName) ? Client::CLIENT_TYPE_ORGANIZATION : Client::CLIENT_TYPE_INDIVIDUAL,
                    'first_name' => $first_name = !empty($customer->GivenName) ? $customer->GivenName : null,
                    'last_name' => $last_name = !empty($customer->FamilyName) ? $customer->FamilyName : null,
                    'initial_star_rating' => config('settings.import.default_initial_star_rating'),
                ]);
            }
        }
    }

    public function testImportFromQuickbooksCreatesReviewForClient()
    {
        /**
         * @var QuickbooksImport $import
         */
        $import = QuickbooksImport::query()
            ->create([
                'user_id' => $this->user->id,
                'path' => storage_prefix_dir($this->quickbooksCustomersPath),
                'status' => QuickbooksImport::STATUS_PENDING,
            ]);
        dispatch_now(new ImportQuickbooksClientsJob($import));
        $data = json_decode(file_get_contents(storage_path('app/testing/quickbooks-customers.json')));
        foreach ($data->QueryResponse->Customer as $offset => $customer) {
            if (!(empty($customer->GivenName) || empty($customer->FamilyName) || empty($customer->PrimaryPhone))) {
                $this->assertDatabaseHas('clients', [
                    'user_id' => $this->user->id,
                    'client_type' => $clientType = !empty($customer->CompanyName) ? Client::CLIENT_TYPE_ORGANIZATION : Client::CLIENT_TYPE_INDIVIDUAL,
                    'first_name' => $first_name = !empty($customer->GivenName) ? $customer->GivenName : null,
                    'last_name' => $last_name = !empty($customer->FamilyName) ? $customer->FamilyName : null,
                ]);
                $email = !empty($customer->PrimaryEmailAddr->Address) ? $customer->PrimaryEmailAddr->Address : null;
                $client = Client::query()
                    ->where('email', $email)
                    ->where('user_id', $this->user->id)
                    ->first();
                $this->assertNotNull($client, 'Cannot find client.');
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
    }

    public function testImportGetsFirstAndLastNameFromDisplayName()
    {
        $path = disk_s3()->putFileAs(storage_prefix_dir('quickbooks'), new File(storage_path('app/testing/quickbooks-one-customer.json')), 'one-customers.json');
        /**
         * @var QuickbooksImport $import
         */
        $import = QuickbooksImport::query()
            ->create([
                'user_id' => $this->user->id,
                'path' => $path,
                'status' => QuickbooksImport::STATUS_PENDING,
            ]);
        dispatch_now(new ImportQuickbooksClientsJob($import));
        $this->assertDatabaseHas('clients', [
            'user_id' => $this->user->id,
            'first_name' => "Rodney",
            'last_name' => 'Hills',
        ]);
    }

    public function testImportAcceptsStateFullName()
    {
        $path = disk_s3()->putFileAs(storage_prefix_dir('quickbooks'), new File(storage_path('app/testing/quickbooks-customer-full-state-name.json')), 'customer-full-state-name.json');
        /**
         * @var QuickbooksImport $import
         */
        $import = QuickbooksImport::query()
            ->create([
                'user_id' => $this->user->id,
                'path' => $path,
                'status' => QuickbooksImport::STATUS_PENDING,
            ]);
        dispatch_now(new ImportQuickbooksClientsJob($import));
        $this->assertDatabaseHas('clients', [
            'user_id' => $this->user->id,
            'first_name' => "Rodney",
            'last_name' => 'Hills',
            'street_address' => '4581 Finch St.',
            'city' => 'Bayshore',
            'state' => 'CA',
        ]);
    }

    public function testImportDoesNotAcceptInvalidUSState()
    {
        $path = disk_s3()->putFileAs(storage_prefix_dir('quickbooks'), new File(storage_path('app/testing/quickbooks-customer-invalid-state.json')), 'customer-invalid-state.json');
        /**
         * @var QuickbooksImport $import
         */
        $import = QuickbooksImport::query()
            ->create([
                'user_id' => $this->user->id,
                'path' => $path,
                'status' => QuickbooksImport::STATUS_PENDING,
            ]);
        dispatch_now(new ImportQuickbooksClientsJob($import));
        $this->assertDatabaseMissing('clients', [
            'user_id' => $this->user->id,
            'first_name' => "Rodney",
            'last_name' => 'Hills',
            'street_address' => '4581 Finch St.',
            'city' => 'Bayshore',
            'state' => 'Invalid State',
        ]);
        $import->refresh();
        $this->assertArrayHasKey('state', array_get($import->errors, '0.error'));
        $this->assertEquals("State name is unrecognized.", array_get($import->errors, '0.error.state.0'));
    }

    public function testImportAcceptsStateAbbreviation()
    {
        $path = disk_s3()->putFileAs(storage_prefix_dir('quickbooks'), new File($path = storage_path('app/testing/quickbooks-customer-state-abbr.json')), 'customer-state-abbr.json');
        /**
         * @var QuickbooksImport $import
         */
        $import = QuickbooksImport::query()
            ->create([
                'user_id' => $this->user->id,
                'path' => $path,
                'status' => QuickbooksImport::STATUS_PENDING,
            ]);
        dispatch_now(new ImportQuickbooksClientsJob($import));
        $this->assertDatabaseHas('clients', [
            'user_id' => $this->user->id,
            'first_name' => 'Rodney',
            'last_name' => 'Hills',
            'state' => 'AZ',
        ]);
        $this->assertDatabaseHas('clients', [
            'user_id' => $this->user->id,
            'first_name' => 'Waino',
            'last_name' => 'Lakin',
            'state' => 'ID',
        ]);
    }

    public function testImportAcceptsStateWithNonAlphabeticCharacters()
    {
        $path = disk_s3()->putFileAs(storage_prefix_dir('quickbooks'), new File($path = storage_path('app/testing/quickbooks-customer-state-non-alphabets.json')), 'customer-state-non-alphabets.json');
        /**
         * @var QuickbooksImport $import
         */
        $import = QuickbooksImport::query()
            ->create([
                'user_id' => $this->user->id,
                'path' => $path,
                'status' => QuickbooksImport::STATUS_PENDING,
            ]);
        dispatch_now(new ImportQuickbooksClientsJob($import));
        $this->assertDatabaseHas('clients', [
            'user_id' => $this->user->id,
            'first_name' => 'Rodney',
            'last_name' => 'Hills',
            'state' => 'AZ',
        ]);
        $this->assertDatabaseHas('clients', [
            'user_id' => $this->user->id,
            'first_name' => 'Waino',
            'last_name' => 'Lakin',
            'state' => 'ID',
        ]);
    }
}
