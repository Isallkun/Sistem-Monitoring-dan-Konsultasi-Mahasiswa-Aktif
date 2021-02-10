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
            <h1 class="m-0 text-dark">Daftar Data Konsultasi</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('mahasiswa')}}">Home</a></li>
              <li class="breadcrumb-item active">Daftar Data Konsultasi</li>
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

    
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

         @if(!empty($konsultasi_berikutnya))
          <div class="alert alert-primary">
            <p style="font-weight: bold">Informasi Jadwal Konsultasi: </p>
            @foreach($konsultasi_berikutnya as $no => $n)
            ({{$no+1}}). Tanggal {{$n->konsultasiselanjutnya}}, Anda harus menemui Bapak/Ibu dosen wali.
            <br>
            @endforeach
          </div>
        @endif

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Konsultasi (Menunggu Konfirmasi)</h3>
          </div>  
          <div class="card-body">
            <table id="tabel_konsultasi_menunggu" class="table table-bordered table-striped">
              <thead>
                <tr> 
                  <th width="1%">No.</th>
                  <th width="1%">Tanggal</th>
                  <th width="1%">Tahun Akademik</th>
                  <th width="1%">Topik</th>
                  <th width="1%">Konsultasi Selanjutnya</th>
                  <th width="1%">Konfirmasi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($data_konsultasi as $no => $dk)
                <tr>
                  <td>{{$no+1}}</td>
                  <td>{{$dk->tanggalkonsultasi}}</td>
                  <td>{{$dk->semester}} {{$dk->tahun}}</td>
                  <td>{{$dk->namatopik}}</td>
                  <td>{{$dk->konsultasiselanjutnya}}</td>
                  <td>
                    @if($dk->konfirmasi == "0")
                    <a href="{{url('mahasiswa/data/konsultasimahasiswa/proseskonfirmasi/'.$dk->idkonsultasi)}}" class="btn btn-primary btn-sm">konfirmasi</a>
                    @else
                    <i class="fas fa-thumbs-up btn btn-success btn-sm"></i>
                    @endif
                  </td>
                 
                </tr>
                @endforeach
              </tbody>
            </table>  
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Seluruh Konsultasi</h3>
          </div>  
          <div class="card-body">
            <table id="tabel_konsultasi" class="table table-bordered table-striped">
              <thead>
                <tr> 
                  <th width="1%">No.</th>
                  <th width="1%">Tanggal</th>
                  <th width="1%">Topik</th>
                  <th width="1%">Tahun Akademik</th>
                  <th width="1%">Konsultasi Selanjutnya</th>
                  <th width="1%">Konfirmasi</th>
                  <th width="1%">Detail</th>
                </tr>
              </thead>
              <tbody>
                @foreach($semua_konsultasi as $ds)
                <tr>
                  <td>{{$ds->idkonsultasi}}</td>
                  <td>{{$ds->tanggalkonsultasi}}</td>
                  <td>{{$ds->namatopik}}</td>
                  <td>{{$ds->semester}} {{$ds->tahun}}</td>
                  <td>{{$ds->konsultasiselanjutnya}}</td>
                  <td>
                    @if($ds->konfirmasi == "0")
                    <i class="fas fa-thumbs-down btn btn-danger btn-sm"></i>
                    @else
                    <i class="fas fa-thumbs-up btn btn-success btn-sm"></i>
                    @endif
                  </td>
                  <td>
                    <a href="#" class="fas fa-eye" data-toggle="modal" data-target="#detailKonsultasi_{{$ds->idkonsultasi}}"></a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>  
          </div>
        </div>


        @foreach($semua_konsultasi as $d)
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
                <p style="font-weight: bold; text-transform: uppercase;">{{$d->namatopik}} </p>
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
                <p style="text-align: left; font-weight: bold;">Dosen Wali : {{$d->namadosen}} - {{$d->npkdosen}}
                  </p>
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