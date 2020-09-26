<!--Wajib untuk inisialisasi file views/layouts/appadmin-->
@extends('layouts.appadmin')

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
    
    @if (\Session::has('Success'))
      <div class="alert alert-success alert-block">
        <ul>
            <li>{!! \Session::get('Success') !!}</li>
        </ul>
      </div>
    @endif
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Ubah Data Dosen</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{url('admin/daftardosen')}}">Daftar Dosen</a></li>
              <li class="breadcrumb-item active">Ubah Data Dosen</li>
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
                <h3 class="card-title">Form Input Data Dosen</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form action="{{url('admin/master/dosen/ubahproses')}}" role="form" method="post">
                {{ csrf_field() }}

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

                @foreach($datadosen as $d)
                <div class="card-body">

                  <input type="hidden" name="npk_dosen" id="exampleInputNpk" value="{{$d->npkdosen}}">
              
                
                  <div class="form-group">
                    <label for="exampleInputNama">Nama Dosen</label>
                    <input type="text" name="nama_dosen" class="form-control" id="exampleInputNama" placeholder="Enter Nama Dosen" value="{{$d->namadosen}}">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputJenisKelamin">Jenis Kelamin</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="jenis_kelamin" data-toggle="dropdown" id="exampleInputJenisKelamin">
                      @if($d->jeniskelamin == "laki-laki")
                        <option value="laki-laki" selected>Laki-laki</option>
                        <option value="perempuan" >Perempuan</option>
                      @else if($d->jeniskelamin == "perempuan")
                      <option value="laki-laki">Laki-laki</option>
                        <option value="perempuan" selected>Perempuan</option>
                      @endif
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputEmail">Email</label>
                    <input type="text" name="email" class="form-control" id="exampleInputEmail" placeholder="Enter Email" value="{{$d->email}}">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputTelepon">Telepon</label>
                    <input type="text" name="telepon" class="form-control" id="exampleInputTelepon" placeholder="Enter Telepon" value="{{$d->telepon}}">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputStatus">Status</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="status" data-toggle="dropdown" id="exampleInputStatus">
                      @if($d->status == "aktif")
                        <option value="aktif" selected>Aktif</option>
                        <option value="tidak aktif">Tidak Aktif</option>
                      @else if($d->status == "tidak aktif")
                        <option value="aktif">Aktif</option>
                        <option value="tidak aktif" selected>Tidak Aktif</option>
                      @endif
                    </select>
                  </div>

                   <div class="form-group">
                    <label for="exampleInputKodeJurusan">Kode Jurusan</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="kode_jurusan" data-toggle="dropdown" id="exampleInputKodeJurusan">
                      @foreach($jurusan as $j)
                        @if($d->kode_jurusan == $j->idjurusan)
                          <option value="{{$j->idjurusan}}" selected>{{$j->idjurusan}} - {{$j->nama}}</option>
                        @else if($d->status != $j->idjurusan)
                          <option value="{{$j->idjurusan}}">{{$j->idjurusan}} - {{$j->nama}}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputRole">Jabatan</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="id_role" data-toggle="dropdown" id="exampleInputIdRole">
                      @foreach($role as $r)
                        @if($r->idrole == $d->id_role)
                          <option value="{{$r->idrole}}" selected>{{$r->idrole}} - {{$r->nama}}</option>
                        @else
                          <option value="{{$r->idrole}}">{{$r->idrole}} - {{$r->nama}}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputUsername">Username</label>
                    <input type="username" name="username" class="form-control" id="exampleInputUsername" value="{{$d->username}}">
                  </div>
                  
                
                  <div class="form-group">
                    <label for="exampleInputPassword">Password</label>
                    <input type="password" name="password" class="form-control" id="exampleInputPassword" placeholder="Password" value="{{$decrypted}}" >
                  </div>

                  <input type="checkbox" onclick="myFunction()">Show Password
                  
                  @endforeach
                

                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
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
  function myFunction() {
    var x = document.getElementById("exampleInputPassword");
    if (x.type === "password") 
    {
      x.type = "text";
    } 
    else 
    {
      x.type = "password";
    }
  }
</script>

@endpush
