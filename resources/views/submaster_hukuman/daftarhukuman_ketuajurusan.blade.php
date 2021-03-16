<!--Wajib untuk inisialisasi file views/layouts/appadmin-->
@extends('layouts.appketuajurusan')

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
            <h1 class="m-0 text-dark">Daftar Data Hukuman Mahasiswa</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('ketuajurusan')}}">Home</a></li>
              <li class="breadcrumb-item active">Daftar Data Hukuman Mahasiswa</li>
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
    
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Hukuman</h3>
          </div>  
          <div class="card-body">
            <table id="tabel_hukuman" class="table table-bordered table-striped">
              <thead>
                <tr> 
                  <th width="1%">Tanggal Input</th>
                  <th width="1%">Nama Dosen</th>
                  <th width="1%">Keterangan</th>
                  <th width="1%">Tanggal Konfirmasi</th>
                  <th width="1%">Status</th>
                  <th width="1%">Penilaian</th>
                  <th width="1%">Mahasiswa</th>
                  <th width="1%">Masa Berlaku</th>
                  <th width="1%">Unggah Berkas</th>
                </tr>
              </thead>
              <tbody>
                 @foreach($data_hukuman as $no => $d)
                <tr>
                  <td>{{$d->tanggalinput}}</td>
                  <td>{{$d->namadosen}} ({{$d->npkdosen}})</td>
                  <td>{{$d->keterangan}}</td>
                  <td>{{$d->tanggalkonfirmasi}}</td>
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
                  <td>{{$d->namamahasiswa}} ({{$d->nrpmahasiswa}})</td>
                  <td>{{$d->masaberlaku}}</td>
                  <td>
                    @if($d->status == "0")
                      <p style="color: red">Berkas tidak tersedia</p>
                    @else
                      <form action="{{url('ketuajurusan/submaster/hukuman/prosesunduh/'.$d->idhukuman)}}" role="form" method="post">
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