<!--Wajib untuk inisialisasi file views/layouts/appadmin-->
@extends('layouts.appmahasiswa')

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
            <h1 class="m-0 text-dark">Daftar Data Hukuman</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('mahasiswa')}}">Home</a></li>
              <li class="breadcrumb-item active">Daftar Data Hukuman</li>
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

    @if (\Session::has('Error'))
      <div class="alert alert-danger alert-block">
        <ul>
            <li>{!! \Session::get('Error') !!}</li>
        </ul>
      </div>
    @endif
    
    
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
         @if(!empty($notifikasi_hukuman))
          <div class="alert alert-info">
            <p style="font-weight: bold">Informasi Masa Berlaku Hukuman: </p>
            @foreach($notifikasi_hukuman as $no => $d)
              @if($d->total <= "30" AND $d->total > "0")
                <ul>
                  <li>
                    {{$d->namamahasiswa}} ({{$d->nrpmahasiswa}}) memiliki masa berlaku hukuman kurang dari {{$d->total}} Hari.
                    <br>
                    ID: {{$d->idhukuman}} &nbsp [Hukuman: {{$d->namahukuman}}]
                  </li>
                </ul>
              @endif
            @endforeach
          </div>
        @endif

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Hukuman</h3>
          </div>  
          <div class="card-body">
            <table id="tabel_hukuman" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th width="1%">Tanggal Input</th> 
                  <th width="1%">Dosen Wali</th>
                  <th width="1%">Hukuman</th>
                  <th width="1%">Status</th>
                  <th width="1%">Nilai</th>
                  <th width="1%">Detail</th>
                  <th width="1%">Unggah Berkas</th>
                </tr>
              </thead>
              <tbody>
                 @foreach($data_hukuman as $no => $d)
                <tr>
                  <td>{{$d->tanggalinput}}</td>
                  <td>{{$d->namadosen}} <br> ({{$d->npkdosen}})</td>
                  <td>{{$d->namahukuman}}</td>
                  <td>
                    @if($d->status == "0")
                      <a href="#" class="btn btn-danger btn-sm">Tidak Aktif</a>
                    @elseif($d->status == "1")
                      <a href="#" class="btn btn-success btn-sm">Aktif</a>
                    @else
                      <a href="#" class="btn btn-dark btn-sm">Masa Berlaku Habis</a>
                    @endif
                  </td>
                  <td>
                    @if($d->status == "1")
                      @if($d->penilaian == "kurang")
                        <a href="#" class="btn btn-outline-danger btn-sm">Kurang</a>
                      @elseif($d->penilaian == "cukup")
                        <a href="#" class="btn btn-outline-warning btn-sm">Cukup</a>
                      @elseif($d->penilaian == "baik")
                        <a href="#" class="btn btn-outline-success btn-sm">Baik</a>
                      @else
                        <a href="#" class="btn btn-outline-info btn-sm">Menunggu penilaian</a>
                      @endif
                    @elseif($d->status == "0")
                      <a href="#" class="btn btn-outline-dark btn-sm disabled">Belum ada nilai</a>
                    @else
                      @if($d->penilaian == "kurang")
                        <a href="#" class="btn btn-danger btn-sm">Kurang</a>
                      @elseif($d->penilaian == "cukup")
                        <a href="#" class="btn btn-warning btn-sm">Cukup</a>
                      @elseif($d->penilaian == "baik")
                        <a href="#" class="btn btn-success btn-sm">Baik</a>
                      @else
                      <a href="#" class="btn btn-info btn-sm">Tidak ada nilai</a>
                      @endif
                    @endif
                  </td>
                  <td>
                    <a href="#" class="fas fa-eye" data-toggle="modal" data-target="#detail_{{$d->idhukuman}}"></a>
                  </td>
                  
                  <td>
                    @if($d->status == "0")
                      <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#upload_{{$d->idhukuman}}">Upload</a>
                    @else
                      <form action="{{url('mahasiswa/data/hukumanmahasiswa/prosesunduh/'.$d->idhukuman)}}" role="form" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="nrpmahasiswa" value="{{$d->nrpmahasiswa}}">
                        <button type="submit" class="btn btn-link">Lihat Berkas</button>
                      </form>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>  
          </div>
        </div>
        
        @foreach($data_hukuman as $d)
        <div id="detail_{{$d->idhukuman}}" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <!-- konten modal-->
            <div class="modal-content">
              <!-- heading modal -->
              <div class="modal-header">
                <h4 class="modal-title">Detail Hukuman Mahasiswa</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <!-- body modal -->
              <div class="modal-body">
                <p>
                  <b>ID Hukuman: {{$d->idhukuman}} <br>
                  {{$d->namahukuman}}</b>
                </p>
                <table class="table table-head-fixed text-nowrap">
                  <tr>
                    <th>Mahasiswa</th>
                    <td>{{$d->namamahasiswa}} - {{$d->nrpmahasiswa}}</td>
                  </tr>
                  <tr>
                    <th>Tanggal Input</th>
                    <td>{{$d->tanggalinput}}</td>
                  </tr>
                  <tr>
                    <th>Kategori </th>
                    <td>
                      @if($d->kategori == 'ringan')
                      <a href="#" class="btn btn-success btn-sm">Kategori Ringan</a>
                      @elseif($d->kategori == 'sedang')
                      <a href="#" class="btn btn-warning btn-sm">Kategori Sedang</a>
                      @else
                       <a href="#" class="btn btn-danger btn-sm">Kategori Berat</a>
                      @endif
                    </td>
                  </tr>
                  <tr>
                    <th>Keterangan </th>
                    <td>{{$d->keterangan}}</td>
                  </tr>
                  <tr>
                    <th>Tanggal Konfirmasi </th>
                    <td>{{$d->tanggalkonfirmasi}}</td>
                  </tr>
                  <tr>
                    <th>Masa Berlaku </th>
                    <td>{{$d->masaberlaku}}</td>
                  </tr>
                  
                </table>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
        @endforeach


        @foreach($data_hukuman as $d)
        <div id="upload_{{$d->idhukuman}}" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <!-- konten modal-->
            <div class="modal-content">
              <!-- heading modal -->
              <div class="modal-header">
                <h4 class="modal-title">Unggah Berkas Hukuman</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <!-- body modal -->
              <form action="{{url('mahasiswa/data/hukumanmahasiswa/prosesunggah/'.$d->idhukuman)}}" role="form" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" name="nrpmahasiswa" value="{{$d->nrpmahasiswa}}">
                <div class="modal-body">
                  <p style="text-align: left;text-transform: uppercase;">
                    <b>{{$d->namamahasiswa}} - {{$d->nrpmahasiswa}}</b>
                  </p>
                
                  <p style="text-align: left;">
                  ID: {{$d->idhukuman}} <br>
                  {{$d->keterangan}}
                  </p>
                  <!--UNTUK UPLOAD -->
                  
                  <div class="unggah">
                    Pilih File: <input type="file" name="berkas[]" multiple="multiple" required>
                  </div>
                  <br>
                  <p style="text-transform: uppercase; font-weight: bold;font-size: 12px; color: red">* maksimal upload: 2 Berkas</p>
                </div>

                
                <!-- footer modal -->
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        @endforeach



      </div>
    </section>
@endsection
 
@push('scripts')
<script>
  $(function () {
    $('#tabel_hukuman').DataTable({
      "dom": '<"pull-right"f><"pull-left"l>tip'
    });
  });

  $(document).ready(function(){
      setTimeout(function() {
          location.reload();
      },900000);
  })
</script>

@endpush