Wajib untuk inisialisasi file views/layouts/appadmin
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
            <h1>Ubah Data Matakuliah</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{url('admin/master/matakuliah')}}">Daftar Matakuliah</a></li>
              <li class="breadcrumb-item active">Ubah Data Matakuliah</li>
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
                <h3 class="card-title">Form Input Data Matakuliah</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
             
              <form action="{{url('admin/master/matakuliah/ubahproses')}}" role="form" method="post">
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

                @foreach($datamatakuliah as $d)
                <div class="card-body">
                  
                  <input type="hidden" name="kodematakuliah" value="{{ $d->kodematakuliah }}">

                  <div class="form-group">
                    <label for="exampleInputNamaMatakuliah">Nama Matakuliah</label>
                    <input type="text" name="namamatakuliah" class="form-control" id="exampleInputNamaMatakuliah" value="{{$d->namamatakuliah}}">
                  </div>
                 
                  <div class="form-group">
                    <label for="exampleInputTotalSKS">Total SKS</label>
                    <input type="number" name="totalsks" class="form-control" id="exampleInputTotalSKS" value="{{$d->sks}}" min="1" max="10">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputTotalPertemuan">Total Pertemuan</label>
                    <input type="number" name="totalpertemuan" class="form-control" id="exampleInputTotalPertemuan" value="{{$d->totalpertemuan}}" min="1" max="5">
                  </div>

                  <div class="form-group">
                    <label for="exampleInputNisbi">Nisbi Minimal</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="nisbi" data-toggle="dropdown" id="exampleInputNisbi">
                      @if($d->nisbimin == "A")
                        <option value="A" selected>A</option>
                        <option value="AB">AB</option>
                        <option value="B">B</option>
                        <option value="BC">BC</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                      @elseif($d->nisbimin == "AB")
                        <option value="AB" selected>AB</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="BC">BC</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                      @elseif($d->nisbimin == "B")
                        <option value="B" selected>B</option>
                        <option value="A">A</option>
                        <option value="AB">AB</option>
                        <option value="BC">BC</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                      @elseif($d->nisbimin == "BC")
                        <option value="BC" selected>BC</option>
                        <option value="A">A</option>
                        <option value="AB">AB</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                      @elseif($d->nisbimin == "C")
                        <option value="C" selected>C</option>
                        <option value="A">A</option>
                        <option value="AB">AB</option>
                        <option value="B">B</option>
                        <option value="BC">BC</option>
                        <option value="D">D</option>
                      @elseif($d->nisbimin == "D")
                        <option value="D" selected>D</option>
                        <option value="A" >A</option>
                        <option value="AB">AB</option>
                        <option value="B">B</option>
                        <option value="BC">BC</option>
                        <option value="C">C</option>
                      @endif
                    </select>
                  </div>

                    <div class="form-group">
                    <label for="exampleInputSemester">Semester</label>
                    <br>
                    <select class="btn btn-primary dropdown-toggle" name="semester" data-toggle="dropdown" id="exampleInputSemester">
                      @foreach($semester as $s)
                        @if($d->semester_idsemester == $s->idsemester)
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
                    <select class="btn btn-primary dropdown-toggle" name="tahunakademik" data-toggle="dropdown" id="exampleInputTahunAkademik">
                       @foreach($tahun_akademik as $t)
                        @if($d->thnakademik_idthnakademik == $t->idtahunakademik)
                           <option value="{{$t->idtahunakademik}}" selected>{{$t->tahun}}</option>
                        @else
                          <option value="{{$t->idtahunakademik}}">{{$t->tahun}}</option>
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
