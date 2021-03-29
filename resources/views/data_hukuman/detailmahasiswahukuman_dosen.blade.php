<!--Wajib untuk inisialisasi file views/layouts/appadmin-->
@extends('layouts.appdosen')

@push('styles')
<!-- Untuk menambahkan style baru -->

<!-- SweetAlert2 -->
<link rel="stylesheet" href="{{url('asset/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')}}">
<!-- Toastr -->
<link rel="stylesheet" href="{{url('asset/plugins/toastr/toastr.min.css')}}">


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
            <h1 class="m-0 text-dark">Detail Data Hukuman Mahasiswa</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('dosen')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{url('dosen/data/hukuman')}}">Daftar Mahasiswa (Hukuman)</a></li>
              <li class="breadcrumb-item active">Detail Daftar Hukuman</li>
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
        @foreach($mahasiswa as $m)
        <a href="{{ url('dosen/data/hukuman/detail/tambah/'.$m->nrpmahasiswa) }}" class="btn btn-primary" role="button">Tambah Data</a>
        @endforeach
        <br><br>

         @if(!empty($notifikasi_hukuman))
          <div class="alert alert-info">
            <p style="font-weight: bold">Informasi Masa Berlaku Hukuman: </p>
            @foreach($notifikasi_hukuman as $no => $d)
              @if($d->total <= "30" AND $d->total > "0")
                <ul>
                  <li>
                    {{$d->namamahasiswa}} ({{$d->nrpmahasiswa}}) memiliki masa berlaku hukuman kurang dari {{$d->total}} Hari.
                    <br>
                    ID: {{$d->idhukuman}} &nbsp [Keterangan: {{$d->keterangan}}]
                  </li>
                </ul>
              @endif
            @endforeach
          </div>
        @endif

        <div class="card">
          <div class="card-header">
            @foreach($mahasiswa as $m)
            <h3 class="card-title">Data Hukuman ({{$m->namamahasiswa}} - {{$m->nrpmahasiswa}})</h3>

            <a href="#" class="fas fa-info-circle float-right toastrDefaultInfo"></a>
            @endforeach
          </div>  
          <div class="card-body">
            <table id="tabel_hukuman" class="table table-bordered table-striped">
              <thead>
                <tr> 
                  <th width="1%">Tanggal Input</th>
                  <th width="1%">Dosen Wali</th>
                  <th width="1%">Hukuman</th>
                  <th width="1%">Status Hukuman</th>
                  <th width="1%">Nilai</th>
                  <th width="1%">Detail</th>
                  <th width="1%">Action</th>
                </tr>
              </thead>
              <tbody>
                 @foreach($data_hukuman as $no => $d)
                <tr>
                  <td>{{$d->tanggalinput}}</td>
                  <td>{{$d->namadosen}} <br> ({{$d->npkdosen}} )</td>
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
                      <a href="{{url('dosen/data/hukuman/detail/ubahnilai/'.$d->idhukuman)}}" class="btn btn-outline-danger btn-sm">Kurang</a>
                    @elseif($d->penilaian == "cukup")
                      <a href="{{url('dosen/data/hukuman/detail/ubahnilai/'.$d->idhukuman)}}" class="btn btn-outline-warning btn-sm">Cukup</a>
                    @elseif($d->penilaian == "baik")
                      <a href="{{url('dosen/data/hukuman/detail/ubahnilai/'.$d->idhukuman)}}" class="btn btn-outline-success btn-sm">Baik</a>
                    @else
                      <a href="{{url('dosen/data/hukuman/detail/ubahnilai/'.$d->idhukuman)}}" class="btn btn-outline-info btn-sm">Menunggu penilaian</a>
                    @endif
                  @elseif($d->status == "0")
                    <a href="#" class="btn btn-outline-dark btn-sm disabled">Belum ada nilai</a>
                  @else
                     @if($d->penilaian == "kurang")
                      <a href="#" class="btn btn-outline-danger btn-sm">Kurang</a>
                    @elseif($d->penilaian == "cukup")
                      <a href="#" class="btn btn-outline-warning btn-sm">Cukup</a>
                    @elseif($d->penilaian == "baik")
                      <a href="#" class="btn btn-outline-success btn-sm">Baik</a>
                    @else
                      <a href="#" class="btn btn-outline-info btn-sm">Tidak ada nilai</a>
                    @endif
                  @endif
                  </td>
                  
                  <td>
                    <a href="#" class="fas fa-eye" data-toggle="modal" data-target="#detail_{{$d->idhukuman}}"></a>
                  </td>
                  
                  <td>
                    @if($d->status == "0")
                    <a href="{{url('dosen/data/hukuman/detail/ubah/'.$d->idhukuman)}}" class="btn btn-warning">Ubah</a>
                    @endif

                    <form method="get" action="{{url('dosen/data/hukuman/detail/hapus/'.$d->idhukuman)}}">
                      <input type="hidden" name="mahasiswa" value="{{$d->nrpmahasiswa}}">
                      <button type="submmit" class="btn btn-danger">Hapus</button>
                    </form>
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
                  <tr>
                    <th>Download</th>
                    <td>
                      @if($d->status == "0")
                      <p style="color: red">Berkas tidak tersedia</p>
                      @else
                      <form action="{{url('dosen/data/hukuman/detail/prosesunduh/'.$d->idhukuman)}}" role="form" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="nrpmahasiswa" value="{{$d->nrpmahasiswa}}">
                        <button type="submit" class="btn btn-info">Unduh berkas</button>
                      </form>
                      @endif
                    </td>
                    <br>
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

      </div>
    </section>
@endsection
 
@push('scripts')
<!-- SweetAlert2 -->
<script src="{{url('asset/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
<!-- Toastr -->
<script src="{{url('asset/plugins/toastr/toastr.min.js')}}"></script>

<script>
  $(function () {
    $('#tabel_hukuman').DataTable({
      "dom": '<"pull-right"f><"pull-left"l>tip'
    });



    @foreach($mahasiswa as $m)   
      var rating = {{$m->total_rate}};              
    @endforeach

    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });

    $('.toastrDefaultInfo').click(function() {
      toastr.info('Rate &nbsp&nbsp&nbsp: ' + 
                  '@for($i=0; $i < $m->total_rate; $i++)' +
                    '<span class="fa fa-star checked" ></span>' +
                  '@endfor'+
                  '@for($i=0; $i < (5-$m->total_rate); $i++)'+
                    '<span class="fa fa-star"></span>'+
                  '@endfor' + '<br>' +

                  'Total Konsultasi Dosen Wali : ' + {{$konsultasi_mahasiswa}} + '<br>'+
                  'Total Hukuman Mahasiswa &nbsp: ' + {{$hukuman_mahasiswa}} + '<br>' 
                  ) 
    });
  });
</script>
@endpush