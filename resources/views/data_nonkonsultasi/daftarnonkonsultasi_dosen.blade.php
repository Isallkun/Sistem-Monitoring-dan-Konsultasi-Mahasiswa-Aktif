<!--Wajib untuk inisialisasi file views/layouts/appadmin-->
@extends('layouts.appdosen')

@push('styles')
  <!-- Untuk menambahkan style baru -->
@endpush

<!-- Isi dari yield -->
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Daftar Data Konsultasi Tidak Terjadwal</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('dosen')}}">Home</a></li>
              <li class="breadcrumb-item active">Daftar Data Tidak Terjadwal</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    @if (count($errors) > 0)
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if (\Session::has('Success'))
      <div class="alert alert-success alert-block">
        <ul>
            <li>
              {!! \Session::get('Success') !!} <br>
              <a href="{!! \Session::get('url') !!}" target="_blank" class="fab fa-whatsapp"> Click WhatsApp link!</a>

              <script type="text/javascript">
                window.onload = function(event) {
                  window.open("{!! \Session::get('url') !!}", '_blank');
                };  
              </script>
            </li>
        </ul>
      </div>
    @endif

    @if (\Session::has('Error'))
      <div class="alert alert-danger alert-block">
        <ul>
            <li>{!! \Session::get('Error') !!}</li>
        </ul>
      </div>
    @endif
    
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <a href="{{url('dosen/data/nonkonsultasi/tambah')}}" class="btn btn-primary" role="button">Tambah Data</a>
        <br><br>

         @if(!empty($non_konsultasi_berikutnya))
          <div class="alert alert-primary">
            <p style="font-weight: bold">Informasi Jadwal Konsultasi Tidak Terjadwal: </p>
            @foreach($non_konsultasi_berikutnya as $no => $n)
            ({{$no+1}}). Tanggal {{$n->tanggalpertemuan}},   {{$n->namamahasiswa}} ({{$n->nrpmahasiswa}}) akan melakukan bertemu dengan anda.
            <br>
            @endforeach
          </div>
        @endif

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Non-Konsultasi</h3>
          </div>  
          <div class="card-body">
            <a style="float: right;" href="{{url('dosen/data/nonkonsultasi/broadcast')}}" class="btn btn-warning btn-sm fa fa-bullhorn" role="button"> Broadcast Informasi</a>
            <br><br>
            <table id="tabel_nonkonsultasi" class="table table-bordered table-striped">
              <thead>
                <tr> 
                  <th width="1%">No.</th>
                  <th width="1%">Tanggal Input</th>
                  <th width="1%">Tanggal Pertemuan</th>
                  <th width="1%">Status</th>
                  <th width="1%">Pesan</th>
                  <th width="1%">Mahasiswa</th>
                  <th width="1%">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($data_non_konsultasi as $no => $dn)
                <tr>
                  <td>{{$no+1}}</td>
                  <td>{{$dn->tanggalinput}}</td>
                  <td>{{$dn->tanggalpertemuan}}</td>
                  <td>
                    @if($dn->status == "0")
                    <i class="btn btn-danger btn-sm"> Dalam proses...</i>
                    @else
                    <i class="btn btn-success btn-sm"> Selesai</i>
                    @endif
                  </td>
                  <td>{{$dn->pesan}}</td>
                  <td>{{$dn->namamahasiswa}} <br>({{$dn->nrpmahasiswa}})</td>
                  <td>
                    @if($dn->status == "0")
                    <a href="{{url('dosen/data/nonkonsultasi/ubah/'.$dn->idnonkonsultasi)}}" class="btn btn-warning">Ubah</a>
                    @endif

                    <form method="get" action="{{url('dosen/data/nonkonsultasi/hapus/'.$dn->idnonkonsultasi)}}">
                      <button type="submmit" class="btn btn-danger">Hapus</button>
                    </form>

                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>  
          </div>
        </div>
        
       
      </div>
    </section>
@endsection
 
@push('scripts')
<script>
  $(function () {
    $('#tabel_nonkonsultasi').DataTable({
      "dom": '<"pull-right"f><"pull-left"l>tip'
    });
  });
</script>
@endpush