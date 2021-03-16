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
            <h1 class="m-0 text-dark">Daftar Data Konsultasi Mahasiswa</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('ketuajurusan')}}">Home</a></li>
              <li class="breadcrumb-item active">Daftar Data Konsultasi Mahasiswa</li>
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
            <h3 class="card-title">Data Konsultasi</h3>
          </div>  
          <div class="card-body">
            <table id="tabel_konsultasi" class="table table-bordered table-striped">
              <thead>
                <tr> 
                  <th width="1%">No.</th>
                  <th width="1%">Tanggal</th>
                  <th width="1%">Dosen Wali</th>
                  <th width="1%">Topik</th>
                  <th width="1%">Tahun Akademik</th>
                  <th width="1%">Konsultasi Selanjutnya</th>
                  <th width="1%">Konfirmasi</th>
                  <th width="1%">Mahasiswa</th>
                  <th width="1%">Detail</th>
                </tr>
              </thead>
              <tbody>
                @foreach($data_konsultasi as $no => $dk)
                <tr>
                  <td>{{$no+1}}</td>
                  <td>{{$dk->tanggalkonsultasi}}</td>
                  <td>{{$dk->namadosen}} ({{$dk->npkdosen}})</td>
                  <td>{{$dk->namatopik}}</td>
                  <td>{{$dk->semester}} {{$dk->tahun}}</td>
                  <td>{{$dk->konsultasiselanjutnya}}</td>
                  <td>
                    @if($dk->konfirmasi == "0")
                    <i class="fas fa-thumbs-down btn btn-danger btn-sm"></i>
                    @else
                    <i class="fas fa-thumbs-up btn btn-success btn-sm"></i>
                    @endif
                  </td>
                  <td>{{$dk->namamahasiswa}} ({{$dk->nrpmahasiswa}})</td>
                  <td>
                    <a href="#" class="fas fa-eye" data-toggle="modal" data-target="#detailKonsultasi_{{$dk->idkonsultasi}}"></a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>  
          </div>
        </div>
        @foreach($data_konsultasi as $d)
        <div id="detailKonsultasi_{{$d->idkonsultasi}}" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <!-- konten modal-->
            <div class="modal-content">
              <!-- heading modal -->
              <div class="modal-header">
                <h4 class="modal-title">Detail Konsultasi</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <!-- body modal -->
              <div class="modal-body">
                <p><b>{{$d->namatopik}}</b></p>
                <table class="table table-bordered table-hover">
                  <tr>
                   <th>Tanggal</th>
                   <td>{{$d->tanggalkonsultasi}}</td>
                  </tr>
                  <tr>
                   <th>Permasalahan</th>
                   <td>{{$d->permasalahan}}</td>
                  </tr>
                  <tr>
                   <th>Solusi:</th>
                   <td>{{$d->solusi}}</td>
                  </tr>
                  <tr>
                   <th>Konsultasi Berikutnya:</th>
                   <td>{{$d->konsultasiselanjutnya}}</td>
                  </tr>
                  <tr>
                  @if($d->konfirmasi == 0)
                    <th>Status Konfirmasi:</th>
                    <td>Belum Disetujui</td>
                  @else
                    <th>Status Konfirmasi:</th>
                    <td>Disetujui</td>
                  @endif
                  </tr>
                </table>
              </div>
              <!-- footer modal -->
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
    $('#tabel_konsultasi').DataTable({
      "dom": '<"pull-right"f><"pull-left"l>tip'
    });
  });
</script>
@endpush