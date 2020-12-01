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
            <h1 class="m-0 text-dark">Detail Hukuman</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('dosen')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{url('dosen/data/hukuman')}}">Daftar Data Hukuman</a></li>
              <li class="breadcrumb-item active">Detail Data Hukuman</li>
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
    
    
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Hukuman Mahasiswa</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0" style="height: 50%;">
                @foreach($data_hukuman as $d)
                <table class="table table-head-fixed text-nowrap">
                  <tr>
                    <th>ID Hukuman</th>
                    <td>{{$d->idhukuman}}</td>
                  </tr>
                  <tr>
                    <th>Mahasiswa</th>
                    <td>{{$d->namamahasiswa}} - {{$d->nrpmahasiswa}}</td>
                  </tr>
                  <tr>
                    <th>Tanggal Input</th>
                    <td>{{$d->tanggalinput}}</td>
                  </tr>
                  <tr>
                    <th>Keterangan </th>
                    <td>{{$d->keterangan}}</td>
                  </tr>
                  <tr>
                    <th>Tanggal Konfirmasi </th>
                    <td>{{$d->tanggalkonfirmasi}}</td>
                  </tr>
                  <tr>
                    <th>Masa Berlaku </th>
                    <td>{{$d->masaberlaku}}</td>
                  </tr>
                  <tr>
                    <th>Download</th>
                    <td>
                      @foreach($data_detail_hukuman as $no => $b)
                        @if(!empty($b->berkas))
                          <a href="{{url('data_hukuman/'.$b->berkas)}}" class="">
                            <i class="fas fa-file-pdf fa-2x"></i>
                            <br>
                            {{$b->berkas}}
                          </a>
                          <br><br>
                        @else
                          -
                        @endif
                      @endforeach
                    </td>
                    <br>
                  </tr>
                </table>
                @endforeach
              </div>
              <div class="card-footer">
                <a href="{{url('dosen/data/hukuman')}}" class="btn btn-default pull-right">Kembali</a>
              </div>
              <!-- /.card-body -->
            </div>
            
            <!-- /.card -->
          </div>
        </div>
      </div>
    </section>
@endsection
 
@push('scripts')

@endpush