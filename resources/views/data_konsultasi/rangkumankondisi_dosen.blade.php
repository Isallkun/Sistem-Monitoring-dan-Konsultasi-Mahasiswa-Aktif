<!--Wajib untuk inisialisasi file views/layouts/appadmin-->
@extends('layouts.appdosen')

@push('styles')
  <!-- Untuk menambahkan style baru -->
  <style type="text/css">
  body{
    margin:0;
    padding: 0;
    background:#262626;
  }
  .rating{
    position: absolute;
    top:50%;
    left: 50%;
    transform: translate(-50%, -50%) rotateY(180deg);
    display: flex;
  }
  .rating input{
    display: none;
  }
  .rating label{
    display: block;
    cursor: pointer;
    width: 50px;
    /*background: #ccc;*/
  }
  .rating label:before{
    content:'\f005';
    font-family: fontAwesome;
    position: relative;
    display: block;
    font-size: 50px;
    color: #101010;
  }
  .rating label:after{
    content:'\f005';
    font-family: fontAwesome;
    position: absolute;
    display: block;
    font-size: 50px;
    color: #fffa00;
    top:0;
    opacity: 0;
    transition: .5s;
    text-shadow: 0 2px 5px rgba(0,0,0,.5);
  }
  .rating label:hover:after,
  .rating label:hover ~ label:after,
  .rating input:checked ~ label:after
  {
    opacity: 1;
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
            <h1 class="m-0 text-dark">Rangkuman Kondisi Mahasiswa</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('dosen')}}">Home</a></li>
              <li class="breadcrumb-item"><a href="{{url('dosen/data/tambah')}}">Tambah Data Konsultasi</a></li>
              <li class="breadcrumb-item active">Rangkuman Kondisi Mahasiswa</li>
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
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">{{$konsultasi_mhs[0]->namamahasiswa}} ({{$konsultasi_mhs[0]->nrpmahasiswa}})</h3>
          </div>  
          <div class="card-body">
            <table class="table table-bordered table-striped">
              <thead>
                <tr> 
                  <th width="1%">No.</th>
                  <th width="1%">Tanggal Konsultasi</th>
                  <th width="1%">Tahun Akademik</th>
                  <th width="1%">Topik</th>
                  <th width="1%">Permasalahan</th>
                  <th width="1%">Solusi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($konsultasi_mhs as $no => $k)
                <tr>
                  <td>{{$no+1}}</td>
                  <td>{{$k->tanggalkonsultasi}}</td>
                  <td>{{$k->semester}} {{$k->tahun}}</td>
                  <td>{{$k->namatopik}}</td>
                  <td>{{$k->permasalahan}}</td>
                  <td>{{$k->solusi}}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
            <br>
            <div class="float-sm-right">
              
              <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#rating_{{$konsultasi_mhs[0]->nrpmahasiswa}}">
              Berikutnya</a>
              
            </div>  
          </div>
        </div>

        @foreach($konsultasi_mhs as $d)
        <div id="rating_{{$d->nrpmahasiswa}}" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <!-- konten modal-->
            <div class="modal-content">
              <!-- heading modal -->
              <div class="modal-header">
                <h4 class="modal-title">Rating Mahasiswa</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <!-- body modal -->
              <form action="{{url('dosen/data/rating/prosestambah/'.$d->nrpmahasiswa)}}" role="form" method="post">
                {{ csrf_field() }}

                <div class="modal-body">
                  <p style="text-align: center;text-transform: uppercase;"><b>{{$d->namamahasiswa}} - {{$d->nrpmahasiswa}}</b></p>
                  <!--UNTUK RATING -->
                  <br><br><br>
                  <div class="rating">
                    <input type="radio" name="star" id="star5" value="5"><label for="star5"></label>
                    <input type="radio" name="star" id="star4" value="4"><label for="star4"></label>
                    <input type="radio" name="star" id="star3" value="3"><label for="star3"></label>
                    <input type="radio" name="star" id="star2" value="2"><label for="star2"></label>
                    <input type="radio" name="star" id="star1" value="1"><label for="star1"></label>
                  </div>
                  <br>
                  <p style="text-align: center;">Terima kasih atas penilaian <br> yang diberikan :)</p>
                </div>

                
                <!-- footer modal -->
                <div class="modal-footer">
                  <a href="{{url('dosen/data/konsultasi')}}" class="btn btn-default">Skip</a>
                </div>
              </form>
            </div>
          </div>
        </div>
        @endforeach

      </div>
    </section>
@endsection
 
@push('scripts')
<script type="text/javascript">
  $('input[type=radio]').on('change', function() {
    $(this).closest("form").submit();
});
</script>
@endpush