<?php

namespace App\Repositories\Entity;

use App\Models\Entity;
use Config;
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


class EntityRepository extends RepositoryAbstract implements EntityInterface, CrudableInterface
{
    protected $width;
    protected $height;
    protected $thumbWidth;
    protected $thumbHeight;
    protected $imgDir;
    protected $perPage;
    protected $entity;
    /**
     * Rules.
     *
     * @var array
     */
    protected static $rules = [
        'title' => 'required',
        'price' => 'required',
        
    ];

    /**
     * @param Entity $Entity
     */
    public function __construct(Entity $entity)
    {
        $config = Config::get('fully');
        $this->perPage = $config['per_page'];
        $this->width = $config['modules']['entity']['image_size']['width'];
        $this->height = $config['modules']['entity']['image_size']['height'];
        $this->thumbWidth = $config['modules']['entity']['thumb_size']['width'];
        $this->thumbHeight = $config['modules']['entity']['thumb_size']['height'];
        $this->imgDir = $config['modules']['entity']['image_dir'];
        $this->entity = $entity;
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->entity->with('tags')->orderBy('id', 'DESC')->where('lang', $this->getLang())->get();
    }

    /**
     * @param $limit
     *
     * @return mixed
     */
    public function getLastEntity($limit)
    {
        return $this->entity->orderBy('id', 'desc')->where('lang', $this->getLang())->take($limit)->offset(0)->get();
    }

    /**
     * @return mixed
     */
    public function lists()
    {
        return $this->entity->get()->where('lang', $this->getLang())->lists('title', 'id');
    }

    /*
    public function paginate($perPage = null, $all = false) {

        if ($all)
            return $this->Entity->with('tags')->orderBy('created_at', 'DESC')
                ->paginate(($perPage) ? $perPage : $this->perPage);

        return $this->Entity->with('tags')->orderBy('created_at', 'DESC')
            ->where('is_published', 1)
            ->paginate(($perPage) ? $perPage : $this->perPage);
    }
    */

    /**
     * Get paginated Entitys.
     *
     * @param int  $page  Number of Entitys per page
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

        $query = $this->entity->with('tags')->orderBy('id', 'DESC')->where('lang', $this->getLang());

        $entitys = $query->skip($limit * ($page - 1))->take($limit)->get();

        $result->totalItems = $this->totalEntitys($all);
        $result->items = $entitys->all();

        return $result;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->entity->with(['tags', 'subCategories'])->findOrFail($id);
    }

    /**
     * @param $slug
     *
     * @return mixed
     */
    public function getBySlug($slug)
    {
        return $this->entity->with(['tags', 'subCategories'])->where('slug', $slug)->first();
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

                    $this->entity->lang = $this->getLang();
                    $this->entity->file_name = $fileName;
                    $this->entity->file_size = $fileSize;
                    $this->entity->path = $this->imgDir.'/'.$fileName;
                }
            }

            //--------------------------------------------------------

            $this->entity->lang = $this->getLang();
            if ($this->entity->fill($attributes)->save()) {
                $category = Category::find($attributes['category']);
                $category->entitys()->save($this->entity);
            }

            $entityTags = explode(',', $attributes['tag']);

            foreach ($entityTags as $entityTag) {
                if (!$entityTag) {
                    continue;
                }

                $tag = Tag::where('name', '=', $entityTag)->first();

                if (!$tag) {
                    $tag = new Tag();
                }

                $tag->lang = $this->getLang();
                $tag->name = $entityTag;
                //$tag->slug = Str::slug($EntityTag);

                $this->entity->tags()->save($tag);
            }

            //Event::fire('Entity.created', $this->Entity);
            Event::fire('entity.creating', $this->entity);

            return true;
        }

        throw new ValidationException('Entity validation failed', $this->getErrors());
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
        $this->entity = $this->find($id);
        $attributes['is_published'] = isset($attributes['is_published']) ? true : false;

        if ($this->isValid($attributes)) {

            //-------------------------------------------------------
            if (isset($attributes['image'])) {
                $file = $attributes['image'];

                // delete old image
                $destinationPath = public_path().$this->imgDir;
                File::delete($destinationPath.$this->entity->file_name);
                File::delete($destinationPath.'thumb_'.$this->entity->file_name);

                $destinationPath = public_path().$this->imgDir;
                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getClientSize();

                $upload_success = $file->move($destinationPath, $fileName);

                if ($upload_success) {

                    // resizing an uploaded file
                    Image::make($destinationPath.$fileName)->resize($this->width, $this->height)->save($destinationPath.$fileName);

                    // thumb
                    Image::make($destinationPath.$fileName)->resize($this->thumbWidth, $this->thumbHeight)->save($destinationPath.'thumb_'.$fileName);

                    $this->entity->file_name = $fileName;
                    $this->entity->file_size = $fileSize;
                    $this->entity->path = $this->imgDir.'/'.$fileName;
                }
            }
            //-------------------------------------------------------

            if ($this->entity->fill($attributes)->save()) {
                $this->entity->resluggify();
                $category = Category::find($attributes['category']);
                $category->entitys()->save($this->entity);
            }

            $entityTags = explode(',', $attributes['tag']);

            foreach ($entityTags as $entityTag) {
                if (!$entityTag) {
                    continue;
                }

                $tag = Tag::where('name', '=', $entityTag)->where('lang', $this->getLang()) ->first();

                if (!$tag) {
                    $tag = new Tag();
                    $tag->lang = $this->getLang();
                    $tag->name = $entityTag;
                    $this->entity->tags()->save($tag);
                }


            }

            return true;
        }

        throw new ValidationException('Entity validation failed', $this->getErrors());
    }

    /**
     * @param $id
     *
     * @return mixed|void
     */
    public function delete($id)
    {
        $entity = $this->entity->findOrFail($id);
        $entity->tags()->detach();
        $entity->delete();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function togglePublish($id)
    {
        $entity = $this->entity->find($id);

        $entity->is_published = ($entity->is_published) ? false : true;
        $entity->save();

        return Response::json(array('result' => 'success', 'changed' => ($entity->is_published) ? 1 : 0));
    }

    /**
     * @param $id
     *
     * @return string
     */
    public function getUrl($id)
    {
        $entity = $this->entity->findOrFail($id);

        return url('entity/'.$id.'/'.$entity->slug, $parameters = array(), $secure = null);
    }

    /**
     * Get total Entity count.
     *
     * @param bool $all
     *
     * @return mixed
     */
    protected function totalEntitys($all = false)
    {
        if (!$all) {
            return $this->entity->where('is_published', 1)->where('lang', $this->getLang())->count();
        }

        return $this->entity->where('lang', $this->getLang())->count();
    }
}
