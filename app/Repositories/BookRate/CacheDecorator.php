<?php

namespace App\Repositories\BookRate;

use App\Services\Cache\CacheInterface;

/**
 * Class CacheDecorator.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
class CacheDecorator extends AbstractBookRateDecorator
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
    protected $cacheKey = 'bookRate';

    /**
     * @param BookRateInterface   $bookRate
     * @param CacheInterface $cache
     */
    public function __construct(BookRateInterface $bookRate, CacheInterface $cache)
    {
        parent::__construct($bookRate);
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

        $bookRate = $this->bookRate->find($id);

        $this->cache->put($key, $bookRate);

        return $bookRate;
    }

    /**
     * @return mixed
     */
    public function all()
    {
        $key = md5(getLang().$this->cacheKey.'.all.authors');

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $bookRates = $this->bookRate->all();

        $this->cache->put($key, $bookRates);

        return $bookRates;
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

        $paginated = $this->bookRate->paginate($page, $limit, $all);

        $this->cache->put($key, $paginated);

        return $paginated;
    }

   
}
