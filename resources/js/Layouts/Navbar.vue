<script setup>
    import { Link } from '@inertiajs/vue3';

    const toggleSidenav = () => {
        const body = document.getElementsByTagName('body')[0];
        const className = 'g-sidenav-pinned';

        if (body.classList.contains(className)) {
            body.classList.remove(className);
        } else {
            body.classList.add(className);
        }
    };
</script>

<template>
    <nav class="navbar navbar-main navbar-expand-lg px-2 mx-3 mb-2 shadow-none border-radius-xl" id="navbarBlur"
    data-scroll="true">
        <div class="container-fluid py-1 px-3 d-flex align-items-center justify-content-between">
            <nav aria-label="breadcrumb" class="d-flex align-items-center m-0 p-0">
                <ol class="breadcrumb bg-transparent mb-0 p-0">
                    <li class="breadcrumb-item text-sm">
                        <Link v-if="$page.props.auth.user" href="/admin/dashboard" class="opacity-5 text-dark">KKN Unaya</Link>
                        <Link v-else-if="$page.props.authMahasiswa.user" href="/mahasiswa/dashboard" class="opacity-5 text-dark">KKN Unaya</Link>
                    </li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">{{ $page.props.title }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center" id="navbar">
                <ul class="navbar-nav d-flex flex-row align-items-center gap-3 m-0 p-0">
                    <li class="nav-item d-xl-none d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-body p-0 d-flex align-items-center" id="iconNavbarSidenav" @click="toggleSidenav">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                            </div>
                        </a>
                    </li>
                    <li v-if="$page.props.authMahasiswa.user" class="nav-item d-flex align-items-center">
                        <Link href="/mahasiswa/profile" class="nav-link text-body font-weight-bold p-0 d-flex align-items-center">
                            <i class="material-symbols-rounded">account_circle</i>
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>