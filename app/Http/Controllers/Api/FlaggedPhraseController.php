<?php

namespace App\Http\Controllers\Api;

use App\FlaggedPhrase;
use App\Http\Controllers\Controller;
use App\Transformers\FlaggedPhraseTransformer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FlaggedPhraseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return fractal(FlaggedPhrase::all(), new FlaggedPhraseTransformer())
            ->respond();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'phrase' => 'required|string|max:191',
        ]);
        $phrase = FlaggedPhrase::query()->create($request->only('phrase'));
        return fractal($phrase, new FlaggedPhraseTransformer())
            ->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param FlaggedPhrase $flagged_phrase
     * @return Response
     */
    public function update(Request $request, FlaggedPhrase $flagged_phrase)
    {
        $this->validate($request, [
            'phrase' => 'required|string|max:191',
        ]);
        $flagged_phrase->update($request->only('phrase'));
        return fractal($flagged_phrase, new FlaggedPhraseTransformer())
            ->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param FlaggedPhrase $flagged_phrase
     * @return void
     * @throws Exception
     */
    public function destroy(FlaggedPhrase $flagged_phrase)
    {
        $flagged_phrase->delete();
    }
}
