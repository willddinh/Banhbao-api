<?php

namespace App\Repositories\SubCategory;

/**
 * Class AbstractSubCategoryDecorator.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
abstract class AbstractSubCategoryDecorator implements SubCategoryInterface
{
    /**
     * @var SubCategoryInterface
     */
    protected $subCategory;

    /**
     * @param SubCategoryInterface $subCategory
     */
    public function __construct(SubCategoryInterface $subCategory)
    {
        $this->subCategory = $subCategory;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->subCategory->find($id);
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->subCategory->all();
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
        return $this->subCategory->paginate($page = 1, $limit = 10, $all = false);
    }

   
}
