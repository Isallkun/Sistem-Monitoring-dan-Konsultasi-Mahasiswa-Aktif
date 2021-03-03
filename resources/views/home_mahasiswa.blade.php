<!--Wajib untuk inisialisasi file views/layouts/appadmin-->
@extends('layouts.appmahasiswa')

@push('styles')
  <!-- Untuk menambahkan style baru -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.bundle.js"></script>
@endpush

<!-- Isi dari yield -->
@section('content')

<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard Mahasiswa</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('mahasiswa/')}}">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{$menunggu_konfirmasi}} / {{$konsultasi_mahasiswa}}</h3>

                <p>Konsultasi Dosen Wali</p>
              </div>
              <div class="icon">
                <i class="ion ion-archive"></i>
              </div>
              <a href="{{url('mahasiswa/data/konsultasimahasiswa')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{$hukuman_mahasiswa}}</h3>

                <p>Total Hukuman</p>
              </div>
              <div class="icon">
                <i class="ion ion-ios-cog"></i>
              </div>
              <a href="{{url('mahasiswa/data/hukumanmahasiswa')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{$konsultasi_berikutnya}}</h3>

                <p>Jadwal Konsultasi</p>
              </div>
              <div class="icon">
                <i class="ion ion-calendar"></i>
              </div>
                <a href="{{url('mahasiswa/data/konsultasimahasiswa')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{$nonkonsultasi_berikutnya}}</h3>

                <p>Jadwal Non-Konsultasi</p>
              </div>
              <div class="icon">
                <i class="ion ion-ios-alarm"></i>
              </div>
              <a href="{{url('mahasiswa/data/nonkonsultasimahasiswa')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>

        <div class="row">
        <!-- ISI HALAMAN -->

        <div class="card" >
          <div class="card-header">
            <h3 class="card-title">Data Konsultasi Dosen Wali</h3>
          </div>  
          <div class="card-body">
            <table id="tabel_konsultasi" class="table table-bordered table-striped">
              <thead>
                @foreach($mahasiswa as $m)
                <form action="{{url('mahasiswa/data/cetakkonsultasi/'.$m->nrpmahasiswa)}}" role="form" method="get">
                {{ csrf_field() }}
                  <button type="submit" class="btn btn-primary pull-right">Cetak Konsultasi (.pdf)</button>
                </form>
                @endforeach
                <br><br>
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
                @foreach($data_konsultasi_mhs as $no =>$d)
                <tr>
                  <td>{{$no+1}}</td>
                  <td>{{$d->tanggalkonsultasi}}</td>
                  <td>{{$d->semester}} {{$d->tahun}}</td>
                  <td>{{$d->namatopik}}</td>
                  <td>{{$d->permasalahan}}</td>
                  <td>{{$d->solusi}}</td>
                  <td>{{$d->namadosen}} {{$d->npkdosen}}</td>
                </tr>
                @endforeach
              </tbody>
            </table>  
          </div>

        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@endsection


@push('scripts')
<!-- Untuk Menambahkan script baru -->

@endpush