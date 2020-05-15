<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

class Quickbooks
{
    const STATE_PARAM = 'quickbooks_state';
    const AUTHORIZATION_ENDPOINT = 'https://appcenter.intuit.com/connect/oauth2';
    const TOKEN_ENDPOINT = 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer';
    const BASE_URL_SANDBOX = 'https://sandbox-quickbooks.api.intuit.com';
    const BASE_URL_PRODUCTION = 'https://quickbooks.api.intuit.com';

    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @return RedirectResponse|Redirector
     */
    public function redirect()
    {
        session()->put(self::STATE_PARAM, $state = str_random(30));
        return redirect(qs_url('https://appcenter.intuit.com/connect/oauth2', [
            'client_id' => config('services.quickbooks.client_id'),
            'response_type' => 'code',
            'scope' => 'com.intuit.quickbooks.accounting',
            'redirect_uri' => config('services.quickbooks.redirect'),
            'state' => $state
        ]));
    }

    /**
     * @param $code
     * @return mixed
     */
    public function getAccessTokenFromCode($code)
    {
        $response = $this->client->post(self::TOKEN_ENDPOINT, [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => config('services.quickbooks.redirect')
            ],
            'headers' => [
                'Authorization' => "Basic " . base64_encode(config('services.quickbooks.client_id') . ":" . config('services.quickbooks.client_secret'))
            ]
        ]);
        return json_decode($response->getBody()->getContents());
    }

    /**
     * @param string $access_token
     * @param string $realm_id
     * @return Client
     */
    public function getAuthClient($access_token, $realm_id)
    {
        return new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Accept' => 'application/json',
            ],
            'base_uri' => $this->getBaseUrl() . '/v3/company/' . $realm_id . '/'
        ]);
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->isOnProduction() ? self::BASE_URL_PRODUCTION : self::BASE_URL_SANDBOX;
    }

    /**
     * @return bool
     */
    public function isOnProduction()
    {
        return config('app.env') === 'production';
    }
}
