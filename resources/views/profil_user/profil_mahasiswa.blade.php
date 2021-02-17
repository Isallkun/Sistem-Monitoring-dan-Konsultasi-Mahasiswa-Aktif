<!--Wajib untuk inisialisasi file views/layouts/appadmin-->
@extends('layouts.appmahasiswa')

@push('styles')
  <!-- Untuk menambahkan style baru -->
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
            <h1 class="m-0 text-dark"><i class=""></i>Profile Pengguna</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('mahasiswa')}}">Home</a></li>
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

            @foreach($user_mahasiswa as $u)
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle" src="{{url('data_pengguna/'. Session::get('profil_mahasiswa') )}}" alt="User profile picture">

                </div>
                 <h3 class="profile-username text-center">{{$u->namamahasiswa}} - {{$u->nrpmahasiswa}}</h3>

                  <p class="text-muted text-center">
                  Student of Informatics Engineering <br>University of Surabaya 
                  </p>

                <ul class="list-group list-group-unbordered mb-3">

                  <li class="list-group-item">
                    <a class="float-left">
                      @if($u->level == "Bronze")
                      <img src="{{url('rank_pictures/Bronze.png')}}" class="rounded mx-auto d-block" alt="rank image">
                      <center>Bronze</center>
                      <br>

                      @elseif($u->level == "Silver")
                      <img src="{{url('rank_pictures/Silver.png')}}" class="rounded mx-auto d-block" alt="rank image">
                      <center>Silver</center>
                      @else
                      <img src="{{url('rank_pictures/Gold.png')}}" class="rounded mx-auto d-block" alt="rank image">
                      <center>Gold</center>
                      @endif 
                    </a>
                    <a class="float-left">
                      <b>Rating Anda Sekarang </b>
                      <br> 
                       @if($u->poin != "0")
                          @for($i=0; $i < $u->poin; $i++)
                            <span class="fa fa-star checked"></span>
                          @endfor
                       @else
                          @for($i=0; $i < 5; $i++)
                            <span class="fa fa-star"></span>
                          @endfor 
                       @endif 
                    </a>
                  </li>

                  <li class="list-group-item">
                    <b>Nama Lengkap</b> 
                    <a class="float-right">{{$u->namamahasiswa}} </a>
                    <br>
                    <a href="#" class="float-right" style="text-decoration: underline;" data-toggle="modal" data-target="#ubahData_{{$u->nrpmahasiswa}}">ubah</a>
                  </li>

                  <li class="list-group-item">
                    <b>Kondisi</b> 
                    <a class="float-right">
                      @if($u->flag == 0)
                      <i class="btn btn-success btn-sm" style="width: 100px;font-weight: bold;">Normal</i>
                      @elseif($u->flag == 1)
                      <i class="btn btn-warning btn-sm" style="width: 100px;font-weight: bold;">Waspada</i>
                      @else
                      <i class="btn btn-danger btn-sm" style="width: 100px;font-weight: bold;">Kurang</i>
                      @endif
                    </a>
                  </li>
                  <li class="list-group-item">
                    <b>Jenis Kelamin</b> <a class="float-right">{{$u->jeniskelamin}} </a>
                  </li>
                  <li class="list-group-item">
                    <b>Tempat, Tanggal Lahir</b> <a class="float-right">{{$u->tempatlahir}}, {{$u->tanggallahir}} </a>
                  </li>
                  <li class="list-group-item">
                    <b>Email</b> <a class="float-right">{{$u->email}} </a>
                  </li>
                  <li class="list-group-item">
                    <b>Nomor Telepon</b> <a class="float-right">{{$u->telepon}} </a>
                  </li>
                  <li class="list-group-item">
                    <b>Alamat</b> <a class="float-right">{{$u->alamat}} </a>
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
                    <a href="#" class="float-right" style="text-decoration: underline;" data-toggle="modal" data-target="#ubahPassword_{{$u->nrpmahasiswa}}">ubah</a>  
                  </li>                   
                </ul>
              </div>
              <!-- /.card-header -->
              <!-- /.card-body -->
            </div>
          @endforeach
          </div>
        </div>
        <!-- /.row (main row) -->

        @foreach($user_mahasiswa as $m)
        <div id="ubahData_{{$m->nrpmahasiswa}}" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <!-- konten modal-->
            <div class="modal-content">
              <!-- heading modal -->
              <div class="modal-header">
                <h4 class="modal-title">Ubah Data Profil Pengguna</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <!-- body modal -->
              <div class="modal-body">
                
                <form action="{{url('mahasiswa/profil/profilmahasiswa/ubahproses')}}" role="form" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                  <input type="hidden" name="nrp_mahasiswa" value="{{$m->nrpmahasiswa}}">

                  <div class="form-group">
                    <label for="exampleInputNamaLengkap">Nama Lengkap</label>
                    <input type="text" class="form-control" id="exampleInputNamaLengkap" name="namalengkap" placeholder="Enter Nama Lengkap" value="{{$m->namamahasiswa}}" required>
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

        @foreach($user_mahasiswa as $m)
        <div id="ubahPassword_{{$m->nrpmahasiswa}}" class="modal fade" role="dialog">
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
                
                <form action="{{url('mahasiswa/profil/profilmahasiswa/ubahproses')}}" role="form" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                  <input type="hidden" name="nrp_mahasiswa" value="{{$m->nrpmahasiswa}}">
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

      </div><!-- /.container-fluid -->
    </section>
    
@endsection
 
@push('scripts')

@endpush