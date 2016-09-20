<?php

namespace App\Repositories\Author;

/**
 * Class AbstractAuthorDecorator.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
abstract class AbstractAuthorDecorator implements AuthorInterface
{
    /**
     * @var AuthorInterface
     */
    protected $author;

    /**
     * @param AuthorInterface $author
     */
    public function __construct(AuthorInterface $author)
    {
        $this->author = $author;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->author->find($id);
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->author->all();
    }

    /**
     * @param null $perPage
     * @param bool $all
     *
     * @return mixed
     */
    public function paginate($page = 1, $limit = 10, $all = false)
    {
        return $this->author->paginate($page, $limit, $all);
    }
}
