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
            <h1>Ubah Data Mahasiswa</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{url('admin/master/mahasiswa')}}">Daftar Mahasiswa</a></li>
              <li class="breadcrumb-item active">Ubah Data Mahasiswa</li>
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
                <h3 class="card-title">Form Input Data Mahasiswa</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form action="{{url('admin/master/mahasiswa/ubahproses')}}" role="form" method="post">
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
                
                @foreach($datamahasiswa as $m)
                <div class="card-body">
                  
                  <input type="hidden" name="nrp_mahasiswa" value="{{$m->nrpmahasiswa}}">

                  <div class="form-group">
                    <label for="exampleInputNama">Nama Mahasiswa</label>
                    <input type="text" name="nama_mahasiswa" class="form-control" id="exampleInputNama" placeholder="Enter Nama Mahasiswa" value="{{$m->namamahasiswa}}">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputJenisKelamin">Jenis Kelamin</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="jenis_kelamin" data-toggle="dropdown" id="exampleInputJenisKelamin">
                      @if($m->jeniskelamin == "laki-laki")
                        <option value="laki-laki" selected>Laki-laki</option>
                        <option value="perempuan" >Perempuan</option>
                      @else if($m->jeniskelamin == "perempuan")
                      <option value="laki-laki">Laki-laki</option>
                        <option value="perempuan" selected>Perempuan</option>
                      @endif
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputTanggalLahir">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" id="exampleInputTanggalLahir" value="{{$m->tanggallahir}}">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputTempatLahir">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" id="exampleInputTempatLahir" placeholder="Enter Tempat Lahir" value="{{$m->tempatlahir}}">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputEmail">Email</label>
                    <input type="email" name="email" class="form-control" id="exampleInputEmail" placeholder="Enter Email" value="{{$m->email}}">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputTelepon">Telepon</label>
                    <input type="text" name="telepon" class="form-control" id="exampleInputTelepon" placeholder="Enter Telepon" value="{{$m->telepon}}">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputAngkatan">Angkatan</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="angkatan" data-toggle="dropdown" id="exampleInputAngkatan">
                      {{ $now = date('Y') }}
                      {{ $last = date('Y')-4 }}   

                      @for ($i = $now; $i >= $last; $i--)
                        @if($m->angkatan != $i)
                        <option value="{{ $i }}">{{ $i }}</option>
                        @else
                        <option value="{{ $i }}" selected>{{ $i }}</option>
                        @endif
                      @endfor
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputAlamat">Alamat</label>
                    <input type="text" name="alamat" class="form-control" id="exampleInputAlamat" placeholder="Enter Alamat" value="{{$m->alamat}}">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputStatus">Status</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="status" data-toggle="dropdown" id="exampleInputStatus">
                      @if($m->status == "aktif")
                        <option value="aktif" selected>Aktif</option>
                        <option value="tidak aktif">Tidak Aktif</option>
                      @else if($m->status == "tidak aktif")
                        <option value="aktif">Aktif</option>
                        <option value="tidak aktif" selected>Tidak Aktif</option>
                      @endif
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputKodeJurusan">Dosen Wali</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="npk_dosenwali" data-toggle="dropdown" id="exampleInputNPKDosen">
                       @foreach($dosen as $d)
                        @if($m->dosen_npkdosen == $d->npkdosen)
                          <option value="{{$d->npkdosen}}" selected>{{$d->npkdosen}} - {{$d->namadosen}}</option>
                        @else
                          <option value="{{$d->npkdosen}}">{{$d->npkdosen}} - {{$d->namadosen}}</option>
                        @endif
                       @endforeach
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputKodeJurusan">Kode Jurusan</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="kode_jurusan" data-toggle="dropdown" id="exampleInputKodeJurusan">
                      @foreach($jurusan as $j)
                        @if($m->jurusan_kodejurusan == $j->kodejurusan)
                          <option value="{{$j->kodejurusan}}" selected>{{$j->kodejurusan}} - {{$j->namajurusan}}</option>
                        @else if($m->status != $j->kodejurusan)
                          <option value="{{$j->kodejurusan}}">{{$j->kodejurusan}} - {{$j->namajurusan}}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputRole">Jabatan</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="id_role" data-toggle="dropdown" id="exampleInputIdRole">
                        @foreach($role as $r)
                        @if($r->idrole == $m->role_idrole)
                          <option value="{{$r->idrole}}" selected>{{$r->idrole}} - {{$r->nama}}</option>
                        @else
                          <option value="{{$r->idrole}}">{{$r->idrole}} - {{$r->nama}}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputUsername">Username</label>
                    <input type="username" name="username" class="form-control" id="exampleInputUsername" placeholder="Username" value="{{$m->username}}">
                  </div>
                  
                  <div class="form-group">
                    <label for="exampleInputPassword">Password</label>
                    <input type="password" name="password" class="form-control" id="exampleInputPassword" placeholder="Password" value="{{$decrypted}}">
                  </div>

                  <input type="checkbox" onclick="myFunction()"> Show Password

                </div>
                @endforeach
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
