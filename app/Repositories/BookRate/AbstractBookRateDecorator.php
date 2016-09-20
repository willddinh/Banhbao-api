<?php

namespace App\Repositories\BookRate;

/**
 * Class AbstractBookRateDecorator.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
abstract class AbstractBookRateDecorator implements BookRateInterface
{
    /**
     * @var BookRateInterface
     */
    protected $bookRate;

    /**
     * @param BookRateInterface $bookRate
     */
    public function __construct(BookRateInterface $bookRate)
    {
        $this->bookRate = $bookRate;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->bookRate->find($id);
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->bookRate->all();
    }

    /**
     * @param null $perPage
     * @param bool $all
     *
     * @return mixed
     */
    public function paginate($page = 1, $limit = 10, $all = false)
    {
        return $this->bookRate->paginate($page, $limit, $all);
    }
}
