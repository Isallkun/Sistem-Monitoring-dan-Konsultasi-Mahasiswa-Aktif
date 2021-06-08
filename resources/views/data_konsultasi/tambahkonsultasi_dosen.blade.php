<!--Wajib untuk inisialisasi file views/layouts/appadmin-->
@extends('layouts.appdosen')

@push('styles')
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{url('../../asset/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{url('../../asset/dist/css/adminlte.min.css')}}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
@endpush

<!-- Isi dari yield -->
@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Tambah Data Konsultasi Terjadwal</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('dosen')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{url('dosen/data/konsultasi')}}">Daftar Terjadwal</a></li>
              <li class="breadcrumb-item active">Tambah Data Terjadwal</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Form Input Data Konsultasi Terjadwal</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
          
              <div class="card-body">
                <form method="GET" name="formfilter" action="{{url('dosen/data/konsultasi/tampilkanfilter')}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                  <div class="form-group float-sm-right">
                    <i class="fas fa-filter fa-xs"> Filter Data Mahasiswa (Tahun angkatan) </i>

                  <select class="btn btn-primary btn-xs dropdown-toggle" name="filterAngkatan" data-toggle="dropdown" id="filterAngkatan" onchange="formfilter.submit();">
                    <option value="">-- Pilih Tahun Angkatan --</option>
                    @foreach($angkatan as $a)
                      <option value="{{$a->idtahunakademik}}">{{$a->tahun}}</option>
                    @endforeach
                  </select>
                <p style="font-size: 13px; margin-top: 10px;font-weight: bold;color: red; ">
                @if(!empty($info))
                  @foreach($info as $i)
                   Menampilkan Data Mahasiswa Angkatan {{$i->tahun}}
                  @endforeach
                @else
                  Tidak ada filter data
                @endif
                </p>
                </div>
                </form>
                <br><br><br>

                <form action="{{url('dosen/data/konsultasi/prosestambah')}}" role="form" method="post">
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

                  @if (\Session::has('Error'))
                    <div class="alert alert-danger alert-block">
                      <ul>
                          <li>{!! \Session::get('Error') !!}</li>
                      </ul>
                    </div>
                  @endif
               
                
                  <div class="form-group p-2 mb-3 bg-danger text-white" style="font-family: times-new-roman">
                    <h6>Durasi konsultasi dosen wali</h6> 
                    <p id="demo" name="demo">00 Menit :: 00 Detik</p>
                    <input type="hidden" name="temp_value" id="temp_value">
                  </div>

                 

                  <div class="form-group">
                    <label for="exampleInputMahasiswa">Mahasiswa</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="mahasiswa" data-toggle="dropdown" id="exampleInputMahasiswa">
                      <option value="">-- Pilih Mahasiswa --</option>
                       @foreach($mahasiswa as $m)
                        <option value="{{$m->nrpmahasiswa}}">{{$m->nrpmahasiswa}} - {{$m->namamahasiswa}}</option>
                       @endforeach
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputSemester">Semester </label>
                    @foreach($semester as $s)
                      <p>{{$s->semester}}</p>
                      <input type="hidden" name="semester" value="{{$s->idsemester}}">
                    @endforeach
                  </div>

                  <div class="form-group">
                    <label for="exampleInputTahunAkademik">Tahun Akademik</label>
                    @foreach($tahun_akademik as $th)
                      <p>{{$th->tahun}}</p>
                      <input type="hidden" name="tahun_akademik" value="{{$th->idtahunakademik}}">
                    @endforeach
                  </div>

                  <div class="form-group">
                    <label for="exampleInputTopik">Topik Konsultasi</label>
                    <input type="text" name="topik_konsultasi" class="form-control" id="exampleInputTopik" placeholder="Enter Topik">
                  </div>
                 
                  <div class="form-group">
                    <label for="exampleInputPermasalahan">Permasalahan</label>
                     <textarea class="form-control" name="permasalahan" id="exampleInputPermasalahan" rows="3" placeholder="Enter Permasalahan"></textarea>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputSolusi">Solusi</label>
                    <input type="text" name="solusi" class="form-control" id="exampleInputSolusi" placeholder="Enter Solusi">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputKonsultasiSelanjutnya">Konsultasi Selanjutnya</label>
                    <input type="date" name="konsultasi_selanjutnya" class="form-control" id="exampleInputKonsultasiSelanjutnya" >
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" name="submit" id="submit" class="btn btn-primary" onClick="stopTimeFunction()">Submit</button>
                </div>
              </form>

            </div>
            <!-- /.card -->
            </div>
          <!--/.col (left) -->
          <!-- right column -->
          <div class="col-md-6">

          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@endsection


@push('scripts')
<script src="{{url('../../asset/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{url('../../asset/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- jquery-validation -->
<script src="{{url('../../asset/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
<script src="{{url('../../asset/plugins/jquery-validation/additional-methods.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{url('../../asset/dist/js/adminlte.min.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{url('../../asset/dist/js/demo.js')}}"></script>

<script type="text/javascript">
  
  var myVar = setInterval(setTime, 1000);
  var time="";
  var totalSeconds = 0;

  function stopTimeFunction() 
  { 
    clearInterval(myVar); 

  }

  function setTime() 
  {
    document.getElementById("demo").innerHTML = time;
    var time = pad(parseInt(totalSeconds / 60))+" Menit"+" :: "+pad(totalSeconds % 60)+" Detik";
    
    //Display Time
    document.getElementById("demo").innerHTML = time;
    //Temp time untuk dikirim ke controller
    $("#temp_value").val(time);
  
    if(pad(parseInt(totalSeconds / 60)) < 10)
    { 
      ++totalSeconds; 
    }
    else
    {
      ++totalSeconds;
      alert('Batas waktu konsultasi telah melebihi 10 menit');
    } 
  }

  function pad(val) 
  {
    var valString = val + "";
    if (valString.length < 2) 
    { 
      return "0" + valString; 
    } 
    else 
    { 
      return valString; 
    }
  }

</script>

@endpush
