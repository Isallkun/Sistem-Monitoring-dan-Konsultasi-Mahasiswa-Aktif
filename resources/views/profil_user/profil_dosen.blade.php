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
            <h1 class="m-0 text-dark"><i class=""></i>Profile Pengguna</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('dosen')}}">Home</a></li>
              <li class="breadcrumb-item active">Profil Pengguna</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">

            <form action="{{url('dosen/profil/profildosen/ubahproses')}}" role="form" method="post" enctype="multipart/form-data">
              {{ csrf_field() }}

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

              @foreach($user_dosen as $u)
              <input type="hidden" name="npk_dosen" value="{{$u->npkdosen}}">
              
              <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle" src="{{url('data_pengguna/'. Session::get('profil') )}}" alt="User profile picture">

                  </div>
                   <h3 class="profile-username text-center">{{$u->namadosen}} - {{$u->npkdosen}}</h3>

                    <p class="text-muted text-center">
                    Department of Informatics Engineering <br>University of Surabaya
                    </p>

                  <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                      <b>Nama Lengkap</b> 
                      <input type="text" name="nama_dosen" class="form-control" id="exampleInputNama" placeholder="Enter Nama Dosen" value="{{$u->namadosen}}">
                    </li>

                    <li class="list-group-item">
                      <b>Jenis Kelamin</b> <a class="float-right">{{$u->jeniskelamin}} </a>
                    </li>
                    <li class="list-group-item">
                      <b>Email</b> <a class="float-right">{{$u->email}} </a>
                    </li>
                    <li class="list-group-item">
                      <b>Nomor Telepon</b> <a class="float-right">{{$u->telepon}} </a>
                    </li>
                    <li class="list-group-item">
                      <b>Jurusan</b> <a class="float-right">{{$u->namajurusan}} </a>
                    </li>
                    <li class="list-group-item">
                      <b>Status</b> <a class="float-right">{{$u->status}}</a>
                    </li>

                    <li class="list-group-item"></li>
                    
                    <li class="list-group-item">
                      <b>Username</b> <a class="float-right">{{$u->username}}</a>
                    </li>

                     <li class="list-group-item">
                      <b>Password</b> <a class="float-right" required="*">{{$decrypted}}</a>
                    </li>

                    <li class="list-group-item">
                      <button type="submit" class="btn btn-primary float-right">Simpan</button>
                    </li>

                    <li class="list-group-item">
                      <b>Grafik Pelayanan Konsultasi Per-bulan</b>
                    </li>
                  </ul>

                  <div class="chart">
                    <canvas id="areaChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    
                  </div>
                </div>
                <!-- /.card-header -->
                <!-- /.card-body -->
              </div>
            @endforeach
            </form>
          </div>


        </div>
      
        
       

        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    
@endsection
 
@push('scripts')
<script>
  $(function () {
    
    //Total seluruh konsultasi per bulan
    var nama_bulan= new Array();
    var total= new Array();

    @foreach($total_konsultasi as $t)
      nama_bulan.push('{{$t->bulan}}');
      total.push({{$t->total}});
    @endforeach

   
    var areaChartCanvas = $('#areaChart').get(0).getContext('2d')

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