<script setup>
import Layout from '../../../Layouts/App.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import Swal from 'sweetalert2';

// Data Props From Controller
const props = defineProps({
    mahasiswas: Object,
    filters: Object,
    errors : Object
});

// Search & Filter
const searchQuery = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');
let searchTimeout = null;

const applyFilters = () => {
    router.get('/admin/mahasiswa', {
        search: searchQuery.value || undefined,
        status: statusFilter.value || undefined,
    }, { preserveState: true, replace: true });
};

watch(searchQuery, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});

const setFilter = (status) => {
    statusFilter.value = statusFilter.value === status ? '' : status;
    applyFilters();
};

// Pagination window
const pageUrl = (page) => {
    const params = [`page=${page}`];
    if (searchQuery.value) params.push(`search=${searchQuery.value}`);
    if (statusFilter.value) params.push(`status=${statusFilter.value}`);
    return `/admin/mahasiswa?${params.join('&')}`;
};

const paginationPages = computed(() => {
    const current = props.mahasiswas?.current_page || 1;
    const last = props.mahasiswas?.last_page || 1;
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
                                        <h4 class="text-white text-capitalize mb-0">Mahasiswa</h4>
                                        <p class="text-white text-sm mb-0 opacity-8">Data mahasiswa KKN</p>
                                    </div>
                                </div>
                                <span class="badge bg-white text-dark py-2 px-3 shadow-sm">
                                    <i class="material-symbols-rounded text-sm me-1 align-middle">database</i>
                                    {{ mahasiswas?.total || 0 }} Data
                                </span>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="card-body pb-0 pt-3 px-4">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                                <!-- Filter Buttons -->
                                <div class="d-flex gap-2 align-items-center">
                                    <span class="text-xs text-secondary font-weight-bold me-1">
                                        <i class="material-symbols-rounded text-xs align-middle">filter_list</i> Filter:
                                    </span>
                                    <button class="btn btn-sm mb-0 py-1 px-3"
                                        :class="statusFilter === '' ? 'bg-gradient-dark text-white' : 'btn-outline-dark'"
                                        @click="setFilter('')">
                                        Semua
                                    </button>
                                    <button class="btn btn-sm mb-0 py-1 px-3"
                                        :class="statusFilter === 'Sudah Daftar' ? 'bg-gradient-success text-white' : 'btn-outline-success'"
                                        @click="setFilter('Sudah Daftar')">
                                        <i class="material-symbols-rounded text-xs me-1 align-middle">check_circle</i>
                                        Sudah Daftar
                                    </button>
                                    <button class="btn btn-sm mb-0 py-1 px-3"
                                        :class="statusFilter === 'Belum Daftar' ? 'bg-gradient-secondary text-white' : 'btn-outline-secondary'"
                                        @click="setFilter('Belum Daftar')">
                                        <i class="material-symbols-rounded text-xs me-1 align-middle">pending</i>
                                        Belum Daftar
                                    </button>
                                </div>
                                <!-- Search Bar -->
                                <div class="input-group input-group-outline" :class="{ 'is-focused': false, 'is-filled': searchQuery }" style="max-width: 300px;">
                                    <label class="form-label">
                                        Cari Mahasiswa...
                                    </label>
                                    <input type="text" class="form-control" v-model="searchQuery"
                                        @focus="$event.target.parentElement.classList.add('is-focused')"
                                        @blur="$event.target.parentElement.classList.remove('is-focused')">
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
                                                NIM
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">person</i>
                                                Nama 
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">email</i>
                                                Email
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">badge</i>
                                                Status KKN
                                            </th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">settings</i>
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Empty State -->
                                        <tr v-if="!mahasiswas || !mahasiswas.data || mahasiswas.data.length === 0">
                                            <td colspan="6" class="text-center py-5">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="material-symbols-rounded opacity-3 mb-2" style="font-size: 64px;">group_off</i>
                                                    <h6 class="text-secondary mb-1">Belum Ada Data</h6>
                                                    <p class="text-xs text-secondary mb-3">Data mahasiswa masih kosong.</p>
                                                    <Link href="/admin/mahasiswa/sync" class="btn btn-sm bg-gradient-dark mb-0">
                                                        <i class="material-symbols-rounded text-sm me-1">sync</i> Sync Mahasiswa
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Data Rows -->
                                        <tr v-for="(mhs, index) in mahasiswas.data" :key="mhs.id"
                                            class="table-row-hover">
                                             <td>
                                                <div class="d-flex px-3 py-1">
                                                    <div class="icon-shape icon-sm bg-gradient-dark shadow text-center border-radius-md d-flex align-items-center justify-content-center me-2">
                                                        <span class="text-white text-xs font-weight-bold">{{ (mahasiswas.current_page - 1) * mahasiswas.per_page + index + 1 }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-3 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm font-weight-bold">{{ mhs.nim }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ mhs.nama }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ mhs.email }}</p>
                                            </td>
                                            <td>
                                                <span class="badge bg-gradient-success py-1 px-3">{{ mhs.status_kkn }}</span>
                                            </td>
                                            <!-- Aksi -->
                                            <td class="align-middle text-center">
                                                <Link :href="`/admin/mahasiswa/${mhs.id}`"
                                                    class="btn btn-link text-dark mb-0 px-2 py-1"
                                                    title="Lihat Detail">
                                                    <i class="material-symbols-rounded text-lg">visibility</i>
                                                </Link>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination & Footer Info -->
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 gap-3" v-if="mahasiswas && mahasiswas.data && mahasiswas.data.length > 0">
                                <p class="text-xs text-secondary mb-0">
                                    <i class="material-symbols-rounded text-xs align-middle me-1">info</i>
                                    Menampilkan {{ mahasiswas.from }}-{{ mahasiswas.to }} dari {{ mahasiswas.total }} data.
                                </p>

                                <!-- Pagination Buttons -->
                                <nav v-if="mahasiswas.last_page > 1">
                                    <ul class="pagination pagination-sm mb-0">
                                        <!-- Previous -->
                                        <li class="page-item" :class="{ disabled: !mahasiswas.prev_page_url }">
                                            <Link :href="mahasiswas.prev_page_url || '#'" class="page-link" preserve-scroll>
                                                <i class="material-symbols-rounded text-sm">chevron_left</i>
                                            </Link>
                                        </li>

                                        <!-- Page Numbers (windowed) -->
                                        <template v-for="(pg, idx) in paginationPages" :key="idx">
                                            <li v-if="pg === '...'" class="page-item disabled">
                                                <span class="page-link">…</span>
                                            </li>
                                            <li v-else class="page-item" :class="{ active: pg === mahasiswas.current_page }">
                                                <Link :href="pageUrl(pg)" class="page-link" preserve-scroll>
                                                    {{ pg }}
                                                </Link>
                                            </li>
                                        </template>

                                        <!-- Next -->
                                        <li class="page-item" :class="{ disabled: !mahasiswas.next_page_url }">
                                            <Link :href="mahasiswas.next_page_url || '#'" class="page-link" preserve-scroll>
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