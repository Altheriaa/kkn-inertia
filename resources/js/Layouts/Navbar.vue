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
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Sistem KKN Unaya</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">{{ $page.props.title }}</li>
                </ol>
            </nav>
            <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                <div class="ms-md-auto pe-md-3 d-flex align-items-center">

                </div>
                <ul class="navbar-nav d-flex align-items-center  justify-content-end">
                    <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav" @click="toggleSidenav">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item dropdown pe-3 d-flex align-items-center">
                    </li>
                    <li v-if="$page.props.authMahasiswa.user" class="nav-item d-flex align-items-center">
                        <Link href="/mahasiswa/profile" class="nav-link text-body font-weight-bold px-0">
                            <i class="material-symbols-rounded">account_circle</i>
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>