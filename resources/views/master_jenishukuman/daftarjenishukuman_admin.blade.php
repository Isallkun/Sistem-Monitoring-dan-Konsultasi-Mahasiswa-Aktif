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
            <h1 class="m-0 text-dark">Daftar Data Jenis Hukuman</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
              <li class="breadcrumb-item active">Daftar Data Jenis Hukuman</li>
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
        <a href="{{ url('admin/master/jenishukuman/tambah') }}" class="btn btn-primary" role="button">Tambah Data</a>
        <br><br>

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Jenis Hukuman</h3>
          </div>  
          <div class="card-body">
            <table id="tabel_jenishukuman" class="table table-bordered table-striped">
              <thead>
                <tr> 
                  <th width="1%">No.</th>
                  <th width="1%">Nama Hukuman</th>
                  <th width="1%">Kategori</th>
                  <th width="1%">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($jenishukuman as $no => $j)
                <tr>
                  <td>{{$no+1}}</td>
                  <td>{{$j->namahukuman}}</td>
                  <td>
                    @if($j->kategori == 'ringan')
                    <a href="#" class="btn btn-success btn-sm">Kategori Ringan</a>
                    @elseif($j->kategori == 'sedang')
                    <a href="#" class="btn btn-warning btn-sm">Kategori Sedang</a>
                    @else
                     <a href="#" class="btn btn-danger btn-sm">Kategori Berat</a>
                    @endif
                  </td>
                  
                  <td>
                    <a href="{{url('admin/master/jenishukuman/ubah/'.$j->idjenishukuman)}}" class="btn btn-warning">Ubah</a>
                    
                    <form method="get" action="{{url('admin/master/jenishukuman/hapus/'.$j->idjenishukuman)}}">
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
    $('#tabel_jenishukuman').DataTable({
      "dom": '<"pull-right"f><"pull-left"l>tip'
    });
  });
</script>
@endpush