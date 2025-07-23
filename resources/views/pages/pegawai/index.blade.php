@extends('layouts.default')

@section('title')
    Pegawai
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item active">Pegawai</li>
@endsection

@section('content')
    <!-- Default box -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Pegawai</h3>
        </div>
        <div class="card-body">
            <a href="/pegawai/create">
                <button class="btn btn-success mb-3"><i class="fa fa-plus"></i> Tambah Pegawai</button>
            </a>
            <div class="row mb-4">
                <div class="col-4">
                    <label for="tipe">Tipe</label>
                    <select type="text" class="form-control" id="tipe" x-model.lazy="formData.tipe"
                            :class="{'is-invalid': validationErrors.tipe}">
                        <option value="">Semua</option>
                        <option value="pegawai">Pegawai</option>
                        <option value="admin">Admin</option>
                    </select>
                    @push('scripts')
                        <script>
                            $(document).ready(function() {
                                $('#tipe').change(function() {
                                    $('#tabel').DataTable().ajax.reload();
                                });
                            });
                        </script>
                    @endpush
                </div>
                <div class="col-4">
                    <label for="status">Status</label>
                    <select type="text" class="form-control" id="status" x-model.lazy="formData.status"
                            :class="{'is-invalid': validationErrors.status}">
                        <option value="">Semua</option>
                        <option value="aktif">Aktif</option>
                        <option value="non aktif">Non Aktif</option>
                    </select>
                    @push('scripts')
                        <script>
                            $(document).ready(function() {
                                $('#status').change(function() {
                                    $('#tabel').DataTable().ajax.reload();
                                });
                            });
                        </script>
                    @endpush
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-4">
                    <label for="bidang_id">Bidang</label>
                    <select type="text" class="form-control" id="bidang_id" x-model.lazy="formData.bidang_id"
                            :class="{'is-invalid': validationErrors.bidang_id}">
                        <option value="">Semua</option>
                        @foreach ($bidangs as $bidang)
                            <option value="{{ $bidang->id }}">{{$bidang->bidang}}</option>
                        @endforeach
                    </select>
                    @push('scripts')
                        <script>
                            $(document).ready(function() {
                                $('#bidang_id').change(function() {
                                    $('#tabel').DataTable().ajax.reload();
                                });
                            });
                        </script>
                    @endpush
                </div>
                <div class="col-4">
                    <label for="pangkat_golongan_id">Pangkat Golongan</label>
                    <select type="text" class="form-control" id="pangkat_golongan_id"
                            x-model.lazy="formData.pangkat_golongan_id"
                            :class="{'is-invalid': validationErrors.pangkat_golongan_id}">
                        <option value="">Semua</option>
                        @foreach ($pangkats as $pangkat)
                            <option value="{{ $pangkat->id }}">{{$pangkat->pangkat}} ({{$pangkat->golongan}}
                                /{{$pangkat->ruang}})
                            </option>
                        @endforeach
                    </select>
                    @push('scripts')
                        <script>
                            $(document).ready(function() {
                                $('#pangkat_golongan_id').change(function() {
                                    $('#tabel').DataTable().ajax.reload();
                                });
                            });
                        </script>
                    @endpush
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover table-striped" id="tabel">
                    <thead>
                    <tr>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Bidang</th>
                        <th>Pangkat Golongan</th>
                        <th>Jabatan</th>
                        <th>Peran</th>
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
                    url: `/pegawai/datatable`,
                    type: 'POST',
                    data: function(d) {
                        d.tipe = $('#tipe').val();
                        d.status = $('#status').val();
                        d.bidang_id = $('#bidang_id').val();
                        d.pangkat_golongan_id = $('#pangkat_golongan_id').val();
                    }
                },
                columns: [
                    { data: 'nip', name: 'p.nip' },
                    { data: 'nama', name: 'p.nama' },
                    { data: 'tipe', name: 'p.tipe' },
                    { data: 'status', name: 'p.status' },
                    { data: 'bidang', name: 'b.bidang' },
                    { data: 'pangkat_golongan', name: 'pg.id' },
                    { data: 'jabatan', name: 'p.jabatan' },
                    { data: 'peran', name: 'p.peran' },
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
                    await axios.delete(`/pegawai/${id}`);

                    $('#tabel').DataTable().ajax.reload();

                    toastr.success('Pegawai berhasil dihapus');
                } catch (e) {
                    toastr.error('Terjadi kesalahan sistem. Silahkan refresh halaman ini. Jika error masih terjadi, silahkan hubungi Tim IT.');
                }
            }
        }
    </script>
@endpush
