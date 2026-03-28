<script setup>
import Layout from '../../../Layouts/App.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    nuptk: '',
    nama_dosen: '',
    prodi: '',
    bidang_keahlian: '',
    no_hp: '',
});

const submit = () => {
    form.post('/admin/dosen-dpl', {
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
                                    <i class="material-symbols-rounded text-white me-2" style="font-size: 28px;">person_add</i>
                                    <div>
                                        <h4 class="text-white text-capitalize mb-0">Tambah Dosen DPL</h4>
                                        <p class="text-white text-sm mb-0 opacity-8">Lengkapi data dosen pembimbing lapangan baru</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4 pt-3">
                            <!-- Action Bar -->
                            <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                                <Link href="/admin/dosen-dpl" class="btn btn-outline-dark mb-0">
                                    <i class="material-symbols-rounded text-sm me-1">arrow_back</i> Kembali
                                </Link>
                                <span class="badge bg-gradient-dark py-2 px-3">
                                    <i class="material-symbols-rounded text-sm me-1 align-middle">edit_note</i>
                                    Form Data DPL
                                </span>
                            </div>

                            <hr class="horizontal dark mt-0 mb-4">

                            <!-- Form -->
                            <form @submit.prevent="submit" id="createDosenForm">
                                <div class="row">
                                    <!-- NUPTK -->
                                    <div class="col-md-6 mb-4">
                                        <div class="input-group input-group-outline" :class="{ 'is-filled': form.nuptk, 'is-invalid': form.errors.nuptk }">
                                            <label class="form-label">NUPTK</label>
                                            <input type="text" class="form-control" v-model="form.nuptk"
                                                @focus="$event.target.parentElement.classList.add('is-focused')"
                                                @blur="$event.target.parentElement.classList.remove('is-focused')">
                                        </div>
                                        <small v-if="form.errors.nuptk" class="text-danger text-xs mt-1 d-block">
                                            <i class="material-symbols-rounded text-xs align-middle">error</i>
                                            {{ form.errors.nuptk }}
                                        </small>
                                    </div>

                                    <!-- Nama DPL -->
                                    <div class="col-md-6 mb-4">
                                        <div class="input-group input-group-outline" :class="{ 'is-filled': form.nama_dosen, 'is-invalid': form.errors.nama_dosen }">
                                            <label class="form-label">Nama DPL</label>
                                            <input type="text" class="form-control" v-model="form.nama_dosen"
                                                @focus="$event.target.parentElement.classList.add('is-focused')"
                                                @blur="$event.target.parentElement.classList.remove('is-focused')">
                                        </div>
                                        <small v-if="form.errors.nama_dosen" class="text-danger text-xs mt-1 d-block">
                                            <i class="material-symbols-rounded text-xs align-middle">error</i>
                                            {{ form.errors.nama_dosen }}
                                        </small>
                                    </div>

                                    <!-- Program Studi -->
                                    <div class="col-md-6 mb-4">
                                        <div class="input-group input-group-outline" :class="{ 'is-filled': form.prodi, 'is-invalid': form.errors.prodi }">
                                            <label class="form-label">Program Studi</label>
                                            <input type="text" class="form-control" v-model="form.prodi"
                                                @focus="$event.target.parentElement.classList.add('is-focused')"
                                                @blur="$event.target.parentElement.classList.remove('is-focused')">
                                        </div>
                                        <small v-if="form.errors.prodi" class="text-danger text-xs mt-1 d-block">
                                            <i class="material-symbols-rounded text-xs align-middle">error</i>
                                            {{ form.errors.prodi }}
                                        </small>
                                    </div>

                                    <!-- Bidang Keahlian -->
                                    <div class="col-md-6 mb-4">
                                        <div class="input-group input-group-outline" :class="{ 'is-filled': form.bidang_keahlian, 'is-invalid': form.errors.bidang_keahlian }">
                                            <label class="form-label">Bidang Keahlian</label>
                                            <input type="text" class="form-control" v-model="form.bidang_keahlian"
                                                @focus="$event.target.parentElement.classList.add('is-focused')"
                                                @blur="$event.target.parentElement.classList.remove('is-focused')">
                                        </div>
                                        <small v-if="form.errors.bidang_keahlian" class="text-danger text-xs mt-1 d-block">
                                            <i class="material-symbols-rounded text-xs align-middle">error</i>
                                            {{ form.errors.bidang_keahlian }}
                                        </small>
                                    </div>

                                    <!-- No Handphone -->
                                    <div class="col-md-6 mb-4">
                                        <div class="input-group input-group-outline" :class="{ 'is-filled': form.no_hp, 'is-invalid': form.errors.no_hp }">
                                            <label class="form-label">No Handphone</label>
                                            <input type="text" class="form-control" v-model="form.no_hp"
                                                @focus="$event.target.parentElement.classList.add('is-focused')"
                                                @blur="$event.target.parentElement.classList.remove('is-focused')">
                                        </div>
                                        <small v-if="form.errors.no_hp" class="text-danger text-xs mt-1 d-block">
                                            <i class="material-symbols-rounded text-xs align-middle">error</i>
                                            {{ form.errors.no_hp }}
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