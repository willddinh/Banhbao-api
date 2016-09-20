<?php

namespace App\Repositories\Publisher;

use App\Repositories\RepositoryInterface;

/**
 * Interface PublisherInterface.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
interface PublisherInterface extends RepositoryInterface
{
    public function findByGroupForDropDown();
}
