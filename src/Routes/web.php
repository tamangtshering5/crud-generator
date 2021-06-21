<?php

use Illuminate\Support\Str;

Route::get('/package',function (){
//   dd(strtolower(Str::plural('Hello')));
//    $pluralLower = strtolower(Str::plural('Hello World'));
    $output=getCamelCaseName('hello_worlds');
    dd($output);
});
