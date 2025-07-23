<!-- Sidebar -->
<div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

            <li class="nav-header">Dashboard</li>
            <li class="nav-item">
                <a href="/dashboard" class="nav-link">
                    <i class="nav-icon fas fa-home"></i>
                    <p>
                        Dashboard
                    </p>
                </a>
            </li>
            @role('admin')
            <li class="nav-item">
                <a href="/dashboard-admin" class="nav-link">
                    <i class="nav-icon fas fa-home"></i>
                    <p>
                        Dashboard Admin
                    </p>
                </a>
            </li>
            @endrole

            @role('admin')
            <li class="nav-header">Master Data</li>
            <li class="nav-item">
                <a href="/pegawai" class="nav-link">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                        Pegawai
                    </p>
                </a>
            </li>
            @endrole

            <li class="nav-header">Kartu Kontrol</li>
            <li class="nav-item">
                <a href="/diklat" class="nav-link">
                    <i class="nav-icon fas fa-book"></i>
                    <p>
                        Diklat
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="/ppm" class="nav-link">
                    <i class="nav-icon fas fa-book"></i>
                    <p>
                        PPM
                    </p>
                </a>
            </li>

            <li class="nav-header">Pengembangan</li>
            <li class="nav-item">
                <a href="/seminar" class="nav-link">
                    <i class="nav-icon fas fa-book"></i>
                    <p>
                        Seminar
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="/webinar" class="nav-link">
                    <i class="nav-icon fas fa-book"></i>
                    <p>
                        Webinar
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="/lc" class="nav-link">
                    <i class="nav-icon fas fa-book"></i>
                    <p>
                        LC
                    </p>
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->
