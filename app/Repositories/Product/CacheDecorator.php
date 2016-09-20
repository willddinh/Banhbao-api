<?php

namespace App\Repositories\Product;

use App\Services\Cache\CacheInterface;

/**
 * Class CacheDecorator.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
class CacheDecorator extends AbstractProductDecorator
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
    protected $cacheKey = 'product';

    /**
     * @param ProductInterface $product
     * @param CacheInterface   $cache
     */
    public function __construct(ProductInterface $product, CacheInterface $cache)
    {
        parent::__construct($product);
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

        $product = $this->product->find($id);

        $this->cache->put($key, $product);

        return $product;
    }

    /**
     * @return mixed
     */
    public function all()
    {
        $key = md5(getLang().$this->cacheKey.'.all.products');

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $products = $this->product->all();

        $this->cache->put($key, $products);

        return $products;
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

        $paginated = $this->product->paginate($page, $limit, $all);

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

        $product = $this->product->getBySlug($slug);

        $this->cache->put($key, $product);

        return $product;
    }

    /**
     * @param $limit
     *
     * @return mixed
     */
    public function getLastProduct($limit)
    {
        $key = md5(getLang().$limit.$this->cacheKey.'.last');

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $products = $this->product->getLastProduct($limit);

        $this->cache->put($key, $products);

        return $products;
    }
}
