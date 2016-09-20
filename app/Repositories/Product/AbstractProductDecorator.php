<?php

namespace App\Repositories\Product;

/**
 * Class AbstractProductDecorator.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
abstract class AbstractProductDecorator implements ProductInterface
{
    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @param ProductInterface $product
     */
    public function __construct(ProductInterface $product)
    {
        $this->product = $product;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->product->find($id);
    }

    /**
     * @param $slug
     *
     * @return mixed
     */
    public function getBySlug($slug)
    {
        return $this->product->getBySlug($slug);
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->product->all();
    }

    /**
     * @param null $perPage
     * @param bool $all
     *
     * @return mixed
     */
    public function paginate($page = 1, $limit = 10, $all = false)
    {
        return $this->product->paginate($page, $limit, $all);
    }
}
