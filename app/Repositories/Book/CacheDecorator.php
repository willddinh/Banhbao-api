<?php

namespace App\Repositories\Book;

use App\Services\Cache\CacheInterface;

/**
 * Class CacheDecorator.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
class CacheDecorator extends AbstractBookDecorator
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
    protected $cacheKey = 'book';

    /**
     * @param BookInterface $book
     * @param CacheInterface   $cache
     */
    public function __construct(BookInterface $book, CacheInterface $cache)
    {
        parent::__construct($book);
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

        $book = $this->book->find($id);

        $this->cache->put($key, $book);

        return $book;
    }

    /**
     * @return mixed
     */
    public function all()
    {
        $key = md5(getLang().$this->cacheKey.'.all.books');

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $books = $this->book->all();

        $this->cache->put($key, $books);

        return $books;
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

        $paginated = $this->book->paginate($page, $limit, $all);

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

        $book = $this->book->getBySlug($slug);

        $this->cache->put($key, $book);

        return $book;
    }

    /**
     * @param $limit
     *
     * @return mixed
     */
    public function getLastBook($limit)
    {
        $key = md5(getLang().$limit.$this->cacheKey.'.last');

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $books = $this->book->getLastBook($limit);

        $this->cache->put($key, $books);

        return $books;
    }

    public function getBySubCat($subCatId)
    {
       return $this->book->getBySubCat($subCatId);
    }

    public function listBook($publisherId, $categoryId, $subCategoriesIdArr, $priceOrder,$page)
    {
        return $this->book->listBook($publisherId, $categoryId, $subCategoriesIdArr, $priceOrder, $page);
    }

    public function getBookById($id)
    {
        return $this->book->getBookById($id);
    }
}
