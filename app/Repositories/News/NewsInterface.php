<?php

namespace App\Repositories\News;

use App\Repositories\RepositoryInterface;

/**
 * Interface NewsInterface.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
interface NewsInterface extends RepositoryInterface
{
    /**
     * @param $slug
     *
     * @return mixed
     */
    public function getBySlug($slug);
}
