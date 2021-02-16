<html>
<head>
	<title>Laporan Hasil Konsultasi Dosen Wali</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 9pt;
		}
	</style>

	<table>
		<tr>
			<td align="center">
				<img src="logo_univeristas_surabaya.jpg" style="width: 120px;height: 120px;">
			</td>
			<td align="center">
				<div style="margin-left: 50px">
					<h5>LAPORAN HASIL BIMBINGAN DOSEN WALI</h5> 
					<p style="font-family: times new roman;font-weight: bold;font-size: 15px">Jurusan Teknik Informatika  Universitas Surabaya</p>
				</div>
			</td>

			
		</tr>
	</table>
	
	<center>
		
		<br>
	</center>
	<p style="font-family: times new roman; font-size: 13px; font-weight: bold;">
	@foreach($mhs as $m)
	Nama Mahasiswa : {{$m->namamahasiswa}}
	<br>
	NRP Mahasiswa &nbsp; : {{$m->nrpmahasiswa}}
	@endforeach
	</p>
 
	<table class='table table-bordered'>
		<thead>
			<tr>
				<th width="1%">No.</th>
				<th width="1%">Tanggal Konsultasi</th>
				<th width="1%">Tahun Akademik</th>
				<th width="1%">Topik</th>
				<th width="1%">Permasalahan</th>
				<th width="1%">Solusi</th>
				<th width="1%">Dosen Wali</th>
			</tr>
		</thead>
		<tbody>
			@foreach($konsultasi as $no =>$k)
                <tr>
                  <td>{{$no+1}}</td>
                  <td>{{$k->tanggalkonsultasi}}</td>
                  <td>{{$k->semester}} {{$k->tahun}}</td>
                  <td>{{$k->namatopik}}</td>
                  <td>{{$k->permasalahan}}</td>
                  <td>{{$k->solusi}}</td>
                  <td>{{$k->namadosen}} {{$k->npkdosen}}</td>
                </tr>
                @endforeach
		</tbody>
	</table>
 
</body>
</html>