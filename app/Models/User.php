<?php

namespace App\Models;

use App\Traits\AppleLoginTrait;
use App\Traits\FacebookLoginTrait;
use Gerardojbaez\Laraplans\Contracts\PlanSubscriberInterface;
use Gerardojbaez\Laraplans\Traits\PlanSubscriber;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements PlanSubscriberInterface
{
    use HasApiTokens, Notifiable, FacebookLoginTrait, PlanSubscriber, AppleLoginTrait;
    use SoftDeletes;

    const ADMIN_ROLE = 'ADMIN_USER';
    const BASIC_ROLE = 'BASIC_USER';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'uid',
        'name',
        'password',
        'country_code',
        'phone',
        'phone_verified',
        'email',
        'email_verified',
        'gender',
        'dob',
        'role',
        'about_me',
        'school',
        'work',
        'is_active',
        'provider',
        'provider_id',
        'fcm_registration_id',
        'access_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return (isset($this->role) ? $this->role : self::BASIC_ROLE) == self::ADMIN_ROLE;
    }

    public function images()
    {
        return $this->hasMany('App\Models\Image');
    }

    public function setting()
    {
        return $this->hasOne('App\Models\Setting');
    }

    public function location()
    {
        return $this->hasOne('App\Models\Location');
    }

    public function video()
    {
        return $this->hasOne('App\Models\Video');
    }

    public function sound()
    {
        return $this->hasOne('App\Models\Sound');
    }

    public function story()
    {
        return $this->hasMany('App\Models\Story');
    }

    /**
     * Find the user identified by the given $identifier.
     *
     * @param $identifier email|phone
     *
     * @return mixed
     */
    public function findForPassport($identifier)
    {

//        $user =  User::orWhere('email', $identifier)->orWhere('phone', $identifier)->first();

        $found = User::where(DB::raw('CONCAT(country_code,phone)'), $identifier)->first();

        if ($found != null) {
            return $found;
        }

        $found = User::where('email', $identifier)->first();

        if ($found != null) {
            return $found;
        }

        //Search user in trash for temporary deleted users
        $found = User::onlyTrashed()->where(DB::raw('CONCAT(country_code,phone)'), $identifier);

        $tempFound = $found->first();
        if ($tempFound != null) {
            //restore it
            $found->restore();

            return $tempFound;
        }

        $found = User::onlyTrashed()->where('email', $identifier);

        $tempFound = $found->first();
        if ($tempFound != null) {
            //restore it
            $found->restore();

            return $tempFound;
        }

    }

    public function validateForPassportPasswordGrant($password)
    {
        //check for password
        if (Hash::check($password, $this->getAuthPassword())) {
            //is user active?
            if ($this->is_active) {
                return true;
            }
        }
    }

//    public function getIsActiveAttribute($value)
//    {
//
//       if($value == 1){
//           return 'active';
//
//       } else {
//           return 'not active';
//       }
//
//    }

//    protected $appends = ['is_active'];
}
