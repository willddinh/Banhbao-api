<?php

namespace App\Repositories\TreeCategory;

use App\Services\Cache\CacheInterface;

/**
 * Class CacheDecorator.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
class CacheDecorator extends AbstractTreeCategoryDecorator
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
    protected $cacheKey = 'tree_category';

    /**
     * @param TreeCategoryInterface $category
     * @param CacheInterface    $cache
     */
    public function __construct(TreeCategoryInterface $category, CacheInterface $cache)
    {
        parent::__construct($category);
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

        $category = $this->category->find($id);

        $this->cache->put($key, $category);

        return $category;
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

        $categories = $this->category->all();

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

        $paginated = $this->category->paginate($page, $limit, $all);
        $this->cache->put($key, $paginated);

        return $paginated;
    }

    /**
     * @param $slug
     *
     * @return mixed
     */
    public function getArticlesBySlug($slug)
    {
        return $this->category->getArticlesBySlug($slug);
    }

    public function findByGroup($group)
    {
        return $this->category->findByGroupForDropDown($group);
    }

    public function findByGroupForDropDown($group)
    {
        return $this->category->findByGroupForDropDown($group);
    }

    public function getCategoryAsKendoDataSource($code)
    {
        return $this->category->getCategoryAsKendoDataSource($code);
    }
}
