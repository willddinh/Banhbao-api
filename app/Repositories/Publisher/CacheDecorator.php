<?php

namespace App\Repositories\Publisher;

use App\Services\Cache\CacheInterface;

/**
 * Class CacheDecorator.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
class CacheDecorator extends AbstractPublisherDecorator
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
    protected $cacheKey = 'publisher';

    /**
     * @param PublisherInterface   $publisher
     * @param CacheInterface $cache
     */
    public function __construct(PublisherInterface $publisher, CacheInterface $cache)
    {
        parent::__construct($publisher);
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

        $publisher = $this->publisher->find($id);

        $this->cache->put($key, $publisher);

        return $publisher;
    }

    /**
     * @return mixed
     */
    public function all()
    {
        $key = md5(getLang().$this->cacheKey.'.all.publishers');

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $publishers = $this->publisher->all();

        $this->cache->put($key, $publishers);

        return $publishers;
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

        $paginated = $this->publisher->paginate($page, $limit, $all);

        $this->cache->put($key, $paginated);

        return $paginated;
    }

   
}
