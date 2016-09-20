<?php

namespace App\Repositories\Publisher;

use Config;
use App\Models\Publisher;
use App\Repositories\RepositoryAbstract;
use App\Repositories\CrudableInterface;
use App\Exceptions\Validation\ValidationException;


class PublisherRepository extends RepositoryAbstract implements PublisherInterface, CrudableInterface
{
    /**
     * @var
     */
    protected $perPage;
    /**
     * @var \Publisher
     */
    protected $publisher;
    /**
     * Rules.
     *
     * @var array
     */
    protected static $rules = [
        'name' => 'required',
    ];

    /**
     * @param Publisher $publisher
     */
    public function __construct(Publisher $publisher)
    {
        $this->publisher = $publisher;
        $config = Config::get('fully');
        $this->perPage = $config['per_page'];
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->publisher->query()->where('lang', $this->getLang())->get();
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

        $query = $this->publisher->orderBy('id', 'DESC')->where('lang', $this->getLang());

        $publishers = $query->skip($limit * ($page - 1))->take($limit)->get();

        $result->totalItems = $this->totalPublishers();
        $result->items = $publishers->all();

        return $result;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->publisher->findOrFail($id);
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
            $this->publisher->lang = $this->getLang();
            $this->publisher->fill($attributes)->save();

            return true;
        }

        throw new ValidationException('Publisher validation failed', $this->getErrors());
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
        $this->publisher = $this->find($id);

        if ($this->isValid($attributes)) {
            $this->publisher->fill($attributes)->save();

            return true;
        }

        throw new ValidationException('Publisher validation failed', $this->getErrors());
    }

    /**
     * @param $id
     *
     * @return mixed|void
     */
    public function delete($id)
    {
        $this->publisher->find($id)->delete();
    }

    /**
     * Get total faq count.
     *
     * @param bool $all
     *
     * @return mixed
     */
    protected function totalPublishers()
    {
        return $this->publisher->where('lang', $this->getLang())->count();
    }

    public function findByGroupForDropDown()
    {
        return $this->publisher->where('lang', $this->getLang())->lists('name', 'id');
    }
}
