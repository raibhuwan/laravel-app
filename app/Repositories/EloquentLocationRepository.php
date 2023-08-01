<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\LocationRepository;
use App\Repositories\Eloquent\AbstractEloquentRepository;

class EloquentLocationRepository extends AbstractEloquentRepository implements LocationRepository
{
    /*
 * @inheritdoc
 */
    public function save(array $data)
    {

        if ( ! isset($data['user_id'])) {
            $data['user_id'] = $this->getLoggedInUser()->id;;
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