<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{config('app.name')}}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/assets/AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="/assets/AdminLTE-3.2.0/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/assets/AdminLTE-3.2.0/dist/css/adminlte.min.css">

    <link rel="icon" href="/assets/images/426af677dddc080bde5939670bea6d39~tplv-tiktokx-cropcenter_1080_1080.jpeg"
          type="image/x-icon">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body.login-page {
            background-image: url('/assets/images/WhatsApp Image 2025-07-09 at 13.51.39.jpeg'); /* Ganti path gambar sesuai lokasi kamu */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary" x-data="form">
        <div class="card-header text-center">
            <a href="javascript:void(0)" class="h1"><b>{{config('app.name')}}</b></a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <form @submit.prevent="submit">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Username" x-model.lazy="formData.username">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Password" x-model.lazy="formData.password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button :disabled="isSubmitting" type="submit" class="btn btn-primary btn-block">Sign In
                        </button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="/assets/AdminLTE-3.2.0/plugins/jquery/jquery.min.js"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        Alpine.start();
    });
</script>
<!-- Bootstrap 4 -->
<script src="/assets/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="/assets/AdminLTE-3.2.0/dist/js/adminlte.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('form', () => ({
            formData: {
                username: '',
                password: ''
            },
            validationErrors: {},
            isSubmitting: false,

            async submit() {
                try {
                    this.isSubmitting = true;

                    await axios.post('/login', this.formData);

                    window.location.href = '/';
                } catch (err) {
                    if (err.response?.status === 422) {
                        toastr.error('Username atau password salah');
                    } else {
                        toastr.error('Terjadi kesalahan saat mengirim data');
                    }
                } finally {
                    this.isSubmitting = false;
                }
            }

        }));
    });
</script>
</body>
</html>
