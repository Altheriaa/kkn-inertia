<script setup>
import Layout from '../../../Layouts/App.vue';
import { Link } from '@inertiajs/vue3';

// Data Props From Controller
const props = defineProps({
    kelompok: Object,
    pendaftaranKkn: Number,
    anggota: Array,
});

const getInitials = (name) => {
    return name ? name.substring(0, 2).toUpperCase() : '??';
};
</script>

<template>
    <Layout>
        <div class="container-fluid py-2">
            <!-- Header & Navigasi -->
            <div class="row">
                <div class="col-12">
                    <div class="mb-2 mt-2">
                        <Link href="/mahasiswa/plotting" class="btn btn-outline-dark">
                            <i class="material-symbols-rounded text-sm me-1 align-middle">arrow_back</i> Kembali
                        </Link>
                    </div>
                    <!-- Info Kelompok -->
                    <div class="card">
                        <div class="card-header ps-3 bg-gradient-dark shadow-dark border-radius-lg">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 p-2">
                                <div class="text-start mb-2">
                                    <p class="text-sm mb-0 text-capitalize text-white opacity-7">Nama Kelompok</p>
                                    <h4 class="mb-0 text-white">{{ kelompok.nama_kelompok }}</h4>
                                    <div class="mt-2">
                                        <span class="badge badge-sm bg-gradient-primary border-0 text-white">{{ kelompok.jadwal_kkn?.nama_periode }}</span>
                                        <span class="text-white mx-2">|</span>
                                        <span class="badge badge-sm bg-gradient-secondary border-0 text-white">{{ kelompok.jenis_kkn }}</span>
                                        <span class="text-white mx-2">|</span>
                                        <span class="font-weight-bold text-light">
                                            <i class="material-symbols-rounded text-success text-gradient me-1 align-middle">location_on</i>
                                            {{ kelompok.lokasi_kkn?.nama_desa }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-start text-md-end rounded-3 bg-gradient-secondary px-3 py-2">
                                    <p class="text-sm mb-0 text-capitalize text-white opacity-7 font-weight-bold">Dosen Pembimbing</p>
                                    <h4 class="mb-0 text-white">
                                        <i class="material-symbols-rounded text-success text-gradient me-1 align-middle">account_circle</i>
                                        <span class="align-middle text-break">{{ kelompok.dosen_dpl?.nama_dosen }}</span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4 mb-4">
                <div class="col-lg-12 col-md-12 mb-4">
                    <div class="card h-100">
                        <div class="card-header pb-0 p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Anggota Terdaftar 
                                    <span class="badge badge-sm bg-gradient-success border-0 ms-1 px-2 py-1">
                                        {{ anggota.length }} / 10
                                    </span>
                                </h6>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Mahasiswa</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status Kendaraan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-if="anggota.length > 0">
                                            <tr v-for="item in anggota" :key="item.id">
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div>
                                                            <div class="avatar avatar-sm me-3 bg-gradient-dark rounded-circle d-flex align-items-center justify-content-center">
                                                                <span class="text-white text-xs font-weight-bold">{{ getInitials(item.mahasiswa?.nama) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ item.mahasiswa?.nama }}</h6>
                                                            <p class="text-xs text-secondary mb-0">{{ item.mahasiswa?.nim }} • {{ item.mahasiswa?.prodi }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span v-if="item.mahasiswa?.punya_kendaraan === 'Punya'" class="badge badge-sm badge-success-soft text-success border border-success">
                                                        <i class="material-symbols-rounded text-xs align-middle me-1">two_wheeler</i> Ada
                                                    </span>
                                                    <span v-else class="badge badge-sm badge-danger-soft text-danger border border-danger">
                                                        <i class="material-symbols-rounded text-xs align-middle me-1">no_photography</i> Tidak Ada
                                                    </span>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr v-else>
                                            <td colspan="2" class="text-center text-secondary text-sm py-4">Belum ada anggota.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
.badge-success-soft {
    background-color: rgba(76, 175, 80, 0.1);
}
.badge-danger-soft {
    background-color: rgba(244, 67, 54, 0.1);
}
</style>