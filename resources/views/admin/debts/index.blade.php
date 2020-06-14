@extends('layouts.element.main')

@section('title', 'Hutang')

@section('custom-css')
    <style>
        .breadcrumb-item + .breadcrumb-item::before{
            content: '-';
            color: #5e72e4;
        }
    </style>
@endsection

@section('content')

@php
    $session = Session::get('user');
@endphp

@include('layouts.element.navbar')

<!-- Header -->
<div class="header bg-gradient-warning pb-8 pt-5 pt-md-8">
</div>
<div class="container-fluid mt--7">
          <!-- Table -->
  <div class="row">
    <div class="col">
      <div class="card shadow">
        <div class="card-header border-1">
            <div class="row">
                <div class="col-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-links" style="background:none;">
                            <li class="breadcrumb-item">
                                <a href="javascript:;">
                                    <i class="fa fa-home text-warning"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                    Hutang
                            </li>
                        </ol>
                    </nav>
                </div>

            </div>
        </div>
        <div class="table-responsive p-3">
          <table class="table align-items-center table-flush" id="table-cart">
            <thead class="thead-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Total Sebelumnya</th>
                    <th scope="col">Sudah Bayar</th>
                    <th scope="col">Sisa Bayar</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>

            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @foreach ($debts as $d)
                <tr>
                    <td>{{ $no }}</td>
                    <td>{{ ucwords($d->name) }}</td>
                    <td>{{ number_format($d->total_sebelumnya) }}</td>
                    <td>{{ number_format($d->sudah_bayar) }}</td>
                    <td>{{ number_format($d->sisa_bayar) }}</td>
                    @if ($d->status == 1)
                    <td>
                        <span class="badge badge-pill badge-success">Lunas</span>
                    </td>
                    @else
                    <td>
                        <span class="badge badge-pill badge-danger">Belum Lunas</span>
                    </td>
                    @endif

                    <td>
                        <div class="dropdown">
                            <a class="btn btn-sm btn-icon-only pt-2 text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <i class="fas fa-ellipsis-v"></i>
                            </a>

                            <div style="min-width: 6rem;" class="dropdown-menu dropdown-menu-left dropdown-menu-arrow">
                                @if ($d->status == 1)
                                <div class="dropdown-item">
                                    <a href="{{ route('debts.destroy', $d->id) }}" class="badge badge-pill badge-danger">
                                        Delete
                                    </a>
                                </div>
                                @else
                                <div class="dropdown-item">
                                    <a href="{{ route('debts.bayar', $d->id) }}" class="badge badge-pill badge-success">
                                        Bayar Lagi
                                    </a>
                                </div>
                                <div class="dropdown-item">
                                    <a href="javascript:;" data-id="{{ route('debts.destroy', $d->id) }}" class="btn-danger badge badge-pill badge-danger">
                                        Delete
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @php
                    $no++;
                @endphp
                @endforeach
            </tbody>
          </table>
        </div>
        <div class="card-footer py-4">
            {{ $debts->render() }}
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

<script src="{{ asset('/assets/js/jquery-3.4.1.min.js') }}"></script>
<script>

    $(document).ready( function () {

        $('#table-cart').DataTable({
            paging: false,
            searching: true,
        });

        $('.btn-danger').on('click', function() {

            Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1aae6f',
            cancelButtonColor: '#f80031',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel'
            }).then((result) => {
                if (result.value) {
                    var data = $(this).data('id')
                    console.log(data)
                    window.location = data;
                }else{
                    Swal.fire(
                    'Cancelled!',
                    'Your file has been cancel.',
                    'error'
                    )
                }
            })
        });

    });

</script>
