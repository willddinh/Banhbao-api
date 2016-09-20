<?php

namespace App\Repositories\SubCategory;

use Config;
use App\Models\SubCategory;
use App\Repositories\RepositoryAbstract;
use App\Repositories\CrudableInterface;
use App\Exceptions\Validation\ValidationException;

/**
 * Class SubCategoryRepository.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
class SubCategoryRepository extends RepositoryAbstract implements SubCategoryInterface, CrudableInterface
{
    /**
     * @var
     */
    protected $perPage;
    /**
     * @var \App\Models\SubCategory
     */
    protected $subCategory;
    /**
     * Rules.
     *
     * @var array
     */
    protected static $rules = [
        'title' => 'required|min:3|unique:sub_categories',
        'group' => 'required|min:1',
    ];

    /**
     * @param SubCategory $subCategory
     */
    public function __construct(SubCategory $subCategory)
    {
        $this->subCategory = $subCategory;
        $config = Config::get('fully');
        $this->perPage = $config['per_page'];
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->subCategory->where('lang', $this->getLang())->get();
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

        $query = $this->subCategory->orderBy('title');

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
        return $this->subCategory->where('lang', $this->getLang())->lists('title', 'id');
    }
    
    public function findByGroup($group){
        return $this->subCategory->where('lang', $this->getLang())->where('group', $group)->get();
    }

    public function findByGroupForDropDown($group){
        return $this->subCategory->query()->where('lang', $this->getLang())->where('group', $group)->lists('title', 'id')->all();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->subCategory->findOrFail($id);
    }

    /**
     * @param $slug
     *
     * @return mixed
     */
    public function getArticlesBySlug($slug)
    {
        return $this->subCategory->where('slug', $slug)->where('lang', $this->getLang())->first()->articles()->paginate($this->perPage);
    }

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
            $this->subCategory->lang = $this->getLang();
            $this->subCategory->fill($attributes)->save();

            return true;
        }

        throw new ValidationException('SubCategory validation failed', $this->getErrors());
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
        $this->subCategory = $this->find($id);
        $updateRules = $this::$rules;
        $updateRules["title"] = $updateRules["title"].",title,$id";
        if ($this->isValid($attributes, $updateRules)) {
//            $this->subCategory->resluggify();
            $this->subCategory->fill($attributes)->save();

            return true;
        }

        throw new ValidationException('SubCategory validation failed', $this->getErrors());
    }

    /**
     * @param $id
     *
     * @return mixed|void
     */
    public function delete($id)
    {
        $this->subCategory = $this->subCategory->find($id);
        $this->subCategory->delete();
    }

    /**
     * Get total subCategory count.
     *
     * @return mixed
     */
    protected function totalCategories()
    {
        return $this->subCategory->where('lang', $this->getLang())->count();
    }
}
