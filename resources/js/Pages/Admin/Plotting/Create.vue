<script setup>
import Layout from '../../../Layouts/App.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    jadwalKkns: Array,
    dosenDpls: Array,
    lokasiKkns: Array,
});

const form = useForm({
    jadwal_kkn_id: '',
    dpl_id: '',
    lokasi_kkn_id: '',
    nama_kelompok: '',
    jenis_kkn: '',
});

// Picu react select dosen
watch(() => form.jadwal_kkn_id,  (newId) => {
    // Reset isi form
    form.dpl_id = ''; 
    form.lokasi_kkn_id = ''; 

    // Panggil kembali controller create dengan parameter baru
    router.get('/admin/plotting/create', 
        { jadwal_kkn_id: newId }, 
        { 
            preserveState: true,  // input lain (seperti nama kelompok) tidak hilang
            preserveScroll: true, // halaman tidak loncat ke atas
            only: ['dosenDpls', 'lokasiKkns'],  // Hanya ambil data dosenDpls dan lokasi kkn
        }
    );
});

const submit = () => {
    form.post('/admin/plotting', {
        preserveScroll: true,
    });
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
                            <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 d-flex align-items-center">
                                <div class="d-flex align-items-center ps-3">
                                    <i class="material-symbols-rounded text-white me-2" style="font-size: 28px;">groups</i>
                                    <div>
                                        <h4 class="text-white text-capitalize mb-0">Tambah Kelompok KKN</h4>
                                        <p class="text-white text-sm mb-0 opacity-8">Lengkapi data kelompok kkn baru</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4 pt-3">
                            <!-- Action Bar -->
                            <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                                <Link href="/admin/plotting" class="btn btn-outline-dark mb-0">
                                    <i class="material-symbols-rounded text-sm me-1">arrow_back</i> Kembali
                                </Link>
                                <span class="badge bg-gradient-dark py-2 px-3">
                                    <i class="material-symbols-rounded text-sm me-1 align-middle">edit_note</i>
                                    Form Data Kelompok KKN
                                </span>
                            </div>

                            <hr class="horizontal dark mt-0 mb-4">

                            <!-- Form -->
                            <form @submit.prevent="submit" id="createDosenForm">
                                <div class="row">
                                    <!-- Pilih Jadwal -->
                                    <div class="col-md-6 mb-4">
                                        <div class="input-group input-group-outline" style="min-width: 150px;">
                                            <select class="form-control" v-model="form.jadwal_kkn_id">
                                                <option value=""> Pilih Jadwal </option>
                                                <option v-for="jadwal in jadwalKkns" :key="jadwal.id" :value="jadwal.id"> {{ jadwal.nama_periode }} </option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Pilih Dosen Pembimbing Lapangan -->
                                    <div class="col-md-6 mb-4">
                                        <div class="input-group input-group-outline" style="min-width: 150px;" :class="{ 'is-filled': form.dpl_id, 'is-invalid': form.errors.dpl_id }">
                                            <select class="form-control" v-model="form.dpl_id">
                                                <option value=""> Pilih Dosen Pembimbing Lapangan </option>
                                                <option v-for="dosen in dosenDpls" :key="dosen.id" :value="dosen.id"> {{ dosen.nama_dosen }} </option>
                                            </select>
                                        </div>
                                        <small v-if="form.errors.dpl_id" class="text-danger text-xs mt-1 d-block">
                                            <i class="material-symbols-rounded text-xs align-middle">error</i>
                                            {{ form.errors.dpl_id }}
                                        </small>
                                    </div>

                                    <!-- Pilih Lokasi KKN -->
                                    <div class="col-md-6 mb-4">
                                        <div class="input-group input-group-outline" style="min-width: 150px;" :class="{ 'is-filled': form.lokasi_kkn_id, 'is-invalid': form.errors.lokasi_kkn_id }">
                                            <select class="form-control"  v-model="form.lokasi_kkn_id">
                                                <option value=""> Pilih Lokasi KKN </option>
                                                <option v-for="lokasi in lokasiKkns" :key="lokasi.id" :value="lokasi.id"> {{lokasi.nama_desa }} </option>
                                            </select>
                                        </div>
                                        <small v-if="form.errors.lokasi_kkn_id" class="text-danger text-xs mt-1 d-block">
                                            <i class="material-symbols-rounded text-xs align-middle">error</i>
                                            {{ form.errors.lokasi_kkn_id }}
                                        </small>
                                    </div>

                                    <!-- Nama Kelompok -->
                                    <div class="col-md-6 mb-4">
                                        <div class="input-group input-group-outline" :class="{ 'is-filled': form.nama_kelompok, 'is-invalid': form.errors.nama_kelompok }">
                                            <label class="form-label">Nama Kelompok</label>
                                            <input type="text" class="form-control" v-model="form.nama_kelompok"
                                                @focus="$event.target.parentElement.classList.add('is-focused')"
                                                @blur="$event.target.parentElement.classList.remove('is-focused')">
                                        </div>
                                        <small v-if="form.errors.nama_kelompok" class="text-danger text-xs mt-1 d-block">
                                            <i class="material-symbols-rounded text-xs align-middle">error</i>
                                            {{ form.errors.nama_kelompok }}
                                        </small>
                                    </div>
                                    
                                    <!-- Pilih Jenis KKN -->
                                    <div class="col-md-6 mb-4">
                                        <div class="input-group input-group-outline" style="min-width: 150px;" :class="{ 'is-filled': form.jenis_kkn, 'is-invalid': form.errors.jenis_kkn }">
                                            <select class="form-control" v-model="form.jenis_kkn">
                                                <option value=""> Pilih Jenis KKN </option>
                                                <option value="KKN-UNAYA Regular">KKN-UNAYA Regular</option>
                                                <option value="KKN-UNAYA Non-Regular">KKN-UNAYA Non-Regular</option>
                                            </select>
                                        </div>
                                        <small v-if="form.errors.jenis_kkn" class="text-danger text-xs mt-1 d-block">
                                            <i class="material-symbols-rounded text-xs align-middle">error</i>
                                            {{ form.errors.jenis_kkn }}
                                        </small>
                                    </div>
                                </div>

                                <hr class="horizontal dark my-3">

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <p class="text-xs text-secondary mb-0">
                                        <i class="material-symbols-rounded text-xs align-middle me-1">info</i>
                                        Pastikan semua data terisi dengan benar sebelum menyimpan.
                                    </p>
                                    <div class="d-flex gap-2">
                                        <button type="reset" class="btn btn-outline-secondary mb-0" @click="form.reset()">
                                            <i class="material-symbols-rounded text-sm me-1">restart_alt</i> Reset
                                        </button>
                                        <button type="submit" class="btn bg-gradient-dark mb-0" :disabled="form.processing">
                                            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                            <i v-else class="material-symbols-rounded text-sm me-1">save</i>
                                            {{ form.processing ? 'Menyimpan...' : 'Simpan Data' }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>