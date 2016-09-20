<?php

namespace App\Repositories\Author;

use App\Models\Author;
use App\Repositories\RepositoryAbstract;
use App\Repositories\CrudableInterface;
use App\Exceptions\Validation\ValidationException;
use Illuminate\Support\Facades\Config;


class AuthorRepository extends RepositoryAbstract implements AuthorInterface, CrudableInterface
{
    /**
     * @var
     */
    protected $perPage;
    /**
     * @var \Author
     */
    protected $author;
    /**
     * Rules.
     *
     * @var array
     */
    protected static $rules = [
        'name' => 'required',
    ];

    /**
     * @param Author $author
     */
    public function __construct(Author $author)
    {
        $this->author = $author;
        $config = Config::get('fully');
        $this->perPage = $config['per_page'];
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->author->query()->where('lang', $this->getLang())->get();
    }

    /**
     * Get paginated faqs.
     *
     * @param int  $page  Number of faqs per page
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

        $query = $this->author->orderBy('id', 'DESC')->where('lang', $this->getLang());

        $authors = $query->skip($limit * ($page - 1))->take($limit)->get();

        $result->totalItems = $this->totalAuthors();
        $result->items = $authors->all();

        return $result;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->author->findOrFail($id);
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
            $this->author->lang = $this->getLang();
            $this->author->fill($attributes)->save();

            return true;
        }

        throw new ValidationException('Author validation failed', $this->getErrors());
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
        $this->author = $this->find($id);

        if ($this->isValid($attributes)) {
            $this->author->fill($attributes)->save();

            return true;
        }

        throw new ValidationException('Author validation failed', $this->getErrors());
    }

    /**
     * @param $id
     *
     * @return mixed|void
     */
    public function delete($id)
    {
        $this->author->find($id)->delete();
    }

    /**
     * Get total faq count.
     *
     * @param bool $all
     *
     * @return mixed
     */
    protected function totalAuthors()
    {
        return $this->author->where('lang', $this->getLang())->count();
    }

    public function findByGroupForDropDown()
    {
        return $this->author->where('lang', $this->getLang())->lists('name', 'id');
    }
}
