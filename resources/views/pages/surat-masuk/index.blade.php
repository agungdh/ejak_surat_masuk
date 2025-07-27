@extends('layouts.default')

@section('title')
    Surat Masuk
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item active">Surat Masuk</li>
@endsection

@section('content')
    <!-- Default box -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Surat Masuk</h3>
        </div>
        <div class="card-body">
            <a href="/surat-masuk/create">
                <button class="btn btn-success mb-3"><i class="fa fa-plus"></i> Tambah Surat Masuk</button>
            </a>
            <div class="row mb-4">
                <div class="col-12 row mb-2">
                    <div class="col-4">
                        <label for="dari_tanggal">Dari Tanggal</label>
                        <input type="text" class="form-control datepicker" id="dari_tanggal" placeholder="Dari Tanggal"
                               :class="{'is-invalid': validationErrors.dari_tanggal}">
                        @push('scripts')
                            <script>
                                $(document).ready(function() {
                                    $('#dari_tanggal').change(function() {
                                        $('#tabel').DataTable().ajax.reload();
                                    });

                                    $('#dari_tanggal').on('apply.daterangepicker', function(ev, picker) {
                                        $(this).val(picker.startDate.format('DD-MM-YYYY'));
                                        $('#dari_tanggal').change();
                                    });

                                    $('#dari_tanggal').on('cancel.daterangepicker', function(ev, picker) {
                                        $(this).val('');
                                        $('#dari_tanggal').change();
                                    });
                                });
                            </script>
                        @endpush
                    </div>
                    <div class="col-4">
                        <label for="sampai_tanggal">Sampai Tanggal</label>
                        <input type="text" class="form-control datepicker" id="sampai_tanggal"
                               placeholder="Dari Tanggal" :class="{'is-invalid': validationErrors.sampai_tanggal}">
                        @push('scripts')
                            <script>
                                $(document).ready(function() {
                                    $('#sampai_tanggal').change(function() {
                                        $('#tabel').DataTable().ajax.reload();
                                    });

                                    $('#sampai_tanggal').on('apply.daterangepicker', function(ev, picker) {
                                        $(this).val(picker.startDate.format('DD-MM-YYYY'));
                                        $('#sampai_tanggal').change();
                                    });

                                    $('#sampai_tanggal').on('cancel.daterangepicker', function(ev, picker) {
                                        $(this).val('');
                                        $('#sampai_tanggal').change();
                                    });
                                });
                            </script>
                        @endpush
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover table-striped" id="tabel">
                    <thead>
                    <tr>
                        <th>Nomor Surat</th>
                        <th>Tanggal Surat</th>
                        <th>Perihal</th>
                        <th>Pengirim</th>
                        <th>Penerima</th>
                        <th>Berkas</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- /.card -->
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#tabel').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: `/surat-masuk/datatable`,
                    type: 'POST',
                    data: function(d) {
                        d.dari_tanggal = $('#dari_tanggal').val() ? moment($('#dari_tanggal').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : '';
                        d.sampai_tanggal = $('#sampai_tanggal').val() ? moment($('#sampai_tanggal').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : '';
                    }
                },
                columns: [
                    { data: 'nomor_surat', name: 'nomor_surat' },
                    { data: 'tanggal_surat', name: 'tanggal_surat' },
                    { data: 'perihal', name: 'perihal' },
                    { data: 'pengirim', name: 'pengirim' },
                    { data: 'penerima', name: 'penerima' },
                    { data: 'berkas', name: 'berkas', searchable: false, orderable: false },
                    { data: 'action', name: 'action', searchable: false, orderable: false }
                ]
            });
        });

        async function hapusData(id) {
            let result = await Swal.fire({
                title: 'Apakah Anda yakin menghapus isian ini?',
                text: 'Data yang telah terhapus tidak dapat dikembalikan lagi',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ya, Hapus'
            });

            if (result.value) {
                try {
                    await axios.delete(`/surat-masuk/${id}`);

                    $('#tabel').DataTable().ajax.reload();

                    toastr.success('Surat Masuk berhasil dihapus');
                } catch (e) {
                    toastr.error('Terjadi kesalahan sistem. Silahkan refresh halaman ini. Jika error masih terjadi, silahkan hubungi Tim IT.');
                }
            }
        }
    </script>
@endpush
