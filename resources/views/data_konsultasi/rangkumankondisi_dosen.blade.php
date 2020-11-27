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
            <h1 class="m-0 text-dark">Rangkuman Kondisi Mahasiswa</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('dosen')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{url('dosen/data/tambah')}}">Tambah Data Konsultasi</a></li>
              <li class="breadcrumb-item active">Rangkuman Kondisi Mahasiswa</li>
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
        

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">{{$konsultasi_mhs[0]->namamahasiswa}} ({{$konsultasi_mhs[0]->nrpmahasiswa}})</h3>
          </div>  
          <div class="card-body">
            <table class="table table-bordered table-striped">
              <thead>
                <tr> 
                  <th width="1%">No.</th>
                  <th width="1%">Tanggal Konsultasi</th>
                  <th width="1%">Tahun Akademik</th>
                  <th width="1%">Topik</th>
                  <th width="1%">Permasalahan</th>
                  <th width="1%">Solusi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($konsultasi_mhs as $no => $k)
                <tr>
                  <td>{{$no+1}}</td>
                  <td>{{$k->tanggalkonsultasi}}</td>
                  <td>{{$k->semester}} {{$k->tahun}}</td>
                  <td>{{$k->namatopik}}</td>
                  <td>{{$k->permasalahan}}</td>
                  <td>{{$k->solusi}}</td>
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

@endpush