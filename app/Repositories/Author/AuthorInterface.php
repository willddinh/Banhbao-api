<?php

namespace App\Repositories\Author;

use App\Repositories\RepositoryInterface;

/**
 * Interface AuthorInterface.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
interface AuthorInterface extends RepositoryInterface
{
    public function findByGroupForDropDown();
}
