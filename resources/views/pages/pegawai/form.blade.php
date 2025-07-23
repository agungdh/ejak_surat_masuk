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
    <div class="card" x-data="form" id="formComponent">
        <div class="card-header">
            <h3 class="card-title">{{ isset($pegawai) ? 'Ubah' : 'Tambah' }} Data Pegawai</h3>
        </div>
        <form @submit.prevent="submit">
            <div class="card-body row">

                <div class="form-group col-6">
                    @php($formName = 'nip')
                    @php($formLabel = 'NIP')
                    <label for="{{$formName}}">{{$formLabel}}</label>
                    <input type="text" class="form-control" id="{{$formName}}" placeholder="{{$formLabel}}"
                           x-model.lazy="formData.{{$formName}}"
                           :class="{'is-invalid': validationErrors.{{$formName}}}">
                    <template x-if="validationErrors.{{$formName}}">
                        <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                    </template>
                </div>

                <div class="form-group col-6">
                    @php($formName = 'nama')
                    @php($formLabel = 'Nama')
                    <label for="{{$formName}}">{{$formLabel}}</label>
                    <input type="text" class="form-control" id="{{$formName}}" placeholder="{{$formLabel}}"
                           x-model.lazy="formData.{{$formName}}"
                           :class="{'is-invalid': validationErrors.{{$formName}}}">
                    <template x-if="validationErrors.{{$formName}}">
                        <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                    </template>
                </div>

                <div class="form-group col-6">
                    @php($formName = 'tipe')
                    @php($formLabel = 'Tipe')
                    <label for="{{$formName}}">{{$formLabel}}</label>
                    <select type="text" class="form-control" id="{{$formName}}" x-model.lazy="formData.{{$formName}}"
                            :class="{'is-invalid': validationErrors.{{$formName}}}">
                        <option value="">{{$formLabel}}</option>
                        <option value="pegawai">Pegawai</option>
                        <option value="admin">Admin</option>
                    </select>
                    <template x-if="validationErrors.{{$formName}}">
                        <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                    </template>
                </div>

                <div class="form-group col-6">
                    @php($formName = 'status')
                    @php($formLabel = 'Status')
                    <label for="{{$formName}}">{{$formLabel}}</label>
                    <select type="text" class="form-control" id="{{$formName}}" x-model.lazy="formData.{{$formName}}"
                            :class="{'is-invalid': validationErrors.{{$formName}}}">
                        <option value="">{{$formLabel}}</option>
                        <option value="aktif">Aktif</option>
                        <option value="non aktif">Non Aktif</option>
                    </select>
                    <template x-if="validationErrors.{{$formName}}">
                        <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                    </template>
                </div>

                <div class="form-group col-6">
                    @php($formName = 'bidang_id')
                    @php($formLabel = 'Bidang')
                    <label for="{{$formName}}">{{$formLabel}}</label>
                    <select type="text" class="form-control" id="{{$formName}}" x-model.lazy="formData.{{$formName}}"
                            :class="{'is-invalid': validationErrors.{{$formName}}}">
                        <option value="">{{$formLabel}}</option>
                        @foreach ($bidangs as $bidang)
                            <option value="{{ $bidang->id }}">{{$bidang->bidang}}</option>
                        @endforeach
                    </select>
                    <template x-if="validationErrors.{{$formName}}">
                        <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                    </template>
                </div>

                <div class="form-group col-6">
                    @php($formName = 'pangkat_golongan_id')
                    @php($formLabel = 'Pangkat Golongan')
                    <label for="{{$formName}}">{{$formLabel}}</label>
                    <select type="text" class="form-control" id="{{$formName}}" x-model.lazy="formData.{{$formName}}"
                            :class="{'is-invalid': validationErrors.{{$formName}}}">
                        <option value="">{{$formLabel}}</option>
                        @foreach ($pangkats as $pangkat)
                            <option value="{{ $pangkat->id }}">{{$pangkat->pangkat}} ({{$pangkat->golongan}}
                                /{{$pangkat->ruang}})
                            </option>
                        @endforeach
                    </select>
                    <template x-if="validationErrors.{{$formName}}">
                        <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                    </template>
                </div>

                <div class="form-group col-6">
                    @php($formName = 'jabatan')
                    @php($formLabel = 'Jabatan')
                    <label for="{{$formName}}">{{$formLabel}}</label>
                    <input type="text" class="form-control" id="{{$formName}}" placeholder="{{$formLabel}}"
                           x-model.lazy="formData.{{$formName}}"
                           :class="{'is-invalid': validationErrors.{{$formName}}}">
                    <template x-if="validationErrors.{{$formName}}">
                        <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                    </template>
                </div>

                <div class="form-group col-6">
                    @php($formName = 'peran')
                    @php($formLabel = 'Peran')
                    <label for="{{$formName}}">{{$formLabel}}</label>
                    <input type="text" class="form-control" id="{{$formName}}" placeholder="{{$formLabel}}"
                           x-model.lazy="formData.{{$formName}}"
                           :class="{'is-invalid': validationErrors.{{$formName}}}">
                    <template x-if="validationErrors.{{$formName}}">
                        <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                    </template>
                </div>

                <div class="form-group col-6">
                    @php($formName = 'password')
                    @php($formLabel = 'Password')
                    <label for="{{$formName}}">{{$formLabel}}</label>
                    <input type="password" class="form-control" id="{{$formName}}" placeholder="{{$formLabel}}"
                           x-model.lazy="formData.{{$formName}}"
                           :class="{'is-invalid': validationErrors.{{$formName}}}">
                    <template x-if="validationErrors.{{$formName}}">
                        <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                    </template>
                </div>

                <div class="form-group col-6">
                    @php($formName = 'password_confirmation')
                    @php($formLabel = 'Konfirmasi Password')
                    <label for="{{$formName}}">{{$formLabel}}</label>
                    <input type="password" class="form-control" id="{{$formName}}" placeholder="{{$formLabel}}"
                           x-model.lazy="formData.{{$formName}}"
                           :class="{'is-invalid': validationErrors.{{$formName}}}">
                    <template x-if="validationErrors.{{$formName}}">
                        <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                    </template>
                </div>

            </div>

            <div class="card-footer">
                <a href="/pegawai">
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
        id = @json($pegawai?->id ?? null);

        document.addEventListener('alpine:init', () => {
            Alpine.data('form', () => ({
                formData: {
                    nip: '',
                    nama: '',
                    tipe: '',
                    status: '',
                    bidang_id: '',
                    pangkat_golongan_id: '',
                    jabatan: '',
                    peran: ''
                },
                validationErrors: {},

                async initData(id) {
                    let res = await axios.get(`/pegawai/${id}`);
                    let data = res.data;

                    for (let key in this.formData) {
                        if (data.hasOwnProperty(key)) {
                            this.formData[key] = data[key];
                        }
                    }
                },

                async submit() {
                    try {
                        if (id) {
                            await axios.put(`/pegawai/${id}`, this.formData);
                        } else {
                            await axios.post('/pegawai', this.formData);
                        }

                        window.location.href = '/pegawai';
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
