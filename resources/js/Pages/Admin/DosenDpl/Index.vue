<script setup>
import Layout from '../../../Layouts/App.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    dosenDpls: Array,
    errors : Object
});

const searchQuery = ref('');

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

const confirmDelete = (dosen) => {
    Swal.fire({
        title: 'Hapus Data?',
        text: `Data "${dosen.nama_dosen}" akan dihapus permanen!`,
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
            router.delete(`/admin/dosen-dpl/${dosen.id}`);
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
                                    <i class="material-symbols-rounded text-white me-2" style="font-size: 28px;">groups</i>
                                    <div>
                                        <h4 class="text-white text-capitalize mb-0">Dosen Pembimbing Lapangan</h4>
                                        <p class="text-white text-sm mb-0 opacity-8">Kelola data dosen pembimbing lapangan KKN</p>
                                    </div>
                                </div>
                                <span class="badge bg-white text-dark py-2 px-3 shadow-sm">
                                    <i class="material-symbols-rounded text-sm me-1 align-middle">database</i>
                                    {{ dosenDpls?.length || 0 }} Data
                                </span>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="card-body pb-0 pt-3 px-4">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                                <Link href="/admin/dosen-dpl/create" class="btn bg-gradient-dark mb-0 shadow-sm">
                                    <i class="material-symbols-rounded text-sm me-1">person_add</i> Tambah DPL
                                </Link>
                                <div class="input-group input-group-outline" :class="{ 'is-focused': false, 'is-filled': searchQuery }" style="max-width: 300px;">
                                    <label class="form-label">
                                        Cari dosen...
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
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">badge</i>
                                                NUPTK
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">person</i>
                                                Nama Dosen
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">school</i>
                                                Program Studi
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">workspace_premium</i>
                                                Bidang Keahlian
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">phone</i>
                                                No Handphone
                                            </th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                <i class="material-symbols-rounded text-xs me-1 align-middle">settings</i>
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Empty State -->
                                        <tr v-if="!dosenDpls || dosenDpls.length === 0">
                                            <td colspan="6" class="text-center py-5">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="material-symbols-rounded opacity-3 mb-2" style="font-size: 64px;">group_off</i>
                                                    <h6 class="text-secondary mb-1">Belum Ada Data</h6>
                                                    <p class="text-xs text-secondary mb-3">Data dosen pembimbing lapangan masih kosong.</p>
                                                    <Link href="/admin/dosen-dpl/create" class="btn btn-sm bg-gradient-dark mb-0">
                                                        <i class="material-symbols-rounded text-sm me-1">add</i> Tambah DPL Pertama
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Data Rows -->
                                        <tr v-for="(dosen, index) in dosenDpls" :key="dosen.id"
                                            class="table-row-hover">
                                            <!-- NUPTK -->
                                            <td>
                                                <div class="d-flex px-3 py-1">
                                                    <div class="icon-shape icon-sm bg-gradient-dark shadow text-center border-radius-md d-flex align-items-center justify-content-center me-2">
                                                        <span class="text-white text-xs font-weight-bold">{{ index + 1 }}</span>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm font-weight-bold">{{ dosen.nuptk }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ dosen.nama_dosen }}</p>
                                            </td>
                                            <td>
                                                <span class="badge bg-gradient-success py-1 px-3">{{ dosen.prodi }}</span>
                                            </td>
                                            <td>
                                                <p class="text-sm mb-0">{{ dosen.bidang_keahlian }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm mb-0">
                                                    {{ dosen.no_hp }}
                                                </p>
                                            </td>
                                            <!-- Aksi -->
                                            <td class="align-middle text-center">
                                                <Link :href="`/admin/dosen-dpl/${dosen.id}/edit`"
                                                    class="btn btn-link text-warning mb-0 px-2 py-1"
                                                    title="Edit Data">
                                                    <i class="material-symbols-rounded text-lg">edit</i>
                                                </Link>
                                                <button type="button"
                                                    class="btn btn-link text-danger mb-0 px-2 py-1"
                                                    title="Hapus Data"
                                                    @click="confirmDelete(dosen)">
                                                    <i class="material-symbols-rounded text-lg">delete</i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Footer Info -->
                            <div class="d-flex justify-content-between align-items-center px-4 py-3" v-if="dosenDpls && dosenDpls.length > 0">
                                <p class="text-xs text-secondary mb-0">
                                    <i class="material-symbols-rounded text-xs align-middle me-1">info</i>
                                    Menampilkan {{ dosenDpls.length }} data dosen pembimbing lapangan.
                                </p>
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
</style>