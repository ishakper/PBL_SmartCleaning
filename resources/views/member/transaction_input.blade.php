@extends('member.template.main')

@section('css')
    <link href="{{ asset('vendor/datatables-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables-responsive/css/responsive.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('main-content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Tambah Pesanan</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @foreach (['error', 'warning', 'success'] as $msg)
                        @if (session($msg))
                            <div class="alert alert-{{ $msg }} alert-dismissible fade show" role="alert">
                                {{ session($msg) }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                    @endforeach

                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('member.transactions.store') }}" method="post">
                                @csrf
                                <div class="form-group row">
                                    <label for="id-member" class="col-sm-2 col-form-label">ID Member</label>
                                    <div class="col-sm-2">
                                        <input type="number" min="1" class="form-control" id="id-member" name="member-id"
                                            @if (isset($memberIdSessionTransaction)) value="{{ $memberIdSessionTransaction }}" disabled title="Harap selesaikan transaksi yang ada untuk mengganti id member" @endif
                                            required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="barang" class="col-sm-2 col-form-label">Barang</label>
                                    <div class="col-sm-4">
                                        <select class="form-control" id="barang" name="item">
                                            @foreach ($items as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="servis" class="col-sm-2 col-form-label">Servis</label>
                                    <div class="col-sm-4">
                                        <select class="form-control" id="servis" name="service">
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="kategori" class="col-sm-2 col-form-label">Kategori</label>
                                    <div class="col-sm-4">
                                        <select class="form-control" id="kategori" name="category">
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="banyak" class="col-sm-2 col-form-label">Banyak</label>
                                    <div class="col-sm-2">
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </span>
                                            <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1" min="1" max="100">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <button type="submit" id="tambah-transaksi" class="btn btn-primary">Tambah Pesanan</button>
                                    </div>
                                </div>
                            </form>

                            <table id="tbl-input-transaksi" class="table mt-2 dt-responsive nowrap" style="width: 100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Barang</th>
                                        <th>Servis</th>
                                        <th>Kategori</th>
                                        <th>Banyak</th>
                                        <th>Harga</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Tambahkan data transaksi jika ada --}}
                                </tbody>
                            </table>

                            @if (isset($sessionTransaction))
                                <button id="btn-bayar" class="btn btn-success" data-toggle="modal"
                                    data-target="#paymentModal">Bayar</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modals')
    {{-- Buatkan modal khusus member (copy dari admin jika perlu) --}}
    <x-member.modals.payment-modal 
        :serviceTypes="$serviceTypes" 
        :vouchers="$vouchers ?? []" 
        :totalPrice="$totalPrice ?? '0'" 
        :show="isset($sessionTransaction)" />
@endsection

@section('scripts')
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#tbl-input-transaksi').DataTable({
                "searching": false,
                "bPaginate": false,
                "bLengthChange": false,
                "bFilter": false,
                "bInfo": false
            });
        });
    </script>

    @if (session('id_trs'))
        <script type="text/javascript">
            window.open('{{ route('member.transactions.print.index', ['transaction' => session('id_trs')]) }}', '_blank');
        </script>
    @endif
@endsection
