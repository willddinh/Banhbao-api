<?php

namespace App\Http\Controllers;

use App;
use App\Models\Menu;
use Illuminate\Auth\AuthManager;
use Laravel\Lumen\Routing\Controller as BaseController;

class WebUIController extends BaseController
{
    use ApiControllerTrait;

    protected $auth;
    /**
     * @var Menu
     */
    protected $menu;

    public function __construct(AuthManager $auth, Menu $menu)
    {
        $this->auth = $auth;
        $this->menu = $menu;
    }

    public function menu($group){
        $menu = $this->menu->query()->where(['lang'=> getLang(), 'mnugroup'=>$group])->orderBy('order', 'desc')->get();
        return $menu;
    }


}
