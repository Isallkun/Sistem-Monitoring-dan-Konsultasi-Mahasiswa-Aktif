<!--Wajib untuk inisialisasi file views/layouts/appadmin-->
@extends('layouts.appketuajurusan')

@push('styles')
  <!-- Untuk menambahkan style baru -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  
  <style type="text/css">
  .checked {
    color: orange;
  }
  </style>
@endpush

<!-- Isi dari yield -->
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Daftar Data Mahasiswa</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('ketuajurusan')}}">Home</a></li>
              <li class="breadcrumb-item active">Daftar Mahasiswa</li>
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
   
    
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <div style="font-size: 12px">
          <b>Keterangan:</b>
          <br>
          <a href="#" class="btn btn-danger btn-sm"></a> 
          Keadaan mahasiswa membutuhkan perhatian lebih/khusus. 
          <br>
          <a href="#" class="btn btn-warning btn-sm"></a> 
          Keadaan mahasiswa dalam proses pemantauan/pengawasan. 
          <br>
          <a href="#" class="btn btn-success btn-sm"></a> 
          Keadaan mahasiswa dalam kondisi cukup baik.
        </div>
        <br><br>

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Mahasiswa</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table id="tabel_mahasiswa" class="table table-bordered table-striped">
              <thead>
                <tr> 
                  <th width="1%">No.</th>
                  <th width="1%">Nama</th>
                  <th width="1%">NRP</th>
                  <th width="1%">Angkatan</th>
                  <th width="1%">Kondisi</th>
                  <th width="2%">Rate</th>
                  <th width="1%">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($mahasiswa as $no => $m)
                <tr>
                    <td>{{$no+1}}</td>
                    <td>{{$m->namamahasiswa}}</td>
                    <td>{{$m->nrpmahasiswa}}</td>
                    <td>{{$m->tahun}}</td>
                    <td>
                     @if($m->flag == 0)
                     <i class="btn btn-success btn-sm">Normal</i>
                     @elseif($m->flag == 1)
                      <i class="btn btn-warning btn-sm">Waspada</i>
                     @else
                      <i class="btn btn-danger btn-sm">Kurang</i>
                     @endif
                    </td>
                    <td>
                      @for($i=0; $i < $m->total; $i++)
                        <span class="fa fa-star checked" ></span>
                      @endfor
                      @for($i=0; $i < (5-$m->total); $i++)
                        <span class="fa fa-star"></span>
                      @endfor 
                    </td>
                    <td>
                     <a href="{{url('ketuajurusan/submaster/mahasiswa/detail/'.$m->nrpmahasiswa)}}" class="btn btn-primary">Detail</a>
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
    $('#tabel_mahasiswa').DataTable({
      "dom": '<"pull-right"f><"pull-left"l>tip'
    });
  });
</script>
@endpush