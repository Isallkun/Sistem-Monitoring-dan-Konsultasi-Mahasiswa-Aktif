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
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Tambah Data Dosen</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{url('admin/daftardosen')}}">Daftar Dosen</a></li>
              <li class="breadcrumb-item active">Tambah Data Dosen</li>
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
              <form action="{{url('admin/master/dosen/prosestambah')}}" role="form" method="post">
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

               
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputNpk">NPK Dosen</label>
                    <input type="text" name="npk_dosen" class="form-control" id="exampleInputNpk" placeholder="Enter NPK Dosen">
                  </div>
                
                  <div class="form-group">
                    <label for="exampleInputNama">Nama Dosen</label>
                    <input type="text" name="nama_dosen" class="form-control" id="exampleInputNama" placeholder="Enter Nama Dosen">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputJenisKelamin">Jenis Kelamin</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="jenis_kelamin" data-toggle="dropdown" id="exampleInputJenisKelamin">
                      <option value="">Pilih Jenis Kelamin</option>
                      <option value="laki-laki">Laki-laki</option>
                      <option value="perempuan">Perempuan</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputEmail">Email</label>
                    <input type="text" name="email" class="form-control" id="exampleInputEmail" placeholder="Enter Email">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputTelepon">Telepon</label>
                    <input type="text" name="telepon" class="form-control" id="exampleInputTelepon" placeholder="Enter Telepon">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputStatus">Status</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="status" data-toggle="dropdown" id="exampleInputStatus">
                      <option value="">Pilih Status</option>
                      <option value="aktif">Aktif</option>
                      <option value="tidak aktif">Tidak Aktif</option>
                    </select>
                  </div>

                   <div class="form-group">
                    <label for="exampleInputKodeJurusan">Kode Jurusan</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="kode_jurusan" data-toggle="dropdown" id="exampleInputKodeJurusan">
                      <option value="">Pilih Jurusan</option>
                       @foreach($jurusan as $j)
                        <option value="{{$j->idjurusan}}">{{$j->idjurusan}} - {{$j->nama}}</option>
                       @endforeach
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputRole">Jabatan</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="id_role" data-toggle="dropdown" id="exampleInputIdRole">
                      <option value="">Pilih Jabatan</option>
                       @foreach($role as $r)
                        <option value="{{$r->idrole}}">{{$r->idrole}} - {{$r->nama}}</option>
                       @endforeach
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputUsername">Username</label>
                    <input type="username" name="username" class="form-control" id="exampleInputUsername" placeholder="Username">
                  </div>
                  
                  <div class="form-group">
                    <label for="exampleInputPassword">Password</label>
                    <input type="password" name="password" class="form-control" id="exampleInputPassword" placeholder="Password">
                  </div>

                  <input type="checkbox" onclick="myFunction()">Show Password

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
