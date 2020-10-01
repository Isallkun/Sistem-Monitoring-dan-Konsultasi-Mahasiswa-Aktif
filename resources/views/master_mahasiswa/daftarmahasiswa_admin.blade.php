<!--Wajib untuk inisialisasi file views/layouts/appadmin-->
@extends('layouts.appadmin')

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
            <h1 class="m-0 text-dark">Daftar Mahasiswa</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
              <li class="breadcrumb-item active">Daftar Mahasiswa</li>
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
        <a href="{{ url('admin/master/mahasiswa/tambah') }}" class="btn btn-primary" role="button">Tambah Data</a>
        <br><br><br>

        <form method="GET" action="{{url('admin/master/mahasiswa/prosescari')}}" enctype="multipart/form-data">
          {{ csrf_field() }}
          <input type="hidden" name="_token" value="{{csrf_token() }}"> 
          
          <label for="exampleInputPencarian">Pencarian Data: </label>

          <div class="form-group">
            <input type="text" name="keyword" id="keyword" placeholder="Enter Keyword">
            
            <select class="btn btn-primary dropdown-toggle btn-sm" name="pencarian" id="pencarian" data-toggle="dropdown">
              <option value="nrpmahasiswa">NRP Mahasiswa</option>
              <option value="namamahasiswa">Nama</option>
              <option value="jeniskelamin">Jenis Kelamin</option>
              <option value="email">Email</option>
              <option value="telepon">Telepon</option>
              <option value="angkatan">Angkatan</option>
              <option value="alamat">Alamat</option>
              <option value="status">Status</option>
              <option value="username">Username</option>
              <option value="namadosen">Nama Dosen</option>
            </select>

            <button type="submit" class="btn btn-light"><i class="fas fa-search"></i></button>

            <div id="mahasiswaList"></div>
          
          </div>

        </form>
        
        <!-- Small boxes (Stat box) -->
        <table class="table table-bordered table-striped">
          <thead>
            <tr> 
              <th width="1%">No.</th>
              <th width="1%">NRP</th>
              <th width="1%">Nama</th>
              <th width="1%">Jenis Kelamin</th>
              <th width="1%">Email</th>
              <th width="1%">Telepon</th>
              <th width="1%">Angkatan</th>
              <th width="1%">Status</th>
              <th width="1%">Username</th>
              <th width="1%">Dosen Wali</th>
              <th width="1%">Action</th>
                     
            </tr>
          </thead>
          <tbody>
            @foreach($mahasiswa as $no => $m)
            <tr>
              <td>{{$no+1}}</td>
              <td>{{$m->nrpmahasiswa}}</td>
              <td>{{$m->namamahasiswa}}</td>
              <td>{{$m->jeniskelamin}}</td>
              <td>{{$m->email}}</td>
              <td>{{$m->telepon}}</td>
              <td>{{$m->angkatan}}</td>
              <td>{{$m->status}}</td>
              <td>{{$m->users_username}}</td>
              <td>{{$m->namadosen}}  ({{$m->npkdosen}})</td>
              <td>
               <a href="{{url('admin/master/mahasiswa/ubah/'.$m->nrpmahasiswa)}}" class="btn btn-warning">Ubah</a>

              <form method="get" action="{{url('admin/master/mahasiswa/hapus/'.$m->nrpmahasiswa)}}">
                 <input type="hidden" name="username" value="{{$m->users_username}}">
                 <button type="submmit" class="btn btn-danger">Hapus</button>
               </form>
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