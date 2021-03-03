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
		Route::get('/', 'HomeController@index_admin');
		
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


		//3. MASTER MATAKULIAH
		//localhost:8000/admin/matakuliah
		Route::get('master/matakuliah', 'MasterMatakuliahController@daftarmatakuliah');

		//Tambah data matakuliah
		Route::get('master/matakuliah/tambah', 'MasterMatakuliahController@tambahmatakuliah');
		Route::post('master/matakuliah/prosestambah', 'MasterMatakuliahController@tambahmatakuliah_proses');


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


		//5. MASTER NOTIFIKASI
		//localhost:8000/admin/notifikasi
		Route::get('master/notifikasi', 'MasterNotifikasiController@daftarnotifikasi');

		//Tambah data notifikasi
		Route::get('master/notifikasi/tambah', 'MasterNotifikasiController@tambahnotifikasi');
		Route::post('master/notifikasi/prosestambah', 'MasterNotifikasiController@tambahnotifikasi_proses');

		//Hapus data notifikasi
		Route::get('master/notifikasi/hapus/{id}', 'MasterNotifikasiController@hapusnotifikasi');

		Route::get('master/notifikasi/remind', 'MasterNotifikasiController@remind_mahasiswa');
		
	});

	//localhost:8000/dosen/
	Route::group(['prefix' => 'dosen'], function()
	{
		//localhost:8000/dosen/(halaman home dosen)
		Route::get('/', 'HomeController@index_dosen');
		//Menampilkan hasil pencarian mata kuliah
		Route::get('tampilkanmatakuliah', 'HomeController@tampilkan_matakuliah');

		//1. DATA MAHASISWA
		//localhost:8000/dosen/data/mahasiswa
		Route::get('data/mahasiswa', 'DataMahasiswaController@daftarmahasiswa');

		//Ubah Flag
		Route::get('data/mahasiswa/ubahflag/{id}', 'DataMahasiswaController@ubahflag');

		//Menampilkan Detail Mahasiswa
		Route::get('data/mahasiswa/detail/{id}', 'DataMahasiswaController@detailmahasiswa')->name('detail');
		//Mencari data Kartu Studi Mahasiswa
		Route::get('data/mahasiswa/prosescari/', 'DataMahasiswaController@carikartustudi');


		//2. DATA KONSULTASI
		//localhost:8000/dosen/data/konsultasi
		Route::get('data/konsultasi','DataKonsultasiController@daftarkonsultasi');

		//Tambah data konsultasi
		Route::get('data/konsultasi/tambah', 'DataKonsultasiController@tambahkonsultasi');
		Route::post('data/konsultasi/prosestambah', 'DataKonsultasiController@tambahkonsultasi_proses');

		//Menampilkan rangkuman kondisi
		Route::get('data/konsultasi/rangkumankondisi/{id}', 'DataKonsultasiController@kondisi');
		//Tambah data rating
		Route::post('data/rating/prosestambah/{id}', 'DataKonsultasiController@tambahrating_proses');

		//Ubah data konsultasi
		Route::get('data/konsultasi/ubah/{id}', 'DataKonsultasiController@ubahkonsultasi');
		Route::post('data/konsultasi/ubahproses', 'DataKonsultasiController@ubahkonsultasi_proses');


		//3. DATA HUKUMAN
		//localhost:8000/dosen/data/hukuman
		Route::get('data/hukuman','DataHukumanController@daftarhukuman');
		
		//Ubah penilaian hukuman
		Route::get('data/hukuman/ubahnilai/{id}', 'DataHukumanController@ubahnilai');

		//Tampilkan Berkas hukuman
		Route::get('data/hukuman/detailhukuman/{id}', 'DataHukumanController@detailhukuman');
		//Unduh Berkas
		Route::post('data/hukuman/prosesunduh/{id}', 'DataHukumanController@unduhberkas_proses');


		//Tambah data hukuman
		Route::get('data/hukuman/tambah', 'DataHukumanController@tambahhukuman');
		Route::post('data/hukuman/prosestambah', 'DataHukumanController@tambahhukuman_proses');

		//Ubah data hukuman
		Route::get('data/hukuman/ubah/{id}', 'DataHukumanController@ubahhukuman');
		Route::post('data/hukuman/ubahproses', 'DataHukumanController@ubahhukuman_proses');

		//Hapus data hukuman
		Route::get('data/hukuman/hapus/{id}', 'DataHukumanController@hapushukuman');


		//4. PROFILE DOSEN
		//Menampilkan halaman profile dosen
		Route::get('profil/profildosen', 'profildosenController@profil_dosen');
		//Ubah Halaman Profile Dosen
		Route::post('profil/profildosen/ubahproses', 'profildosenController@ubahprofildosen_proses');	


		//5. DATA NON KONSULTASI
		//localhost:8000/dosen/data/non konsultasi
		Route::get('data/nonkonsultasi','DataNonKonsultasiController@daftarnonkonsultasi');

		//Tambah data non konsultasi
		Route::get('data/nonkonsultasi/tambah', 'DataNonKonsultasiController@tambahnonkonsultasi');
		Route::post('data/nonkonsultasi/prosestambah', 'DataNonKonsultasiController@tambahnonkonsultasi_proses');

		//Ubah data non-konsultasi
		Route::get('data/nonkonsultasi/ubah/{id}', 'DataNonKonsultasiController@ubahnonkonsultasi');
		Route::post('data/nonkonsultasi/ubahproses', 'DataNonKonsultasiController@ubahnonkonsultasi_proses');

		//Hapus data non konsultasi
		Route::get('data/nonkonsultasi/hapus/{id}', 'DataNonKonsultasiController@hapusnonkonsultasi');
	
	});

	//localhost:8000/mahasiswa/
	Route::group(['prefix' => 'mahasiswa'], function()
	{
		//localhost:8000/mahasiswa/(halaman home mahasiswa)
		Route::get('/', 'HomeController@index_mahasiswa');
		//Cetak hasil konsutasi (.pdf)
		Route::get('data/cetakkonsultasi/{id}', 'HomeController@cetak_konsultasi');

		//1. DATA HUKUMAN 
		//localhost:8000/mahasiswa/data/hukuman
		Route::get('data/hukumanmahasiswa','DataHukumanController@daftarhukuman_mahasiswa');
		//Unggah berkas hukuman 
		Route::post('data/hukumanmahasiswa/prosesunggah/{id}', 'DataHukumanController@unggahberkas_proses');
		//Unduh berkas
		Route::post('data/hukumanmahasiswa/prosesunduh/{id}', 'DataHukumanController@unduhberkas_proses');

		//2. DATA KONSULTASI
		//menampilkan daftar konsultasi mahasiswa 
		Route::get('data/konsultasimahasiswa','DataKonsultasiController@daftarkonsultasi_mahasiswa');
		//Konfirmasi konsultasi
		Route::get('data/konsultasimahasiswa/proseskonfirmasi/{id}', 'DataKonsultasiController@konfirmasikonsultasi_proses');

		//4. PROFIL MAHASISWA
		//Menampilkan halaman profil mahasiswa
		Route::get('profil/profilmahasiswa', 'profilmahasiswaController@profil_mahasiswa');
		//Ubah halaman profil mahasiswa
		Route::post('profil/profilmahasiswa/ubahproses', 'profilmahasiswaController@ubahprofilmahasiswa_proses');	

		//5. DATA NON KONSULTASI
		//localhost:8000/dosen/data/non konsultasi
		Route::get('data/nonkonsultasimahasiswa','DataNonKonsultasiController@daftarnonkonsultasi_mahasiswa');

	});

});






