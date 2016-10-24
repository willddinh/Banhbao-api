<?php

namespace App\Services\Invite;


interface InviteServiceInterface 
{
    public function invite($email);
    public function markClick($email);
    public function finish($email);
}
