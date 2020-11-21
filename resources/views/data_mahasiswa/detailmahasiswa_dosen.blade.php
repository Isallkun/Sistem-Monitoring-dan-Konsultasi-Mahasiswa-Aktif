<!--Wajib untuk inisialisasi file views/layouts/appadmin-->
@extends('layouts.appdosen')

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
            <h1 class="m-0 text-dark">Detail Data Mahasiswa Wali</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('dosen')}}">Home</a></li>
              <li class="breadcrumb-item active">Detail Daftar Mahasiswa Wali</li>
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
            <div class="card card-info card-tabs">
              <div class="card-header p-0 pt-0">
                <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                  <li class="pt-2 px-3" style="background-color: black"><h3 class="card-title">{{$data_mahasiswa[0]->namamahasiswa}} - {{$data_mahasiswa[0]->nrpmahasiswa}}</h3></li>
                  <li class="nav-item">
                    <a class="nav-link active" id="custom-tabs-two-informasi-tab" data-toggle="pill" href="#custom-tabs-two-informasi" role="tab" aria-controls="custom-tabs-two-informasi" aria-selected="true">Informasi</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-two-profil-tab" data-toggle="pill" href="#custom-tabs-two-profil" role="tab" aria-controls="custom-tabs-two-profil" aria-selected="false">Profil Mahasiswa</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-two-akademik-tab" data-toggle="pill" href="#custom-tabs-two-akademik" role="tab" aria-controls="custom-tabs-two-akademik" aria-selected="false">Akademik</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-two-konsultasi-tab" data-toggle="pill" href="#custom-tabs-two-konsultasi" role="tab" aria-controls="custom-tabs-two-konsultasi" aria-selected="false">Konsultasi</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-two-hukuman-tab" data-toggle="pill" href="#custom-tabs-two-hukuman" role="tab" aria-controls="custom-tabs-two-hukuman" aria-selected="false">Hukuman</a>
                  </li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-two-tabContent">
                  <div class="tab-pane fade show active" id="custom-tabs-two-informasi" role="tabpanel" aria-labelledby="custom-tabs-two-informasi-tab">
                    
                    <div class="row">
                      <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                          <div class="inner">
                            <h3>{{$total_konsultasi}}</h3>

                            <p>Total Konsultasi<br>Dosen Wali</p>
                          </div>
                          <div class="icon">
                            <i class="ion ion-archive"></i>
                          </div>
                        </div>
                      </div>
                      <!-- ./col -->
                      <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                          <div class="inner">
                            <h3>{{$total_hukuman}}</h3>

                            <p>Total Hukuman<br>Belum Selesai</p>
                          </div>
                          <div class="icon">
                            <i class="ion ion-ios-cog"></i>
                          </div>
                        </div>
                      </div>
                      <!-- ./col -->
                      <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                          <div class="inner">
                            <h3>{{$total_nisbi_d}}</h3>

                            <p>Total Nilai NISBI "D"<br>Mahasiswa</p>
                          </div>
                          <div class="icon">
                            <i class="ion ion-alert"></i>
                          </div>
                        </div>
                      </div>
                      <!-- ./col -->
                      <!-- ./col -->
                      <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                          <div class="inner">
                            <h3>{{$sisasks}}</h3>

                            <p>Total Sisa SKS<br>Mahasiswa   </p>
                          </div>
                          <div class="icon">
                            <i class="ion ion-ios-timer"></i>
                          </div>
                        </div>
                      </div>
                      <!-- ./col -->
                    </div>

                    <section class="content">
                      <div class="container-fluid">
                        <div class="row">

                          <!-- /.col (RIGHT) -->
                          <div class="col-md-6">
                            <div class="card card-primary">
                              <div class="card-header">
                                <h3 class="card-title">Grafik IPS dan IPK Mahasiswa</h3>
                              </div>
                              <div class="card-body">
                                <div class="chart">
                                  <canvas id="areaChart1" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                  <br>
                                   <p style="font-size: 11px;">
                                    Keterangan:
                                    <br>
                                    Biru: Nilai IPS mahasiswa per semester.
                                    <br>
                                    Abu-abu: Nilai IPK mahasiswa per semester.
                                  </p>
                                </div>
                              </div>
                              <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                          </div>

                          <!-- /.col (LEFT) -->
                          <div class="col-md-6">
                            <div class="card card-primary">
                              <div class="card-header">
                                <h3 class="card-title">Grafik SKS per Semester Mahasiswa</h3>
                              </div>
                              <div class="card-body">
                                <div class="chart">
                                  <canvas id="areaChart2" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                  <br>
                                   <p style="font-size: 11px;">
                                    Keterangan:
                                    <br>
                                    Biru: Total SKS mahasiswa dalam setiap semester.
                                  </p>
                                </div>
                              </div>
                              <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                          </div>
                          <!-- /.col (RIGHT) -->
                        </div>
                        <!-- /.row -->
                      </div><!-- /.container-fluid -->
                    </section>
                  </div>

                  <div class="tab-pane fade" id="custom-tabs-two-profil" role="tabpanel" aria-labelledby="custom-tabs-two-profil-tab">
                    @foreach($data_mahasiswa as $m)   
                    <div class="card card-primary card-outline">
                      <div class="card-body box-profile">
                        <div class="text-center">
                          <img class="profile-user-img img-fluid img-circle" src="{{url('data_pengguna/'. $m->profil )}}" alt="User profile picture">

                        </div>
                         <h3 class="profile-username text-center">{{$m->namamahasiswa}} - {{$m->nrpmahasiswa}}</h3>

                          <p class="text-muted text-center">
                          Student of Informatics Engineering <br>University of Surabaya
                          </p>

                        <ul class="list-group list-group-unbordered mb-3">
                          <li class="list-group-item">
                            
                            <a class="float-left">
                              @if($m->level == "Bronze")
                              <img src="{{url('rank_pictures/Bronze.png')}}" class="rounded mx-auto d-block" alt="rank image">
                              @elseif($m->level == "Silver")
                              <img src="{{url('rank_pictures/Silver.png')}}" class="rounded mx-auto d-block" alt="rank image">
                              @else
                              <img src="{{url('rank_pictures/Gold.png')}}" class="rounded mx-auto d-block" alt="rank image">
                              @endif 
                            </a>
                            <a class="float-left">
                              <b>Rating Mahasiswa </b>
                              <br> 
                               @if($m->poin != "0")
                                  @for($i=0; $i <= $m->poin; $i++)
                                    <span class="fa fa-star checked"></span>
                                  @endfor
                               @else
                                  sorry, no rating 
                               @endif 
                            </a>
                          </li>

                         
                          <li class="list-group-item"></li>
                          
                          <li class="list-group-item">
                            <b>Jenis Kelamin</b> <a class="float-right">{{$m->jeniskelamin}} </a>
                          </li>
                          <li class="list-group-item">
                            <b>Tempat, Tanggal lahir</b> <a class="float-right">{{$m->tempatlahir}},{{$m->tanggallahir}} </a>
                          </li>
                          <li class="list-group-item">
                            <b>Email</b> <a class="float-right">{{$m->email}} </a>
                          </li>
                          <li class="list-group-item">
                            <b>Nomor Telepon</b> <a class="float-right">{{$m->telepon}} </a>
                          </li>
                           <li class="list-group-item">
                            <b>Alamat</b> <a class="float-right">{{$m->alamat}} </a>
                          </li>
                          <li class="list-group-item">
                            <b>Jurusan</b> <a class="float-right">{{$m->namajurusan}} </a>
                          </li>
                          <li class="list-group-item">
                            <b>Status</b> <a class="float-right">{{$m->status}}</a>
                          </li>
                          <li class="list-group-item"></li>

                          <li class="list-group-item">
                            <b>Indeks Prestasi Kumulatif</b> <a class="float-right">{{$m->ipk}}</a>
                          </li>
                          <li class="list-group-item">
                            <b>IPKm</b> <a class="float-right">{{$m->ipkm}}</a>
                          </li>
                          <li class="list-group-item">
                            <b>Indeks Prestasi Semester</b> <a class="float-right">{{$m->ips}}</a>
                          </li>
                          <li class="list-group-item">
                            <b>Total SKS</b> <a class="float-right">{{$m->totalsks}}</a>
                          </li>
                          
                        
                        </ul>

                      </div>
                      <!-- /.card-header -->
                      <!-- /.card-body -->
                    </div>
                    @endforeach
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-two-akademik" role="tabpanel" aria-labelledby="custom-tabs-two-akademik-tab">
                    Akademik
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-two-konsultasi" role="tabpanel" aria-labelledby="custom-tabs-two-konsultasi-tab">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr> 
                          <th width="1%">No.</th>
                          <th width="1%">Tanggal Konsultasi</th>
                          <th width="1%">Topik Konsultasi</th>
                          <th width="1%">Tahun Akademik</th>
                          <th width="1%">Konsultasi Selanjutnya</th>
                          <th width="1%">Detail</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($data_konsultasi as $no => $dk)
                        <tr>
                          <td>{{$no+1}}</td>
                          <td>{{$dk->tanggalkonsultasi}}</td>
                          <td>{{$dk->namatopik}}</td>
                          <td>{{$dk->semester}} {{$dk->tahun}}</td>
                          <td>{{$dk->konsultasiselanjutnya}}</td>
                          <td>
                            <a href="{{url('dosen/data/mahasiswa/detail/'.$dk->idkonsultasi)}}" class="fas fa-eye" data-toggle="modal" data-target="#detailKonsultasi_{{$dk->idkonsultasi}}"></a>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                      <br/>
                      Halaman : {{$data_konsultasi->currentPage()}} <br/>
                      Jumlah Data : {{$data_konsultasi->total()}} <br/>
                      Data Per Halaman : {{$data_konsultasi->perPage()}} <br/>
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-two-hukuman" role="tabpanel" aria-labelledby="custom-tabs-two-hukuman-tab">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr> 
                          <th width="1%">No.</th>
                          <th width="1%">Dosen</th>
                          <th width="1%">Tanggal Input Hukuman</th>
                          <th width="1%">Keterangan</th>
                          <th width="1%">Status</th>
                          <th width="1%">Nilai</th>
                          <th width="1%">Tanggal Konfirmasi</th>
                          <th width="1%">Masa Berlaku</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($data_hukuman as $no => $dh)
                        <tr>
                          <td>{{$no+1}}</td>
                          <td>{{$dh->namadosen}} ({{$dh->npkdosen}})</td>
                          <td>{{$dh->tanggalinput}}</td>
                          <td>{{$dh->keterangan}}</td>
                          <td>
                            @if($dh->status != "0")
                              Belum Selesai
                            @else
                              Selesai
                            @endif
                          </td>
                          <td>
                            @if($dh->penilaian == "kurang")
                              <i class="btn btn-danger btn-sm">Kurang</i>
                            @elseif ($dh->penilaian == "cukup")
                              <i class="btn btn-warning btn-sm">Cukup</i>
                            @else 
                              <i class="btn btn-success btn-sm">Baik</i>
                            @endif
                            
                          </td>   
                          <td>{{$dh->tanggalkonfirmasi}}</td>   
                          <td>{{$dh->masaberlaku}}</td>      
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                      <br/>
                      Halaman : {{$data_hukuman->currentPage()}} <br/>
                      Jumlah Data : {{$data_hukuman->total()}} <br/>
                      Data Per Halaman : {{$data_hukuman->perPage()}} <br/>
                  </div>
                </div>
              </div>
              <!-- /.card -->
            </div>
          </div>
        </div>
      
       
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->

      @foreach($data_konsultasi as $dk)
          <div id="detailKonsultasi_{{$dk->idkonsultasi}}" class="modal fade" role="dialog">
            <div class="modal-dialog">
              <!-- konten modal-->
              <div class="modal-content">
                <!-- heading modal -->
                <div class="modal-header">
                  <h4 class="modal-title">Detail Konsultasi</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- body modal -->
                <div class="modal-body">
                  <p><b>{{$dk->namatopik}}</b></p>
                  <table class="table table-bordered table-hover">
                    <tr>
                     <th>Tanggal</th>
                     <td>{{$dk->tanggalkonsultasi}}</td>
                    </tr>
                    <tr>
                     <th>Permasalahan</th>
                     <td>{{$dk->permasalahan}}</td>
                    </tr>
                    <tr>
                     <th>Solusi:</th>
                     <td>{{$dk->solusi}}</td>
                    </tr>
                    <tr>
                     <th>Konsultasi Berikutnya:</th>
                     <td>{{$dk->konsultasiselanjutnya}}</td>
                    </tr>
                    <tr>
                    @if($dk->konfirmasi == 0)
                      <th>Status Konfirmasi:</th>
                      <td>Belum Disetujui</td>
                    @else
                      <th>Status Konfirmasi:</th>
                      <td>Disetujui</td>
                    @endif
                    </tr>
                    <tr>
                     <th>Tahun akademik:</th>
                     <td>{{$dk->semester}} {{$dk->tahun}}</td>
                    </tr>
                  </table>
                </div>
                <!-- footer modal -->
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
        @endforeach
    </section>
    
@endsection
 
@push('scripts')
<script>
  $(function () {
    var tahunakademik= new Array();
    var ip_semester= new Array();
    var ip_kumulatif= new Array();

    @foreach($grafik_akademik as $g)
      tahunakademik.push('{{$g->semester}} {{$g->tahun}}');
      ip_semester.push({{$g->ips}});
      ip_kumulatif.push({{$g->ipk}});
    @endforeach
   
    var areaChartCanvas = $('#areaChart1').get(0).getContext('2d')

    var areaChartData = {
      labels  : tahunakademik,
      datasets: [
        {
          label               : 'IPS mahasiswa',
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : true,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : ip_semester
        },
        {
          label               : 'IPK mahasiswa',
          backgroundColor     : 'rgba(210, 214, 222, 1)',
          borderColor         : 'rgba(210, 214, 222, 1)',
          pointRadius         : true,
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : ip_kumulatif
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
    var tahunakademik= new Array();
    var sks = new Array();
    
    @foreach($grafik_akademik as $g)
      tahunakademik.push('{{$g->semester}} {{$g->tahun}}');
      sks.push({{$g->totalsks}});
    @endforeach
   
    var areaChartCanvas = $('#areaChart2').get(0).getContext('2d')

    var areaChartData = {
      labels  : tahunakademik,
      datasets: [
        {
          label               : 'SKS mahasiswa',
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : true,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : sks
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