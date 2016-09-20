<?php

namespace App\Repositories\TreeCategory;

use Config;
use DB;
use Exception;
use App\Models\TreeCategory;
use App\Repositories\RepositoryAbstract;
use App\Repositories\CrudableInterface;
use App\Exceptions\Validation\ValidationException;

/**
 * Class TreeCategoryRepository.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
class TreeCategoryRepository extends RepositoryAbstract implements TreeCategoryInterface, CrudableInterface
{
    /**
     * @var
     */
    protected $perPage;
    /**
     * @var \App\Models\TreeCategory
     */
    protected $category;
    /**
     * Rules.
     *
     * @var array
     */
    protected static $rules = [
        'name' => 'required|min:3|unique:tree_categories',
    ];

    /**
     * @param TreeCategory $category
     */
    public function __construct(TreeCategory $category)
    {
        $this->category = $category;
        $config = Config::get('fully');
        $this->perPage = $config['per_page'];
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->category->where('lang', $this->getLang())->get();
    }

    /**
     * @param int  $page
     * @param int  $limit
     * @param bool $all
     *
     * @return mixed|\StdClass
     */
    public function paginate($page = 1, $limit = 10, $all = false)
    {
        $result = new \StdClass();
        $result->page = $page;
        $result->limit = $limit;
        $result->totalItems = 0;
        $result->items = array();

        $query = $this->category->orderBy('name');

        $categories = $query->skip($limit * ($page - 1))->take($limit)->where('lang', $this->getLang())->get();

        $result->totalItems = $this->totalCategories();
        $result->items = $categories->all();

        return $result;
    }

    /**
     * @return mixed
     */
    public function lists()
    {
        return $this->category->where('lang', $this->getLang())->lists('name', 'id');
    }
    
    public function findByGroup($group){
        return $this->category->where('lang', $this->getLang())->where('group', $group)->get();
    }

    public function findByGroupForDropDown($group){
        return $this->category->where('lang', $this->getLang())->where('group', $group)->lists('name', 'id');
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->category->findOrFail($id);
    }

    /**
     * @param $slug
     *
     * @return mixed
     */
//    public function getArticlesBySlug($slug)
//    {
//        return $this->category->where('slug', $slug)->where('lang', $this->getLang())->first()->articles()->paginate($this->perPage);
//    }

    /**
     * @param $attributes
     *
     * @return bool|mixed
     *
     * @throws \App\Exceptions\Validation\ValidationException
     */
    public function create($attributes)
    {
        if ($this->isValid($attributes)) {
            $this->category->lang = $this->getLang();
            $this->category->fill($attributes)->save();

            return true;
        }

        throw new ValidationException('TreeCategory validation failed', $this->getErrors());
    }

    /**
     * @param $id
     * @param $attributes
     *
     * @return bool|mixed
     *
     * @throws \App\Exceptions\Validation\ValidationException
     */
    public function update($id, $attributes)
    {
        $this->category = $this->find($id);
        $updateRules = $this::$rules;
        $updateRules["name"] = $updateRules["name"].",name,$id";
        if ($this->isValid($attributes, $updateRules)) {
            $this->category->resluggify();
            $this->category->fill($attributes)->save();

            return true;
        }

        throw new ValidationException('TreeCategory validation failed', $this->getErrors());
    }

    /**
     * @param $id
     *
     * @return mixed|void
     */
    public function delete($id)
    {
        $this->category = $this->category->find($id);
        $this->category->articles()->delete($id);
        $this->category->delete();
    }

    /**
     * Get total category count.
     *
     * @return mixed
     */
    protected function totalCategories()
    {
        return $this->category->where('lang', $this->getLang())->count();
    }

    public function getCategoryAsKendoDataSource($code)
    {
        $rows = $this->getAllCategoriesByGroupCode($code);
        return $this->getTreeKendoDatasource($rows);
    }

    public function getRootCategory($groupCode){
        return TreeCategory::where('group','=',$groupCode)->where('lang', $this->getLang())
            ->where('lft','=',1)
            ->first();
    }

    public function getAllCategoriesByGroupCode($code)
    {
        if(!$code) throw new Exception("groupCode null!");
        $category_Item = $this->getRootCategory($code);
        $parentCatId = $category_Item->id;

        return DB::select("select tree_categories.* from tree_categories  ,tree_categories as parent
        where tree_categories.lft between parent.lft and parent.rgt
        and parent.id = ? and tree_categories.group = ? and  tree_categories.lang = ? 
        order by tree_categories.lft asc", array($parentCatId, $code,$this->getLang()));
    }

    private function getTreeKendoDatasource($rows){
        $right = array();
        $result = array();

        foreach ($rows as $anItem) {
            $item = get_object_vars($anItem);
            $item["text"] = $item["name"];

            if(count($right)>0){
                while ($right[count($right)-1]['rgt']<$item['rgt']) {
                    array_pop($right);
                }
            }
            if($item['lft'] < $item['rgt'] -1){

                $item["items"] = array();
            }
            if(count($right)>0){
                $latestHasChildren = &$this->findLatestHasChildren($result[(count($result)-1)], $right[count($right)-1]);

                $latestHasChildren["items"][] =  $item;

            }else{
                call_user_func_array("array_push", array(&$result,&$item));
            }

            call_user_func_array("array_push", array(&$right,&$item));

        }
        return $result;
    }
    private function &findLatestHasChildren(&$arr, $sample){

        if(array_key_exists("items",$arr)){
            if(count($arr["items"]) == 0 || !array_key_exists("items",$arr["items"][count($arr["items"]) -1]) ){
                return $arr;
            }elseif(array_key_exists("items",$arr["items"][count($arr["items"]) -1])
                && $arr["items"][count($arr["items"]) -1]["rgt"] < $sample["rgt"]){
                return $arr;
            }
            else {
                return $this->findLatestHasChildren($arr["items"][count($arr["items"]) -1],$sample);
            }
        }
        else{
            return $arr;
        }
    }
}
