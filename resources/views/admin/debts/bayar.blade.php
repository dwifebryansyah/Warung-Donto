@extends('layouts.element.main')

@section('title', 'Hutang - Add')

@section('custom-css')
    <style>
        .breadcrumb-item + .breadcrumb-item::before{
            content: '-';
            color: #5e72e4;
        }
    </style>
@endsection
@php
    $session = Session::get('user');
@endphp
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
<div class="header bg-gradient-warning pb-8 pt-5 pt-md-8">
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
                                    <i class="fa fa-home text-warning"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="text-warning" href="{{ route('products.index') }}">
                                    Hutang
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
            <form action="{{ route('debts.process') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-12 p-1">
                    <div class="form-group">
                        <label class="form-control-label">
                            Nama Pembeli
                        </label>
                        <input type="text" name="name" readonly value="{{ ucwords($debt->name) }}" class="form-control form-control-alternative">
                    </div>
                </div>
                <div class="col-md-12 p-1">
                    <div class="form-group">
                        <label class="form-control-label">
                            Total Sebelumnya
                        </label>
                        <input type="text" name="name" disabled value="{{ number_format($debt->total_sebelumnya) }}" class="form-control form-control-alternative">
                    </div>
                </div>
                <div class="col-md-12 p-1">
                    <div class="form-group">
                        <label class="form-control-label">
                            Sudah Bayar
                        </label>
                        <input type="text" name="name" disabled value="{{ number_format($debt->sudah_bayar) }}" class="form-control form-control-alternative">
                    </div>
                </div>
                <div class="col-md-12 p-1">
                    <div class="form-group">
                        <label class="form-control-label">
                            Sisa Bayar
                        </label>
                        <input type="text" name="sisa_bayar" readonly value="{{ $debt->sisa_bayar }}" class="form-control form-control-alternative">
                    </div>
                </div>
                <div class="col-md-12 p-1">
                    <div class="form-group">
                        <label class="form-control-label">
                            Uang Yang Harus Di Bayar
                        </label>
                        <input type="hidden" name="debt_id" value="{{ $debt->id }}">
                        <input type="hidden" name="order_id" value="{{ $debt->order_id }}">
                        <input type="text" name="uangFix" autofocus class="form-control form-control-alternative">
                    </div>
                </div>
                <div class="col text-right">
                    <input type="hidden" name="user" value="{{ $session['user_id'] }}">
                    <input type="hidden" name="activity" value="membayar">
                    <button type="submit" class="btn btn-icon btn-warning" style="border-radius: 22px;">
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

@section('script')
