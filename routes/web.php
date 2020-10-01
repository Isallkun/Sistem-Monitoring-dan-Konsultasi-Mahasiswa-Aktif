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
	Route::get('/', 'HomeAdminController@index'); ## route yang perlu auth
});

Route::group(['prefix' => '/' ], function()
{
  	//localhost:8000/admin/
	Route::group(['prefix' => 'admin'], function()
	{
		//localhost:8000/admin/(halaman home admin)
		Route::get('/', 'HomeAdminController@index');
		
		//1. MASTER DOSEN
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

		//Pencarian Data Dosen
		Route::get('master/dosen/prosescari', 'MasterDosenController@caridosen');
		//Suggestion pencarian data dosen
		Route::post('master/dosen/fetch', 'MasterDosenController@fetch')->name('masterdosen.fetch');

		//2. MASTER MAHASISWA
		//localhost:8000/admin/dosen
		Route::get('master/mahasiswa', 'MasterMahasiswaController@daftarmahasiswa');

		//Tambah data mahasiswa
		Route::get('master/mahasiswa/tambah', 'MasterMahasiswaController@tambahmahasiswa');
		Route::post('master/mahasiswa/prosestambah', 'MasterMahasiswaController@tambahmahasiswa_proses');
		
		//Ubah data mahasiswa
		Route::get('master/mahasiswa/ubah/{id}', 'MasterMahasiswaController@ubahmahasiswa');
		Route::post('master/mahasiswa/ubahproses', 'MasterMahasiswaController@ubahmahasiswa_proses');

		//Hapus data mahasiswa
		Route::get('master/mahasiswa/hapus/{id}', 'MasterMahasiswaController@hapusmahasiswa');

	});

	//localhost:8000/dosen/
	Route::group(['prefix' => 'dosen'], function()
	{
		Route::get('/', 'HomeAdminController@index');

	});

});






