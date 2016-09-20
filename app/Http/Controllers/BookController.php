<?php

namespace App\Http\Controllers;

use App;
use App\Repositories\Book\BookInterface;
use App\Repositories\Menu\MenuInterface;
use App\Repositories\Slider\SliderInterface;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Config;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class BookController extends BaseController
{
    use ApiControllerTrait;

    protected $auth;
    /**
     * @var BookInterface
     */
    protected $bookRepo;

    
    public function __construct(AuthManager $auth, BookInterface $bookRepo)
    {
        $this->auth = $auth;
        $this->bookRepo = $bookRepo;
    }

    public function getBySubCat($subCatId = null){
        $booksGroupBySubCat = $this->bookRepo->getBySubCat($subCatId);
        return $booksGroupBySubCat;
    }

    public function getCategories(){
        return App\Models\Category::query()->where('lang', getLang())
            ->where('group', Config::get('common.const.CATEGORY_BOOK'))->get(["id","title", "slug"]);
    }

    public function getBookSubCats(){
        return App\Models\SubCategory::where('lang', getLang())
            ->where('group', Config::get('common.const.SUBCATEGORY_BOOK'))
            ->get();
    }

    public function getPublisher(){
        return App\Models\Publisher::query()->where('lang', getLang())->get(["id","name", "slug"]);
    }
    public function listBooks(Request $request){
        $publisherId = $request->input("publisherId", -1);
        $categoryId = $request->input("categoryId", -1);
        $subCategoriesIds = $request->input("subCategoriesIds", null);
        $subCategoriesIdArr  = [];
        if($subCategoriesIds)
         $subCategoriesIdArr = explode(',', $subCategoriesIds );

        $priceOrder = $request->input("priceOrder", null);
        $page = $request->input("page", 1);

        return $this->bookRepo->listBook($publisherId, $categoryId, $subCategoriesIdArr, $priceOrder, $page);
    }

    public function detail($id){
        $book = $this->bookRepo->getBookById($id);
        if(!$book)
            return $this->error(["message"=>"not found"]);
        else
            return $book;

    }

}
