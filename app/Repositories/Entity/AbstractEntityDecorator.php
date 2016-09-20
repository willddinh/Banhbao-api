<?php

namespace App\Repositories\Entity;

abstract class AbstractEntityDecorator implements EntityInterface
{
    /**
     * @var EntityInterface
     */
    protected $entity;

    /**
     * @param EntityInterface $entity
     */
    public function __construct(EntityInterface $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->entity->find($id);
    }

    /**
     * @param $slug
     *
     * @return mixed
     */
    public function getBySlug($slug)
    {
        return $this->entity->getBySlug($slug);
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->entity->all();
    }

    /**
     * @param null $perPage
     * @param bool $all
     *
     * @return mixed
     */
    public function paginate($page = 1, $limit = 10, $all = false)
    {
        return $this->entity->paginate($page, $limit, $all);
    }
}
