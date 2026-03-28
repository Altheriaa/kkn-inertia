<script setup>
import Layout from '../../Layouts/App.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import Swal from 'sweetalert2';

// Data Props From Controller
const props = defineProps({
    pendaftarans: Object,
    listJadwal: Array,
    listKkn: Array,
    filters: Object,
    errors: Object,
    countTeknik: Number,
    countPertanian: Number,
    countPerikanan: Number,
    countKedokteran: Number,
    countHukum: Number,
    countKesmas: Number,
    countFkip: Number,
    countEkonomi: Number,
    countTotal: Number,
    selectedJenisKkn: String,
});

// Search & Filter
const searchQuery = ref(props.filters?.search || '');
const statusJadwal = ref(props.filters?.statusJadwal || '');
const statusJenisKkn = ref(props.filters?.statusJenisKkn || '');
let searchTimeout = null;

const applyFilters = () => {
    router.get('/admin/rekapitulasi-mahasiswa', {
        search: searchQuery.value || undefined,
        statusJadwal: statusJadwal.value || undefined,
        statusJenisKkn: statusJenisKkn.value || undefined,
    }, { preserveState: true, replace: true });
};

watch(searchQuery, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});

watch([statusJadwal, statusJenisKkn], () => {
    applyFilters();
});

// Pagination window
const pageUrl = (page) => {
    const params = [`page=${page}`];
    if (searchQuery.value) params.push(`search=${searchQuery.value}`);
    if (statusJadwal.value) params.push(`statusJadwal=${statusJadwal.value}`);
    if (statusJenisKkn.value) params.push(`statusJenisKkn=${statusJenisKkn.value}`);
    return `/admin/rekapitulasi-mahasiswa?${params.join('&')}`;
};

const paginationPages = computed(() => {
    const current = props.pendaftarans?.current_page || 1;
    const last = props.pendaftarans?.last_page || 1;
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
                                    <i class="material-symbols-rounded text-white me-3" style="font-size: 28px;">clinical_notes</i>
                                    <div>
                                        <h4 class="text-white text-capitalize mb-0">Rekapitulasi Mahasiswa KKN</h4>
                                        <p class="text-white text-sm mb-0 opacity-8">Rekapitulasi data mahasiswa KKN</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="card-body pb-0 pt-3 px-4 mb-2">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                                
                                <a :href="`/admin/rekapitulasi-mahasiswa-cetak?statusJadwal=${statusJadwal}&statusJenisKkn=${statusJenisKkn}`" target="_blank" class="btn bg-gradient-dark mb-0 shadow-sm">
                                    <i class="material-symbols-rounded text-sm me-1">print</i> Cetak Laporan
                                </a>

                                <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                                    <!-- Filter Jenis KKN -->
                                    <div class="input-group input-group-outline" style="min-width: 150px;">
                                        <select class="form-control" v-model="statusJenisKkn">
                                            <option value="">Semua Jenis KKN</option>
                                            <option v-for="kkn in listKkn" :key="kkn.id" :value="kkn.id">
                                                {{ kkn.nama_jenis }}
                                            </option>
                                        </select>
                                    </div>
                                    <!-- Filter Jadwal -->
                                    <div class="input-group input-group-outline" style="min-width: 150px;">
                                        <select class="form-control" v-model="statusJadwal">
                                            <!-- <option value="">Semua Jadwal</option> -->
                                            <option v-for="jadwal in listJadwal" :key="jadwal.id" :value="jadwal.id">
                                                {{ jadwal.nama_periode }}
                                            </option>
                                        </select>
                                    </div>
                                    <!-- Search Bar -->
                                    <div class="input-group input-group-outline" :class="{ 'is-focused': false, 'is-filled': searchQuery }" style="min-width: 250px;">
                                        <label class="form-label" v-if="!searchQuery">Cari Mahasiswa...</label>
                                        <input type="text" class="form-control" v-model="searchQuery"
                                            @focus="$event.target.parentElement.classList.add('is-focused')"
                                            @blur="$event.target.parentElement.classList.remove('is-focused')">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body px-4 bg-gradient-dark m-3 border-radius-md">
                            <div class="row align-items-center">
                                <!-- Faculty List -->
                                <div class="col-lg-9 col-md-8">
                                    <h6 class="text-white text-xs text-uppercase opacity-7 mb-3">Rincian Mahasiswa Per Fakultas
                                    </h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge bg-gradient-primary p-2 mb-1">Fakultas Teknik : <span
                                                class="font-weight-bolder">{{ countTeknik }}</span></span>
                                        <span class="badge bg-gradient-primary p-2 mb-1">Fakultas Pertanian : <span
                                                class="font-weight-bolder">{{ countPertanian }}</span></span>
                                        <span class="badge bg-gradient-primary p-2 mb-1">Fakultas Perikanan : <span
                                                class="font-weight-bolder">{{ countPerikanan }}</span></span>
                                        <span class="badge bg-gradient-primary p-2 mb-1">Fakultas Kedokteran : <span
                                                class="font-weight-bolder">{{ countKedokteran }}</span></span>
                                        <span class="badge bg-gradient-primary p-2 mb-1">Fakultas Hukum : <span
                                                class="font-weight-bolder">{{ countHukum }}</span></span>
                                        <span class="badge bg-gradient-primary p-2 mb-1">Fakultas Kesehatan Masyarakat : <span
                                                class="font-weight-bolder">{{ countKesmas }}</span></span>
                                        <span class="badge bg-gradient-primary p-2 mb-1">Fakultas Keguruan dan Ilmu Pendidikan :
                                            <span class="font-weight-bolder">{{ countFkip }}</span></span>
                                        <span class="badge bg-gradient-primary p-2 mb-1">Fakultas Ekonomi : <span
                                                class="font-weight-bolder">{{ countEkonomi }}</span></span>
                                    </div>
                                </div>

                                <!-- Total Summary -->
                                <div class="col-lg-3 col-md-4 mt-4 mt-md-0 border-start border-secondary">
                                    <div class="text-center">
                                        <h6 class="text-white opacity-7 mb-0 text-sm">Total {{ selectedJenisKkn }}</h6>
                                        <h2 class="text-white font-weight-bolder mb-0">{{ countTotal }}</h2>
                                        <span class="badge badge-sm bg-success mt-2">Verified</span>
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
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">badge</i>
                                                NIM
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">person</i>
                                                Mahasiswa 
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">school</i>
                                                Fakultas
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">book</i>
                                                Prodi
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">group</i>
                                                L/P
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">phone</i>
                                                NO HP
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">work</i>
                                                Kelompok / Lokasi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Empty State -->
                                        <tr v-if="!pendaftarans || !pendaftarans.data || pendaftarans.data.length === 0">
                                            <td colspan="9" class="text-center py-5">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="material-symbols-rounded opacity-3 mb-2" style="font-size: 64px;">clinical_notes</i>
                                                    <h6 class="text-secondary mb-1">Belum Ada Data</h6>
                                                    <p class="text-xs text-secondary mb-3">Rekapitulasi KKN masih kosong.</p>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Data Rows -->
                                        <tr v-for="(pendaftaran, index) in pendaftarans.data" :key="pendaftaran.id" class="table-row-hover">
                                            <td class="ps-4">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ pendaftaran.mahasiswa?.nim }}</h6>
                                                </div>
                                            </td>
                                            <td class="ps-4">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ pendaftaran.mahasiswa?.nama }}</h6>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ pendaftaran.mahasiswa?.fakultas }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ pendaftaran.mahasiswa?.prodi }}</p>
                                            </td>
                                            <td>
                                                <span class="badge badge-sm" :class="pendaftaran.mahasiswa?.jenis_kelamin === 'L' ? 'bg-gradient-success' : 'bg-gradient-warning'">
                                                    {{ pendaftaran.mahasiswa?.jenis_kelamin }}
                                                </span>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ pendaftaran.mahasiswa?.no_hp }}</p>
                                            </td>
                                            <td>
                                                <span v-if="pendaftaran.kelompok_kkn" class="badge bg-gradient-success py-1 px-2">{{ pendaftaran.kelompok_kkn.nama_kelompok }}</span>
                                                <span v-else class="badge bg-gradient-danger py-1 px-2">Belum Ditempatkan</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination & Footer Info -->
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 gap-3" v-if="pendaftarans && pendaftarans.data && pendaftarans.data.length > 0">
                                <p class="text-xs text-secondary mb-0">
                                    <i class="material-symbols-rounded text-xs align-middle me-1">info</i>
                                    Menampilkan {{ pendaftarans.from }}-{{ pendaftarans.to }} dari {{ pendaftarans.total }} data.
                                </p>

                                <!-- Pagination Buttons -->
                                <nav v-if="pendaftarans.last_page > 1">
                                    <ul class="pagination pagination-sm mb-0">
                                        <!-- Previous -->
                                        <li class="page-item" :class="{ disabled: !pendaftarans.prev_page_url }">
                                            <Link :href="pendaftarans.prev_page_url || '#'" class="page-link" preserve-scroll>
                                                <i class="material-symbols-rounded text-sm">chevron_left</i>
                                            </Link>
                                        </li>

                                        <!-- Page Numbers (windowed) -->
                                        <template v-for="(pg, idx) in paginationPages" :key="idx">
                                            <li v-if="pg === '...'" class="page-item disabled">
                                                <span class="page-link">…</span>
                                            </li>
                                            <li v-else class="page-item" :class="{ active: pg === pendaftarans.current_page }">
                                                <Link :href="pageUrl(pg)" class="page-link" preserve-scroll>
                                                    {{ pg }}
                                                </Link>
                                            </li>
                                        </template>

                                        <!-- Next -->
                                        <li class="page-item" :class="{ disabled: !pendaftarans.next_page_url }">
                                            <Link :href="pendaftarans.next_page_url || '#'" class="page-link" preserve-scroll>
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