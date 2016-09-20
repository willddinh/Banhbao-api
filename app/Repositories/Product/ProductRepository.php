<?php

namespace App\Repositories\Product;

use App\Models\Entity;
use App\Models\Product;
use Config;
use App\Models\SubCategory;
use Response;
use App\Models\Tag;
use App\Models\Category;
use Str;
use Event;
use Image;
use File;
use App\Repositories\RepositoryAbstract;
use App\Repositories\CrudableInterface as CrudableInterface;
use App\Exceptions\Validation\ValidationException;

/**
 * Class ProductRepository.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
class ProductRepository extends RepositoryAbstract implements ProductInterface, CrudableInterface
{
    protected $width;
    protected $height;
    protected $thumbWidth;
    protected $thumbHeight;
    protected $imgDir;
    protected $perPage;
    /**
     * @var \App\Models\Product
     */
    protected $product;
    /**
     * @var \App\Models\Entity
     */
    protected $entity;
    /**
     * Rules.
     *
     * @var array
     */
    protected static $rules = [
        'title' => 'required',
        'content' => 'required',
        'price' => 'required',

    ];

    /**
     * @param Product $Product
     */
    public function __construct(Product $product, Entity $entity)
    {
        $config = Config::get('fully');
        $this->perPage = $config['per_page'];
        $this->width = $config['modules']['product']['image_size']['width'];
        $this->height = $config['modules']['product']['image_size']['height'];
        $this->thumbWidth = $config['modules']['product']['thumb_size']['width'];
        $this->thumbHeight = $config['modules']['product']['thumb_size']['height'];
        $this->imgDir = $config['modules']['product']['image_dir'];
        $this->product = $product;
        $this->entity = $entity;
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->product->with('tags')->orderBy('created_at', 'DESC')->where('is_published', 1)->where('lang', $this->getLang())->get();
    }

    /**
     * @param $limit
     *
     * @return mixed
     */
    public function getLastProduct($limit)
    {
        return $this->product->orderBy('created_at', 'desc')->where('lang', $this->getLang())->take($limit)->offset(0)->get();
    }

    /**
     * @return mixed
     */
    public function lists()
    {
        return $this->product->get()->where('lang', $this->getLang())->lists('title', 'id');
    }

    /*
    public function paginate($perPage = null, $all = false) {

        if ($all)
            return $this->Product->with('tags')->orderBy('created_at', 'DESC')
                ->paginate(($perPage) ? $perPage : $this->perPage);

        return $this->Product->with('tags')->orderBy('created_at', 'DESC')
            ->where('is_published', 1)
            ->paginate(($perPage) ? $perPage : $this->perPage);
    }
    */

    /**
     * Get paginated Products.
     *
     * @param int  $page  Number of Products per page
     * @param int  $limit Results per page
     * @param bool $all   Show published or all
     *
     * @return StdClass Object with $items and $totalItems for pagination
     */
    public function paginate($page = 1, $limit = 10, $all = false)
    {
        $result = new \StdClass();
        $result->page = $page;
        $result->limit = $limit;
        $result->totalItems = 0;
        $result->items = array();

        $query = $this->product->with('entity')->orderBy('id', 'DESC')->where('lang', $this->getLang());

        if (!$all) {
            $query->where('is_published', 1);
        }

        $products = $query->skip($limit * ($page - 1))->take($limit)->get();

        $result->totalItems = $this->totalProducts($all);
        $result->items = $products->all();

        return $result;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->product->with(['entity', 'category'])->findOrFail($id);
    }

    /**
     * @param $slug
     *
     * @return mixed
     */
    public function getBySlug($slug)
    {
        return $this->product->with(['entity', 'category'])->where('slug', $slug)->first();
    }

    public function createEntity($attributes){
        $this->entity->lang = $this->getLang();

        $this->entity->fill(['title'=>$attributes['title'], 'price'=>$attributes['price']])->save();
//save tags
        $tags = explode(',', $attributes['tag']);

        foreach ($tags as $tagName) {
            if (!$tagName) {
                continue;
            }

            $tag = Tag::query()->where('name', '=', $tagName)->where('lang', $this->getLang()) ->first();

            if (!$tag) {
                $tag = new Tag();
            }

            $tag->lang = $this->getLang();
            $tag->name = $tagName;
            $this->entity->tags()->save($tag);
        }

//save subcategories

        if(!array_key_exists('subCategories', $attributes))
            return;
        $subCategories = $attributes['subCategories'];

        foreach ($subCategories as $subCatId) {
            if (!$subCatId || $subCatId == -1) {
                continue;
            }

            $subCat = SubCategory::query()->find($subCatId);

            if ($subCat) {
                $this->entity->subCategories()->save($subCat);
            }
        }


    }

    private function updateEntity($attributes)
    {
        $this->entity->lang = $this->getLang();

        $this->entity->fill(['title'=>$attributes['title'], 'price'=>$attributes['price']])->save();
//save tags


        $tags = explode(',', $attributes['tag']);
        $this->entity->tags()->detach();
        foreach ($tags as $tagName) {
            if (!$tagName) {
                continue;
            }

            $tag = Tag::query()->where('name', '=', $tagName)->where('lang', $this->getLang()) ->first();

            if (!$tag) {
                $tag = new Tag();
            }

            $tag->lang = $this->getLang();
            $tag->name = $tagName;
            

            $this->entity->tags()->save($tag);
        }

//save subcategories
        $this->entity->subCategories()->detach();
        if(!array_key_exists('subCategories', $attributes))
            return;
        $subCategories = $attributes['subCategories'];

        foreach ($subCategories as $subCatId) {
            if (!$subCatId || $subCatId == -1) {
                continue;
            }

            $subCat = SubCategory::query()->find($subCatId);

            if ($subCat) {
                $this->entity->subCategories()->save($subCat);
            }
        }
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
        $attributes['is_published'] = isset($attributes['is_published']) ? true : false;

        if ($this->isValid($attributes)) {

            //--------------------------------------------------------

            $file = null;

            if (isset($attributes['image'])) {
                $file = $attributes['image'];
            }

            if ($file) {
                $destinationPath = public_path().$this->imgDir;
                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getClientSize();

                $upload_success = $file->move($destinationPath, $fileName);

                if ($upload_success) {

                    // resizing an uploaded file
                    Image::make($destinationPath.$fileName)->resize($this->width, $this->height)->save($destinationPath.$fileName);

                    // thumb
                    Image::make($destinationPath.$fileName)->resize($this->thumbWidth, $this->thumbHeight)->save($destinationPath.'thumb_'.$fileName);

                    $this->product->lang = $this->getLang();
                    $this->product->file_name = $fileName;
                    $this->product->file_size = $fileSize;
                    $this->product->path = $this->imgDir.'/'.$fileName;
                }
            }

            //--------------------------------------------------------

            $this->createEntity($attributes);
            $this->product->entity()->associate($this->entity);
            $this->product->lang = $this->getLang();
            if ($this->product->fill($attributes)->save()) {
                $category = Category::query()->find($attributes['category']);
                $category->products()->save($this->product);
            }

            //Event::fire('Product.created', $this->Product);
            Event::fire('product.creating', $this->product);

            return true;
        }

        throw new ValidationException('Product validation failed', $this->getErrors());
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
        $this->product = $this->find($id);
        $this->entity = $this->product->entity;
        $attributes['is_published'] = isset($attributes['is_published']) ? true : false;

        if ($this->isValid($attributes)) {

            //-------------------------------------------------------
            if (isset($attributes['image'])) {
                $file = $attributes['image'];

                // delete old image
                $destinationPath = public_path().$this->imgDir;
                File::delete($destinationPath.$this->product->file_name);
                File::delete($destinationPath.'thumb_'.$this->product->file_name);

                $destinationPath = public_path().$this->imgDir;
                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getClientSize();

                $upload_success = $file->move($destinationPath, $fileName);

                if ($upload_success) {

                    // resizing an uploaded file
                    Image::make($destinationPath.$fileName)->resize($this->width, $this->height)->save($destinationPath.$fileName);

                    // thumb
                    Image::make($destinationPath.$fileName)->resize($this->thumbWidth, $this->thumbHeight)->save($destinationPath.'thumb_'.$fileName);

                    $this->product->file_name = $fileName;
                    $this->product->file_size = $fileSize;
                    $this->product->path = $this->imgDir.'/'.$fileName;
                }
            }
            //-------------------------------------------------------
            $this->updateEntity($attributes);
//            $this->product->entity()->associate($this->entity);

            if ($this->product->fill($attributes)->save()) {
                $this->product->resluggify();
                $category = Category::find($attributes['category']);
                $category->products()->save($this->product);
            }

           /* $productTags = explode(',', $attributes['tag']);

            foreach ($productTags as $productTag) {
                if (!$productTag) {
                    continue;
                }

                $tag = Tag::where('name', '=', $productTag)->where('lang', $this->getLang()) ->first();

                if (!$tag) {
                    $tag = new Tag();
                    $tag->lang = $this->getLang();
                    $tag->name = $productTag;
                    $this->product->tags()->save($tag);
                }


            }*/

            return true;
        }

        throw new ValidationException('Product validation failed', $this->getErrors());
    }

    /**
     * @param $id
     *
     * @return mixed|void
     */
    public function delete($id)
    {
        $product = $this->product->findOrFail($id);
        $product->tags()->detach();
        $product->delete();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function togglePublish($id)
    {
        $product = $this->product->find($id);

        $product->is_published = ($product->is_published) ? false : true;
        $product->save();

        return Response::json(array('result' => 'success', 'changed' => ($product->is_published) ? 1 : 0));
    }

    /**
     * @param $id
     *
     * @return string
     */
    public function getUrl($id)
    {
        $product = $this->product->findOrFail($id);

        return url('product/'.$id.'/'.$product->slug, $parameters = array(), $secure = null);
    }

    /**
     * Get total Product count.
     *
     * @param bool $all
     *
     * @return mixed
     */
    protected function totalProducts($all = false)
    {
        if (!$all) {
            return $this->product->where('is_published', 1)->where('lang', $this->getLang())->count();
        }

        return $this->product->where('lang', $this->getLang())->count();
    }


}
