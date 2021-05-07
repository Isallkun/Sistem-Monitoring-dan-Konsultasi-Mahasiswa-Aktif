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
            <h1 class="m-0 text-dark"><i class=""></i>Profil Pengguna</h1>
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

            @foreach($user_dosen as $u) 
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle" src="{{url('data_pengguna/'. Session::get('profil_dosen') )}}" alt="User profile picture">

                </div>
                 <h3 class="profile-username text-center">{{$u->namadosen}} - {{$u->npkdosen}}</h3>

                  <p class="text-muted text-center">
                  Department of Informatics Engineering <br>University of Surabaya
                  </p>

                <ul class="list-group list-group-unbordered mb-3">
                  
                  <li class="list-group-item">
                    <b>Nama Lengkap</b> 
                    <a class="float-right">{{$u->namadosen}} </a>
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
                    <b>Jurusan</b> <a class="float-right">{{$u->namafakultas}} - {{$u->namajurusan}} </a>
                  </li>
                  <li class="list-group-item">
                    <b>Status</b> <a class="float-right">{{$u->status}}</a>
                  </li>

                  <li class="list-group-item"></li>
                  
                  <li class="list-group-item">
                    <b>Username</b> <a class="float-right">{{$u->username}}</a>
                  </li>

                  <li class="list-group-item">
                    <b>Password</b> 
                    <a href="#" class="float-right" style="text-decoration: underline;" data-toggle="modal" data-target="#ubahPassword_{{$u->npkdosen}}">ubah</a>  
                  </li>      

                  <li class="list-group-item">
                    <b>Grafik Pelayanan Konsultasi Terjadwal dan Tidak Terjadwal</b>
                  </li>
                </ul>

                <div class="chart">
                  <canvas id="areaChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                  <br>
                  <p style="font-size: 11px;">
                    Keterangan:
                    <br>
                    <a href="#" class="btn btn-primary btn-sm"></a> 
                    Total layanan konsultasi terjadwal setiap bulan.
                    <br>
                    <a href="#" class="btn btn-secondary btn-sm"></a> 
                    Total layanan konsultasi tidak terjadwal setiap bulan.
                  </p>
                </div>
              </div>
              <!-- /.card-header -->
              <!-- /.card-body -->
            </div>
          @endforeach
          </div>
        </div>
        
      
        @foreach($user_dosen as $d)
        <div id="ubahPassword_{{$d->npkdosen}}" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <!-- konten modal-->
            <div class="modal-content">
              <!-- heading modal -->
              <div class="modal-header">
                <h4 class="modal-title">Ubah Password Pengguna</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <!-- body modal -->
              <div class="modal-body">
                
                <form action="{{url('dosen/profil/profildosen/ubahproses')}}" role="form" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                  <input type="hidden" name="npk_dosen" value="{{$d->npkdosen}}">
                  <input type="hidden" name="password" value="{{$decrypted}}">

                  <div class="form-group">
                    <label for="exampleInputPasswordLama">Password Lama</label>
                    <input type="password" class="form-control" id="exampleInputPasswordLama" name="password_lama" placeholder="Enter Password Lama" required>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputPasswordBaru">Password Baru</label>
                    <input type="password" class="form-control" id="exampleInputPasswordBaru" name="password_baru" placeholder="Enter Password Baru" required>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputRePasswordBaru">Re-Password Baru</label>
                    <input type="password" class="form-control" id="exampleInputRePasswordBaru" name="password_re-baru" placeholder="Enter  Re-Password Baru" required>
                  </div>

                 <button type="submit" class="btn btn-success float-right">Simpan</button>
                </form>
              
              </div>
              <!-- footer modal -->
              <div class="modal-footer">
              </div>
            </div>
          </div>
        </div>
        @endforeach
       

        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    
@endsection
 
@push('scripts')
<script>
  $(function () {
    
    //Total seluruh konsultasi per bulan
    var nama_bulan= new Array();

    var total_konsultasi= new Array();
    @foreach($total_konsultasi as $t)
      nama_bulan.push('{{$t->bulan}}');
      total_konsultasi.push({{$t->total}});
    @endforeach

    var total_nonkonsultasi= new Array();
    @foreach($total_nonkonsultasi as $tn)
      total_nonkonsultasi.push({{$tn->total}});
    @endforeach

   
    var areaChartCanvas = $('#areaChart').get(0).getContext('2d')

    var areaChartData = {
      labels  : nama_bulan,
      datasets: [
        {
          label               : 'Konsultasi Terjadwal',
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : true,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : total_konsultasi
        },
         {
          label               : 'Konsultasi Tidak Terjadwal',
          backgroundColor     : 'rgba(210, 214, 222, 1)',
          borderColor         : 'rgba(210, 214, 222, 1)',
          pointRadius         : true,
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : total_nonkonsultasi
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