<?php

namespace App\Http\Controllers\Backend;

use App\Events\SetUserSessionEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\Contracts\UserRepository;
use App\Transformers\UserTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\Datatables\Datatables;


class UserController extends Controller
{
    private $userRepository;
    private $userTransformer;

    public function __construct(UserRepository $userRepository, UserTransformer $userTransformer)
    {
        $this->userRepository  = $userRepository;
        $this->userTransformer = $userTransformer;
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = $this->userRepository->findBy();

        return view('backend.users.index', ['users' => $users, 'adminPrefix' => config('backend.admin_backend_url')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $select = $this->getCountryList();

        return view('backend.users.create', ['select' => $select]);
    }

    public function getCountryList()
    {
        $contents     = storage_path() . '/' . config('storage.country_code_file_name');
        $countryCodes = json_decode(file_get_contents($contents), true);
        $select       = [];

        foreach ($countryCodes as $countryCode) {

            $code          = str_replace_first('-', '', $countryCode['country_code']);
            $code          = str_start($code, '+');
            $select[$code] = $countryCode['country_name'] . '   ' . '(+' . $countryCode['country_code'] . ') ';
        }

        return $select;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $currentUser = $this->getCurrentUserDetails();
        // Validation
        $validator = $this->validateRequest($request, $this->storeRequestValidationRules($request, $currentUser));
        if ($validator !== true) {
            return redirect(route('user.create'))->withErrors($validator)->withInput();
        }

        $request->request->add(['phone_verified' => 1]);
        $request->request->add(['is_active' => 1]);
        $user = $this->userRepository->save($request->all());


        return redirect(route('user.index'))->with('status',
            trans('strings.backend.users.create.user_has_been_created_successfully'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $select = $this->getCountryList();

        $user = DB::table('users')->where('users.id', '=', $id)->leftjoin('images', function ($join) {
            $join->on('users.id', '=', 'images.user_id')->where('images.number', '=', 1);
        })->select('users.*', 'images.name as image_name', 'images.path as image_path', 'images.number as image_number',
            'images.link as image_link')->first();

        if ( ! $user) {
            return redirect(route('user.index'))->with('fail', trans('strings.backend.users.edit.user_not_found'));
        }

        $userDetails = $this->editUserDetails($id);

        return view('backend.users.edit', ['userDetails' => $userDetails, 'user' => $user, 'select' => $select]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $currentUser = $this->getCurrentUserDetails();
        // Validation
        $validator = $this->validateRequest($request, $this->updateRequestValidationRules($request, $currentUser, $id));

        if ($validator !== true) {
            return redirect(route('user.edit', $id))->withErrors($validator)->withInput();
        }

        if ( ! $request->input('password')) {
            $request->request->remove('password');
        }

        $user = $this->userRepository->findOneById($id);

        if ( ! $user instanceof User) {
            return redirect(route('user.index'))->with('fail', trans('strings.backend.users.edit.user_not_found'));
        }

        $user = $this->userRepository->update($user, $request->all());

        // fire user created event
        event(new SetUserSessionEvent($user));

        return redirect(route('user.index'))->with('status',
            trans('strings.backend.users.edit.user_has_been_edited_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $id
     *
     * @return false|string
     */
    public function destroy(Request $request, $id)
    {
        $user = $this->userRepository->findOneById($id);

        if ($request->input('delType') == 'SOFT') {
            $status   = $user->delete();
            $jsonData = [
                'status'  => $status,
                'message' => trans('strings.backend.users.delete.user_soft_delete')
            ];

            return json_encode($jsonData);
        }

        $userDirectoryPath = 'users/user_' . $user->uid;

        if (file_exists(storage_path('/app/public/' . $userDirectoryPath))) {
            $delstatus = Storage::deleteDirectory('public/' . $userDirectoryPath);

            //check for the delete success
            if ($delstatus) {
                $status   = $user->forcedelete();
                $jsonData = [
                    'status'  => $status,
                    'message' => trans('strings.backend.users.delete.user_hard_delete')
                ];

                return json_encode($jsonData);
            }
        } else {
            $status   = $user->forcedelete();
            $jsonData = [
                'status'  => $status,
                'message' => trans('strings.backend.users.delete.user_hard_delete')
            ];

            return json_encode($jsonData);
        }
    }

    public function readAjax(Datatables $datatables)
    {
        return $datatables->eloquent(User::query())->filterColumn('is_active', function ($query, $keyword) {
            $sql = "IF(is_active = 1, 'active', 'not-active') like ?";
            $query->whereRaw($sql, ["{$keyword}%"]);
        })->toJson();
    }


    public function subscriptionList($id)
    {
        $user = $this->userRepository->findOneByIdWithTrashed($id);

        if ( ! $user instanceof User) {
            return redirect(route('user.index'))->with('fail', trans('strings.backend.users.edit.user_not_found'));
        }

        $userDetails = $this->editUserDetails($id);

        return view('backend.users.subscriptions.index', ['userDetails' => $userDetails]);
    }

    /**
     * Store Request Validation Rules
     *
     * @param Request $request
     *
     * @return array
     */
    private function storeRequestValidationRules(Request $request, $currentUser)
    {
        $rules = [
            'name'         => 'required|max:100',
            'country_code' => 'required|numeric',
            'phone'        => 'required|numeric|uniquePhoneField:' . $request->input('country_code'),
            'password'     => 'required|min:8|caseDiff|numbers',
            'email'        => 'email|unique:users,email',
            'gender'       => 'required|in:MALE,FEMALE',
            'dob'          => 'required|date|before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d'),
            // Extra
            'about_me'     => 'max:255',
            'school'       => 'max:100',
            'work'         => 'max:255',
            'is_active'    => 'required|boolean:true,false'

        ];
        if ($currentUser instanceof User && $currentUser->role === User::ADMIN_ROLE) {
            $rules['role'] = 'required|in:BASIC_USER,ADMIN_USER';
        } else {
            $rules['role'] = 'required|in:BASIC_USER';
        }

        return $rules;
    }


    /**
     * Update Request validation Rules
     *
     * @param Request $request
     *
     * @return array
     */
    private function updateRequestValidationRules(Request $request, $currentUser, $id)
    {

        $rules = [
//            'phone'    => Rule::unique('users')->ignore($id, 'uid'),
            'name'         => 'required|max:100',
            'country_code' => 'required|numeric',
            'phone'        => 'required|numeric|uniquePhoneUpdateField:' . $request->input('country_code') . ',' . $id,
            'password'     => 'required|min:8|caseDiff|numbers',
//            'password'     => 'nullable|min:8|caseDiff|numbers', // Will be nullable later
            'email'        => 'required|email|' . Rule::unique('users')->ignore($id),
//            'email'        => 'nullable|email|'.Rule::unique('users')->ignore($id),  // Will be nullable later
            'gender'       => 'required|in:MALE,FEMALE',
            'dob'          => 'required|date|before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d'),
            // Extra
            'about_me'     => 'max:255',
            'school'       => 'max:100',
            'work'         => 'max:255',
            'is_active'    => 'required|boolean:true,false'
        ];

        // Only admin user can set admin role.
        if ($currentUser instanceof User && $currentUser->role === User::ADMIN_ROLE) {
            $rules['role'] = 'in:BASIC_USER,ADMIN_USER';
        } else {
            $rules['role'] = 'in:BASIC_USER';
        }

        return $rules;
    }
}
