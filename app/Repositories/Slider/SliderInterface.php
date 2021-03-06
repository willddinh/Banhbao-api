<?php

namespace App\Repositories\Slider;

/**
 * Interface SliderInterface.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
interface SliderInterface
{
    /**
     * Get al data.
     *
     * @return mixed
     */
    public function all();

    public function getSliderForHome();
}
