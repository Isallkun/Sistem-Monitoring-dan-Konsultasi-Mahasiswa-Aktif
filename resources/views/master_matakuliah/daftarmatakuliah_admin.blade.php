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
            <h1 class="m-0 text-dark">Daftar Matakuliah</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('admin')}}">Home</a></li>
              <li class="breadcrumb-item active">Daftar Matakuliah</li>
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
        <!-- <a href="{{ url('admin/master/matakuliah/tambah') }}" class="btn btn-primary" role="button">Tambah Data</a>
        <br><br><br> -->

        <form method="GET" action="{{url('admin/master/matakuliah/prosescari')}}" enctype="multipart/form-data">
          {{ csrf_field() }}
          <input type="hidden" name="_token" value="{{csrf_token() }}"> 
          
          <label for="exampleInputPencarian">Pencarian Data: </label>

          <div class="form-group">
            
            <select class="btn btn-primary dropdown-toggle btn-sm" name="pencarian" id="pencarian" data-toggle="dropdown">
              <option value="kodematakuliah">Kode Matakuliah</option>
              <option value="namamatakuliah">Nama Matakuliah</option>
              <option value="totalsks">Total SKS</option>
              <option value="totalpertemuan">Total Pertemuan</option>
              <option value="nisbi">Nisbi Minimal</option>
              <option value="tahunakademik">Tahun Akademik</option>
            </select>

             <input type="text" name="keyword" id="keyword" placeholder="Enter Keyword">

            <button type="submit" class="btn btn-light"><i class="fas fa-search"></i></button>

            <div id="matakuliahList"></div>
          
          </div>

        </form>
        
        <!-- Small boxes (Stat box) -->
        <table class="table table-bordered table-striped">
          <thead>
            <tr> 
              <th width="1%">No.</th>
              <th width="1%">Kode Matakuliah</th>
              <th width="1%">Nama Matakuliah</th>
              <th width="1%">SKS</th>
              <th width="1%">Total Pertemuan</th>
              <th width="1%">Nisbi Minimal</th>
              <th width="1%">Tahun Akademik</th>
              <!-- <th width="1%">Action</th> -->
            </tr>
          </thead>
          <tbody>
            @foreach($matakuliah as $no => $m)
            <tr>
              <td>{{$no+1}}</td>
              <td>{{$m->kodematakuliah}}</td>
              <td>{{$m->namamatakuliah}}</td>
              <td>{{$m->sks}}</td>
              <td>{{$m->totalpertemuan}}</td>
              <td>{{$m->nisbimin}}</td>
              <td>{{$m->semester}} {{$m->tahun}}</td>
            
              <!-- <td>
                 <a href="{{url('admin/master/matakuliah/ubah/'.$m->kodematakuliah)}}" class="btn btn-warning">Ubah</a>
                 
                 <form method="get" action="{{url('admin/master/matakuliah/hapus/'.$m->kodematakuliah)}}">
                  <input type="hidden" name="nama_matkauliah" value="{{$m->namamatakuliah}}">
                  <button type="submmit" class="btn btn-danger">Hapus</button>
               </form>

              </td> -->
            </tr>
            @endforeach
          </tbody>
        </table>
          <br/>
        Halaman : {{$matakuliah->currentPage()}} <br/>
        Jumlah Data : {{$matakuliah->total()}} <br/>
        Data Per Halaman : {{$matakuliah->perPage()}} <br/>
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
          url:"{{ route('mastermatakuliah.fetch') }}",
          method:"POST",
          data:{query:query,_token:_token, jenis:pencarian},
          success:function(data){
            $('#matakuliahList').fadeIn();  
              $('#matakuliahList').html(data);
          }
         });
        }
    });

    $(document).on('click', 'li', function(){  
        $('#keyword').val($(this).text());  
        $('#matakuliahList').fadeOut();  
    });  

});
</script>
@endpush