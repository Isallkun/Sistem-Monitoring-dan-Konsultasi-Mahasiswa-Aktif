<!--Wajib untuk inisialisasi file views/layouts/appadmin-->
@extends('layouts.appadmin')

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
            <h1 class="m-0 text-dark">Daftar Data Notifikasi Konsultasi</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
              <li class="breadcrumb-item active">Daftar Notifikasi Konsultasi</li>
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
            <li>{!! \Session::get('Success') !!}</li>
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
        <a href="{{ url('admin/master/notifikasi/tambah') }}" class="btn btn-primary" role="button">Tambah Data</a>
        <br><br>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Notifikasi Konsultasi</h3>
          </div>  
          <div class="card-body">
            <a style="float: right;" href="{{ url('admin/master/notifikasi/remind')}}" class="btn btn-primary btn-sm fas fa-envelope" role="button"> Notif Mahasiswa</a>

            <br><br>      
            <table id="tabel_notifikasi_konsultasi" class="table table-bordered table-striped">
              <thead>
                <tr> 
                  <th width="1%">No.</th>
                  <th width="1%">Judul</th>
                  <th width="1%">Tanggal Input</th>
                  <th width="1%">Status Kirim</th>
                  <th width="1%">Tanggal Mulai</th>
                  <th width="1%">Tanggal Berakhir</th>
                  <th width="1%">Keterangan</th>
                  <th width="1%">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($jadwalkonsultasi as $no => $jdwl)
                <tr>
                  <td>{{$no+1}}</td>
                  <td>{{$jdwl->judul}}</td>
                  <td>{{$jdwl->tanggalinput}}</td>
                  <td>
                    @if($jdwl->statuskirim == 0)
                      <a href="#" class="btn btn-warning btn-sm">Menunggu...</a>
                    @else
                      <a href="#" class="btn btn-success btn-sm">Terkirim</a>  
                    @endif
                  </td>

               
                  <td>{{$jdwl->tanggalmulai}}</td>
                  <td>{{$jdwl->tanggalberakhir}}</td>
                  <td>
                    @if($jdwl->keterangan=="")
                      -
                    @else
                      {{$jdwl->keterangan}}
                    @endif
                  </td>
                  
                  <td>
                    @if($jdwl->statuskirim == 0)
                      <form method="get" action="{{url('admin/master/notifikasi/hapus/'.$jdwl->idjadwalkonsultasi)}}">
                      <input type="hidden" name="idjadwalkonsultasi" value="">
                      <button type="submmit" class="btn btn-danger">Hapus</button>
                    </form> 
                    @endif  
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
    $('#tabel_notifikasi_konsultasi').DataTable({
      "dom": '<"pull-right"f><"pull-left"l>tip'
    });
  });

  setTimeout(function() {
   location.reload();
   }, 5000); 
</script>
@endpush