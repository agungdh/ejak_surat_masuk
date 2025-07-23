@extends('layouts.default')

@section('title')
    Dashboard
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active"><a href="/">Home</a></li>
@endsection

@section('content')
    <div class="row">
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
                <table class="table table-bordered table-sm table-hover table-striped" id="tabel-rekapitulasi-pegawai">
                    <thead>
                    <tr>
                        <th>Triwulan I</th>
                        <th>Triwulan II</th>
                        <th>Triwulan III</th>
                        <th>Triwulan IV</th>
                        <th>Total JP</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{$rekapitulasiPegawaiData->triwulan_1}}</td>
                        <td>{{$rekapitulasiPegawaiData->triwulan_2}}</td>
                        <td>{{$rekapitulasiPegawaiData->triwulan_3}}</td>
                        <td>{{$rekapitulasiPegawaiData->triwulan_4}}</td>
                        <td>{{$rekapitulasiPegawaiData->triwulan_1 + $rekapitulasiPegawaiData->triwulan_2 + $rekapitulasiPegawaiData->triwulan_3 + $rekapitulasiPegawaiData->triwulan_4}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- /.card -->

    <!-- Default box -->
    <div class="card" x-data="pegawaiTable">
        <div class="card-header">
            <h3 class="card-title">Detail Pegawai</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover table-striped" id="tabel-detail-pegawai">
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
    </div>
    <!-- /.card -->

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
            Alpine.data('pegawaiTable', () => ({
                nama: '',
                nip: '',
                pelatihan_terurut: [],
                formatDate      // expose ke template
            }));
        });

        $(document).ready(function() {
            let alpineInstance = Alpine.$data(document.querySelector('#alpine-tabel-modal-pegawai-body'));

            data = @json($rekapitulasiPegawaiData);

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
        });
    </script>
@endpush
