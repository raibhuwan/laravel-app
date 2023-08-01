<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Ramsey\Uuid\Uuid;

abstract class AbstractEloquentRepository implements BaseRepository
{

    /**
     * @var
     */
    protected $model;

    /**
     * get logged in user
     *
     * @var User $loggedInUser
     */
    protected $loggedInUser;

    /**
     * Constructor
     */
    public function __construct(Model $model)
    {
        $this->model        = $model;
        $this->loggedInUser = $this->getLoggedInUser();
    }

    /**
     * Find by search criteria
     *
     * @param array $searchCriteria
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function findBy(array $searchCriteria = [])
    {

        $limit = ! empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15; // it's needed for pagination

        $queryBuilder = $this->model->where(function ($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        return $queryBuilder->paginate($limit);
    }

    /**
     * Apply condition on query builder based on search criteria
     *
     * @param Object $queryBuilder
     * @param array $searchCriteria
     *
     * @return mixed
     */
    protected function applySearchCriteriaInQueryBuilder($queryBuilder, array $searchCriteria = [])
    {
        foreach ($searchCriteria as $key => $value) {

            //skip pagination related query params
            if (in_array($key, ['page', 'per_page'])) {
                continue;
            }

            //we can pass multiple params for a filter with commas
            $allValues = explode(',', $value);
            if (count($allValues) > 1) {
                $queryBuilder->whereIn($key, $allValues);
            } else {
                $operator = '=';
                $queryBuilder->where($key, $operator, $value);
            }
        }

        return $queryBuilder;
    }

    /**
     * get loggedIn user
     *
     * @return User
     */
    protected function getLoggedInUser()
    {
        $user = \Auth::user();

        if ($user instanceof User) {
            return $user;
        } else {
            return new User();
        }
    }

    /**
     * @inheritdoc
     */
    public function save(array $data)
    {
        // generate uid
        $data['uid'] = Uuid::uuid4();

        return $this->model->create($data);
    }

    /**
     * @inheritdoc
     */
    public function findOne($id)
    {
        return $this->findOneBy(['uid' => $id]);
    }

    /**
     * @inheritdoc
     */
    public function findOneById($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public function findOneByIdWithTrashed($id)
    {
        return $this->findOneByWithTrashed(['id' => $id]);
    }

    public function findOneEmail($email)
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * @inheritdoc
     */
    public function findOneBy(array $criteria)
    {
        return $this->model->where($criteria)->first();
    }

    /**
     * @inheritdoc
     */
    public function findOneByWithTrashed(array $criteria)
    {
        return $this->model->where($criteria)->withTrashed()->first();
    }

    /**
     * @inheritdoc
     */
    public function findOneByCount(array $criteria)
    {
        return $this->model->where($criteria)->count();
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        $fillAbleProperties = $this->model->getFillable();

        foreach ($data as $key => $value) {

            // update only fillAble properties
            if (in_array($key, $fillAbleProperties)) {
                $model->$key = $value;
            }
        }

        // update the model
        $model->save();

        // get updated model from database
        $model = $this->findOne($model->uid);

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function delete(Model $model)
    {
        return $model->delete();
    }

    public function getSql($builder)
    {
        $sql = $builder->toSql();
        foreach ($builder->getBindings() as $binding) {
            $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
            $sql   = preg_replace('/\?/', $value, $sql, 1);
        }

        return $sql;
    }
}