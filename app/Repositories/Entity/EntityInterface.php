<?php

namespace App\Repositories\Entity;

use App\Repositories\RepositoryInterface;


interface EntityInterface extends RepositoryInterface
{
    /**
     * @param $slug
     *
     * @return mixed
     */
    public function getBySlug($slug);
}
