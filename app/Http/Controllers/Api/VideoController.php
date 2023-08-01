<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\VideoRepository;
use App\Transformers\VideoTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class VideoController extends Controller
{

    /**
     * @var VideoRepository
     */
    protected $videoRepository;

    /**
     * @var VideoTransformer
     */
    private $videoTransformer;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        UserRepository $userRepository,
        VideoRepository $videoRepository,
        VideoTransformer $videoTransformer
    ) {
        $this->videoRepository  = $videoRepository;
        $this->videoTransformer = $videoTransformer;
        $this->userRepository   = $userRepository;

        parent::__construct();
    }

    /**
     * Display all the videos
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $videos = $this->videoRepository->findBy($request->all());

        return $this->respondWithCollection($videos, $this->videoTransformer);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules());

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $currentUser   = $this->getCurrentUserDetails();
        $videoName     = $this->getNewFileName($request->file('video'));
        $videoPath     = $this->getVideoPath($currentUser->uid);
        $input['name'] = $videoName;
        $input['path'] = $videoPath;

        $videoExists = $this->videoRepository->findOneBy([
            'user_id' => $currentUser->id
        ]);

        if ($videoExists instanceof Video) {
            return $this->sendNotFoundResponse("The video for userid: {$currentUser->uid} already exists.");
        }

        $video = $this->videoRepository->save($input);

        if ( ! $video instanceof Video) {
            return $this->sendCustomResponse(500, 'Error occurred on creating Video');
        }

        Storage::putFileAs('public/' . $videoPath, $request->file('video'), $videoName);

        return $this->setStatusCode(201)->respondWithItem($video, $this->videoTransformer);
    }

    /**
     *  Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed|string
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, $id)
    {
        // Validation

        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules());

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $videoExists = $this->videoRepository->findOne($id);

        if ( ! $videoExists instanceof Video) {
            return $this->sendNotFoundResponse("The video with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('update', $videoExists);

        $oldVideoName = $videoExists->name;

        $currentUser   = $this->getCurrentUserDetails();
        $videoName     = $this->getNewFileName($request->file('video'));
        $videoPath     = $this->getVideoPath($currentUser->uid);
        $input['name'] = $videoName;
        $input['path'] = $videoPath;

        $video = $this->videoRepository->update($videoExists, $input);

        // Delete old image
        Storage::delete('public/' . $videoPath . $oldVideoName);

        Storage::putFileAs('public/' . $videoPath, $request->file('video'), $videoName);

        return $this->respondWithItem($video, $this->videoTransformer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|string
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $video = $this->videoRepository->findOne($id);

        if ( ! $video instanceof Video) {
            return $this->sendNotFoundResponse("The video with id {$id} doesn't exist");
        }
        // Authorization
        $this->authorize('destroy', $video);

        $this->videoRepository->delete($video);

        // Delete old image
        Storage::delete('public/' . $video->path . $video->name);

        return $this->sendCustomResponse("200", "The video with id {$id} has been removed.");
    }

    /**
     * Store Request Validation Rules
     *
     * @return array
     */

    private function storeRequestValidationRules()
    {

        $rules = [
            'video' => 'required|mimetypes:video/mp4|max:10000'
        ];

        return $rules;
    }


    /**
     * Generate new filename according to timestamp
     *
     * @param $type
     *
     * @return bool
     */
    public function getNewFileName($video)
    {
        $file_name = 'video_' . time() . '.' . $video->getClientOriginalExtension();

        return $file_name;
    }

    /**
     * Define a path using the user id
     *
     * @param $user_uid
     *
     * @return string
     */
    public function getVideoPath($user_uid)
    {
        $videoPath = 'users/user_' . $user_uid . '/videos/';

        return $videoPath;
    }

    /**
     * Get setting of current user.
     *
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function getVideoUser()
    {
        $currentUser = $this->getCurrentUserDetails();
        $video       = $currentUser->video()->get()->first();

        if ( ! $video instanceof Video) {
            return $this->sendNotFoundResponse("Video for user id {$currentUser->uid} doesn't exist.");
        }

        return $this->respondWithItem($video, $this->videoTransformer);
    }

    /**
     * Display the specified resource using ID.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|string
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id)
    {
        $video = $this->videoRepository->findOne($id);

        if ( ! $video instanceof Video) {
            return $this->sendNotFoundResponse("The video with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('show', $video);

        return $this->respondWithItem($video, $this->videoTransformer);
    }

}
