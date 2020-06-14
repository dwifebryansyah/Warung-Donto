@extends('layouts.element.main')

@section('title', 'Konversi - Add')

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
<div class="header bg-gradient-info pb-8 pt-5 pt-md-8">
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
                                    <i class="fa fa-home text-info"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-info" href="{{ route('converts.index') }}">
                                    Konversi
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
            <form action="{{ route('converts.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-control-label">
                            Produk
                        </label>
                        <select name="product_id" id="product_id" class="form-control form-control-alternative">
                            <option value="">Pilih Produk</option>
                            @foreach ($products as $p)
                            <option value="{{ $p->id }}">{{ ucwords($p->name.' - '.$p->unit->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-control-label">
                            Jumlah Awal
                        </label>
                        <select name="jumlah_awal" disabled id="jumlah_awal_id" class="form-control form-control-alternative">
                        </select>
                    </div>
                </div>
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
                            Satuan Awal
                        </label>
                        <select name="convert_awal" id="convert_awal_id" class="form-control form-control-alternative">
                            <option value="">Pilih Satuan</option>

                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-control-label">
                            Satuan Akhir
                        </label>
                        <select name="convert_akhir" id="convert_akhir_id" class="form-control form-control-alternative">
                            <option value="">Pilih Satuan</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-control-label">
                            Stok
                        </label>
                        <input type="text" name="stok" class="form-control form-control-alternative" placeholder="Masukan Stok">
                    </div>
                </div>
                <div class="col-md-8"></div>
                <div class="col text-right">
                    <button type="submit" class="btn btn-icon btn-info" style="border-radius: 22px;">
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

        $("#convert_awal_id").on("change",function(e){
            var thisId = $(this).val();

            $.ajax({
                url : "{{ url('getConvert') }}/" +thisId,
                dataType : 'json',
                type : 'get',
                beforeSend : function(e){
                    $("#convert_akhir_id option").first().html('Sedang memuat data satuan...');
                    $("#convert_akhir_id option,#district_id option").not(":first-child").remove();
                },
                success : function(response){
                    // console.log(response.results.cek)
                    $("#convert_akhir_id").html($("<option value=''>Pilih Satuan</option>"))
                    $.each(response.results,function(e,i){  
                        if (response.results.cek == 'true') {
                            $("#convert_akhir_id").html($("<option selected value=''>Tidak Bisa Convert</option>"))
                        }else{
                            $("#convert_akhir_id").append($("<option value='"+i.id+"'>"+i.name+"</option>"))
                        }

                    })
                }
            })
        })

        $("#product_id").on("change",function(e){
            var thisId = $(this).val();

            $.ajax({
                url : "{{ url('getProduct') }}/" +thisId,
                dataType : 'json',
                type : 'get',
                beforeSend : function(e){
                    $("#jumlah_awal_id option").first().html('Sedang memuat data jumlah awal...');
                    $("#jumlah_awal_id option").not(":first-child").remove();
                },
                success : function(response){
                    //console.log(response)

                    $.each(response.results,function(e,i){
                        if (i.information_unit == null) {
                            $("#jumlah_awal_id").html($("<option value='null'>Tidak Ada Jumlah Awal</option>"))
                        } else {
                            $("#jumlah_awal_id").html($("<option value='"+i.information_unit.id+"'>"+i.information_unit.jumlah_awal+' '+i.information_unit.unit_one.name+' = '+i.information_unit.jumlah_akhir+' '+i.information_unit.unit_two.name+"</option>"))
                        }
                    })
                }
            })
        });

        $("#product_id").on("change",function(e){
            var thisId = $(this).val();
            $.ajax({
                url : "{{ url('handleConvert') }}/" +thisId,
                dataType : 'json',
                type : 'get',
                beforeSend : function(e){
                    $("#category_id option").first().html('Sedang memuat data satuan...');
                    $("#category_id option").not(":first-child").remove();
                },
                success : function(response){
                    //  console.log(response)
                    $("#category_id").html($("<option value=''>Pilih Satuan</option>"))
                    $.each(response.results,function(e,i){
                        $("#category_id").append($("<option value='"+i.unit.category.id+"'>"+i.unit.category.name+"</option>"))
                    })
                }
            })
        })

        $("#product_id").on("change",function(e){
            var thisId = $(this).val();

            $.ajax({
                url : "{{ url('handleConvert') }}/" +thisId,
                dataType : 'json',
                type : 'get',
                beforeSend : function(e){
                    $("#convert_awal_id option").first().html('Sedang memuat data satuan...');
                    $("#convert_awal_id option,#district_id option").not(":first-child").remove();
                },
                success : function(response){
                    // console.log(response)
                    $("#convert_awal_id").html($("<option value=''>Pilih Satuan</option>"))
                    $.each(response.results,function(e,i){
                        $("#convert_awal_id").append($("<option value='"+i.unit.id+"'>"+i.unit.name+'    - '+i.stok+"</option>"))
                    })
                }
            })
        })

        });

</script>
