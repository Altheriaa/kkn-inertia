<script setup>
    import Layout from '../../Layouts/App.vue';
    import { Link, usePage } from '@inertiajs/vue3';
    import { router } from '@inertiajs/vue3';
    import { watch, onMounted } from 'vue';
    import Swal from 'sweetalert2';

    const page = usePage();

    const props = defineProps({
        jadwalKkn: Object,
        jenisKknList: Array,
        mahasiswaSession: Object,
    });

    const formatCurrency = (value) => {
        if (value === undefined || value === null) return 'Rp 0';
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(value);
    };

    const showFlashMessage = () => {
        const flash = page.props.flash;
        const errors = page.props.errors;

        if (flash.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: flash.success,
                showConfirmButton: false,
                customClass: {
                    popup: 'glass-popup rounded-3xl shadow-blur p-6',
                    title: 'font-semibold',
                    icon: 'icon-custom bg-transparent'
                },
                timer: 2000
            });
        } else if (flash.warning) {
            Swal.fire({
                icon: 'warning',
                text: flash.warning,
                showConfirmButton: false,
                customClass: {
                    popup: 'glass-popup rounded-3xl shadow-blur p-6',
                    title: 'font-semibold',
                    icon: 'icon-custom bg-transparent'
                },
                timer: 2000
            });
        }

        if (Object.keys(errors).length > 0) {
            const errorMessages = Object.values(errors).join('<br>');
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: errorMessages,
                customClass: {
                    popup: 'glass-popup rounded-3xl shadow-blur p-6',
                    title: 'font-bold',
                    confirmButton: 'button-confirm px-6 py-2 rounded-xl text-white',
                }
            });
        }
    };

    onMounted(() => {
        showFlashMessage();
    });

    watch(() => page.props.flash, () => {
        showFlashMessage();
    }, { deep: true });

    // Format tanggal
    const formatDate = (dateStr) => {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    };
</script>

<template>
    <Layout>
        <div class="container-fluid py-2">
            <div class="row">
                <div class="ms-3">
                    <h3 class="mb-3 h4 font-weight-bolder">Dashboard Mahasiswa</h3>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-sm mb-0 text-capitalize">Nama</p>
                                    <h5 class="mb-0">{{ mahasiswaSession?.name || '-' }}</h5>
                                </div>
                                <div
                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">person</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-sm"><span class="text-success font-weight-bolder"></span></p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-sm mb-0 text-capitalize">NIM</p>
                                    <h4 class="mb-0">{{ mahasiswaSession?.nim || '-' }}</h4>
                                </div>
                                <div
                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">badge</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-sm"><span class="text-success font-weight-bolder"></span></p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-sm mb-0 text-capitalize">Program Studi</p>
                                    <h4 class="mb-0">{{ mahasiswaSession?.prodi?.nama_prodi || '-' }}</h4>
                                </div>
                                <div
                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                <i class="material-symbols-rounded opacity-10">school</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-sm"><span class="text-success font-weight-bolder"></span></p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-sm mb-0 text-capitalize">Jumlah SKS</p>
                                    <h4 class="mb-0">{{ mahasiswaSession?.jumlah_sks || '-' }}</h4>
                                </div>
                                <div
                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">weekend</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-sm"><span class="text-success font-weight-bolder"></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- {{-- Section Jadwal KKN dan Alur KKN --}} -->
            <div class="row mt-4 mb-4">
                <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
                    <div class="card">

                        <!-- {{-- Jadwal KKN --}} -->
                        <div class="card-header pb-0">
                            <div class="row">
                                <div class="col-lg-6 col-7">
                                    <h6>Jadwal KKN</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Tahun Akademik</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Tanggal Dibuka</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Tanggal Ditutup</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <!-- {{-- Kolom Tahun Akademik (INI PERBAIKANNYA) --}} -->
                                            <td>
                                                <div class="d-flex px-2 py-1 ps-3">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 v-if="jadwalKkn" class="mb-0 text-sm">
                                                            {{ jadwalKkn.nama_periode }}
                                                        </h6>
                                                        <h6 v-else class="mb-0 text-sm">
                                                            Tidak Ada Jadwal
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- {{-- Kolom Tanggal Dibuka (Ini sudah benar) --}} -->
                                            <td class="align-middle text-center text-sm">
                                                <span v-if="jadwalKkn" class="text-xs font-weight-bold">
                                                    {{ formatDate(jadwalKkn.tanggal_dibuka)  }}
                                                </span>
                                                <span v-else class="text-xs font-weight-bold">
                                                    -
                                                </span>
                                            </td>

                                            <!-- {{-- Kolom Tanggal Ditutup (Ini sudah benar) --}} -->
                                            <td class="align-middle text-center text-sm">
                                                <span v-if="jadwalKkn" class="text-xs font-weight-bold">
                                                    {{ formatDate(jadwalKkn.tanggal_ditutup)  }}
                                                </span>
                                                <span v-else class="text-xs font-weight-bold">
                                                    -
                                                </span>
                                            </td>

                                            <!-- {{-- Kolom Status (Baru, karena datanya ada) --}} -->
                                            <td v-if="jadwalKkn" class="align-middle text-center text-sm">
                                                <span v-if="jadwalKkn.is_active" class="badge badge-sm bg-gradient-success">
                                                    Dibuka
                                                </span>
                                                <span v-else class="badge badge-sm bg-gradient-secondary">
                                                    Ditutup
                                                </span>
                                            </td>
                                            <td v-else class="align-middle text-center text-sm">
                                                <span class="badge badge-sm bg-gradient-secondary">Tidak Ada Jadwal</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- {{-- Table Biaya KKN --}} -->
                        <div class="card-header pb-0">
                            <div class="row">
                                <div class="col-lg-6 col-7">
                                    <h6>Biaya KKN</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Jenis KKN</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Biaya</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-if="jenisKknList && jenisKknList.length > 0">
                                            <tr v-for="jenis in jenisKknList" :key="jenis.id">
                                                <!-- Kolom Prodi -->
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">
                                                                {{ jenis.nama_jenis }}
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <h6 class="mb-0 text-sm text">
                                                        {{ formatCurrency(jenis.biaya) }}
                                                    </h6>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span v-if="jenis.is_active" class="badge badge-sm bg-gradient-success">Aktif</span>
                                                    <span v-else class="badge badge-sm bg-gradient-secondary">Nonaktif</span>
                                                </td>
                                            </tr>
                                        </template>
                                        <template v-else>
                                            <tr>
                                                <td colspan="3" class="text-center text-secondary">
                                                    Tidak ada jenis KKN tersedia.
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- {{-- button sync maahasiswa --}} -->
                    <div class="card mt-2">
                        <!-- {{-- Jadwal KKN --}} -->
                        <div class="card-header pb-0">
                            <div class="row p-4">
                                <div class="d-flex justify-content-center">
                                    <form action="" method="GET">
                                        <button class="btn btn-lg btn-dark text-lg">
                                            <i class="material-symbols-rounded">sync</i>
                                            Sync Mahasiswa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- {{-- Table Biaya KKN --}} -->
                    </div>
                    <!-- {{-- end sync --}} -->

                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100">
                        <div class="card-header pb-0">
                            <h6>Alur KKN</h6>
                            <p class="text-sm">
                                Tahapan dari pendaftaran hingga selesai.
                            </p>
                        </div>
                        <div class="card-body p-3">
                            <div class="timeline timeline-one-side">
                                <div class="timeline-block mb-3">
                                    <span class="timeline-step">
                                        <i class="material-symbols-rounded text-success text-gradient">fact_check</i>
                                    </span>
                                    <div class="timeline-content">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">Pengecekan Syarat</h6>
                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">SKS, Status, & Jadwal Aktif
                                        </p>
                                    </div>
                                </div>
                                <div class="timeline-block mb-3">
                                    <span class="timeline-step">
                                        <i class="material-symbols-rounded text-danger text-gradient">how_to_reg</i>
                                    </span>
                                    <div class="timeline-content">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">Pendaftaran & Pilih Jenis KKN</h6>
                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Memilih KKN yang tersedia
                                        </p>
                                    </div>
                                </div>
                                <div class="timeline-block mb-3">
                                    <span class="timeline-step">
                                        <i class="material-symbols-rounded text-info text-gradient">payments</i>
                                    </span>
                                    <div class="timeline-content">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">Pembayaran Biaya KKN</h6>
                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Konfirmasi via Midtrans</p>
                                    </div>
                                </div>
                                <div class="timeline-block mb-3">
                                    <span class="timeline-step">
                                        <i class="material-symbols-rounded text-warning text-gradient">groups</i>
                                    </span>
                                    <div class="timeline-content">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">Penentuan Lokasi & DPL</h6>
                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Plotting oleh Panitia</p>
                                    </div>
                                </div>
                                <div class="timeline-block mb-3">
                                    <span class="timeline-step">
                                        <i class="material-symbols-rounded text-primary text-gradient">event_note</i>
                                    </span>
                                    <div class="timeline-content">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">Pelaksanaan KKN</h6>
                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Pengisian logbook &
                                            bimbingan</p>
                                    </div>
                                </div>
                                <div class="timeline-block">
                                    <span class="timeline-step">
                                        <i class="material-symbols-rounded text-dark text-gradient">task_alt</i>
                                    </span>
                                    <div class="timeline-content">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">Laporan & Penilaian</h6>
                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">KKN Selesai</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>