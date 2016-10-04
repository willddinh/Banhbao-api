<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Auth\AuthManager;
use Illuminate\Translation\Translator;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class SearchController extends BaseController
{
    use ApiControllerTrait;


    public function booksAutoComplete($text){

        $elasticSearch = app('elasticsearch');
        $params = [
            'index' => 'banhbao',
            'body' => [
                'book-suggest' => [
                    'text' => $text,
                    'completion'=>["field"=>"book_suggest"]
                ]
            ]
        ];

        $completion = $elasticSearch->suggest($params);
        return $this->respond(compact('completion'));
    }
}
