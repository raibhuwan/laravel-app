<?php

namespace App\Repositories;

use App\Repositories\Contracts\SettingRepository;
use App\Repositories\Eloquent\AbstractEloquentRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;


class EloquentSettingRepository extends AbstractEloquentRepository implements SettingRepository
{

    /*
 * @inheritdoc
 */
    public function save(array $data)
    {
        $currentUser = $this->getLoggedInUser();
        if ( ! isset($data['user_id'])) {
            $data['user_id'] = $currentUser->id;;
        }

        $user = parent::save($data);

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [])
    {
        // Only admin user can see all users
        if ($this->getLoggedInUser()->role !== User::ADMIN_ROLE) {

            $searchCriteria['user_id'] = $this->getLoggedInUser()->id;
        }

        return parent::findBy($searchCriteria);
    }
}