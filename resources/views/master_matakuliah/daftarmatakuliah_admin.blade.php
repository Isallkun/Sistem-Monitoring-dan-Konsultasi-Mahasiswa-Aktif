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
            <h1 class="m-0 text-dark">Daftar Data Mata Kuliah</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
              <li class="breadcrumb-item active">Daftar Data Matakuliah</li>
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
        <a href="{{ url('admin/master/matakuliah/tambah') }}" class="btn btn-primary" role="button">Tambah Data</a>
        <br><br>

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Mata Kuliah</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table id="tabel_matakuliah" class="table table-bordered table-striped">
              <thead>
                <tr> 
                  <th>No.</th>
                  <th>Kode Matakuliah</th>
                  <th>Nama Matakuliah</th>
                  <th>SKS</th>
                  <th>Total Pertemuan</th>
                  <th>Nisbi Minimal</th>
                  <th>Tahun Akademik</th>
                </tr>
              </thead>
              <tbody>
                @foreach($matakuliah as $no => $m)
                <tr>
                  <td>{{$no+1}}</td>
                  <td>{{$m->kodematakuliah}}</td>
                  <td>{{$m->namamatakuliah}}</td>
                  <td>{{$m->sks}}</td>
                  <td>{{$m->totalpertemuan}}</td>
                  <td>{{$m->nisbimin}}</td>
                  <td>{{$m->semester}} {{$m->tahun}}</td>
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
<!-- page script -->
<script>
  $(function () {
    $('#tabel_matakuliah').DataTable({
      "dom": '<"pull-right"f><"pull-left"l>tip'
    });
  });
</script>
@endpush