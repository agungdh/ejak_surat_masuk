@extends('layouts.default')

@section('title')
    Dashboard Admin
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active"><a href="/dashboard-admin">Home</a></li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-12">
            <!-- small box -->
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{$pegawai}}</h3>

                    <p>Jumlah Pegawai</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
            </div>
        </div>
        <!-- ./col -->

        <div class="col-lg-6 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{$diklat}}</h3>

                    <p>Jumlah Jam Diklat</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
            </div>
        </div>
        <!-- ./col -->

        <div class="col-lg-6 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{$ppm}}</h3>

                    <p>Jumlah Jam PPM</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
            </div>
        </div>
        <!-- ./col -->

        <div class="col-lg-12 col-12">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{$seminar + $webinar + $lc}}</h3>

                    <p>Jumlah Jam Lain-Lain</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
            </div>
        </div>
        <!-- ./col -->

    </div>

    <!-- Default box -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Rekapitulasi Pegawai</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover table-striped" id="tabel">
                    <thead>
                    <tr>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Triwulan I</th>
                        <th>Triwulan II</th>
                        <th>Triwulan III</th>
                        <th>Triwulan IV</th>
                        <th>Total JP</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- /.card -->

    <div class="modal fade" id="modal-pegawai" x-data="pegawaiModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detail Pegawai</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 x-text="`Nama: ${nama}`"></h5>
                    <h5 x-text="`NIP: ${nip}`"></h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-hover table-striped" id="tabel-modal-pegawai">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Materi</th>
                                <th>Jumlah Jam</th>
                            </tr>
                            </thead>
                            <tbody id="alpine-tabel-modal-pegawai-body">
                            <template x-for="(pelatihan, index) in pelatihan_terurut" :key="pelatihan.id">
                                <tr>
                                    <td x-text="index + 1"></td>
                                    <td x-text="formatDate(pelatihan.dari_tanggal_pelaksanaan || pelatihan.tanggal_pelaksanaan)"></td>
                                    <td x-text="pelatihan.type"></td>
                                    <td x-text="pelatihan.materi_pengembangan"></td>
                                    <td x-text="pelatihan.jumlah_jam || pelatihan.jumlah_jam_pelatihan"></td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection

@push('scripts')
    <script>
        // utility untuk format dd-mm-yyyy
        function formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            const dd = String(d.getDate()).padStart(2, '0');
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const yyyy = d.getFullYear();
            return `${dd}-${mm}-${yyyy}`;
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('pegawaiModal', () => ({
                nama: '',
                nip: '',
                pelatihan_terurut: [],
                formatDate      // expose ke template
            }));
        });

        $(document).ready(function() {
            $('#tabel').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: `/dashboard-admin/datatable`,
                    type: 'POST'
                },
                columns: [
                    { data: 'nip', name: 'p.nip' },
                    { data: 'nama', name: 'p.nama' },
                    { data: 'triwulan_1', name: 'triwulan_1', searchable: false, orderable: false },
                    { data: 'triwulan_2', name: 'triwulan_2', searchable: false, orderable: false },
                    { data: 'triwulan_3', name: 'triwulan_3', searchable: false, orderable: false },
                    { data: 'triwulan_4', name: 'triwulan_4', searchable: false, orderable: false },
                    { data: 'total_jp', name: 'total_jp', searchable: false, orderable: false }
                ],
                rowCallback: function(row, data) {
                    $(row).off('click').on('click', function() {
                        let alpineInstance = Alpine.$data(document.querySelector('#alpine-tabel-modal-pegawai-body'));

                        alpineInstance.nama = data.nama;
                        alpineInstance.nip = data.nip;

                        const combinedData = [
                            ...data.diklats.map(d => ({ type: 'Diklat', ...d, id: `Diklat-${d.id}` })),
                            ...data.ppms.map(p => ({ type: 'PPM', ...p, id: `PPM-${p.id}` })),
                            ...data.seminars.map(s => ({ type: 'Seminar', ...s, id: `Seminar-${s.id}` })),
                            ...data.webinars.map(w => ({ type: 'Webinar', ...w, id: `Webinar-${w.id}` })),
                            ...data.lcs.map(l => ({ type: 'LC', ...l, id: `LC-${l.id}` }))
                        ];

                        // Sort ascending: terlama ke terbaru
                        data.pelatihan_terurut = combinedData
                            .sort((a, b) =>
                                new Date(a.dari_tanggal_pelaksanaan || a.tanggal_pelaksanaan)
                                - new Date(b.dari_tanggal_pelaksanaan || b.tanggal_pelaksanaan)
                            );

                        alpineInstance.pelatihan_terurut = data.pelatihan_terurut;
                        $('#modal-pegawai').modal({});
                    });
                }
            });
        });
    </script>
@endpush
