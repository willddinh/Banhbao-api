<?php

namespace App\Repositories\TreeCategory;

/**
 * Class AbstractTreeCategoryDecorator.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
abstract class AbstractTreeCategoryDecorator implements TreeCategoryInterface
{
    /**
     * @var TreeCategoryInterface
     */
    protected $category;

    /**
     * @param TreeCategoryInterface $category
     */
    public function __construct(TreeCategoryInterface $category)
    {
        $this->category = $category;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->category->find($id);
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->category->all();
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
        return $this->category->paginate($page = 1, $limit = 10, $all = false);
    }

    /**
     * @param $slug
     *
     * @return mixed
     */
    public function getArticlesBySlug($slug)
    {
        return $this->category->getArticlesBySlug($slug);
    }
}
