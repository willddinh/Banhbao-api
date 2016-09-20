<?php

namespace App\Repositories\Entity;

use App\Services\Cache\CacheInterface;


class CacheDecorator extends AbstractEntityDecorator
{
    /**
     * @var \App\Services\Cache\CacheInterface
     */
    protected $cache;

    /**
     * Cache key.
     *
     * @var string
     */
    protected $cacheKey = 'entity';

    /**
     * @param EntityInterface $entity
     * @param CacheInterface   $cache
     */
    public function __construct(EntityInterface $entity, CacheInterface $cache)
    {
        parent::__construct($entity);
        $this->cache = $cache;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        $key = md5(getLang().$this->cacheKey.'.id.'.$id);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $entity = $this->entity->find($id);

        $this->cache->put($key, $entity);

        return $entity;
    }

    /**
     * @return mixed
     */
    public function all()
    {
        $key = md5(getLang().$this->cacheKey.'.all.entities');

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $entitys = $this->entity->all();

        $this->cache->put($key, $entitys);

        return $entitys;
    }

    /**
     * @param null $page
     * @param bool $all
     *
     * @return mixed
     */
    public function paginate($page = 1, $limit = 10, $all = false)
    {
        $allkey = ($all) ? '.all' : '';
        $key = md5(getLang().$this->cacheKey.'.page.'.$page.'.'.$limit.$allkey);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $paginated = $this->entity->paginate($page, $limit, $all);

        $this->cache->put($key, $paginated);

        return $paginated;
    }

    /**
     * @param $slug
     *
     * @return mixed
     */
    public function getBySlug($slug)
    {
        $key = md5(getLang().$this->cacheKey.'.slug.'.$slug);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $entity = $this->entity->getBySlug($slug);

        $this->cache->put($key, $entity);

        return $entity;
    }

    /**
     * @param $limit
     *
     * @return mixed
     */
    public function getLastEntity($limit)
    {
        $key = md5(getLang().$limit.$this->cacheKey.'.last');

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $entitys = $this->entity->getLastEntity($limit);

        $this->cache->put($key, $entitys);

        return $entitys;
    }
}
