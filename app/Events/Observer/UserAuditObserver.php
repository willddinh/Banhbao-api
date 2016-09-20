<?php

namespace App\Events\Observer;

use App\Models\BaseModel;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserAuditObserver
{

    public function saving(BaseModel $model)
    {
            $user = JWTAuth::user();
            $model->created_by = $user->getUserId();
    }

    public function saved($model)
    {
        //
    }
}
