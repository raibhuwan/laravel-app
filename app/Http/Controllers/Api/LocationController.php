<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\User;
use App\Repositories\Contracts\LocationRepository;
use App\Repositories\EloquentUserRepository;
use App\Transformers\LocationTransformer;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected $locationRepository;

    protected $locationTransformer;

    protected $eloquentUserRepository;

    /**
     * LocationController constructor.
     *
     * @param LocationRepository $locationRepository
     * @param LocationTransformer $locationTransformer
     */
    public function __construct(LocationRepository $locationRepository, LocationTransformer $locationTransformer)
    {
        $this->locationRepository  = $locationRepository;
        $this->locationTransformer = $locationTransformer;

        $this->eloquentUserRepository = new EloquentUserRepository(new User());

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $setting = $this->locationRepository->findBy($request->all());

        return $this->respondWithCollection($setting, $this->locationTransformer);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show($id)
    {
        $location = $this->locationRepository->findOne($id);

        if ( ! $location instanceof Location) {
            return $this->sendNotFoundResponse("The location with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('show', $location);

        return $this->respondWithItem($location, $this->locationTransformer);
    }

    public function store(Request $request)
    {
        // Validation
        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules());

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $currentUser = $this->getCurrentUserDetails();

        $locationExists = $this->locationRepository->findOneBy([
            'user_id' => $currentUser->id
        ]);

        if ($locationExists instanceof Location) {
            return $this->sendNotFoundResponse("The location for userid: {$currentUser->id} already exists.");
        }

        $location = $this->locationRepository->save($request->all());

        if ( ! $location instanceof Location) {
            return $this->sendCustomResponse(500, 'Error occurred on creating Location.');
        }

        return $this->setStatusCode(201)->respondWithItem($location, $this->locationTransformer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed|string
     */
    public function update(Request $request, $id)
    {
        // Validation

        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules());

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $location = $this->locationRepository->findOne($id);

        if ( ! $location instanceof Location) {
            return $this->sendNotFoundResponse("The location with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('update', $location);

        $location = $this->locationRepository->update($location, $request->all());

        return $this->respondWithItem($location, $this->locationTransformer);

    }

    private function storeRequestValidationRules()
    {
        $rules = [
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric'
        ];

        return $rules;
    }

    /**
     * Get setting of current user.
     *
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function getLocationUser()
    {
        $currentUser = $this->getCurrentUserDetails();

        $location = $currentUser->location()->get()->first();

        if ( ! $location instanceof Location) {
            return $this->sendNotFoundResponse("Location for user id {$currentUser->uid} doesn't exist.");
        }

        return $this->respondWithItem($location, $this->locationTransformer);
    }
}

