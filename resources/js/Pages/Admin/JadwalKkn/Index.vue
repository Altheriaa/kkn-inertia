<script setup>
import Layout from '../../../Layouts/App.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    jadwalkkn: Object,
    errors: Object
});

// SweetAlert2
const page = usePage();

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

// Sync Jadwal
const syncing = ref(false);

const syncJadwal = () => {
    syncing.value = true;
    router.post('/admin/sinkron-jadwal', {}, {
        preserveScroll: true,
        onFinish: () => {
            syncing.value = false;
        },
    });
};

// Format tanggal
const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};
</script>

<template>
    <Layout>
        <div class="container-fluid py-2 mb-5">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <!-- Floating Gradient Header -->
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 d-flex align-items-center justify-content-between px-4">
                                <div class="d-flex align-items-center">
                                    <i class="material-symbols-rounded text-white me-3" style="font-size: 28px;">calendar_month</i>
                                    <div>
                                        <h4 class="text-white text-capitalize mb-0">Jadwal KKN</h4>
                                        <p class="text-white text-sm mb-0 opacity-8">Sinkronisasi jadwal pendaftaran KKN Unaya</p>
                                    </div>
                                </div>
                                <span class="badge bg-white text-dark py-2 px-3 shadow-sm" v-if="jadwalkkn">
                                    <i class="material-symbols-rounded text-sm me-1 align-middle" :class="jadwalkkn.is_active ? 'text-success' : 'text-danger'">
                                        {{ jadwalkkn.is_active ? 'check_circle' : 'cancel' }}
                                    </i>
                                    {{ jadwalkkn.is_active ? 'Pendaftaran Dibuka' : 'Pendaftaran Ditutup' }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="card-body pb-0 pt-3 px-4">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                                <button type="button" class="btn bg-gradient-dark mb-0 shadow-sm"
                                    @click="syncJadwal" :disabled="syncing">
                                    <span v-if="syncing" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    <i v-else class="material-symbols-rounded text-sm me-1">sync</i>
                                    {{ syncing ? 'Syncing...' : 'Sync Jadwal' }}
                                </button>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">badge</i>
                                                Nama Periode
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">school</i>
                                                Tahun / Semester
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">date_range</i>
                                                Masa Pendaftaran
                                            </th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">toggle_on</i>
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Empty State -->
                                        <tr v-if="!jadwalkkn">
                                            <td colspan="4" class="text-center py-5">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="material-symbols-rounded opacity-3 mb-2" style="font-size: 64px;">event_busy</i>
                                                    <h6 class="text-secondary mb-1">Belum Ada Data</h6>
                                                    <p class="text-xs text-secondary mb-3">Jadwal KKN masih kosong. Klik tombol Sync untuk mengambil data dari Siakad.</p>
                                                    <button type="button" class="btn btn-sm bg-gradient-dark mb-0"
                                                        @click="syncJadwal" :disabled="syncing">
                                                        <i class="material-symbols-rounded text-sm me-1">sync</i> Sync Sekarang
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Data Row -->
                                        <tr v-if="jadwalkkn" class="table-row-hover">
                                            <td>
                                                <div class="d-flex px-3 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm font-weight-bold">{{ jadwalkkn.nama_periode }}</h6>
                                                        <p class="text-xs text-secondary mb-0">ID Siakad: {{ jadwalkkn.id_siakad }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ jadwalkkn.tahun_ajaran }}</p>
                                                <p class="text-xs text-secondary mb-0">{{ jadwalkkn.semester }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">
                                                    {{ formatDate(jadwalkkn.tanggal_dibuka) }}
                                                    <span class="text-xs text-secondary"> s/d </span>
                                                    {{ formatDate(jadwalkkn.tanggal_ditutup) }}
                                                </p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span v-if="jadwalkkn.is_active" class="badge bg-gradient-success py-1 px-3">
                                                    <i class="material-symbols-rounded text-xs me-1 align-middle">check_circle</i>
                                                    Sedang Dibuka
                                                </span>
                                                <span v-else class="badge bg-gradient-danger py-1 px-3">
                                                    <i class="material-symbols-rounded text-xs me-1 align-middle">cancel</i>
                                                    Tutup
                                                </span>
                                            </td>
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
.table-row-hover:hover {
    background-color: rgba(0, 0, 0, 0.02);
    transition: background-color 0.2s ease;
}

.icon-shape {
    width: 28px;
    height: 28px;
    min-width: 28px;
}
</style>