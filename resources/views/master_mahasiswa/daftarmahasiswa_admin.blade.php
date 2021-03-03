<!--Wajib untuk inisialisasi file views/layouts/appadmin-->
@extends('layouts.appadmin')

@push('styles')
  <!-- Untuk menambahkan style baru -->
  <style type="text/css">
  .checked 
  { color: orange; }
  </style>
@endpush
 
<!-- Isi dari yield -->
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Daftar Data Mahasiswa</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
              <li class="breadcrumb-item active">Daftar Data Mahasiswa</li>
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

    @if (\Session::has('Failed'))
      <div class="alert alert-warning alert-block">
        <ul>
            <li>{!! \Session::get('Failed') !!}</li>
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
        <a href="{{ url('admin/master/mahasiswa/tambah') }}" class="btn btn-primary" role="button">Tambah Data</a>
        <br><br>

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Mahasiswa</h3>
          </div>

          <div class="card-body">
            <table id="tabel_mahasiswa" class="table table-bordered table-striped">
              <thead>
                <tr> 
                  <th>NRP</th>
                  <th>Nama</th>
                  <th>Jenis Kelamin</th>
                  <th>Email</th>
                  <th>Telepon</th>
                  <th>Status</th>
                  <th>Username</th>
                  <th>Detail</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($mahasiswa as $m)
                <tr>
                  <td>{{$m->nrpmahasiswa}}</td>
                  <td>{{$m->namamahasiswa}}</td>
                  <td>{{$m->jeniskelamin}}</td>
                  <td>{{$m->email}}</td>
                  <td>{{$m->telepon}}</td>
                  <td>{{$m->status}}</td>
                  <td>{{$m->users_username}}</td>
                  <td>
                    <a href="#" class="fas fa-eye" data-toggle="modal" data-target="#detailMahasiswa_{{$m->nrpmahasiswa}}"></a>
                  </td>
                  
                  <td>
                   <a href="{{url('admin/master/mahasiswa/ubah/'.$m->nrpmahasiswa)}}" class="btn btn-warning">Ubah</a>

                  <form method="get" action="{{url('admin/master/mahasiswa/hapus/'.$m->nrpmahasiswa)}}">
                     <input type="hidden" name="username" value="{{$m->users_username}}">
                      <input type="hidden" name="idgamifikasi" value="{{$m->gamifikasi_idgamifikasi}}">
                     <button type="submmit" class="btn btn-danger">Hapus</button>
                   </form>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

        @foreach($mahasiswa as $m)
        <div id="detailMahasiswa_{{$m->nrpmahasiswa}}" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <!-- konten modal-->
            <div class="modal-content">
              <!-- heading modal -->
              <div class="modal-header">
                <h4 class="modal-title">Detail Mahasiswa</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <!-- body modal -->
              <div class="modal-body">
                <p><b>{{$m->namamahasiswa}} - {{$m->nrpmahasiswa}}</b></p>
                <table class="table table-bordered table-hover">
                  <tr>
                   <th>Angkatan</th>
                   <td>{{$m->tahun}}</td>
                  </tr>
                  <tr>
                   <th>Tempat, Tanggal Lahir</th>
                   <td>{{$m->tempatlahir}}, {{$m->tanggallahir}}</td>
                  </tr>
                  <tr>
                   <th>Alamat</th>
                   <td>{{$m->alamat}}</td>
                  </tr>
                  <tr>
                   <th>Jurusan</th>
                   <td>{{$m->namajurusan}}</td>
                  </tr>
                  <tr>
                   <th>Dosen Wali</th>
                   <td>{{$m->namadosen}} ({{$m->npkdosen}})</td>
                  </tr>
                  <tr>
                    <th>Chart</th>
                    <td>    
                      <div style="width: 365px;height: 200px">
                        <canvas id="radarChart_{{$m->nrpmahasiswa}}"></canvas>
                      </div>
                    </td>
                  </tr>

                  <tr>
                   <th>Level</th>
                   <td>
                    <a class="">
                      @if($m->level == "Bronze")
                        <img src="{{url('rank_pictures/Bronze.png')}}" class="rounded mx-auto d-block" alt="rank image"> 
                        <a class="float-left" style="font-weight: bold;">BRONZE MEMBER</a>
                      @elseif($m->level == "Silver")
                        <img src="{{url('rank_pictures/Silver.png')}}" class="rounded mx-auto d-block" alt="rank image">
                        <a class="float-left" style="font-weight: bold;">SILVER MEMBER</a>
                      @else 
                        <img src="{{url('rank_pictures/Gold.png')}}" class="rounded mx-auto d-block" alt="rank image">
                        <a class="float-left" style="font-weight: bold;">GOLD MEMBER</a>
                      @endif
                  
                      <br>
                      <div class="float-left">
                        @for($i=0; $i < $m->aspek_manfaat_konsultasi; $i++)
                          <span class="fa fa-star checked"></span>
                        @endfor
                        @for($i=0; $i < (5-$m->aspek_manfaat_konsultasi); $i++)
                          <span class="fa fa-star"></span>
                        @endfor 
                      </div>
                    
                    </a> 
                   </td>
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
    $('#tabel_mahasiswa').DataTable({
      "dom": '<"pull-right"f><"pull-left"l>tip'
    });

    
    @foreach($mahasiswa as $m)

      var value=[
        {{$m->aspek_durasi_konsultasi}},
        {{$m->aspek_manfaat_konsultasi}},
        {{$m->aspek_sifat_konsultasi}},
        {{$m->aspek_interaksi}},
        {{$m->aspek_pencapaian}}
      ];
  
      new Chart(document.getElementById("radarChart_{{$m->nrpmahasiswa}}"), {
        type: 'radar',
        data: {
          labels: ["Durasi konsultasi", "Manfaat konsultasi", "Sifat mahasiswa dalam konsultasi", "Interaksi mahasiswa", "Pencapaian mahasiswa"],
          datasets: [{
            label: "#nilai per bagian",
            data: value,

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
    @endforeach

  });
</script>
@endpush