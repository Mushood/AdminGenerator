<?php

namespace App\Http\Controllers;

use App\Http\Requests\MediaValidator;
use App\Models\Media;
use App\Transformers\MediaTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    const PAGINATION = 12;

    /**
     * @var MediaTransformer
     */
    protected $transformer;

    /**
     * MediaController constructor.
     * @param MediaTransformer $transformer
     */
    public function __construct(MediaTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $medias = Media::paginate(self::PAGINATION);
        $medias = fractal($medias, $this->transformer);

        return response()->json(
            $medias, Response::HTTP_OK
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MediaValidator $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MediaValidator $request)
    {
        $mediaDetails   = Media::storeFile($request->file);
        $media          = Media::create($mediaDetails);
        $media          = fractal($media, $this->transformer);

        return response()->json(
            $media, Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Media $media
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Media $media)
    {
        $media = fractal($media, $this->transformer);

        return response()->json(
            $media, Response::HTTP_OK
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Media $media
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Media $media)
    {
        $media = fractal($media, $this->transformer);

        return response()->json(
            $media, Response::HTTP_OK
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MediaValidator $request
     * @param Media $media
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MediaValidator $request, Media $media)
    {
        $validatedData = $request->validated();
        $validatedData = $this->transformer->conform($validatedData);

        $media->update($validatedData);

        $media = fractal($media, $this->transformer);

        return response()->json(
            $media, Response::HTTP_OK
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Media $media
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Media $media)
    {
        try {
            $media->delete();

            Storage::disk('public')->delete(str_replace("/storage/","",$media->path));

        } catch ( \Exception $e) {

            return response()->json(
                [
                    'errors'    => [
                        'message' => 'Cannot delete Media. Is it being used?'
                    ],
                ],
                Response::HTTP_OK
            );
        }

        return response()->json(
            null, Response::HTTP_NO_CONTENT
        );
    }
}
