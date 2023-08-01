<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sound;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\SoundRepository;
use App\Transformers\SoundTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class SoundController extends Controller
{

    /**
     * @var SoundRepository
     */
    protected $soundRepository;

    /**
     * @var SoundTransformer
     */
    private $soundTransformer;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        UserRepository $userRepository,
        SoundRepository $soundRepository,
        SoundTransformer $soundTransformer
    ) {
        $this->soundRepository  = $soundRepository;
        $this->soundTransformer = $soundTransformer;
        $this->userRepository   = $userRepository;

        parent::__construct();
    }

    /**
     * Display all the sounds
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $sounds = $this->soundRepository->findBy($request->all());

        return $this->respondWithCollection($sounds, $this->soundTransformer);
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
        $soundName     = $this->getNewFileName($request->file('sound'));
        $soundPath     = $this->getSoundPath($currentUser->uid);
        $input['name'] = $soundName;
        $input['path'] = $soundPath;

        $soundExists = $this->soundRepository->findOneBy([
            'user_id' => $currentUser->id
        ]);

        if ($soundExists instanceof Sound) {
            return $this->sendNotFoundResponse("The sound for userid: {$currentUser->uid} already exists.");
        }

        $sound = $this->soundRepository->save($input);

        if ( ! $sound instanceof Sound) {
            return $this->sendCustomResponse(500, 'Error occurred on creating Sound');
        }

        Storage::putFileAs('public/' . $soundPath, $request->file('sound'), $soundName);

        return $this->setStatusCode(201)->respondWithItem($sound, $this->soundTransformer);
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

        $soundExists = $this->soundRepository->findOne($id);

        if ( ! $soundExists instanceof Sound) {
            return $this->sendNotFoundResponse("The sound with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('update', $soundExists);

        $oldSoundName = $soundExists->name;

        $currentUser   = $this->getCurrentUserDetails();
        $soundName     = $this->getNewFileName($request->file('sound'));
        $soundPath     = $this->getSoundPath($currentUser->uid);
        $input['name'] = $soundName;
        $input['path'] = $soundPath;

        $sound = $this->soundRepository->update($soundExists, $input);

        // Delete old image
        Storage::delete('public/' . $soundPath . $oldSoundName);

        Storage::putFileAs('public/' . $soundPath, $request->file('sound'), $soundName);

        return $this->respondWithItem($sound, $this->soundTransformer);
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
        $sound = $this->soundRepository->findOne($id);

        if ( ! $sound instanceof Sound) {
            return $this->sendNotFoundResponse("The sound with id {$id} doesn't exist");
        }
        // Authorization
        $this->authorize('destroy', $sound);

        $this->soundRepository->delete($sound);

        // Delete old image
        Storage::delete('public/' . $sound->path . $sound->name);

        return $this->sendCustomResponse("200", "The sound with id {$id} has been removed.");
    }

    /**
     * Store Request Validation Rules
     *
     * @return array
     */

    private function storeRequestValidationRules()
    {

        $rules = [
            'sound' => 'required|mimetypes:application/octet-stream,audio/x-m4a|max:5000'
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
    public function getNewFileName($sound)
    {
        $file_name = 'sound_' . time() . '.' . $sound->getClientOriginalExtension();

        return $file_name;
    }

    /**
     * Define a path using the user id
     *
     * @param $user_uid
     *
     * @return string
     */
    public function getSoundPath($user_uid)
    {
        $soundPath = 'users/user_' . $user_uid . '/sounds/';

        return $soundPath;
    }

    /**
     * Get setting of current user.
     *
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function getSoundUser()
    {
        $currentUser = $this->getCurrentUserDetails();
        $sound       = $currentUser->sound()->get()->first();

        if ( ! $sound instanceof Sound) {
            return $this->sendNotFoundResponse("Sound for user id {$currentUser->uid} doesn't exist.");
        }

        return $this->respondWithItem($sound, $this->soundTransformer);
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
        $sound = $this->soundRepository->findOne($id);

        if ( ! $sound instanceof Sound) {
            return $this->sendNotFoundResponse("The sound with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('show', $sound);

        return $this->respondWithItem($sound, $this->soundTransformer);
    }

}
