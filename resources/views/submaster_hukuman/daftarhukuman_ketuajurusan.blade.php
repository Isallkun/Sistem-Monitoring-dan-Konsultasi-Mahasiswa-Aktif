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
              <li class="breadcrumb-item active">Daftar Data Hukuman Mahasiswa</a></li>
            
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    
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
                  <th width="1%">Mahasiswa</th>
                  <th width="1%">Hukuman</th>
                  <th width="1%">Status Hukuman</th>
                  <th width="1%">Dosen Wali</th>
                  <th width="1%">Detail</th>
                </tr>
              </thead>
              <tbody>
                 @foreach($data_hukuman as $no => $d)
                <tr>
                  <td>{{$d->tanggalinput}}</td>
                  <td>{{$d->namamahasiswa}} <br> ({{$d->nrpmahasiswa}} )</td>
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
                
                   <td>{{$d->namadosen}} <br> ({{$d->npkdosen}} )</td>
                  
                  
                  <td>
                    <a href="#" class="fas fa-eye" data-toggle="modal" data-target="#detail_{{$d->idhukuman}}"></a>
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
                    <th>Penilaian </th>
                    <td>
                      @if($d->penilaian == "kurang")
                        <a href="#" class="btn btn-outline-danger btn-sm">Kurang</a>
                      @elseif($d->penilaian == "cukup")
                        <a href="#" class="btn btn-outline-warning btn-sm">Cukup</a>
                      @elseif($d->penilaian == "baik")
                        <a href="#" class="btn btn-outline-success btn-sm">Baik</a>
                      @else
                        <a href="#" class="btn btn-outline-info btn-sm">Tidak ada nilai</a>
                      @endif
                    </td>
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
                      <form action="{{url('ketuajurusan/submaster/hukuman/prosesunduh/'.$d->idhukuman)}}" role="form" method="post">
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
<script>
  $(function () {
    $('#tabel_hukuman').DataTable({
      "dom": '<"pull-right"f><"pull-left"l>tip'
    });
  });

</script>
@endpush