@foreach($data_konsultasi as $d)
	<h3>{{$d->judul}}</h3>

	<p style="font-family: times-new-roman;font-size: 13px">Kepada Seluruh Bapak/Ibu Dosen dan Mahasiswa Teknik Informatika (IF/SI/MM/IT-DD) Universitas Surabaya <br>
	Di beritahukan untuk konsultasi / bimbingan dosen wali akan dilaksanakan sebagai berikut: <br>
	Waktu Pelaksanaan : [{{$d->tanggalmulai}}] s/d [{{$d->tanggalberakhir}}]
	<br>
	Keterangan:<br>
	{{$d->keterangan}} <br><br>

	Untuk seluruh mahasiswa diharapkan dapat melakukan konsultasi bimbingan dosen wali secara tepat waktu, berdasarkan waktu yang telah ditentukan. <br>
	Atas perhatiannya kami sampaikan terima kasih.
	<br><br>
	
	<p style="font-weight:bold; font-size: 12px">NB: Konsultasi dosen wali hanya dapat dilakukan pada saat hari kerja, konsultasi dosen wali selain hari tergantung dari kebijakan yang diberikan oleh dosen wali masing-msaing. </p>

	</p>

	<a href="https://if.ubaya.ac.id/" style="font-weight: bold; text-align: center;	">Jurusan Teknik Informatika - Universitas Surabaya</a>
@endforeach