<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrependCountryCodeToClientPhoneNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        transaction(function () {
            \App\Client::query()->chunk(100, function ($clients) {
                /**
                 * @var \App\Client $client
                 */
                foreach ($clients as $client) {
                    if (!empty($client->phone_number)) {
                        try {
                            $client->phone_number = (string)phone($client->phone_number, config('settings.default_country'));
                        } catch (\Exception $e) {

                        }
                    }
                    if (!empty($client->alt_phone_number)) {
                        try {
                            $client->alt_phone_number = (string)phone($client->alt_phone_number, config('settings.default_country'));
                        } catch (\Exception $e) {

                        }
                    }
                    $client->save();
                }
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
