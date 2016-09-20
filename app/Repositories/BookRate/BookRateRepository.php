<?php

namespace App\Repositories\BookRate;

use Illuminate\Support\Facades\Config;
use App\Models\BookRate;
use App\Repositories\RepositoryAbstract;
use App\Repositories\CrudableInterface;
use App\Exceptions\Validation\ValidationException;


class BookRateRepository extends RepositoryAbstract implements BookRateInterface, CrudableInterface
{
    
    protected $perPage;
    
    protected $bookRate;
    /**
     * Rules.
     *
     * @var array
     */
    protected static $rules = [
        'name' => 'required',
    ];

    /**
     * @param BookRate $bookRate
     */
    public function __construct(BookRate $bookRate)
    {
        $this->bookRate = $bookRate;
        $config = Config::get('fully');
        $this->perPage = $config['per_page'];
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->bookRate->query()->where('lang', $this->getLang())->get();
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

        $query = $this->bookRate->orderBy('id', 'DESC')->where('lang', $this->getLang());

        $bookRates = $query->skip($limit * ($page - 1))->take($limit)->get();

        $result->totalItems = $this->totalBookRates();
        $result->items = $bookRates->all();

        return $result;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->bookRate->findOrFail($id);
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
            $this->bookRate->lang = $this->getLang();
            $this->bookRate->fill($attributes)->save();

            return true;
        }

        throw new ValidationException('BookRate validation failed', $this->getErrors());
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
        $this->bookRate = $this->find($id);

        if ($this->isValid($attributes)) {
            $this->bookRate->fill($attributes)->save();

            return true;
        }

        throw new ValidationException('BookRate validation failed', $this->getErrors());
    }

    /**
     * @param $id
     *
     * @return mixed|void
     */
    public function delete($id)
    {
        $this->bookRate->find($id)->delete();
    }

    /**
     * Get total faq count.
     *
     * @param bool $all
     *
     * @return mixed
     */
    protected function totalBookRates()
    {
        return $this->bookRate->where('lang', $this->getLang())->count();
    }
}
