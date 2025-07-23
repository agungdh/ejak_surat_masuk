@extends('layouts.default')

@section('title')
    PPM
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item active">PPM</li>
@endsection

@section('content')
    <!-- Default box -->
    <div class="card" x-data="form" id="formComponent">
        <div class="card-header">
            <h3 class="card-title">{{ isset($ppm) ? 'Ubah' : 'Tambah' }} Data PPM</h3>
        </div>
        <form @submit.prevent="submit">
            <div class="card-body row">

                @role('admin')
                <div class="form-group col-6">
                    @php($formName = 'pegawai_id')
                    @php($formLabel = 'Pegawai')
                    <label for="{{$formName}}">{{$formLabel}}</label>
                    <select type="text" class="form-control select2" id="{{$formName}}"
                            :class="{'is-invalid': validationErrors.{{$formName}}}">
                        <option value="">{{$formLabel}}</option>
                        @foreach($pegawais as $pegawai)
                            <option value="{{$pegawai->id}}">{{$pegawai->nama}} - {{$pegawai->nip}}</option>
                        @endforeach
                    </select>
                    <template x-if="validationErrors.{{$formName}}">
                        <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                    </template>
                    @push('scripts')
                        <script>
                            $(document).ready(function() {
                                $('#{{$formName}}').change(function() {
                                    formAlpine.formData.{{$formName}} = $(this).val();
                                });
                            });
                        </script>
                    @endpush
                </div>
                @endrole

                <div class="form-group @role('admin') col-6 @else col-12 @endrole">
                    @php($formName = 'jenis_pelatihan_id')
                    @php($formLabel = 'Jenis Pelatihan')
                    <label for="{{$formName}}">{{$formLabel}}</label>
                    <select type="text" class="form-control" id="{{$formName}}" x-model.lazy="formData.{{$formName}}"
                            :class="{'is-invalid': validationErrors.{{$formName}}}">
                        <option value="">{{$formLabel}}</option>
                        @foreach($jenisPelatihans as $jenisPelatihan)
                            <option value="{{$jenisPelatihan->id}}">{{$jenisPelatihan->jenis_pelatihan}}</option>
                        @endforeach
                    </select>
                    <template x-if="validationErrors.{{$formName}}">
                        <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                    </template>
                </div>

                <div class="form-group col-6">
                    @php($formName = 'nomor_surat')
                    @php($formLabel = 'Nomor Surat')
                    <label for="{{$formName}}">{{$formLabel}}</label>
                    <input type="text" class="form-control" id="{{$formName}}" placeholder="{{$formLabel}}"
                           x-model.lazy="formData.{{$formName}}"
                           :class="{'is-invalid': validationErrors.{{$formName}}}">
                    <template x-if="validationErrors.{{$formName}}">
                        <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                    </template>
                </div>

                <div class="form-group col-6">
                    @php($formName = 'materi_pengembangan')
                    @php($formLabel = 'Materi Pengembangan')
                    <label for="{{$formName}}">{{$formLabel}}</label>
                    <input type="text" class="form-control" id="{{$formName}}" placeholder="{{$formLabel}}"
                           x-model.lazy="formData.{{$formName}}"
                           :class="{'is-invalid': validationErrors.{{$formName}}}">
                    <template x-if="validationErrors.{{$formName}}">
                        <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                    </template>
                </div>

                <div class="form-group col-6">
                    @php($formName = 'tanggal_pelaksanaan')
                    @php($formLabel = 'Tanggal Pelaksanaan')
                    <label for="{{$formName}}">{{$formLabel}}</label>
                    <input type="text" class="form-control datepicker" id="{{$formName}}" placeholder="{{$formLabel}}"
                           :class="{'is-invalid': validationErrors.{{$formName}}}">
                    <template x-if="validationErrors.{{$formName}}">
                        <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                    </template>
                    @push('scripts')
                        <script>
                            $(document).ready(function() {
                                $('#{{$formName}}').change(function() {
                                    formAlpine.formData.{{$formName}} = moment($(this).val(), 'DD-MM-YYYY').format('YYYY-MM-DD');
                                });

                                $('#{{$formName}}').on('apply.daterangepicker', function(ev, picker) {
                                    $(this).val(picker.startDate.format('DD-MM-YYYY'));
                                    $('#{{$formName}}').change();
                                });

                                $('#{{$formName}}').on('cancel.daterangepicker', function(ev, picker) {
                                    $(this).val('');
                                    $('#{{$formName}}').change();
                                });
                            });
                        </script>
                    @endpush
                </div>

                <div class="form-group col-6">
                    @php($formName = 'jumlah_jam_pelatihan')
                    @php($formLabel = 'Jumlah Jam Pelatihan')
                    <label for="{{$formName}}">{{$formLabel}}</label>
                    <input type="text" class="form-control" id="{{$formName}}" placeholder="{{$formLabel}}"
                           x-model.lazy="formData.{{$formName}}"
                           :class="{'is-invalid': validationErrors.{{$formName}}}">
                    <template x-if="validationErrors.{{$formName}}">
                        <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                    </template>
                </div>

            </div>

            <div class="card-footer">
                <a href="/ppm">
                    <button type="button" class="btn btn-info">Kembali</button>
                </a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
    <!-- /.card -->
@endsection

@push('scripts')
    <script>
        id = @json($ppm?->id ?? null);

        document.addEventListener('alpine:init', () => {
            Alpine.data('form', () => ({
                formData: {
                    pegawai_id: '',
                    jenis_pelatihan_id: '',
                    nomor_surat: '',
                    materi_pengembangan: '',
                    tanggal_pelaksanaan: '',
                    jumlah_jam_pelatihan: '',
                    berkas: ''
                },
                validationErrors: {},

                async initData(id) {
                    let res = await axios.get(`/ppm/${id}`);
                    let data = res.data;

                    for (let key in this.formData) {
                        if (data.hasOwnProperty(key)) {
                            this.formData[key] = data[key];
                        }
                    }

                    $('#pegawai_id').val(data.pegawai_id).change();
                    $('#tanggal_pelaksanaan').val(moment(data.tanggal_pelaksanaan, 'YYYY-MM-DD').format('DD-MM-YYYY')).change();
                },

                async submit() {
                    let formData = new FormData();

                    for (let key in this.formData) {
                        formData.append(key, this.formData[key]);
                    }

                    try {
                        if (id) {
                            formData.append('_method', 'PUT');

                            await axios.post(`/ppm/${id}`, formData);
                        } else {
                            await axios.post('/ppm', formData);
                        }

                        window.location.href = '/ppm';
                    } catch (err) {
                        if (err.response?.status === 422) {
                            this.validationErrors = err.response.data.errors ?? {};
                        } else {
                            toastr.error('Terjadi kesalahan sistem. Silahkan refresh halaman ini. Jika error masih terjadi, silahkan hubungi Tim IT.');
                        }
                    }
                }

            }));
        });

        $(document).ready(function() {
            formComponent = document.getElementById('formComponent');

            formAlpine = Alpine.$data(formComponent);

            id && formAlpine.initData(id);
        });
    </script>
@endpush
