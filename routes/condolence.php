<?php
    //$api->group(['middleware' => 'token'],function($api){
        $api->post('createCond','App\Http\Controllers\CondolenceController@createCond');
        $api->post('deleteCond','App\Http\Controllers\CondolenceController@deleteCond');
    //});