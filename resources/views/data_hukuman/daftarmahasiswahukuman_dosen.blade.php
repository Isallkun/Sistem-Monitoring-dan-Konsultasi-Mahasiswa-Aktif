<!--Wajib untuk inisialisasi file views/layouts/appadmin-->
@extends('layouts.appdosen')

@push('styles')
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
            <h1 class="m-0 text-dark">Daftar Mahasiswa (Hukuman)</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('dosen')}}">Home</a></li>
              <li class="breadcrumb-item active">Daftar MHS Hukuman</li>
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
            <h3 class="card-title">Data Mahasiswa (Hukuman)</h3>
          </div>  
          <div class="card-body">
             <table id="tabel_mahasiswa_wali" class="table table-bordered table-striped">
              <thead>
                <tr> 
                  <th width="1%">No.</th>
                  <th width="1%">Mahasiswa</th>
                  <th width="1%">Jenis Kelamin</th>
                  <th width="1%">Angkatan</th>
                  <th width="1%">Status</th>
                  <th width="1%">Kontak</th>
                  <th width="1%">Action</th>
                </tr>
              </thead>
              <tbody>
                 @foreach($mahasiswa_wali as $no => $m)
                <tr>
                  <td>{{$no+1}}</td>
                  <td>{{$m->namamahasiswa}} <br> ({{$m->nrpmahasiswa}})</td>
                  <td>{{$m->jeniskelamin}} </td>
                  <td>{{$m->tahun}}</td>
                  <td>{{$m->status}}</td>
                  <td>{{$m->email}} <br> {{$m->telepon}}</td>
                  <td>
                    <a href="{{url('dosen/data/hukuman/detail/'.$m->nrpmahasiswa)}}" class="btn btn-primary">Detail</a>
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
    $('#tabel_mahasiswa_wali').DataTable({
      "dom": '<"pull-right"f><"pull-left"l>tip'
    });
  });

  $(document).ready(function(){
      setTimeout(function() {
          location.reload();
      },10000);
  })

</script>
@endpush