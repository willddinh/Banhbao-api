<?php

namespace App\Http\Controllers;

use App;
use App\Repositories\Menu\MenuInterface;
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

    public function __construct(AuthManager $auth, MenuInterface $menuRepo)
    {
        $this->auth = $auth;
        $this->menuRepo = $menuRepo;
    }

    /*public function menu($group){
        $menu = $this->menuRepo->menu->query()->where(['lang'=> getLang(), 'mnugroup'=>$group])->orderBy('order', 'desc')->get();
        return $menu;
    }*/

    public function menu($group){
        $menu = $this->menuRepo->getMenuByGroup($group);
        return $menu;
    }


}
