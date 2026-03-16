<script setup>
import Layout from '../../../Layouts/App.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    kelompok: Object,
    kandidat: Array,
    anggota: Array,
});

// Search 
const searchQuery = ref(props.filters?.search || '');
let searchTimeout = null;

const applyFilters = () => {
    router.get(`/admin/plotting/${props.kelompok.id}/kelola-anggota`, {
        search: searchQuery.value || undefined,
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

// Modal Detail mahasiswa
const isModalOpen = ref(false);
const selectedKandidat = ref(null);

// Fungsi pemicu saat tombol + diklik
const openAddModal = (kandidat) => {
    selectedKandidat.value = kandidat;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    selectedKandidat.value = null;
};

</script>

<template>
    <Layout>
        <div class="container-fluid py-2">
        <!-- Header & Navigasi -->
        <div class="row">
            <div class="col-12">
                <div class="mb-1 mt-2">
                    <Link href="/admin/plotting" class="btn btn-outline-dark">
                        <i class="material-symbols-rounded text-sm me-1 align-middle">arrow_back</i> Kembali
                    </Link>
                </div>
                <!-- {{-- Info Kelompok --}} -->
                <div class="card">
                    <div class="card-header ps-3 bg-gradient-dark shadow-dark border-radius-lg">
                        <div
                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 p-2">
                            <div class="text-start mb-2">
                                <p class="text-sm mb-0 text-capitalize text-white opacity-7">Nama Kelompok</p>
                                <h4 class="mb-0 text-white">{{ kelompok.nama_kelompok }}</h4>
                                <span
                                    class="badge badge-sm bg-gradient-primary border-0 mt-2 text-white">{{ kelompok.jadwal_kkn.tahun_ajaran }}</span>
                                |
                                <span
                                    class="badge badge-sm bg-gradient-secondary border-0 mt-2 text-white">{{ kelompok.jenis_kkn }}</span>
                                |
                                <span class="font-weight-bold text-light"><i
                                        class="material-symbols-rounded text-success text-gradient me-1 align-middle">location_on</i>{{ kelompok.lokasi_kkn.nama_desa }}</span>
                            </div>
                            <div class="text-start text-md-end rounded-3 bg-gradient-secondary px-3 py-2">
                                <p class="text-sm mb-0 text-capitalize text-white opacity-7 font-weight-bold">Dosen
                                    Pembimbing</p>
                                <h4 class="mb-0 text-white">
                                    <i
                                        class="material-symbols-rounded text-success text-gradient me-1 align-middle">account_circle</i>
                                    <span
                                        class="align-middle text-break">{{ kelompok.dosen_dpl.nama_dosen }}</span>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4 mb-4">

            <!-- {{-- KOLOM KIRI: Anggota Terdaftar (FORM UTAMA) --}} -->
            <div class="col-lg-7 col-md-12 mb-4">

                <!-- {{-- Pastikan route ini ada di web.php: Route::put('/plotting/{id}/sync', ...)->name('admin.plotting.sync')
                --}} -->
                <form action="{{ route('syncAnggota', $kelompok->id) }}" method="POST" id="formBatchUpdate">
                    <div class="card h-100">
                        <div class="card-header pb-0 p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Anggota Terdaftar <span id="counter-anggota"
                                        class="badge badge-sm bg-gradient-success border-0 ms-1 px-2 py-1">{{ anggota?.total || 0 }}
                                        / 10</span>
                                </h6>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0" id="table-anggota">
                                    <thead>
                                        <tr>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Mahasiswa</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Status Kendaraan</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <!-- <tbody>
                                        @forelse($anggota as $item)
                                            <tr data-id="{{ $item->id }}">
                                                {{-- INPUT HIDDEN UTAMA (Ini yang dibaca Controller) --}}
                                                {{-- Name harus 'anggota_ids[]' sesuai request controller --}}
                                                <input type="hidden" name="anggota_ids[]" value="{{ $item->id }}">

                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div>
                                                            <div class="avatar avatar-sm me-3 bg-gradient-dark rounded-circle">
                                                                <span
                                                                    class="text-white text-xs font-weight-bold">{{ substr($item->mahasiswa->nama, 0, 2) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $item->mahasiswa->nama }}</h6>
                                                            <p class="text-xs text-secondary mb-0">{{ $item->mahasiswa->nim }} •
                                                                {{ $item->mahasiswa->prodi }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    @if($item->mahasiswa->punya_kendaraan === 'Punya')
                                                        <span
                                                            class="badge badge-sm badge-success-soft text-success border border-success">
                                                            <i
                                                                class="material-symbols-rounded text-xs align-middle me-1">two_wheeler</i>
                                                            Ada
                                                        </span>
                                                    @else
                                                        <span
                                                            class="badge badge-sm badge-danger-soft text-danger border border-danger">
                                                            <i
                                                                class="material-symbols-rounded text-xs align-middle me-1">no_photography</i>
                                                            Tidak Ada
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="align-middle text-center">
                                                    {{-- Tombol KICK (Hanya hapus baris via JS, tidak reload) --}}
                                                    <button type="button"
                                                        class="btn btn-link text-danger text-gradient p-0 mb-0 btn-kick">
                                                        <i class="material-symbols-rounded text-lg">delete</i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr id="empty-row">
                                                <td colspan="3" class="text-center text-secondary text-sm py-4">Belum ada
                                                    anggota.</td>
                                            </tr>
                                        @endforelse
                                    </tbody> -->
                                </table>
                            </div>
                        </div>
                        <div class="card-footer p-3 d-flex justify-content-end">
                            <!-- {{-- Tombol Submit Form Utama --}} -->
                            <button type="submit" class="btn bg-gradient-success mb-0" id="btn-save">
                                <i class="material-symbols-rounded text-sm me-1">save</i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- {{-- KOLOM KANAN: List Kandidat --}} -->
            <div class="col-lg-5 col-md-12">
                <div class="card h-100">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-1">Tambah Anggota</h6>
                        <p class="text-sm mb-0 text-secondary">Cari mahasiswa yang belum memiliki kelompok.</p>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3 input-group input-group-outline" :class="{ 'is-focused': false, 'is-filled': searchQuery }">
                            <label class="form-label">
                                Cari Nama / NIM / Fakultas / Prodi...
                            </label>
                            <input type="text" class="form-control" v-model="searchQuery"
                                @focus="$event.target.parentElement.classList.add('is-focused')"
                                @blur="$event.target.parentElement.classList.remove('is-focused')">
                        </div>

                        <div style="max-height: 400px; overflow-y: auto;" class="pe-2">
                            <ul class="table-body list-group" id="list-kandidat">
                                <li v-for="k in kandidat" class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg item-kandidat"
                                    id="kandidat-{{ k.id }}">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3 bg-gradient-success rounded-circle">
                                            <span class="text-white text-xs font-weight-bold">{{ k.mahasiswa.nama.substring(0, 2).toUpperCase() }}</span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">{{ k.mahasiswa.nama }}</h6>
                                            <span class="text-xs">{{ k.mahasiswa.nim }} - {{ k.mahasiswa.jenis_kelamin }}</span>
                                            <span class="text-xs text-success font-weight-bold">{{ k.mahasiswa.prodi }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <!-- Tombol Trigger Modal -->
                                        <button class="btn btn-rounded btn-outline-secondary mb-0 btn-detail-mahasiswa" @click="openAddModal(k)">
                                            <i class="material-symbols-rounded text-lg">add</i>
                                        </button>
                                    </div>
                                </li>
                                <li v-if="kandidat.length === 0" class="list-group-item border-0 text-center text-secondary text-sm">Tidak ada kandidat
                                    ditemukan.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Detail & Add (Client Side Only) -->
    <div v-if="isModalOpen" class="modal fade show" tabindex="-1" role="dialog" aria-hidden="true" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-normal">Detail Mahasiswa</h5>
                    <button type="button" class="btn-close text-dark" @click="isModalOpen = false" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- <input type="hidden" id="temp_id">
                    <input type="hidden" id="temp_kendaraan"> -->

                    <div class="text-center mb-4">
                        <div class="avatar avatar-xl bg-gradient-primary rounded-circle mb-2">
                            <span class="text-white text-lg font-weight-bold">{{ selectedKandidat?.mahasiswa.nama.substring(0, 2).toUpperCase() }}</span>
                        </div>
                        <h5 class="mb-0">{{ selectedKandidat?.mahasiswa.nama }}</h5>
                        <p class="text-sm text-secondary mb-0" id="detailNim">{{ selectedKandidat?.mahasiswa.nim }}</p>
                        <span class="badge badge-sm bg-gradient-success mt-2">{{ selectedKandidat?.mahasiswa.prodi }}</span>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <p class="text-xs font-weight-bold text-secondary mb-0">Fakultas</p>
                            <p class="text-sm font-weight-bold text-dark">{{ selectedKandidat?.mahasiswa.fakultas }}</p>
                        </div>
                        <div class="col-6">
                            <p class="text-xs font-weight-bold text-secondary mb-0">Jenis Kelamin</p>
                            <p v-if="selectedKandidat?.mahasiswa.jenis_kelamin === 'P'" class="text-sm font-weight-bold text-dark">Perempuan</p>
                            <p v-else-if="selectedKandidat?.mahasiswa.jenis_kelamin === 'L'" class="text-sm font-weight-bold text-dark">Laki-laki</p>
                        </div>
                        <div class="col-6">
                            <p class="text-xs font-weight-bold text-secondary mb-0">No. HP</p>
                            <p class="text-sm font-weight-bold text-dark">{{ selectedKandidat?.mahasiswa.no_hp }}</p>
                        </div>
                        <div class="col-6">
                            <p class="text-xs font-weight-bold text-secondary mb-0">Keahlian</p>
                            <p class="text-sm font-weight-bold text-dark">{{ selectedKandidat?.mahasiswa.keahlian }}</p>
                        </div>
                        <div class="col-6">
                            <p class="text-xs font-weight-bold text-secondary mb-0">Ukuran Jacket/Rompi</p>
                            <p class="text-sm font-weight-bold text-dark">{{ selectedKandidat?.mahasiswa.ukuran_jacket_rompi }}</p>
                        </div>
                        <div class="col-6">
                            <p class="text-xs font-weight-bold text-secondary mb-0">Punya Kendaraan</p>
                            <p v-if="selectedKandidat?.mahasiswa.punya_kendaraan === 'Punya'" class="text-sm font-weight-bold text-dark">Ya</p>
                            <p v-else-if="selectedKandidat?.mahasiswa.punya_kendaraan === 'Tidak'" class="text-sm font-weight-bold text-dark">Tidak Ada</p>
                        </div>
                        <div class="col-6">
                            <p class="text-xs font-weight-bold text-secondary mb-0">Tipe Kendaraan</p>
                            <p class="text-sm font-weight-bold text-dark">{{ selectedKandidat?.mahasiswa.tipe_kendaraan }}</p>
                        </div>
                        <div class="col-6">
                            <p class="text-xs font-weight-bold text-secondary mb-0">Punya Lisensi</p>
                            <p class="text-sm font-weight-bold text-dark">{{ selectedKandidat?.mahasiswa.punya_lisensi }}</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="isModalOpen = false" class="btn bg-gradient-secondary">Batal</button>
                    <!-- Type Button (Hanya trigger JS, bukan submit form modal) -->
                    <button type="button" class="btn bg-gradient-primary" id="btn-confirm-add">Tambahkan ke
                        Kelompok</button>
                </div>
            </div>
        </div>
    </div>
    </Layout>
</template>