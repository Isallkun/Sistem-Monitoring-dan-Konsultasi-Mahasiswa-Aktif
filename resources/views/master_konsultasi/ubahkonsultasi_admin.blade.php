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
            <h1>Ubah Data Konsultasi</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{url('admin/master/konsultasi')}}">Daftar Konsultasi</a></li>
              <li class="breadcrumb-item active">Ubah Data Konsultasi</li>
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
                <h3 class="card-title">Form Input Data Konsultasi</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start --> 
              <form action="{{url('admin/master/konsultasi/ubahproses')}}" role="form" method="post">
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
                
                @foreach($datakonsultasi as $m)
                <div class="card-body">
                  <input type="hidden" name="idkonsultasi" value="{{ $m->idkonsultasi }}">

                  <input type="hidden" name="idtopik" value="{{ $m->idtopikkonsultasi }}">

                  <div class="form-group">
                    <label for="exampleInputTopik">Topik Konsultasi</label>
                    <input type="text" name="topik_konsultasi" class="form-control" id="exampleInputTopik" placeholder="Enter Topik" value="{{$m->namatopik}}">
                  </div>
                 
                  <div class="form-group">
                    <label for="exampleInputPermasalahan">Permasalahan</label>
                     <textarea class="form-control" name="permasalahan" id="exampleInputPermasalahan" rows="3" placeholder="Enter Permasalahan">{{$m->permasalahan}}</textarea>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputSolusi">Solusi</label>
                    <input type="text" name="solusi" class="form-control" id="exampleInputSolusi" value="{{$m->solusi}}">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputKonsultasiSelanjutnya">Konsultasi Selanjutnya</label>
                    <input type="date" name="konsultasi_selanjutnya" class="form-control" id="exampleInputKonsultasiSelanjutnya" value="{{$m->konsultasiselanjutnya}}" >
                  </div>

                  <div class="form-group">
                    <label for="exampleInputDosen">Dosen</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="dosen" data-toggle="dropdown" id="exampleInputDosen">
                       @foreach($dosen as $d)
                        @if($d->npkdosen == $m->npkdosen)
                          <option value="{{$d->npkdosen}}" selected>{{$d->npkdosen}} - {{$d->namadosen}}</option>
                        @else 
                          <option value="{{$d->npkdosen}}">{{$d->npkdosen}} - {{$d->namadosen}}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputMahasiswa">Mahasiswa</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="mahasiswa" data-toggle="dropdown" id="exampleInputMahasiswa">
                      @foreach($mahasiswa as $mh)
                        @if($mh->nrpmahasiswa == $m->nrpmahasiswa)
                          <option value="{{$mh->nrpmahasiswa}}" selected>{{$mh->nrpmahasiswa}} - {{$mh->namamahasiswa}}</option>
                        @else 
                           <option value="{{$mh->nrpmahasiswa}}">{{$mh->nrpmahasiswa}} - {{$mh->namamahasiswa}}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputSemester">Semester</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="semester" data-toggle="dropdown" id="exampleInputSemester">
                      @foreach($semester as $s)
                        @if($s->idsemester == $m->idsemester)
                          <option value="{{$s->idsemester}}" selected>{{$s->semester}}</option>
                        @else 
                          <option value="{{$s->idsemester}}">{{$s->semester}}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="exampleInputTahunAkademik">Tahun Akademik</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="tahun_akademik" data-toggle="dropdown" id="exampleInputTahunAkademik">
                      @foreach($tahun_akademik as $th)
                        @if($th->idtahunakademik == $m->idtahunakademik)
                          <option value="{{$th->idtahunakademik}}" selected>{{$th->tahun}}</option>
                        @else 
                          <option value="{{$th->idtahunakademik}}">{{$th->tahun}}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>
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

@endpush
