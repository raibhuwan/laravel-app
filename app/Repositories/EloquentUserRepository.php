<?php

namespace App\Repositories;

use App\Repositories\Contracts\UserRepository;
use App\Repositories\Eloquent\AbstractEloquentRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class EloquentUserRepository extends AbstractEloquentRepository implements UserRepository
{

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [])
    {
        // Only admin user can see all users
        if ($this->getLoggedInUser()->role !== User::ADMIN_ROLE) {
            $searchCriteria['id'] = $this->getLoggedInUser()->id;
        }

        return parent::findBy($searchCriteria);
    }

    public function findByNearBy(array $searchCriteria = [])
    {
        return parent::findBy($searchCriteria);
    }

    /*
 * @inheritdoc
 */
    public function save(array $data)
    {
        // update password
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        if (isset($data['name'])) {
            $data['name'] = title_case($data['name']);
        }

        $user = parent::save($data);

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        if (isset($data['name'])) {
            $data['name'] = title_case($data['name']);
        }

        $updatedUser = parent::update($model, $data);

        return $updatedUser;
    }
}

