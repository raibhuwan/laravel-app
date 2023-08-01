<?php

namespace App\Http\Controllers;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\Fractal\Manager;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use ResponseTrait;

    /**
     * Constructor
     *
     * @param Manager|null $fractal
     */
    public function __construct(Manager $fractal = null)
    {
        $fractal = $fractal === null ? new Manager() : $fractal;
        $this->setFractal($fractal);
    }

    /**
     * Validate HTTP request against the rules
     *
     * @param Request $request
     * @param array $rules
     *
     * @return bool|array
     */
    protected function validateRequest(Request $request, array $rules)
    {
        // Perform Validation
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->messages();

            // crete error message by using key and value
            foreach ($errorMessages as $key => $value) {
                $errorMessages[$key] = $value[0];
            }

            return $errorMessages;
        }

        return true;
    }

    /**
     * Get current user details
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected function getCurrentUserDetails()
    {
        $currentUser = Auth::user();

        return $currentUser;
    }


    /**
     * @param Builder $builder
     *
     * @return string
     */
    public function getSql($builder)
    {
        $sql = $builder->toSql();
        foreach ($builder->getBindings() as $binding) {
            $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
            $sql   = preg_replace('/\?/', $value, $sql, 1);
        }

        return $sql;
    }

    /**
     * Receive minimum details while editing users, images , settings
     *
     * @param $id
     *
     * @return mixed
     */
    public function editUserDetails($id)
    {
        $userDetails = DB::table('users')->where('users.id', '=', $id)->leftjoin('images', function ($join) {
            $join->on('users.id', '=', 'images.user_id')->where('images.number', '=', 1);
        })->select('users.id as user_detail_id', 'users.name as user_detail_name', 'users.email as user_detail_email',
            'users.gender as user_detail_gender', 'images.id as image_detail_id', 'images.name as image_detail_name',
            'images.path as image_detail_path', 'images.number as image_detail_number',
            'images.link as image_detail_link')->first();


        return $userDetails;
    }

}
