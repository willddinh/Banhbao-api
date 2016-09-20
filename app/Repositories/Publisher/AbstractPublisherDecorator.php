<?php

namespace App\Repositories\Publisher;


abstract class AbstractPublisherDecorator implements PublisherInterface
{
    /**
     * @var PublisherInterface
     */
    protected $publisher;

    /**
     * @param PublisherInterface $Publisher
     */
    public function __construct(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->publisher->find($id);
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->publisher->all();
    }

    /**
     * @param null $perPage
     * @param bool $all
     *
     * @return mixed
     */
    public function paginate($page = 1, $limit = 10, $all = false)
    {
        return $this->publisher->paginate($page, $limit, $all);
    }
}
