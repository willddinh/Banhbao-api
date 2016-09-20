<?php

namespace App\Repositories\Category;

use App\Repositories\RepositoryInterface;

/**
 * Interface CategoryInterface.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
interface CategoryInterface extends RepositoryInterface
{
    public function findByGroup($group);

    public function findByGroupForDropDown($group);
}
