<?php

namespace App\Repositories\SubCategory;

use App\Services\Cache\CacheInterface;

/**
 * Class CacheDecorator.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
class CacheDecorator extends AbstractSubCategoryDecorator
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
    protected $cacheKey = 'subCategory';

    /**
     * @param SubCategoryInterface $subCategory
     * @param CacheInterface    $cache
     */
    public function __construct(SubCategoryInterface $subCategory, CacheInterface $cache)
    {
        parent::__construct($subCategory);
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

        $subCategory = $this->subCategory->find($id);

        $this->cache->put($key, $subCategory);

        return $subCategory;
    }

    /**
     * @return mixed
     */
    public function all()
    {
        $key = md5(getLang().$this->cacheKey.'.all.categories');

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $categories = $this->subCategory->all();

        $this->cache->put($key, $categories);

        return $categories;
    }

    /**
     * @param int  $page
     * @param int  $limit
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

        $paginated = $this->subCategory->paginate($page, $limit, $all);
        $this->cache->put($key, $paginated);

        return $paginated;
    }

   

    public function findByGroup($group)
    {
        return $this->subCategory->findByGroupForDropDown($group);
    }

    public function findByGroupForDropDown($group)
    {
        return $this->subCategory->findByGroupForDropDown($group);
    }
}
