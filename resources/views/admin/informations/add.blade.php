@extends('layouts.element.main')

@section('title', 'Keterangan Satuan - Add')

@section('custom-css')
    <style>
        .breadcrumb-item + .breadcrumb-item::before{
            content: '-';
            color: #5e72e4;
        }
    </style>
@endsection

@section('content')

<!-- Navbar -->
<nav class="navbar navbar-top navbar-expand-md navbar-dark" id="navbar-main">
  <div class="container-fluid">
    <!-- Brand -->
    <a class="h4 mb-0 mt-3 text-white text-uppercase d-none d-lg-inline-block" href="javascript:;">
      Dashboard
    </a>
  </div>
</nav>
<!-- End Navbar -->
<!-- Header -->
<div class="header bg-gradient-success pb-8 pt-5 pt-md-8">
</div>
<div class="container-fluid mt--7">
<!-- Table -->
  <div class="row">
    <div class="col">
      <div class="card shadow">
        <div class="card-header border-bottom-1">
            <div class="row">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-links" style="background:none;">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">
                                    <i class="fa fa-home text-success"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-success" href="{{ route('informations.index') }}">
                                        Keterangan Satuan
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Add
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="col-auto text-right">

                </div>
            </div>
        </div>
        <div class="card-body" style="background: #f7f8f9;">
            <form action="{{ route('informations.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-control-label">
                            Kategori
                        </label>
                        <select name="category_id" id="category_id" class="form-control form-control-alternative">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-control-label">
                            Jumlah Satuan Awal
                        </label>
                        <input type="text" name="jumlah_awal" readonly value="1" class="form-control form-control-alternative">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-control-label">
                            Satuan Awal
                        </label>
                        <select name="satuan_awal_id" id="satuan_awal_id" class="form-control form-control-alternative">
                            <option value="">Pilih Satuan</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-control-label">
                            Jumlah Satuan Akhir
                        </label>
                        <input type="text" name="jumlah_akhir" class="form-control form-control-alternative">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-control-label">
                            Satuan Akhir
                        </label>
                        <select name="satuan_akhir_id" id="satuan_akhir_id" class="form-control form-control-alternative">
                            <option value="">Pilih Satuan</option>

                        </select>
                    </div>
                </div>
                <div class="col-md-8"></div>
                <div class="col text-right">
                    <button type="submit" class="btn btn-icon btn-success" style="border-radius: 22px;">
                        <span class="btn-inner--text">Submit</span>
                    </button>
                </div>
            </div>
            </form>
        </div>
        <div class="card-footer py-4">

        </div>
      </div>
    </div>
  </div>
</div>

@endsection

<script src="{{ asset('/assets/js/jquery-3.4.1.min.js') }}"></script>
<script>

    $( document ).ready(function() {

        $("#category_id").on("change",function(e){
            var thisId = $(this).val();

            $.ajax({
                url : "{{ url('getUnits') }}/" +thisId,
                dataType : 'json',
                type : 'get',
                beforeSend : function(e){
                    $("#satuan_awal_id option").first().html('Sedang memuat data satuan...');
                    $("#satuan_awal_id option,#district_id option").not(":first-child").remove();
                },
                success : function(response){
                    console.log(response)
                    $("#satuan_awal_id").html($("<option value=''>Pilih Satuan</option>"))
                    $.each(response.results,function(e,i){
                        $("#satuan_awal_id").append($("<option value='"+i.id+"'>"+i.name+" - tingkat "+i.tingkat+"</option>"))
                    })
                }
            })
        })

        $("#satuan_awal_id").on("change",function(e){
            var thisId = $(this).val();

            $.ajax({
                url : "{{ url('getConvert') }}/" +thisId,
                dataType : 'json',
                type : 'get',
                beforeSend : function(e){
                    $("#satuan_akhir_id option").first().html('Sedang memuat data satuan...');
                    $("#satuan_akhir_id option,#district_id option").not(":first-child").remove();
                },
                success : function(response){
                    //console.log(response)
                    $("#satuan_akhir_id").html($("<option value=''>Pilih Satuan</option>"))
                    $.each(response.results,function(e,i){
                        console.log(i);
                        $("#satuan_akhir_id").append($("<option value='"+i.id+"'>"+i.name+" - tingkat "+i.tingkat+"</option>"))
                    })
                }
            })
        })

        });

</script>
