<!--Wajib untuk inisialisasi file views/layouts/appadmin-->
@extends('layouts.appadmin')

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
            <h1 class="m-0 text-dark">Dashboard Administrator</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin/')}}">Home</a></li>
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
                <h3>{{$dosen_aktif}}</h3>

                <p>Total Dosen Aktif</p>
              </div>
              <div class="icon">
                <i class="ion ion-person"></i>
              </div>
              <a href="{{url('admin/master/dosen')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{$mahasiswa_aktif}}</h3>

                <p>Total Mahasiswa Aktif</p>
              </div>
              <div class="icon">
                <i class="ion ion-ios-people"></i>
              </div>
              <a href="{{url('admin/master/mahasiswa')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{$matakuliah}}</h3>

                <p>Total Matakuliah</p>
              </div>
              <div class="icon">
                <i class="ion ion-ios-book"></i>
              </div>
              <a href="{{url('admin/master/matakuliah')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{$konsultasi}}</h3>

                <p>Total Konsultasi</p>
              </div>
              <div class="icon">
                <i class="ion ion-archive"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
            <section class="col-lg-12 content">
              <form action="{{url('admin/tahunakademik/ubahproses')}}" role="form" method="post">
              {{ csrf_field() }}
                <div class="container-fluid ">
                  <div class="card " >
                    <div class="card-header" style="text-transform: uppercase; font-weight: bold;">
                      Tahun Akademik
                      @foreach($semester_aktif as $s)
                          @foreach($tahun_aktif as $t)
                          ( {{$s->semester}} {{$t->tahun}} )
                          <input type="hidden" name="semester_aktif" value="{{$s->idsemester}}">
                          <input type="hidden" name="tahun_aktif" value="{{$t->idtahunakademik}}">
                          @endforeach
                        @endforeach
                      <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                      </div>
                    </div>
                    <div class="card-body">    
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

                      @if (count($errors) > 0)
                      <div class="alert alert-danger">
                        <ul>
                          @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                          @endforeach
                        </ul>
                      </div>
                    @endif

                      <label for="exampleInputSemester">Pilih Semester: </label>
                      &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
                      <select class="btn btn-secondary dropdown-toggle" name="semester" data-toggle="dropdown" id="exampleInputSemester">
                        <option value="">-- Semester --</option>
                        @foreach($semester as $s)
                          <option value="{{$s->idsemester}}">{{$s->semester}}</option>                         
                        @endforeach
                      </select>

                      <br>
                      <label for="exampleInputTahunAkademik">Pilih Tahun Akademik: </label>
                      <select class="btn btn-secondary dropdown-toggle" name="tahun" data-toggle="dropdown" id="exampleInputTahunAkademik">
                        <option value="">-- Tahun Akademik --</option>
                        @foreach($tahun as $t)
                          <option value="{{$t->idtahunakademik}}">{{$t->tahun}}</option>                         
                        @endforeach
                      </select>
                    </div>
                    <div class="card-footer">
                      <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                  </div>
                </div>
              </form>
            </section>
          

          <section class="col-lg-12 connectedSortable">
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Monitoring Konsultasi Dosen Wali</h3>
              </div>
              <div class="card-body">
                <div class="chart">
                  <canvas id="areaChart1" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                  <br>
                  <p style="font-size: 11px;">
                    Keterangan:
                    <br>
                    <a href="#" class="btn btn-primary btn-sm"></a> 
                    Total seluruh konsultasi dalam setiap bulan.
                    <br>
                    <a href="#" class="btn btn-secondary btn-sm"></a> 
                    Total konsultasi saat ini (3 bulan).
                  </p>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </section>

          <section class="col-lg-12 connectedSortable">
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Monitoring Konsultasi Dosen Wali Dalam Setahun </h3>
              </div>
              <div class="card-body">
                
                <div class="chart">
                  <canvas id="areaChart2" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                  <br>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </section>
          <!-- /.Left col -->
         
          <!-- right col -->
        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@endsection


@push('scripts')
<!-- Untuk Menambahkan script baru -->
<script>
  $(function () {  
    //Total seluruh konsultasi per bulan
    var nama_bulan= new Array();
    var total= new Array();

    @foreach($total_konsultasi as $t)
      nama_bulan.push('{{$t->bulan}}');
      total.push({{$t->total}});
    @endforeach

    //Total seluruh konsultasi bulan ini
    var nama_bulan_sekarang = new Array();
    var total_sekarang = new Array();

    @foreach($total_konsultasi_sekarang as $ts)
      nama_bulan_sekarang.push('{{$ts->bulan}}');
      total_sekarang.push({{$ts->total}});
    @endforeach
    

    var areaChartCanvas = $('#areaChart1').get(0).getContext('2d')

    var areaChartData = {
      labels  : nama_bulan,
      datasets: [
        {
          label               : 'Konsultasi Per Bulan',
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : true,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : total
        },
        {
          label               : 'Konsultasi Saat Ini',
          backgroundColor     : 'rgba(210, 214, 222, 1)',
          borderColor         : 'rgba(210, 214, 222, 1)',
          pointRadius         : true,
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : total_sekarang
        },
      ]
    }

    var areaChartOptions = {
      maintainAspectRatio : false,
      responsive : true,
      legend: {
        display: false
      },
      scales: {
        xAxes: [{
          gridLines : {
            display : false,
          }
        }],
        yAxes: [{
          gridLines : {
            display : false,
          }
        }]
      }
    }

    // This will get the first returned node in the jQuery collection.
    var areaChart       = new Chart(areaChartCanvas, { 
      type: 'line',
      data: areaChartData, 
      options: areaChartOptions
    })    
  })

  $(function () {
    var nama_bulan = new Array();
    var total = new Array();

    @foreach($aktifitaskonsultasi as $a)
      nama_bulan.push('{{$a->bulan}}');
      total.push({{$ts->total}});
    @endforeach
    

    var areaChartCanvas = $('#areaChart2').get(0).getContext('2d')

    var areaChartData = {
      labels  : nama_bulan,
      datasets: [
        {
          label               : 'Konsultasi Per Bulan',
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : true,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : total
        },    
      ]
    }

    var areaChartOptions = {
      maintainAspectRatio : false,
      responsive : true,
      legend: {
        display: false
      },
      scales: {
        xAxes: [{
          gridLines : {
            display : false,
          }
        }],
        yAxes: [{
          gridLines : {
            display : false,
          }
        }]
      }
    }

    // This will get the first returned node in the jQuery collection.
    var areaChart       = new Chart(areaChartCanvas, { 
      type: 'line',
      data: areaChartData, 
      options: areaChartOptions
    })

    
  })
</script>

@endpush