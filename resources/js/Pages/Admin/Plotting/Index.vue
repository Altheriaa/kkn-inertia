<script setup>
import Layout from '../../../Layouts/App.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import Swal from 'sweetalert2';

// Data Props From Controller
const props = defineProps({
    kelompoks: Object,
    listJadwal: Array,
    filters: Object,
    errors : Object
});

// Search & Filter
const searchQuery = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');
let searchTimeout = null;

const applyFilters = () => {
    router.get('/admin/plotting', {
        search: searchQuery.value || undefined,
        status: statusFilter.value || undefined,
    }, { preserveState: true, replace: true });
};

watch(searchQuery, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});

watch(statusFilter, () => {
    applyFilters();
});

// Progress Kuota (Maksimal per kelompok)
const maxKuota = 10;

const getProgressData = (pendaftaranCount) => {
    const count = pendaftaranCount ?? 0;
    const percentage = Math.min((count / maxKuota) * 100, 100);
    
    let color = 'success';
    if (percentage >= 100) {
        color = 'danger';
    } else if (percentage >= 75) {
        color = 'warning';
    }

    return { count, percentage, color };
};

// Pagination window
const pageUrl = (page) => {
    const params = [`page=${page}`];
    if (searchQuery.value) params.push(`search=${searchQuery.value}`);
    if (statusFilter.value) params.push(`status=${statusFilter.value}`);
    return `/admin/plotting?${params.join('&')}`;
};

const paginationPages = computed(() => {
    const current = props.kelompoks?.current_page || 1;
    const last = props.kelompoks?.last_page || 1;
    const pages = [];

    if (last <= 7) {
        for (let i = 1; i <= last; i++) pages.push(i);
        return pages;
    }

    pages.push(1);

    if (current > 3) pages.push('...');

    const start = Math.max(2, current - 1);
    const end = Math.min(last - 1, current + 1);
    for (let i = start; i <= end; i++) pages.push(i);

    if (current < last - 2) pages.push('...');

    pages.push(last);

    return pages;
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

// Confirmation Alert
const confirmDelete = (kelompok) => {
    Swal.fire({
        title: 'Hapus Data?',
        text: `Data "${kelompok.nama_kelompok}" akan dihapus permanen!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="material-symbols-rounded text-sm me-1">delete</i> Ya, Hapus!',
        cancelButtonText: '<i class="material-symbols-rounded text-sm me-1">close</i> Batal',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#344767',
        reverseButtons: true,
        customClass: {
            popup: 'glass-popup rounded-3xl shadow-blur p-6',
            title: 'font-weight-bold',
            confirmButton: 'px-4 py-2 rounded-xl',
            cancelButton: 'px-4 py-2 rounded-xl',
        }
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/admin/plotting/${kelompok.id}`);
        }
    });
};

onMounted(() => {
    showFlashMessage();
});

watch(() => page.props.flash, () => {
    showFlashMessage();
}, { deep: true });

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
                                    <i class="material-symbols-rounded text-white me-3" style="font-size: 28px;">groups</i>
                                    <div>
                                        <h4 class="text-white text-capitalize mb-0">Penempatan Mahasiswa KKN</h4>
                                        <p class="text-white text-sm mb-0 opacity-8">Kelola data penempatan mahasiswa KKN</p>
                                    </div>
                                </div>
                                <span class="badge bg-white text-dark py-2 px-3 shadow-sm">
                                    <i class="material-symbols-rounded text-sm me-1 align-middle">database</i>
                                    {{ kelompoks?.total || 0 }} Data
                                </span>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="card-body pb-0 pt-3 px-4">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                                
                                <Link href="/admin/plotting/create" class="btn bg-gradient-dark mb-0 shadow-sm">
                                    <i class="material-symbols-rounded text-sm me-1">person_add</i> Tambah Kelompok
                                </Link>

                                <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                                    <!-- Filter Jadwal -->
                                    <div class="input-group input-group-outline" style="min-width: 150px;">
                                        <select class="form-control" v-model="statusFilter">
                                            <option v-for="jadwal in listJadwal" :key="jadwal.id" :value="jadwal.id">
                                                {{ jadwal.nama_periode }}
                                            </option>
                                        </select>
                                    </div>
                                    <!-- Search Bar -->
                                    <div class="input-group input-group-outline" :class="{ 'is-focused': false, 'is-filled': searchQuery }" style="min-width: 250px;">
                                        <label class="form-label">Cari Kelompok...</label>
                                        <input type="text" class="form-control" v-model="searchQuery"
                                            @focus="$event.target.parentElement.classList.add('is-focused')"
                                            @blur="$event.target.parentElement.classList.remove('is-focused')">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">list</i>
                                                NO
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">badge</i>
                                                Jadwal KKN
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">person</i>
                                                DPL
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">location_on</i>
                                                Lokasi KKN
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">group</i>
                                                Nama Kelompok
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">work</i>
                                                Jenis KKN
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">badge</i>
                                                Kuota
                                            </th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">settings</i>
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Empty State -->
                                        <tr v-if="!kelompoks || !kelompoks.data || kelompoks.data.length === 0">
                                            <td colspan="9" class="text-center py-5">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="material-symbols-rounded opacity-3 mb-2" style="font-size: 64px;">add_location_alt</i>
                                                    <h6 class="text-secondary mb-1">Belum Ada Data</h6>
                                                    <p class="text-xs text-secondary mb-3">Data lokasi KKN masih kosong.</p>
                                                    <Link href="/admin/plotting/create" class="btn btn-sm bg-gradient-dark mb-0">
                                                        <i class="material-symbols-rounded text-sm me-1">add</i> Tambah Lokasi KKN 
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Data Rows -->
                                        <tr v-for="(kelompok, index) in kelompoks.data" :key="kelompok.id"
                                            class="table-row-hover">
                                            <td>
                                                <div class="d-flex px-3 py-1">
                                                    <div class="icon-shape icon-sm bg-gradient-dark shadow text-center border-radius-md d-flex align-items-center justify-content-center me-2">
                                                        <span class="text-white text-xs font-weight-bold">{{ (kelompoks.current_page - 1) * kelompoks.per_page + index + 1 }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-3 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm font-weight-bold">{{ kelompok.jadwal_kkn.nama_periode }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ kelompok.dosen_dpl.nama_dosen }}</p>
                                            </td>
                                            <td>
                                                <span class="badge bg-gradient-success py-1 px-3">{{ kelompok.lokasi_kkn.nama_desa }}</span>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ kelompok.nama_kelompok }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ kelompok.jenis_kkn }}</p>
                                            </td>
                                            <td>
                                                <div class="align-middle text-center text-sm">
                                                    <div class="d-flex align-items-center justify-content-center" v-with="progress = getProgressData(kelompok.pendaftaran_kkn_count)">
                                                        <span class="me-2 text-xs font-weight-bold">{{ getProgressData(kelompok.pendaftaran_kkn_count).count }} / {{ maxKuota }}</span>
                                                        <div>
                                                            <div class="progress" style="width: 100px; height: 6px;">
                                                                <div class="progress-bar" 
                                                                    :class="`bg-gradient-${getProgressData(kelompok.pendaftaran_kkn_count).color}`"
                                                                    role="progressbar"
                                                                    :aria-valuenow="getProgressData(kelompok.pendaftaran_kkn_count).percentage" 
                                                                    aria-valuemin="0" 
                                                                    aria-valuemax="100"
                                                                    :style="{ width: getProgressData(kelompok.pendaftaran_kkn_count).percentage + '%' }">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <!-- Aksi -->
                                            <td class="align-middle text-center">
                                                <Link :href="`/admin/plotting/${kelompok.id}/kelola-anggota`"
                                                    class="btn btn-link text-success mb-0 px-2 py-1"
                                                    title="Kelola Anggota">
                                                    <i class="material-symbols-rounded text-lg">group</i>
                                                </Link>
                                                <Link :href="`/admin/plotting/${kelompok.id}/edit`"
                                                    class="btn btn-link text-warning mb-0 px-2 py-1"
                                                    title="Edit Data">
                                                    <i class="material-symbols-rounded text-lg">edit</i>
                                                </Link>
                                                <button type="button"
                                                    class="btn btn-link text-danger mb-0 px-2 py-1"
                                                    title="Hapus Data"
                                                    @click="confirmDelete(kelompok)">
                                                    <i class="material-symbols-rounded text-lg">delete</i>
                                                </button>
                                                <a :href="`/admin/plotting/${kelompok.id}/cetak-kelompok`"
                                                    target="_blank"
                                                    class="btn btn-link text-success mb-0 px-2 py-1"
                                                    title="Cetak Kelompok">
                                                    <i class="material-symbols-rounded text-lg">print</i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination & Footer Info -->
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 gap-3" v-if="kelompoks && kelompoks.data && kelompoks.data.length > 0">
                                <p class="text-xs text-secondary mb-0">
                                    <i class="material-symbols-rounded text-xs align-middle me-1">info</i>
                                    Menampilkan {{ kelompoks.from }}-{{ kelompoks.to }} dari {{ kelompoks.total }} data.
                                </p>

                                <!-- Pagination Buttons -->
                                <nav v-if="kelompoks.last_page > 1">
                                    <ul class="pagination pagination-sm mb-0">
                                        <!-- Previous -->
                                        <li class="page-item" :class="{ disabled: !kelompoks.prev_page_url }">
                                            <Link :href="kelompoks.prev_page_url || '#'" class="page-link" preserve-scroll>
                                                <i class="material-symbols-rounded text-sm">chevron_left</i>
                                            </Link>
                                        </li>

                                        <!-- Page Numbers (windowed) -->
                                        <template v-for="(pg, idx) in paginationPages" :key="idx">
                                            <li v-if="pg === '...'" class="page-item disabled">
                                                <span class="page-link">…</span>
                                            </li>
                                            <li v-else class="page-item" :class="{ active: pg === kelompoks.current_page }">
                                                <Link :href="pageUrl(pg)" class="page-link" preserve-scroll>
                                                    {{ pg }}
                                                </Link>
                                            </li>
                                        </template>

                                        <!-- Next -->
                                        <li class="page-item" :class="{ disabled: !kelompoks.next_page_url }">
                                            <Link :href="kelompoks.next_page_url || '#'" class="page-link" preserve-scroll>
                                                <i class="material-symbols-rounded text-sm">chevron_right</i>
                                            </Link>
                                        </li>
                                    </ul>
                                </nav>
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

.btn-link:hover {
    transform: scale(1.15);
    transition: transform 0.2s ease;
}

.pagination .page-item .page-link {
    border: none;
    color: #344767;
    font-size: 0.8rem;
    font-weight: 600;
    min-width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    margin: 0 2px;
    transition: all 0.2s ease;
}

.pagination .page-item .page-link:hover {
    background-color: #e9ecef;
    color: #344767;
}

.pagination .page-item.active .page-link {
    background: linear-gradient(195deg, #42424a 0%, #191919 100%);
    color: #fff;
    box-shadow: 0 3px 5px -1px rgba(0,0,0,.09), 0 2px 3px -1px rgba(0,0,0,.07);
}

.pagination .page-item.disabled .page-link {
    opacity: 0.4;
    pointer-events: none;
}
</style>