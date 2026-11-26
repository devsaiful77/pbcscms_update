<?php

use Illuminate\Http\Request;

/*Route::post('/groups/getGroups/','GroupInfoController@getGroup')->name('groupInfo.getGroup');
Route::post('add-to-group','WorkflowController@addToGroup');*/
//Route::get('delete-to-group','WorkflowController@deleteFoodMenu');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('CI-token-validation','App\Http\Controllers\CI\CustomerInterfaceController@tokenValidate');
