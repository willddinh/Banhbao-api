<?php

namespace App\Repositories\Book;

use App\Repositories\RepositoryInterface;


interface BookInterface extends RepositoryInterface
{
    /**
     * @param $slug
     *
     * @return mixed
     */
    public function getBySlug($slug);

    public function getBySubCat($subCatId);

    public function listBook($publisherId, $categoryId, $subCategoriesIdArr, $priceOrder, $page);

    public function getBookById($id);
}
