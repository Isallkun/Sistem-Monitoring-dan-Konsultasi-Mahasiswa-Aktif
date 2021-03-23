<!--Wajib untuk inisialisasi file views/layouts/appadmin-->
@extends('layouts.appketuajurusan')

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
            <h1 class="m-0 text-dark">Detail Data Mahasiswa</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('ketuajurusan')}}">Home</a></li>
              <li class="breadcrumb-item active">Detail Daftar Mahasiswa</li>
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
        <div class="row">
          <div class="col-12">
            <div class="card card-primary card-tabs">
              <div class="card-header p-0 pt-0">
                <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                  <li class="pt-2 px-3"><h3 class="card-title">{{$data_mahasiswa[0]->namamahasiswa}} - {{$data_mahasiswa[0]->nrpmahasiswa}}</h3></li>
                  <li class="nav-item">
                    <a class="nav-link active" id="custom-tabs-two-profil-tab" data-toggle="pill" href="#custom-tabs-two-profil" role="tab" aria-controls="custom-tabs-two-profil" aria-selected="true">Profil Mahasiswa</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-two-konsultasi-tab" data-toggle="pill" href="#custom-tabs-two-konsultasi" role="tab" aria-controls="custom-tabs-two-konsultasi" aria-selected="false">Konsultasi</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-two-nonkonsultasi-tab" data-toggle="pill" href="#custom-tabs-two-nonkonsultasi" role="tab" aria-controls="custom-tabs-two-nonkonsultasi" aria-selected="false">Non Konsultasi</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-two-hukuman-tab" data-toggle="pill" href="#custom-tabs-two-hukuman" role="tab" aria-controls="custom-tabs-two-hukuman" aria-selected="false">Hukuman</a>
                  </li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-two-tabContent">
                  
                  <div class="tab-pane fade show active" id="custom-tabs-two-profil" role="tabpanel" aria-labelledby="custom-tabs-two-profil-tab">
                    @foreach($data_mahasiswa as $m)   
                    <div class="card card-primary card-outline">
                      <div class="card-body box-profile">
                        <div class="text-center">
                          <img class="profile-user-img img-fluid img-circle" src="{{url('data_pengguna/'. $m->profil )}}" alt="User profile picture">

                        </div>
                         <h3 class="profile-username text-center">{{$m->namamahasiswa}} - {{$m->nrpmahasiswa}}</h3>

                          <p class="text-muted text-center">
                          Student of Informatics Engineering <br>University of Surabaya
                          </p>

                        <ul class="list-group list-group-unbordered mb-3">
                          <li class="list-group-item">
                            <b>Chart Information</b> 
                            <div class="chart">
                              <canvas id="radarChart" width= "100%"; height="25%"></canvas>
                            </div>
                          </li>

                          <li class="list-group-item">
                            <b>Level Information</b> <br>

                            @if($m->level == "Bronze")
                              <img src="{{url('rank_pictures/Bronze.png')}}" class="rounded mx-auto d-block float-left" alt="rank image">
                              <a class="float-right" style="font-weight: bold;">BRONZE MEMBER</a>
                            @elseif($m->level == "Silver")
                              <img src="{{url('rank_pictures/Silver.png')}}" class="rounded mx-auto d-block float-left" alt="rank image">
                              <a class="float-right" style="font-weight: bold;">SILVER MEMBER</a>
                            @else 
                              <img src="{{url('rank_pictures/Gold.png')}}" class="rounded mx-auto d-block float-left" alt="rank image">
                              <a class="float-right" style="font-weight: bold;">GOLD MEMBER</a>
                            @endif

                            <br>

                            <div class="float-right">
                            @for($i=0; $i < $m->total; $i++)
                              <span class="fa fa-star checked" ></span>
                            @endfor
                            @for($i=0; $i < (5-$m->total); $i++)
                              <span class="fa fa-star"></span>
                            @endfor 
                            </div>
                          </li>
                          
                          <li class="list-group-item"></li>
                          
                          <li class="list-group-item">
                            <b>Jenis Kelamin</b> <a class="float-right">{{$m->jeniskelamin}} </a>
                          </li>
                          <li class="list-group-item">
                            <b>Tempat, Tanggal lahir</b> <a class="float-right">{{$m->tempatlahir}},{{$m->tanggallahir}} </a>
                          </li>
                          <li class="list-group-item">
                            <b>Email</b> <a class="float-right">{{$m->email}} </a>
                          </li>
                          <li class="list-group-item">
                            <b>Nomor Telepon</b> <a class="float-right">{{$m->telepon}} </a>
                          </li>
                           <li class="list-group-item">
                            <b>Alamat</b> <a class="float-right">{{$m->alamat}} </a>
                          </li>
                          <li class="list-group-item">
                            <b>Jurusan</b> <a class="float-right">{{$m->namafakultas}} - {{$m->namajurusan}} </a>
                          </li>
                          <li class="list-group-item">
                            <b>Status</b> <a class="float-right">{{$m->status}}</a>
                          </li>    
                        </ul>
                      </div>
                      <!-- /.card-header -->
                      <!-- /.card-body -->
                    </div>
                    @endforeach
                  </div>
                  
                  <div class="tab-pane fade" id="custom-tabs-two-konsultasi" role="tabpanel" aria-labelledby="custom-tabs-two-konsultasi-tab">
                    <table id="tabel_konsultasi" class="table table-bordered table-striped">
                      <thead>
                        <tr> 
                          <th>No.</th>
                          <th>Tanggal Konsultasi</th>
                          <th>Topik Konsultasi</th>
                          <th>Tahun Akademik</th>
                          <th>Konsultasi Selanjutnya</th>
                          <th>Konfirmasi</th>
                          <th>Detail</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($data_konsultasi as $no => $dk)
                        <tr>
                          <td>{{$no+1}}</td>
                          <td>{{$dk->tanggalkonsultasi}}</td>
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
                          <td>
                            <a href="#" class="fas fa-eye" data-toggle="modal" data-target="#detailKonsultasi_{{$dk->idkonsultasi}}"></a>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>  
                  </div>

                  <div class="tab-pane fade" id="custom-tabs-two-nonkonsultasi" role="tabpanel" aria-labelledby="custom-tabs-two-nonkonsultasi-tab">
                    <table id="tabel_nonkonsultasi" class="table table-bordered table-striped">
                      <thead>
                        <tr> 
                          <th width="1%">No.</th>
                          <th width="1%">Tanggal Input</th>
                          <th width="1%">Tanggal Pertemuan</th>
                          <th width="1%">Status</th>
                          <th width="1%">Dosen</th>
                          <th width="1%">Isi Pesan</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($data_nonkonsultasi as $no => $dn)
                        <tr>
                          <td>{{$no+1}}</td>
                          <td>{{$dn->tanggalinput}}</td>
                          <td>{{$dn->tanggalpertemuan}}</td>
                          <td>
                            @if($dn->status == "0")
                            <i class="btn btn-danger btn-sm"> Dalam proses...</i>
                            @else
                            <i class="btn btn-success btn-sm"> Selesai</i>
                            @endif
                          </td>
                          <td>{{$dn->namadosen}} {{$dn->npkdosen}}</td>
                          <td>{{$dn->pesan}}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>  
                  </div>

                  <div class="tab-pane fade" id="custom-tabs-two-hukuman" role="tabpanel" aria-labelledby="custom-tabs-two-hukuman-tab">
                    <table id="tabel_hukuman" class="table table-bordered table-striped">
                      <thead>
                        <tr> 
                          <th>No.</th>
                          <th>Dosen</th>
                          <th>Tanggal Input Hukuman</th>
                          <th>Hukuman</th>
                          <th>Keterangan</th>
                          <th>Status</th>
                          <th>Nilai</th>
                          <th>Tanggal Konfirmasi</th>
                          <th>Masa Berlaku</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($data_hukuman as $no => $dh)
                        <tr>
                          <td>{{$no+1}}</td>
                          <td>{{$dh->namadosen}} ({{$dh->npkdosen}})</td>
                          <td>{{$dh->tanggalinput}}</td>
                          <td>{{$dh->namahukuman}}</td>
                          <td>{{$dh->keterangan}}</td>
                          <td>
                            @if($dh->status != "0")
                              Belum Selesai
                            @else
                              Selesai
                            @endif
                          </td>
                          <td>
                            @if($dh->penilaian == "kurang")
                              <i class="btn btn-danger btn-sm">Kurang</i>
                            @elseif ($dh->penilaian == "cukup")
                              <i class="btn btn-warning btn-sm">Cukup</i>
                            @else 
                              <i class="btn btn-success btn-sm">Baik</i>
                            @endif
                            
                          </td>   
                          <td>{{$dh->tanggalkonfirmasi}}</td>   
                          <td>{{$dh->masaberlaku}}</td>      
                        </tr>
                        @endforeach
                      </tbody>
                    </table>  
                  </div>
                </div>
              </div>
              <!-- /.card -->
            </div>
          </div>
        </div>
      
       
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->

      @foreach($data_konsultasi as $dk)
          <div id="detailKonsultasi_{{$dk->idkonsultasi}}" class="modal fade" role="dialog">
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
                  <p><b>{{$dk->namatopik}}</b></p>
                  <table class="table table-bordered table-hover">
                    <tr>
                     <th>Tanggal</th>
                     <td>{{$dk->tanggalkonsultasi}}</td>
                    </tr>
                    <tr>
                     <th>Permasalahan</th>
                     <td>{{$dk->permasalahan}}</td>
                    </tr>
                    <tr>
                     <th>Solusi:</th>
                     <td>{{$dk->solusi}}</td>
                    </tr>
                    <tr>
                     <th>Konsultasi Berikutnya:</th>
                     <td>{{$dk->konsultasiselanjutnya}}</td>
                    </tr>
                    <tr>
                    @if($dk->konfirmasi == 0)
                      <th>Status Konfirmasi:</th>
                      <td>Belum Disetujui</td>
                    @else
                      <th>Status Konfirmasi:</th>
                      <td>Disetujui</td>
                    @endif
                    </tr>
                    <tr>
                     <th>Tahun akademik:</th>
                     <td>{{$dk->semester}} {{$dk->tahun}}</td>
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
    </section>
    
@endsection
 
@push('scripts')
<script >
  $(function () {
    
    var data_poin = [];
    @foreach($data_mahasiswa as $m)
      data_poin.push({{$m->avg_aspek1}});
      data_poin.push({{$m->avg_aspek2}});
      data_poin.push({{$m->avg_aspek3}});
      data_poin.push({{$m->avg_aspek4}});
      data_poin.push({{$m->avg_aspek5}});
    @endforeach

    new Chart(document.getElementById("radarChart"), {
      type: 'radar',
      data: {
        labels: ["Durasi konsultasi", "Manfaat konsultasi", "Sifat mahasiswa dalam konsultasi", "Interaksi mahasiswa", "Pencapaian mahasiswa"],
        datasets: [{
          label: "#rata-rata per bagian",
          data: data_poin,

          borderColor: [
          'rgba(255,99,132,1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)'
          ],
          borderWidth: 2
        }],
      },
      options: {
        tooltips: {
          callbacks: {
            label: function(tooltipItem, data) {
              return data.datasets[tooltipItem.datasetIndex].label + ": " + tooltipItem.yLabel;
            }
          }
        }
      }
    });
  });

</script>

<script>
  $(function () {
    $('#tabel_hukuman').DataTable({
      "dom": '<"pull-right"f><"pull-left"l>tip'
    });
  });

  $(function () {
    $('#tabel_konsultasi').DataTable({
      "dom": '<"pull-right"f><"pull-left"l>tip'
    });
  });

  $(function () {
    $('#tabel_nonkonsultasi').DataTable({
      "dom": '<"pull-right"f><"pull-left"l>tip'
    });
  });
</script>
@endpush