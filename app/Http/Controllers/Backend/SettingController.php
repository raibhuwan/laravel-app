<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\Contracts\SettingRepository;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private $userRepository;
    private $settingRepository;

    public function __construct(UserRepository $userRepository, SettingRepository $settingRepository)
    {
        $this->userRepository    = $userRepository;
        $this->settingRepository = $settingRepository;

        parent::__construct();
    }

    public function edit($id)
    {
        $user = $this->userRepository->findOneByIdWithTrashed($id);

        if ( ! $user instanceof User) {
            return redirect(route('user.index'))->with('fail', trans('strings.backend.users.edit.user_not_found'));
        }

        $userDetails = $this->editUserDetails($id);

        $settingExists = $this->settingRepository->findOneBy([
            'user_id' => $id
        ]);

        return view('backend.users.settings.edit', ['userDetails' => $userDetails, 'setting' => $settingExists]);
    }

    /**
     * Add or Update Settings
     *
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $validator = $this->validateRequest($request, $this->storeRequestValidationRules());

        if ($validator !== true) {
            return redirect(route('setting.edit', $id))->withErrors($validator)->withInput();
        }

        $settingExists = $this->settingRepository->findOneBy([
            'user_id' => $id
        ]);

        $ages = explode(';', $request->input('show_ages'));

        if ($ages[0] < 18 || $ages[1] > 55) {
            return redirect(route('setting.edit',
                $id))->withErrors(['show_ages' => trans('strings.backend.settings.validation_message.the_age_range_is_invalid')])->withInput();
        } elseif ($ages[0] > $ages[1]) {
            return redirect(route('setting.edit',
                $id))->withErrors(['show_ages' => trans('strings.backend.settings.validation_message.the_minimum_age_cannot_be_greater_than_maximum_age')])->withInput();
        }

        $newInput = [
            'search_distance'       => $request->input('search_distance'),
            'distance_in'           => 'MI',
            'show_ages_min'         => $ages[0],
            'show_ages_max'         => $ages[1],
            'interested_in'         => $request->input('interested_in'),
            'date_with'             => $request->input('date_with'),
            'privacy_show_distance' => $request->has('privacy_show_distance') ? $request->input('privacy_show_distance') : '0',
            'privacy_show_age'      => $request->has('privacy_show_age') ? $request->input('privacy_show_age') : '0',
        ];

        if ($settingExists instanceof Setting) {
            $setting = $this->settingRepository->update($settingExists, $newInput);
        } else {
            $newInput['user_id'] = $id;
            $setting             = $this->settingRepository->save($newInput);
        }

        return redirect(route('user.index'))->with('status',
            trans('strings.backend.settings.edit.settings_has_been_changed_successfully'));
    }

    /**
     * Store Request Validation Rules
     *
     * @param Request $request
     *
     * @return array
     */

    private function storeRequestValidationRules()
    {
        $rules = [
            'search_distance'       => 'required|numeric|min:1|max:100',
            'show_ages'             => 'required',
            'interested_in'         => 'required|in:FRIENDSHIP,RELATIONSHIP,CASUAL_MEETUP',
            'date_with'             => 'required|in:MALE,FEMALE,BOTH',
            'privacy_show_distance' => 'boolean',
            'privacy_show_age'      => 'boolean'
        ];

        return $rules;
    }
}
