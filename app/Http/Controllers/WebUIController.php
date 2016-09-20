<?php

namespace App\Http\Controllers;

use App;
use App\Repositories\Menu\MenuInterface;
use App\Repositories\Slider\SliderInterface;
use Illuminate\Auth\AuthManager;
use Laravel\Lumen\Routing\Controller as BaseController;

class WebUIController extends BaseController
{
    use ApiControllerTrait;

    protected $auth;
    /**
     * @var MenuInterface
     */
    protected $menuRepo;

    /**
     * @var SliderInterface
     */
    protected $sliderRepo;
    
    
    
    public function __construct(AuthManager $auth, MenuInterface $menuRepo, SliderInterface $sliderRepo)
    {
        $this->auth = $auth;
        $this->menuRepo = $menuRepo;
        $this->sliderRepo = $sliderRepo;
    }

    public function menu($group){
        $menu = $this->menuRepo->getMenuByGroup($group);
        return $menu;
    }


    public function slider(){
        $sliders = $this->sliderRepo->getSliderForHome();
        return $sliders;
    }


}
