<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Auth::routes();
// post workaround for logout
Route::get('/logout', 'Auth\LoginController@logout');

Route::get('/', 'ChatController@index');
Route::post('/', 'ChatController@openConversationPOST');
Route::get('/contacts', 'ContactsController@showall');
Route::post('/contacts', 'ContactsController@add');

Route::get('/contacts/{id}/delete', 'ContactsController@delete')->where('id', '[0-9]+');
Route::get('/open/{email}', 'ChatController@openConversation');

Route::get('/conversation/{id}', 'ChatController@show')->where('id', '[0-9]+');
Route::post('/conversation/{id}', 'ChatController@send')->where('id', '[0-9]+');
Route::get('/conversation/{id}/delete', 'ChatController@deleteConversation')->where('id', '[0-9]+');
Route::get('/message/{id}/delete', 'ChatController@deleteMessage')->where('id', '[0-9]+');

Route::get('/account', 'UserController@index');
Route::post('/account', 'UserController@edit');

Route::get('/token/get', 'TokenController@get');
Route::get('/token/renew', 'TokenController@renew');

// API
Route::get('/api/{token}/auth', 'UserController@APIAuth')->where('token', '[a-zA-Z0-9]+');
Route::get('/api/{token}/contacts/list', 'ContactsController@APIshowall')->where('token', '[a-zA-Z0-9]+');
Route::get('/api/{token}/contacts/delete/{id}', 'ContactsController@APIdelete')->where(['token' => '[a-zA-Z0-9]+', 'id' => '[0-9]+']);