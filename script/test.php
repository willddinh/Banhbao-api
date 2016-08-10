<?php
$app = require __DIR__.'/../bootstrap/app.php';

print 'hello--------';
$allUsers = \App\User::all();
foreach ($allUsers as $user) {
    echo $user->name;
}