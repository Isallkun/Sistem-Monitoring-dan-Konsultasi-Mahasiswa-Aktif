<!--Wajib untuk inisialisasi file views/layouts/appadmin-->
@extends('layouts.appdosen')

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
            <h1 class="m-0 text-dark">Daftar Data Mahasiswa Wali</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('dosen')}}">Home</a></li>
              <li class="breadcrumb-item active">Daftar mahasiswa Wali</li>
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

        <form method="GET" action="#" enctype="multipart/form-data">
          {{ csrf_field() }}
          <input type="hidden" name="_token" value="{{csrf_token() }}"> 
          
          <label for="exampleInputPencarian">Pencarian Data: </label>

          <div class="form-group">            
            <select class="btn btn-primary dropdown-toggle btn-sm" name="pencarian" id="pencarian" data-toggle="dropdown">
              <option value="nrpmahasiswa">NRP Mahasiswa</option>
              <option value="namamahasiswa">Nama Mahasiswa</option>
              <option value="angkatan">Angkatan</option>
            </select>

            <input type="text" name="keyword" id="keyword" placeholder="Enter Keyword">

            <button type="submit" class="btn btn-light"><i class="fas fa-search"></i></button>

            <div id="mahasiswaList"></div>
          
          </div>

        </form>
        
        <div style="font-size: 12px">
          <b>Keterangan:</b>
          <br>
          <a href="#" class="btn btn-danger btn-sm"></a> 
          Keadaan mahasiswa membutuhkan perhatian lebih/khusus. 
          <br>
          <a href="#" class="btn btn-warning btn-sm"></a> 
          Keadaan mahasiswa dalam proses pemantauan/pengawasan. 
          <br>
          <a href="#" class="btn btn-success btn-sm"></a> 
          Keadaan mahasiswa dalam kondisi cukup baik.
        
          
        </div>
        <br>
        <!-- Small boxes (Stat box) -->
        <table class="table table-bordered table-striped">
          <thead>
            <tr> 
              <th width="1%">No.</th>
              <th width="1%">Nama</th>
              <th width="1%">NRP</th>
              <th width="1%">Angkatan</th>
              <th width="1%">SKS Kumulatif</th>
              <th width="1%">IPK</th>
              <th width="1%">IPS Terakhir</th>
              <th width="1%">Informasi</th>
              <th width="1%">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($mahasiswa as $no => $m)
            <tr>
              <td>{{$no+1}}</td>
              <td>{{$m->namamahasiswa}}</td>
              <td>{{$m->nrpmahasiswa}}</td>
              <td>{{$m->tahun}}</td>
              
              @if($m->totalsks != null)
                <td>{{$m->totalsks}}</td>
                @else
                <td>-</td>
              @endif

              @if($m->ipk != null)
                <td>{{$m->ipk}}</td>
                @else
                <td>-</td>
              @endif

              @if($m->ips != null)
                <td>{{$m->ips}}</td>
                @else
                <td>-</td>
              @endif

              <td>
               @if($m->flag == 0)
               <a href="{{url('dosen/data/mahasiswa/ubahflag/'.$m->nrpmahasiswa)}}" class="btn btn-success btn-sm">Normal</a>
               @elseif($m->flag == 1)
                <a href="{{url('dosen/data/mahasiswa/ubahflag/'.$m->nrpmahasiswa)}}" class="btn btn-warning btn-sm">Waspada</a>
               @else
                <a href="{{url('dosen/data/mahasiswa/ubahflag/'.$m->nrpmahasiswa)}}" class="btn btn-danger btn-sm">Kurang</a>
               @endif
              </td>
              <td>
               <a href="{{url('admin/master/mahasiswa/ubah/'.$m->nrpmahasiswa)}}" class="btn btn-primary">Detail</a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
          <br/>
        Halaman : {{$mahasiswa->currentPage()}} <br/>
        Jumlah Data : {{$mahasiswa->total()}} <br/>
        Data Per Halaman : {{$mahasiswa->perPage()}} <br/>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    
@endsection
 
@push('scripts')
<script>
$(document).ready(function(){

 $('#keyword').keyup(function(){ 
        var query = $(this).val();

        if(query != '')
        {
         var _token = $('input[name="_token"]').val();
         var pencarian = document.getElementById("pencarian").value;
         $.ajax({
          url:"{{ route('mastermahasiswa.fetch') }}",
          method:"POST",
          data:{query:query,_token:_token, jenis:pencarian},
          success:function(data){
            $('#mahasiswaList').fadeIn();  
              $('#mahasiswaList').html(data);
          }
         });
        }
    });

    $(document).on('click', 'li', function(){  
        $('#keyword').val($(this).text());  
        $('#mahasiswaList').fadeOut();  
    });  

});
</script>
@endpush