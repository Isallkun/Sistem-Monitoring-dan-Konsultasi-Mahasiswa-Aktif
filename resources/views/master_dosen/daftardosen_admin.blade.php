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
            <h1 class="m-0 text-dark">Daftar Data Dosen</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
              <li class="breadcrumb-item active">Daftar Data Dosen</li>
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

    @if (\Session::has('Failed'))
      <div class="alert alert-warning alert-block">
        <ul>
            <li>{!! \Session::get('Failed') !!}</li>
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
        <a href="{{ url('admin/master/dosen/tambah') }}" class="btn btn-primary" role="button">Tambah Data</a>
        <br><br>

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Dosen Wali</h3>
          </div>

          <div class="card-body">
            <table id="tabel_dosen" class="table table-bordered table-striped" >
              <thead>
                <tr> 
                  <th>NPK</th>
                  <th>Nama</th>
                  <th>Jenis Kelamin</th>
                  <th>Email</th>
                  <th>Telepon</th>
                  <th>Status</th>
                  <th>Jurusan</th>
                  <th>Username</th>     
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($dosen as $no => $d)
                <tr>
                  <td>{{$d->npkdosen}}</td>
                  <td>{{$d->namadosen}}</td>
                  <td>{{$d->jeniskelamin}}</td>
                  <td>{{$d->email}}</td>
                  <td>{{$d->telepon}}</td>
                  <td>{{$d->status}}</td>
                  <td>{{$d->namajurusan}}</td>
                  <td>{{$d->users_username}}</td>
                  <td>
                    <a href="{{url('admin/master/dosen/ubah/'.$d->npkdosen)}}" class="btn btn-warning">Ubah</a>

                    <form method="get" action="{{url('admin/master/dosen/hapus/'.$d->npkdosen)}}">
                      <input type="hidden" name="username" value="{{$d->users_username}}">
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
    $('#tabel_dosen').DataTable({
        "dom": '<"pull-right"f><"pull-left"l>tip'
    });
  });

</script>
@endpush