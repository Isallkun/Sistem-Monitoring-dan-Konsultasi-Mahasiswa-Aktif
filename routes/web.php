<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', 'HomeAdminController@index');
Route::get('logout', 'Auth\LoginController@logout')->name('keluar');

Auth::routes();

Route::group(['middleware' => ['auth','revalidate']], function ()
{
	Route::get('/', 'AdminController@index'); ## route yang perlu auth
});

Route::group(['prefix' => '/' ], function()
{
	//localhost:8000
  	Route::get('/', 'HomeController@index');
  	//localhost:8000/admin/
	Route::group(['prefix' => 'admin'], function()
	{
		//localhost:8000/admin/(halaman home admin)
		Route::get('/', 'AdminController@index');
		//localhost:8000/admin/dosen
		Route::get('master/dosen', 'MasterDosenController@daftardosen');
		
		//Tambah data dosen
		Route::get('master/dosen/tambah', 'MasterDosenController@tambahdosen');
		Route::post('master/dosen/prosestambah', 'MasterDosenController@tambahdosen_proses');

		//Ubah data dosen
		Route::get('master/dosen/ubah/{id}', 'MasterDosenController@ubahdosen');
		Route::post('master/dosen/ubahproses', 'MasterDosenController@ubahdosen_proses');

		//Hapus data dosen
		Route::get('master/dosen/hapus/{id}', 'MasterDosenController@hapusdosen');


	});

	Route::group(['prefix' => 'dosen'], function()
	{
		Route::get('/', 'HomeAdminController@index');

	});

});






