<?php
/**
 * Created by PhpStorm.
 * User: DuongLT
 * Date: 9/15/2016
 * Time: 9:58 AM
 */
namespace App\Models\Traits;

trait EntitySupportTrait{

    protected function getTitle(){
     $this->entity->title;   
    }

    protected function getPrice(){
        $this->entity->price;
    }

    protected function getTags(){
        $this->entity->tags();
    }

    protected function getSubCategories(){
        $this->entity->category();
    }
}