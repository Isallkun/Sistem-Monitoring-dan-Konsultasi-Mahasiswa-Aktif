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
Route::get('/', 'HomeController@index');
Route::get('logout', 'Auth\LoginController@logout')->name('keluar');

Auth::routes();

Route::group(['middleware' => ['auth','revalidate']], function ()
{
	Route::get('/', 'HomeController@index'); ## route yang perlu auth
});

Route::group(['prefix' => '/' ], function()
{
  	//localhost:8000/admin/
	Route::group(['prefix' => 'admin'], function()
	{
		//localhost:8000/admin/(halaman home admin)
		Route::get('/', 'HomeController@index');
		
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

		//Pencarian data Dosen
		Route::get('master/dosen/prosescari', 'MasterDosenController@caridosen');
		//Suggestion pencarian data dosen
		Route::post('master/dosen/fetch', 'MasterDosenController@fetch')->name('masterdosen.fetch');


		//2. MASTER MAHASISWA
		//localhost:8000/admin/mahasiswa
		Route::get('master/mahasiswa', 'MasterMahasiswaController@daftarmahasiswa');

		//Tambah data mahasiswa
		Route::get('master/mahasiswa/tambah', 'MasterMahasiswaController@tambahmahasiswa');
		Route::post('master/mahasiswa/prosestambah', 'MasterMahasiswaController@tambahmahasiswa_proses');
		
		//Ubah data mahasiswa
		Route::get('master/mahasiswa/ubah/{id}', 'MasterMahasiswaController@ubahmahasiswa');
		Route::post('master/mahasiswa/ubahproses', 'MasterMahasiswaController@ubahmahasiswa_proses');

		//Hapus data mahasiswa
		Route::get('master/mahasiswa/hapus/{id}', 'MasterMahasiswaController@hapusmahasiswa');

		//Pencarian data Mahasiswa
		Route::get('master/mahasiswa/prosescari', 'MasterMahasiswaController@carimahasiswa');
		//Suggestion pencarian data dosen
		Route::post('master/mahasiswa/fetch', 'MasterMahasiswaController@fetch')->name('mastermahasiswa.fetch');


		//3. MASTER MATAKULIAH
		//localhost:8000/admin/matakuliah
		Route::get('master/matakuliah', 'MasterMatakuliahController@daftarmatakuliah');

		//Tambah data matakuliah
		Route::get('master/matakuliah/tambah', 'MasterMatakuliahController@tambahmatakuliah');
		Route::post('master/matakuliah/prosestambah', 'MasterMatakuliahController@tambahmatakuliah_proses');

		//Ubah data matakuliah
		// Route::get('master/matakuliah/ubah/{id}', 'MasterMatakuliahController@ubahmatakuliah');
		// Route::post('master/matakuliah/ubahproses', 'MasterMatakuliahController@ubahmatakuliah_proses');

		//Hapus data matakuliah
		//Route::get('master/matakuliah/hapus/{id}', 'MasterMatakuliahController@hapusmatakuliah');

		//Pencarian data Matakuliah
		Route::get('master/matakuliah/prosescari', 'MasterMatakuliahController@carimatakuliah');
		//Suggestion pencarian data matakuliah
		Route::post('master/matakuliah/fetch', 'MasterMatakuliahController@fetch')->name('mastermatakuliah.fetch');


		//4. MASTER KONSULTASI
		//localhost:8000/admin/konsultasi
		Route::get('master/konsultasi', 'MasterKonsultasiController@daftarkonsultasi');

		//Tambah data konsultasi
		Route::get('master/konsultasi/tambah', 'MasterKonsultasiController@tambahkonsultasi');
		Route::post('master/konsultasi/prosestambah', 'MasterKonsultasiController@tambahkonsultasi_proses');

		//Ubah data konsultasi
		Route::get('master/konsultasi/ubah/{id}', 'MasterKonsultasiController@ubahkonsultasi');
		Route::post('master/konsultasi/ubahproses', 'MasterKonsultasiController@ubahkonsultasi_proses');

		//Hapus data matakuliah
		Route::get('master/konsultasi/hapus/{id}', 'MasterKonsultasiController@hapuskonsultasi');

		//Pencarian data konsultasi
		Route::get('master/konsultasi/prosescari', 'MasterKonsultasiController@carikonsultasi');
		//Suggestion pencarian data konsultasi
		Route::post('master/konsultasi/fetch', 'MasterKonsultasiController@fetch')->name('masterkonsultasi.fetch');
		
	});

	//localhost:8000/dosen/
	Route::group(['prefix' => 'dosen'], function()
	{
		Route::get('/', 'HomeDosenController@index');


	});

});






