<?php

namespace App\Interfaces;

/**
 * Class ModelInterface.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
interface ModelInterface
{
    /**
     * @param $value
     *
     * @return mixed
     */
    public function setUrlAttribute($value);

    /**
     * @return mixed
     */
    public function getUrlAttribute();
}
