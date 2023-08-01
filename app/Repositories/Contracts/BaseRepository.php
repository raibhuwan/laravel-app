<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface BaseRepository
{
    /**
     * Search All resources by criteria
     *
     * @param array $searchCriteria
     *
     * @return Collection
     */
    public function findBy(array $searchCriteria = []);

    /**
     * Find a resource by id
     *
     * @param $id
     *
     * @return Model|null
     */
    public function findOne($id);

    /**
     * Find a resource by user id
     *
     * @param $id
     *
     * @return mixed
     */
    public function findOneById($id);

    /**
     * Find a resource by user id that are already trashed
     *
     * @param $id
     *
     * @return mixed
     */
    public function findOneByIdWithTrashed($id);

    /**
     * Find a resource by email
     *
     * @param $email
     *
     * @return mixed
     */
    public function findOneEmail($email);

    /**
     * Find a resource by criteria
     *
     * @param array $criteria
     *
     * @return mixed
     */
    public function findOneBy(array $criteria);

    /**
     *
     * @param array $criteria
     *
     * @return mixed
     */
    public function findOneByWithTrashed(array $criteria);

    /**
     * Save a resource
     *
     * @param array $data
     *
     * @return Model
     */
    public function save(array $data);

    /**
     *  Find a resource by criteria and counts it
     *
     * @param array $criteria
     *
     * @return mixed
     */
    public function findOneByCount(array $criteria);

    /**
     * Update a resource
     *
     * @param Model $model
     * @param array $data
     *
     * @return mixed
     */
    public function update(Model $model, array $data);

    /**
     * Delete a resource
     *
     * @param Model $model
     *
     * @return mixed
     */
    public function delete(Model $model);

    /**
     * Convert to query
     *
     * @param $builder
     *
     * @return mixed
     */
    public function getSql($builder);
}