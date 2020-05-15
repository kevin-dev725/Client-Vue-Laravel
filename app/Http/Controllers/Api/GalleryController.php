<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Gallery\DeleteMultipleRequest;
use App\Http\Requests\Api\Gallery\IndexRequest;
use App\Http\Requests\Api\Gallery\StoreRequest;
use App\Media;
use App\Transformers\MediaTransformer;
use App\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GalleryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Media::class, null, [
            'except' => ['index']
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request)
    {
        /** @var User $user */
        $user = $request->has('user_id')
            ? User::find($request->get('user_id'))
            : $request->user();
        return $this->respondIndex($request, $user->gallery_photos()->getQuery(), new MediaTransformer());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return Response
     * @throws Exception
     */
    public function store(StoreRequest $request)
    {
        beginTransaction();
        try {
            /** @var User $user */
            $user = $request->user();

            $media = [];
            foreach ($request->file('photos') as $item) {
                $media[] = $user->addMedia($item)
                    ->toMediaCollection(User::MEDIA_COLLECTION_GALLERY);
            }
            commit();
            return fractal($media, new MediaTransformer())
                ->respond();
        } catch (Exception $e) {
            rollback();
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Media $media
     * @return Response
     */
    public function show(Media $media)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Media $media
     * @return Response
     */
    public function update(Request $request, Media $media)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Media $media
     * @return void
     * @throws Exception
     */
    public function destroy(Media $media)
    {
        $media->delete();
    }

    /**
     * Delete multiple gallery photos
     *
     * @param DeleteMultipleRequest $request
     * @throws AuthorizationException
     */
    public function deleteMultiple(DeleteMultipleRequest $request)
    {
        if (
        Media::whereIn('id', $request->get('id'))
            ->where('model_id', '!=', $request->user()->id)
            ->exists()
        ) {
            throw new AuthorizationException();
        }
        transaction(function () use ($request) {
            Media::whereIn('id', $request->get('id'))
                ->delete();
        });
    }
}
