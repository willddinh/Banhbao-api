<?php

namespace App\Repositories\Book;

/**
 * Class AbstractBookDecorator.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
abstract class AbstractBookDecorator implements BookInterface
{
    /**
     * @var BookInterface
     */
    protected $book;

    /**
     * @param BookInterface $book
     */
    public function __construct(BookInterface $book)
    {
        $this->book = $book;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->book->find($id);
    }

    /**
     * @param $slug
     *
     * @return mixed
     */
    public function getBySlug($slug)
    {
        return $this->book->getBySlug($slug);
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->book->all();
    }

    /**
     * @param null $perPage
     * @param bool $all
     *
     * @return mixed
     */
    public function paginate($page = 1, $limit = 10, $all = false)
    {
        return $this->book->paginate($page, $limit, $all);
    }
}
