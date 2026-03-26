<script setup>
import Layout from '../../../Layouts/App.vue';
import { Link } from '@inertiajs/vue3';
import { computed, watch, onMounted, ref } from 'vue';

const props = defineProps({
    mahasiswa: Object
});

const avatarUrl = computed(() => {
    const name = encodeURIComponent(props.mahasiswa.nama || 'M');
    return `https://ui-avatars.com/api/?name=${name}&background=344767&color=fff&size=128&bold=true`;
});

const genderLabel = computed(() => {
    if (props.mahasiswa.jenis_kelamin === 'L') return 'Laki-laki';
    if (props.mahasiswa.jenis_kelamin === 'P') return 'Perempuan';
    return '-';
});

const genderIcon = computed(() => {
    if (props.mahasiswa.jenis_kelamin === 'L') return 'male';
    if (props.mahasiswa.jenis_kelamin === 'P') return 'female';
    return 'help';
});

const previewUrl = ref(props.mahasiswa.foto_ktp ? `/storage/${props.mahasiswa.foto_ktp}` : null);

// Modal ktp
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
        <div class="container-fluid px-2 px-md-4">
            <!-- Hero Banner -->
            <div class="page-header min-height-200 border-radius-xl mt-4"
                style="background-image: url('/assets/img/Biro Abulyatama.webp'); background-size: cover; background-position: center;">
                <span class="mask bg-gradient-dark opacity-6"></span>
            </div>

            <!-- Profile Card -->
            <div class="card card-body mx-2 mx-md-2 mt-n6">
                <div class="row gx-4 mb-2">
                    <!-- Avatar -->
                    <div class="col-auto">
                        <div class="avatar avatar-xl position-relative">
                            <img :src="avatarUrl"
                                alt="profile_image" class="w-100 border-radius-lg shadow-sm">
                        </div>
                    </div>
                    <!-- Name & NIM -->
                    <div class="col-auto my-auto">
                        <div class="h-100">
                            <h5 class="mb-1">{{ mahasiswa.nama }}</h5>
                            <p class="mb-0 font-weight-normal text-sm">
                                <i class="material-symbols-rounded text-xs align-middle me-1">badge</i>
                                {{ mahasiswa.nim }}
                            </p>
                        </div>
                    </div>
                    <!-- Status & Back Button -->
                    <div class="col-lg-4 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <span class="badge py-2 px-3" :class="mahasiswa.status_kkn === 'Sudah Daftar' ? 'bg-gradient-success' : 'bg-gradient-secondary'">
                                <i class="material-symbols-rounded text-xs me-1 align-middle">{{ mahasiswa.status_kkn === 'Sudah Daftar' ? 'check_circle' : 'pending' }}</i>
                                {{ mahasiswa.status_kkn || 'Belum Daftar' }}
                            </span>
                            <Link href="/admin/mahasiswa" class="btn btn-sm btn-outline-dark mb-0">
                                <i class="material-symbols-rounded text-sm me-1">arrow_back</i> Kembali
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Info Cards -->
                <div class="row mt-4">
                    <!-- Card 1: Informasi Akademik & Pribadi -->
                    <div class="col-12 col-xl-6 mb-4 mb-xl-0">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header p-3 bg-gradient-dark shadow-dark border-radius-lg mx-3 mt-n3 z-index-2">
                                <h6 class="mb-0 text-white">
                                    <i class="material-symbols-rounded text-sm me-2 align-middle">school</i>
                                    Informasi Akademik & Pribadi
                                </h6>
                            </div>
                            <div class="card-body p-0 mt-2">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item px-4 py-3 border-0">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="material-symbols-rounded text-dark text-sm me-2">person</i>
                                            <small class="text-muted text-uppercase fw-bold">Nama Lengkap</small>
                                        </div>
                                        <div class="ms-4 text-dark fw-bold">{{ mahasiswa.nama }}</div>
                                    </div>

                                    <div class="list-group-item px-4 py-3 border-0 bg-light">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="material-symbols-rounded text-dark text-sm me-2">badge</i>
                                            <small class="text-muted text-uppercase fw-bold">NIM</small>
                                        </div>
                                        <div class="ms-4 text-dark">{{ mahasiswa.nim }}</div>
                                    </div>

                                    <div class="list-group-item px-4 py-3 border-0">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="material-symbols-rounded text-dark text-sm me-2">apartment</i>
                                            <small class="text-muted text-uppercase fw-bold">Fakultas</small>
                                        </div>
                                        <div class="ms-4 text-dark">{{ mahasiswa.fakultas || '-' }}</div>
                                    </div>

                                    <div class="list-group-item px-4 py-3 border-0 bg-light">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="material-symbols-rounded text-dark text-sm me-2">school</i>
                                            <small class="text-muted text-uppercase fw-bold">Program Studi</small>
                                        </div>
                                        <div class="ms-4 text-dark">{{ mahasiswa.prodi || '-' }}</div>
                                    </div>

                                    <div class="list-group-item px-4 py-3 border-0">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="material-symbols-rounded text-dark text-sm me-2">mail</i>
                                            <small class="text-muted text-uppercase fw-bold">Email</small>
                                        </div>
                                        <div class="ms-4 text-dark">{{ mahasiswa.email || '-' }}</div>
                                    </div>

                                    <div class="list-group-item px-4 py-3 border-0 bg-light">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="material-symbols-rounded text-dark text-sm me-2">call</i>
                                            <small class="text-muted text-uppercase fw-bold">No. HP</small>
                                        </div>
                                        <div class="ms-4 text-dark">{{ mahasiswa.no_hp || '-' }}</div>
                                    </div>

                                    <div class="list-group-item px-4 py-3 border-0">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="material-symbols-rounded text-dark text-sm me-2">contact_phone</i>
                                            <small class="text-muted text-uppercase fw-bold">No. HP Wali</small>
                                        </div>
                                        <div class="ms-4 text-dark">{{ mahasiswa.no_hp_wali || '-' }}</div>
                                    </div>

                                    <div class="list-group-item px-4 py-3 border-0 bg-light">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="material-symbols-rounded text-dark text-sm me-2">{{ genderIcon }}</i>
                                            <small class="text-muted text-uppercase fw-bold">Jenis Kelamin</small>
                                        </div>
                                        <div class="ms-4">
                                            <span class="badge bg-gradient-dark text-white">{{ genderLabel }}</span>
                                        </div>
                                    </div>

                                    <div class="list-group-item px-4 py-3 border-0">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="material-symbols-rounded text-dark text-sm me-2">menu_book</i>
                                            <small class="text-muted text-uppercase fw-bold">Jumlah SKS</small>
                                        </div>
                                        <div class="ms-4 text-dark fw-bold">{{ mahasiswa.jumlah_sks || '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Biodata & Perlengkapan KKN -->
                    <div class="col-12 col-xl-6">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header p-3 bg-gradient-dark shadow-dark border-radius-lg mx-3 mt-n3 z-index-2">
                                <h6 class="mb-0 text-white">
                                    <i class="material-symbols-rounded text-sm me-2 align-middle">checklist</i>
                                    Biodata & Perlengkapan KKN
                                </h6>
                            </div>
                            <div class="card-body p-0 mt-2">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item px-4 py-3 border-0">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="material-symbols-rounded text-dark text-sm me-2">checkroom</i>
                                            <small class="text-muted text-uppercase fw-bold">Ukuran Jaket/Rompi</small>
                                        </div>
                                        <div class="ms-4 text-dark fw-bold">{{ mahasiswa.ukuran_jacket_rompi || '-' }}</div>
                                    </div>

                                    <div class="list-group-item px-4 py-3 border-0 bg-light">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="material-symbols-rounded text-dark text-sm me-2">two_wheeler</i>
                                            <small class="text-muted text-uppercase fw-bold">Kepemilikan Kendaraan</small>
                                        </div>
                                        <div class="ms-4 text-dark">{{ mahasiswa.punya_kendaraan || '-' }}</div>
                                    </div>

                                    <div class="list-group-item px-4 py-3 border-0">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="material-symbols-rounded text-dark text-sm me-2">directions_car</i>
                                            <small class="text-muted text-uppercase fw-bold">Tipe Kendaraan</small>
                                        </div>
                                        <div class="ms-4 text-dark">{{ mahasiswa.tipe_kendaraan || '-' }}</div>
                                    </div>

                                    <div class="list-group-item px-4 py-3 border-0 bg-light">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="material-symbols-rounded text-dark text-sm me-2">id_card</i>
                                            <small class="text-muted text-uppercase fw-bold">Kepemilikan Lisensi (SIM)</small>
                                        </div>
                                        <div class="ms-4 text-dark">{{ mahasiswa.punya_lisensi || '-' }}</div>
                                    </div>

                                    <div class="list-group-item px-4 py-3 border-0">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="material-symbols-rounded text-dark text-sm me-2">star</i>
                                            <small class="text-muted text-uppercase fw-bold">Keahlian</small>
                                        </div>
                                        <div class="ms-4 text-dark">{{ mahasiswa.keahlian || '-' }}</div>
                                    </div>

                                    <div class="list-group-item px-4 py-3 border-0 bg-light">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="material-symbols-rounded text-dark text-sm me-2">id_card</i>
                                            <small class="text-muted text-uppercase fw-bold">Kartu Tanda Penduduk</small>
                                        </div>
                                        <div class="ms-4 text-dark">
                                            <button class="btn btn-sm btn-outline-dark mb-0 mt-2" @click="openAddModal(k)">
                                                <i class="material-symbols-rounded text-sm me-1">visibility</i>Lihat Kartu Tanda Penduduk
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal KTP -->
         <div v-if="isModalOpen" class="modal fade show" tabindex="-1" role="dialog" aria-hidden="true" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-normal">Kartu Identitas {{ mahasiswa.nama }}</h5>
                        <button type="button" class="btn-close text-dark" @click="isModalOpen = false" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="previewUrl" class="text-center">
                            <img :src="previewUrl" class="img-fluid rounded-3 w-100 p-3" style="max-height: 190px; object-fit: contain; background: #f8f9fa;">
                            <a :href="previewUrl" target="_blank" class="btn btn-primary mt-4">Unduh Foto</a>
                        </div>
                        <div v-else class="text-center mt-2 mb-3">
                            <p class="text-muted">Tidak ada foto KTP</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
