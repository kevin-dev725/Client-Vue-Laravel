<?php

namespace App\Http\Controllers;

use App\Jobs\ImportQuickbooksClientsJob;
use App\QuickbooksImport;
use App\Services\Quickbooks;
use Exception;
use Illuminate\Http\Request;

class QuickbooksController extends Controller
{
    protected $redirect_url = '/dashboard/settings';

    public function handle()
    {
        return quickbooks()->redirect();
    }

    /**
     * @param Request $request
     * @return string
     * @throws Exception
     */
    public function callback(Request $request)
    {
        if ($request->get('state') !== session(Quickbooks::STATE_PARAM)) {
            throw new Exception('Invalid state');
        }
        $realm_id = $request->get('realmId');
        $access_token = \quickbooks()->getAccessTokenFromCode($request->get('code'));
        $client = \quickbooks()->getAuthClient($access_token->access_token, $realm_id);
        $response = $client->get('query?query=' . rawurlencode('Select * from Customer'));


        disk_s3()->put($path = storage_prefix_dir('quickbooks_imports') . '/' . uniqueFilename('json'), $response->getBody()->getContents());
        $import = QuickbooksImport::query()->create([
            'user_id' => $request->user()->id,
            'status' => QuickbooksImport::STATUS_PENDING,
            'path' => $path
        ]);
        /**
         * @var QuickbooksImport $import
         */
        dispatch(new ImportQuickbooksClientsJob($import));

        return $this->redirect();
    }

    public function redirect()
    {
        return redirect($this->redirect_url . '/?quickbooks_import=success');
    }
}
