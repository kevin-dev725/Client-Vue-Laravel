<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\License\StoreRequest;
use App\Http\Requests\Api\License\UpdateRequest;
use App\License;
use App\Media;
use App\Transformers\LicenseTransformer;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LicenseController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(License::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $query = License::query()
            ->where('user_id', $request->get('user_id', $request->user()->id));
        return $this->respondIndex($request, $query, new LicenseTransformer());
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
            /** @var License $license */
            $license = $request->user()
                ->licenses()
                ->create($request->only('name', 'number', 'expiration', 'is_insured'));
            if ($request->has('photos')) {
                foreach ($request->all('photos')['photos'] as $key => $item) {
                    $photo = $request->file("photos.$key.photo");
                    $license->addMedia($photo)
                        ->toMediaCollection(License::MEDIA_COLLECTION_PHOTOS);
                }
            }
            if ($request->has('certs')) {
                foreach ($request->all('certs')['certs'] as $key => $item) {
                    $photo = $request->file("certs.$key.photo");
                    $license->addMedia($photo)
                        ->toMediaCollection(License::MEDIA_COLLECTION_CERTS);
                }
            }
            commit();
            return fractal($license, new LicenseTransformer())
                ->respond();
        } catch (Exception $e) {
            rollback();
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param License $license
     * @return Response
     */
    public function show(License $license)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param License $license
     * @return Response
     */
    public function update(UpdateRequest $request, License $license)
    {
        transaction(function () use ($request, $license) {
            $license->update($request->only('name', 'number', 'expiration', 'is_insured'));
            if ($request->has('photos')) {
                $ids = [];
                foreach ($request->all('photos')['photos'] as $key => $item) {
                    /** @var Media $media */
                    $media = Media::find(array_get($item, 'id'));
                    $photo = $request->file("photos.$key.photo");
                    if ($photo) {
                        if ($media) {
                            $media->updateFile($photo);
                        } else {
                            $media = $license->addMedia($photo)
                                ->toMediaCollection(License::MEDIA_COLLECTION_PHOTOS);
                        }
                    }
                    $ids[] = $media->id;
                }
                /** @var Collection|Media[] $deleted_photos */
                $deleted_photos = $license->photos()->whereNotIn('id', $ids)->get();
                foreach ($deleted_photos as $item) {
                    $item->delete();
                }
            }
            if ($request->has('certs')) {
                $ids = [];
                foreach ($request->all('certs')['certs'] as $key => $item) {
                    /** @var Media $media */
                    $media = Media::find(array_get($item, 'id'));
                    $photo = $request->file("certs.$key.photo");
                    if ($photo) {
                        if ($media) {
                            $media->updateFile($photo);
                        } else {
                            $media = $license->addMedia($photo)
                                ->toMediaCollection(License::MEDIA_COLLECTION_CERTS);
                        }
                    }
                    $ids[] = $media->id;
                }
                /** @var Collection|Media[] $deleted_certs */
                $deleted_certs = $license->certs()->whereNotIn('id', $ids)->get();
                foreach ($deleted_certs as $item) {
                    $item->delete();
                }
            }
            $license->refresh();
            if ($request->get('clear_photos')) {
                foreach ($license->photos as $photo) {
                    $photo->delete();
                }
            }
            if ($request->get('clear_certs')) {
                foreach ($license->certs as $cert) {
                    $cert->delete();
                }
            }
        });
        return fractal($license->refresh(), new LicenseTransformer())
            ->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param License $license
     * @return void
     */
    public function destroy(License $license)
    {
        transaction(function () use ($license) {
            $license->delete();
        });
    }
}
