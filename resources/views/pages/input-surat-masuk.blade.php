@extends('layouts.guest')

@section('title')
    <h1 class="m-0"> Surat Masuk
@endsection

@section('breadcrumb')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card" x-data="form" id="formComponent">
                <form @submit.prevent="submit">
                    <div class="card-body row">

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
                            @php($formName = 'tanggal_surat')
                            @php($formLabel = 'Tanggal Surat')
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

                        <div class="form-group col-12">
                            @php($formName = 'perihal')
                            @php($formLabel = 'Perihal')
                            <label for="{{$formName}}">{{$formLabel}}</label>
                            <input type="text" class="form-control" id="{{$formName}}" placeholder="{{$formLabel}}"
                                   x-model.lazy="formData.{{$formName}}"
                                   :class="{'is-invalid': validationErrors.{{$formName}}}">
                            <template x-if="validationErrors.{{$formName}}">
                                <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                            </template>
                        </div>

                        <div class="form-group col-6">
                            @php($formName = 'pengirim')
                            @php($formLabel = 'Pengirim')
                            <label for="{{$formName}}">{{$formLabel}}</label>
                            <input type="text" class="form-control" id="{{$formName}}" placeholder="{{$formLabel}}"
                                   x-model.lazy="formData.{{$formName}}"
                                   :class="{'is-invalid': validationErrors.{{$formName}}}">
                            <template x-if="validationErrors.{{$formName}}">
                                <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                            </template>
                        </div>

                        <div class="form-group col-6">
                            @php($formName = 'penerima')
                            @php($formLabel = 'Penerima')
                            <label for="{{$formName}}">{{$formLabel}}</label>
                            <input type="text" class="form-control" id="{{$formName}}" placeholder="{{$formLabel}}"
                                   x-model.lazy="formData.{{$formName}}"
                                   :class="{'is-invalid': validationErrors.{{$formName}}}">
                            <template x-if="validationErrors.{{$formName}}">
                                <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                            </template>
                        </div>

                        <div class="form-group col-12">
                            @php($formName = 'berkas')
                            @php($formLabel = 'Berkas')
                            <label for="{{$formName}}">{{$formLabel}}</label>
                            <input type="file" class="form-control" id="{{$formName}}" placeholder="{{$formLabel}}"
                                   :class="{'is-invalid': validationErrors.{{$formName}}}"
                                   @change="formData.{{$formName}} = $event.target.files[0]">
                            <template x-if="validationErrors.{{$formName}}">
                                <div class="invalid-feedback" x-text="validationErrors.{{$formName}}"></div>
                            </template>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    id = @json($pegawai?->id ?? null);

    document.addEventListener('alpine:init', () => {
        Alpine.data('form', () => ({
            formData: {
                nomor_surat: '',
                tanggal_surat: '',
                perihal: '',
                pengirim: '',
                penerima: '',
                berkas: '',
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
                let formData = new FormData();

                for (let key in this.formData) {
                    formData.append(key, this.formData[key]);
                }

                try {
                    await axios.post('/', formData);

                    await Swal.fire('Berhasil', 'Surat masuk berhasil dikirim', 'success')

                    window.location.href = '/'
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
