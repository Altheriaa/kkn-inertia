<script setup>
    import { Link } from '@inertiajs/vue3';
</script>

<template>
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="material-symbols-rounded p-3 cursor-pointer text-dark opacity-5 position-absolute top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav">close</i>
        <Link class="navbar-brand px-4 py-3 m-0" href="/">
            <img src="/assets/img/Unaya.png" class="navbar-brand-img" width="26" height="26"
                alt="main_logo">
            <span class="ms-2 text-sm font-weight-bold text-dark">Sistem KKN Unaya</span>
        </Link>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <!-- Admin -->
            <!-- Jika User terotentikasi dan merupkan Admin (contoh pengecekan dari email "operatorkkn", ganti dengan `role` nantinya) -->
            <template v-if="$page.props.auth.user && $page.props.auth.user.email === 'operatorkkn@abulyatama.ac.id'">
                <li class="nav-item">
                    <Link class="nav-link" :class="{ 'active bg-gradient-dark text-white': $page.url === '/admin/dashboard', 'text-dark': $page.url !== '/admin/dashboard' }" href="/admin/dashboard">
                        <i class="material-symbols-rounded opacity-5">dashboard</i>
                        <span class="nav-link-text ms-2">Dashboard</span>
                    </Link>
                </li>
                <li class="nav-item">
                    <Link class="nav-link" :class="{ 'active bg-gradient-dark text-white': $page.url === '/admin/jadwal-kkn', 'text-dark': $page.url !== '/admin/jadwal-kkn' }" href="/admin/jadwal-kkn">
                        <i class="material-symbols-rounded opacity-5">calendar_month</i>
                        <span class="nav-link-text ms-2">Jadwal KKN</span>
                    </Link>
                </li>
                <li class="nav-item">
                    <Link class="nav-link" :class="{ 'active bg-gradient-dark text-white': $page.url.startsWith('/admin/mahasiswa'), 'text-dark': !$page.url.startsWith('/admin/mahasiswa') }" href="/admin/mahasiswa">
                        <i class="material-symbols-rounded opacity-5">group</i>
                        <span class="nav-link-text ms-2">Mahasiswa</span>
                    </Link>
                </li>
                <li class="nav-item">
                    <Link class="nav-link" :class="{ 'active bg-gradient-dark text-white': $page.url.startsWith('/admin/dosen-dpl'), 'text-dark': !$page.url.startsWith('/admin/dosen-dpl') }" href="/admin/dosen-dpl">
                        <i class="material-symbols-rounded opacity-5">groups_3</i>
                        <span class="nav-link-text ms-2">DPL</span>
                    </Link>
                </li>
                <li class="nav-item">
                    <Link class="nav-link" :class="{ 'active bg-gradient-dark text-white': $page.url.startsWith('/admin/lokasi-kkn'), 'text-dark': !$page.url.startsWith('/admin/lokasi-kkn') }" href="/admin/lokasi-kkn">
                        <i class="material-symbols-rounded opacity-5">location_on</i>
                        <span class="nav-link-text ms-2">Lokasi KKN</span>
                    </Link>
                </li>
                <!-- Tambahkan link admin yang lain persis seperti pola di atas... -->
            </template>

            <!-- Mahasiswa -->
            <!-- Menu Mahasiswa: Muncul bila mahasiswa login -->
            <template v-else-if="$page.props.authMahasiswa.user">
                <li class="nav-item">
                    <Link class="nav-link" :class="{ 'active bg-gradient-dark text-white': $page.url === '/mahasiswa/dashboard', 'text-dark': $page.url !== '/mahasiswa/dashboard' }" href="/mahasiswa/dashboard">
                        <i class="material-symbols-rounded opacity-5">dashboard</i>
                        <span class="nav-link-text ms-1">Dashboard Mahasiswa</span>
                    </Link>
                </li>
                <li class="nav-item">
                    <Link class="nav-link" :class="{ 'active bg-gradient-dark text-white': $page.url === '/mahasiswa/pendaftaran', 'text-dark': $page.url !== '/mahasiswa/pendaftaran' }" href="/mahasiswa/pendaftaran">
                        <i class="material-symbols-rounded opacity-5">app_registration</i>
                        <span class="nav-link-text ms-1">Pendaftaran / Biodata</span>
                    </Link>
                </li>
                <li class="nav-item">
                    <Link class="nav-link" :class="{ 'active bg-gradient-dark text-white': $page.url === '/mahasiswa/pembayaran', 'text-dark': $page.url !== '/mahasiswa/pembayaran' }" href="/mahasiswa/pembayaran">
                        <i class="material-symbols-rounded opacity-5">payment</i>
                        <span class="nav-link-text ms-1">Pembayaran</span>
                    </Link>
                </li>
                <li class="nav-item">
                    <Link class="nav-link" :class="{ 'active bg-gradient-dark text-white': $page.url === '/mahasiswa/riwayat-transaksi', 'text-dark': $page.url !== '/mahasiswa/riwayat-transaksi' }" href="/mahasiswa/riwayat-transaksi">
                        <i class="material-symbols-rounded opacity-5">receipt_long</i>
                        <span class="nav-link-text ms-1">Riwayat Transaksi</span>
                    </Link>
                </li>
                <li class="nav-item">
                    <Link class="nav-link" :class="{ 'active bg-gradient-dark text-white': $page.url === '/mahasiswa/profile', 'text-dark': $page.url !== '/mahasiswa/profile' }" href="/mahasiswa/profile">
                        <i class="material-symbols-rounded opacity-5">account_circle</i>
                        <span class="nav-link-text ms-1">Profil</span>
                    </Link>
                </li>
                <!-- Tambahkan link mahasiswa yang lain mengikuti pola di atas -->
            </template>
        </ul>
    </div>
    <div class="sidenav-footer position-absolute w-100 bottom-0 ">
        <div class="mx-3">
            <template v-if="$page.props.auth.user && $page.props.auth.user.email === 'operatorkkn@abulyatama.ac.id'">
                <Link href="/logout/admin" class="btn bg-gradient-dark w-100" type="button">
                    Logout
                </Link>
            </template>
            <template v-else-if="$page.props.authMahasiswa.user">
                <Link href="/logout" class="btn bg-gradient-dark w-100" type="button">
                    Logout
                </Link>
            </template>
        </div>
    </div>
</aside>
</template>