<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Lien\ImportFilesRequest;
use App\Http\Requests\Api\Lien\ImportRequest;
use App\Http\Requests\Api\Lien\IndexRequest;
use App\Jobs\Lien\ImportFilesJob;
use App\Jobs\Lien\ImportLienJob;
use App\Lien;
use App\LienFile;
use App\State;
use App\Transformers\LienTransformer;
use App\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Storage;

class LienController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $query = Lien::query();
        if ($owner = $request->get('owner')) {
            $owner = escape_like($owner);
            $query->where('owner', 'like', "%$owner%");
        }
        if ($county = $request->get('county')) {
            $county = trim(str_replace("County","", $county));
            $county = escape_like($county);
            $query->where('county', 'like', "%$county%");
        }
        if ($state = $request->get('state')) {
            $query->where('state', $state);
        }
        $paginate = $query->paginate($request->get('per_page', 15));
        return fractal()
            ->collection($paginate->items(), new LienTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($paginate))
            ->respond();
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
     * @param Lien $lien
     * @return Response
     */
    public function show(Lien $lien)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Lien $lien
     * @return Response
     */
    public function update(Request $request, Lien $lien)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Lien $lien
     * @return Response
     */
    public function destroy(Lien $lien)
    {
        //
    }

    public function import(ImportRequest $request)
    {
        $path = Storage::disk(config('filesystems.cloud'))
            ->putFile('imports/lien', $request->file('file'));
        dispatch(new ImportLienJob(config('filesystems.cloud'), $path, $request->get('state'), $request->get('county')));
    }

    public function importFiles(ImportFilesRequest $request)
    {
        $path = Storage::disk(config('filesystems.cloud'))
            ->putFile('imports/lien', $request->file('zip'));
        dispatch(new ImportFilesJob(config('filesystems.cloud'), $path));
    }

    public function addRecords(Request  $request) {
        try {
            $recordData = $request->get('data');
            $records = [];
            $duplicatedRecords = [];
            foreach( $recordData as $item) {
                if (!$item['isBinary']) {
                    $pdfUrl = $item['pdfUrl'];
                    // $pdfContent = file_get_contents($pdfUrl);
                    $curlSession = curl_init();
                    curl_setopt($curlSession, CURLOPT_URL, $pdfUrl);
                    curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
                    curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curlSession,CURLOPT_SSL_VERIFYPEER, false);

                    $fileContent = curl_exec($curlSession);
                    curl_close($curlSession);
    
                } else  {
                    $fileContent = base64_decode($item['pdfFile']);
                    if (strpos($fileContent, '%PDF') !== 0) {
                        $fileContent = base64_decode($fileContent);
                    }
                }
                
                
               
                $stateCode = "";
                $state = State::where('name',isset($item['state'])?$item['state']:null)->first();
                if ($state) {
                    $stateCode = $state->iso_3166_2;
                }
                

                $fileName="";
                if ($fileContent) {
                    $fileName = storage_prefix_dir('liens/') . $item['instrumentId'] . "-" . time() .  ".pdf";
                    // $fileName = disk_s3()->putFile('liens/', $fileContent, 'public');
                }

                $record = [
                    'user_id' => $request->user()->id,
                    'first_direct_name' => isset($item['firstDirectName'])?$item['firstDirectName']:"",
                    'first_indirect_name' => isset($item['firstIndirectName'])?$item['firstIndirectName']:"",
                    'record_date' => date("Y-m-d", strtotime($item['recordDate'])),
                    'doc_type' => isset($item['docType'])?$item['docType']:"",
                    'book_type' => isset($item['bookType'])?$item['bookType']:"",
                    'book_page' => isset($item['bookPage'])?$item['bookPage']:"",
                    'instrument_id' => $item['instrumentId'],
                    'legal'=>isset($item['bookPage'])?$item['legal']:'',
                    'row_in_db' => isset($item['rowInDB'])?$item['rowInDB']:0,
                    'grantor' =>isset($item['grantor'])?$item['grantor']:"",
                    'grantee' => $item['grantee'],
                    'case_number' => $item['caseNumber'],
                    'is_binary' => $item['isBinary'],
                    'file_urls' =>   $fileName,
                    'site' => isset($item['site'])?$item['site']:"",
                    'status' => isset($item['status'])?$item['status']:"",
                    'cross_party' => isset($item['crossParty'])?$item['crossParty']:"",
                    'record_fee' => isset($item['recordFee'])?$item['recordFee']:"",
                    'deed_tax' => isset($item['deedTax'])?$item['deedTax']:"",
                    'mortgage_tax' =>isset($item['mortgageTax'])? $item['mortgageTax']:"",
                    'intangible_tax' => isset($item['intangibleTax'])?$item['intangibleTax']:"",
                    'pdf_url'=>$item['isBinary']?'':$item['pdfUrl'],
                    'state'=>$stateCode,
                    'county'=>isset($item['county'])?$item['county']:"",
                    'lienor'=> '',
                    'owner' => isset($item['owner']) ? $item['owner'] : (isset($item['firstIndirectName'])? $item['firstIndirectName']:"")
                ];
                
                try {
                    $lien = Lien::query()->create($record);

                    $lienFile = LienFile::query()->create([
                        'lien_id'=>$lien->id,
                        'file_name' => $fileName?$fileName:""
                    ]);
                    if ($fileContent) {
                        disk_s3()->put($fileName, $fileContent);
                    }
                    $records[] = $record;
                    
                } catch (QueryException $e) {
                    $duplicatedRecords[] = [
                        'instrument_id' => $record['instrument_id'],
                        'state'=>$record['state'],
                        'county'=>$record['county'],
                        'owner' => $record['owner'],
                        'site'=>$record['site'],
                        'message' => "This lien record not added"
                    ];
                }
            }
            
            return ApiResponse::success(["message"=>count($records)." records are addded", "added"=>$records, "duplicated"=>$duplicatedRecords]);
        } catch (Exception $e) {
             return ApiResponse::internalServerError(["message" =>"Import is failed."]);
        }
    }

    /**
     * @param Illuminate\Http\Request;
     * @return void
     */
    
    public function checkRecord(Request  $request) {
        try {
            $record = $request->get('data');
            $user = Lien::query()
                ->where('instrument_id', $record['instrumentId'])
                ->where('site', $record['site'])
                ->where('state', $record['state'])
                ->where('county', $record['county'])
                ->where('owner', $record['owner'])
                ->first();
            if ($user) {
                return ApiResponse::success(["exist"=>1, "message"=>"This record is exist"]);
            } 
            return ApiResponse::success(["exist"=>0, "message"=>"This record is not exist"]);
        } catch (Exception $e) {
             return ApiResponse::internalServerError(["message" =>"Something is wrong"]);
        }
    }

    public function truncate(Request $request) {
        Lien::truncate();
        return ApiResponse::success();
    }
}
