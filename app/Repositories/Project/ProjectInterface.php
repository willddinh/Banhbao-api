<?php

namespace App\Repositories\Project;

use App\Repositories\RepositoryInterface;

/**
 * Interface ProjectInterface.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
interface ProjectInterface extends RepositoryInterface
{
    /**
     * @param $slug
     *
     * @return mixed
     */
    public function getBySlug($slug);
}
