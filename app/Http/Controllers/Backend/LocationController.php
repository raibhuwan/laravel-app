<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\User;
use App\Repositories\Contracts\LocationRepository;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    private $userRepository;
    private $locationRepository;

    public function __construct(UserRepository $userRepository, LocationRepository $locationRepository)
    {
        $this->userRepository = $userRepository;
        $this->locationRepository = $locationRepository;

        parent::__construct();
    }

    public function edit($id)
    {
        $user = $this->userRepository->findOneByIdWithTrashed($id);

        if (!$user instanceof User) {
            return redirect(route('user.index'))->with('fail', trans('strings.backend.users.edit.user_not_found'));
        }

        $userDetails = $this->editUserDetails($id);

        $locationExists = $this->locationRepository->findOneBy([
            'user_id' => $id
        ]);

        return view('backend.users.locations.edit', ['userDetails' => $userDetails, 'location' => $locationExists]);
    }

    public function update(Request $request, $id)
    {
        // Validation
        $validatorResponse = $this->validateRequest($request, [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        if ($validatorResponse !== true) {
            return redirect(route('location.edit', $id))->withErrors($validatorResponse)->withInput();
        }

        $location = $this->locationRepository->findOneBy(
            ['user_id' => $id]
        );

        $input = [
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude')
        ];

        if (!$location instanceof Location) {
            $input['user_id'] = $id;
            $location = $this->locationRepository->save($input);
        } else {
            $location = $this->locationRepository->update($location, $input);
        }

        return redirect(route('user.index'))->with('status',
            trans('strings.backend.locations.edit.location_has_been_changed_successfully'));
    }
}
