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
            <h1 class="m-0 text-dark">Daftar Dosen</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
              <li class="breadcrumb-item active">Daftar Dosen</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <a href="{{ url('admin/master/dosen/tambah') }}" class="btn btn-info" role="button">Tambah Data</a>
        <br><br>
        <!-- Small boxes (Stat box) -->
        <table class="table table-bordered table-striped">
          <thead>
            <tr> 
              <th width="1%">No.</th>
              <th width="1%">NPK</th>
              <th width="1%">Nama</th>
              <th width="1%">Jenis Kelamin</th>
              <th width="1%">Email</th>
              <th width="1%">Telepon</th>
              <th width="1%">Status</th>
              <th width="1%">Kode Jurusan</th>
              <th width="1%">Username</th>     
              <th width="1%">Action</th>
                     
            </tr>
          </thead>
          <tbody>
            @foreach($dosen as $no => $d)
            <tr>
              <td>{{$no+1}}</td>
              <td>{{$d->npkdosen}}</td>
              <td>{{$d->namadosen}}</td>
              <td>{{$d->jeniskelamin}}</td>
              <td>{{$d->email}}</td>
              <td>{{$d->telepon}}</td>
              <td>{{$d->status}}</td>
              <td>{{$d->kode_jurusan}}</td>
              <td>{{$d->users_username}}</td>
              <td>
                Untuk Button
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
          <br/>
        Halaman : {{$dosen->currentPage()}} <br/>
        Jumlah Data : {{$dosen->total()}} <br/>
        Data Per Halaman : {{$dosen->perPage()}} <br/>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>

@endsection

@push('scripts')
@endpush