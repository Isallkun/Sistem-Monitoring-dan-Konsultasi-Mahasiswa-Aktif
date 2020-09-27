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
            <h1 class="m-0 text-dark">Daftar Dosen</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
              <li class="breadcrumb-item active">Daftar Dosen</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    @if (\Session::has('Success'))
      <div class="alert alert-success alert-block">
        <ul>
            <li>{!! \Session::get('Success') !!}</li>
        </ul>
      </div>
    @endif

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
        <a href="{{ url('admin/master/dosen/tambah') }}" class="btn btn-primary" role="button">Tambah Data</a>
        <br><br><br>

        <form method="GET" action="{{url('admin/master/dosen/prosescari')}}" enctype="multipart/form-data">
          {{ csrf_field() }}
          <input type="hidden" name="_token" value="{{csrf_token() }}"> 
          
          <label for="exampleInputPencarian">Pencarian Data: </label>

          <div class="form-group">
            <input type="text" name="keyword" id="keyword" placeholder="Enter Keyword">
            
            <select class="btn btn-primary dropdown-toggle btn-sm" name="pencarian" id="exampleInputPencarian" data-toggle="dropdown">
              <option value="npkdosen">NPK Dosen</option>
              <option value="namadosen">Nama</option>
              <option value="jeniskelamin">Jenis Kelamin</option>
              <option value="email">Email</option>
              <option value="telepon">Telepon</option>
              <option value="status">Status</option>
              <option value="kodejurusan">Kode Jurusan</option>
              <option value="username">Username</option>
            </select>

            <button type="submit" class="btn btn-light"><i class="fas fa-search"></i></button>

            <div id="dosenList"></div>
          
          </div>

        </form>
        
        <!-- Small boxes (Stat box) -->
        <table class="table table-bordered table-striped">
          <thead>
            <tr> 
              <th width="1%">No.</th>
              <th width="1%">NPK</th>
              <th width="1%">Nama</th>
              <th width="1%">Jenis Kelamin</th>
              <th width="1%">Email</th>
              <th width="1%">Telepon</th>
              <th width="1%">Status</th>
              <th width="1%">Kode Jurusan</th>
              <th width="1%">Username</th>     
              <th width="1%">Action</th>
                     
            </tr>
          </thead>
          <tbody>
            @foreach($dosen as $no => $d)
            <tr>
              <td>{{$no+1}}</td>
              <td>{{$d->npkdosen}}</td>
              <td>{{$d->namadosen}}</td>
              <td>{{$d->jeniskelamin}}</td>
              <td>{{$d->email}}</td>
              <td>{{$d->telepon}}</td>
              <td>{{$d->status}}</td>
              <td>{{$d->kode_jurusan}}</td>
              <td>{{$d->users_username}}</td>
              <td>
                 <a href="{{url('admin/master/dosen/ubah/'.$d->npkdosen)}}" class="btn btn-warning">Ubah</a>

                 <form method="get" action="{{url('admin/master/dosen/hapus/'.$d->npkdosen)}}">
                   <input type="hidden" name="username" value="{{$d->users_username}}">
                   <button type="submmit" class="btn btn-danger">Hapus</button>
                 </form>

              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
          <br/>
        Halaman : {{$dosen->currentPage()}} <br/>
        Jumlah Data : {{$dosen->total()}} <br/>
        Data Per Halaman : {{$dosen->perPage()}} <br/>
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
         $.ajax({
          url:"{{ route('masterdosen.fetch') }}",
          method:"POST",
          data:{query:query,_token:_token},
          success:function(data){
            $('#dosenList').fadeIn();  
              $('#dosenList').html(data);
          }
         });
        }
    });

    $(document).on('click', 'li', function(){  
        $('#keyword').val($(this).text());  
        $('#dosenList').fadeOut();  
    });  

});
</script>
@endpush