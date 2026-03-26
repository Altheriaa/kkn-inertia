<script setup>
    import Layout from '../../Layouts/App.vue';
    import { Link, useForm, usePage } from '@inertiajs/vue3';
    import { router } from '@inertiajs/vue3';
    import { watch, onMounted, ref } from 'vue';
    import Swal from 'sweetalert2';

    const props = defineProps({
        mahasiswa: Object,
        errors: Object
    })

    const form = useForm({
        no_hp: props.mahasiswa.no_hp,
        no_hp_wali: props.mahasiswa.no_hp_wali,
        jenis_kelamin: props.mahasiswa.jenis_kelamin,
        ukuran_jacket_rompi: props.mahasiswa.ukuran_jacket_rompi,
        punya_kendaraan: props.mahasiswa.punya_kendaraan,
        tipe_kendaraan: props.mahasiswa.tipe_kendaraan,
        punya_lisensi: props.mahasiswa.punya_lisensi,
        keahlian: props.mahasiswa.keahlian,
        status_kkn: props.mahasiswa.status_kkn,
        foto_ktp: null,
        remove_foto_ktp: false
    });

    const previewUrl = ref(props.mahasiswa.foto_ktp ? `/storage/${props.mahasiswa.foto_ktp}` : null);

    const handleFileUpload = (e) => {
        const file = e.target.files[0];
        if (file) {
            form.foto_ktp = file;
            form.remove_foto_ktp = false;
            previewUrl.value = URL.createObjectURL(file);
        }
    };

    const removePhoto = () => {
        form.foto_ktp = null;
        previewUrl.value = null;
        form.remove_foto_ktp = true;
        // Reset the input value so the same file can be selected again
        const input = document.getElementById('foto_ktp_input');
        if (input) input.value = '';
    };

    const page = usePage();

    const showFlashMessage = () => {
        const flash = page.props.flash || {};
        const errors = page.props.errors || {};

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

        if (Object.keys(errors).length > 0 && !flash.success && !flash.warning) {
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
    const confirmBiodata = () => {
        Swal.fire({
            title: 'Simpan Data?',
            text: `Data akan disimpan!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="material-symbols-rounded text-sm me-1">save</i> Ya, Simpan!',
            cancelButtonText: '<i class="material-symbols-rounded text-sm me-1">close</i> Batal',
            confirmButtonColor: '#344767',
            cancelButtonColor: '#d33',
            reverseButtons: true,
            customClass: {
                popup: 'glass-popup rounded-3xl shadow-blur p-6',
                title: 'font-weight-bold',
                confirmButton: 'px-4 py-2 rounded-xl',
                cancelButton: 'px-4 py-2 rounded-xl',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.post(`/mahasiswa/pendaftaran`, {
                    preserveScroll: true,
                    forceFormData: true,
                    onSuccess: () => {
                        if (props.mahasiswa.foto_ktp) {
                            previewUrl.value = `/storage/${props.mahasiswa.foto_ktp}`;
                        } else {
                            previewUrl.value = null;
                        }
                        form.foto_ktp = null;
                        form.remove_foto_ktp = false;
                    }
                });
            }
        });
    };

    onMounted(() => {
        showFlashMessage();
    });

    watch(() => page.props.flash, () => {
        showFlashMessage();
    }, { deep: true });

    watch(() => props.mahasiswa.foto_ktp, (newVal) => {
        if (newVal && !form.foto_ktp) {
            previewUrl.value = `/storage/${newVal}`;
        } else if (!newVal && !form.foto_ktp) {
            previewUrl.value = null;
        }
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
                            <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 d-flex align-items-center">
                                <div class="d-flex align-items-center ps-3">
                                    <i class="material-symbols-rounded text-white me-2" style="font-size: 28px;">wallet</i>
                                    <div>
                                        <h4 class="text-white text-capitalize mb-0">Pendaftaran KKN Unaya</h4>
                                        <p class="text-white text-sm mb-0 opacity-8">Lengkapi biodata di bawah ini dengan benar sebelum menyimpan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- main -->
                         <div class="card-body pt-4 p-3">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body p-4">
                                            <!-- @if ($mahasiswa->status_kkn === 'Belum Daftar') -->
                                            <template v-if="mahasiswa.status_kkn == 'Belum Daftar'">
                                                <form @submit.prevent="confirmBiodata" id="biodataForm">
                                                    <div class="row px-3 mt-2">
                                                        <!-- No HP -->
                                                        <div class="col-md-6 mb-4">
                                                            <div class="input-group input-group-outline" :class="{ 'is-filled': form.no_hp, 'is-invalid': form.errors.no_hp }">
                                                                <label class="form-label">NO HP</label>
                                                                <input type="text" class="form-control" v-model="form.no_hp"
                                                                    @focus="$event.target.parentElement.classList.add('is-focused')"
                                                                    @blur="$event.target.parentElement.classList.remove('is-focused')">
                                                            </div>
                                                            <small v-if="form.errors.no_hp" class="text-danger text-xs mt-1 d-block">
                                                                <i class="material-symbols-rounded text-xs align-middle">error</i>
                                                                {{ form.errors.no_hp }}
                                                            </small>
                                                        </div>

                                                        <!-- No HP Darurat -->
                                                        <div class="col-md-6 mb-4">
                                                            <div class="input-group input-group-outline" :class="{ 'is-filled': form.no_hp_wali, 'is-invalid': form.errors.no_hp_wali }">
                                                                <label class="form-label">NO HP Wali</label>
                                                                <input type="text" class="form-control" v-model="form.no_hp_wali"
                                                                    @focus="$event.target.parentElement.classList.add('is-focused')"
                                                                    @blur="$event.target.parentElement.classList.remove('is-focused')">
                                                            </div>
                                                            <small v-if="form.errors.no_hp_wali" class="text-danger text-xs mt-1 d-block">
                                                                <i class="material-symbols-rounded text-xs align-middle">error</i>
                                                                {{ form.errors.no_hp_wali }}
                                                            </small>
                                                        </div>

                                                        <!-- Jenis Kelamin -->
                                                        <div class="col-md-6 mb-4">
                                                            <div class="input-group input-group-outline" :class="{ 'is-filled': form.jenis_kelamin, 'is-invalid': form.errors.jenis_kelamin }">
                                                                <label class="form-label">Jenis Kelamin</label>
                                                                <select class="form-control" v-model="form.jenis_kelamin">
                                                                    <option value="L">Laki-laki</option>
                                                                    <option value="P">Perempuan</option>
                                                                </select>
                                                            </div>
                                                            <small v-if="form.errors.jenis_kelamin" class="text-danger text-xs mt-1 d-block">
                                                                <i class="material-symbols-rounded text-xs align-middle">error</i>
                                                                {{ form.errors.jenis_kelamin }}
                                                            </small>
                                                        </div>


                                                        <!-- Ukuran Jaket/Rompi -->
                                                        <div class="col-md-6 mb-4">
                                                            <div class="input-group input-group-outline" :class="{ 'is-filled': form.ukuran_jacket_rompi, 'is-invalid': form.errors.ukuran_jacket_rompi }">
                                                                <label class="form-label">Ukuran Jacket / Rompi</label>
                                                                <select class="form-control" v-model="form.ukuran_jacket_rompi">
                                                                    <option value="S">S</option>
                                                                    <option value="M">M</option>
                                                                    <option value="L">L</option>
                                                                    <option value="XL">XL</option>
                                                                    <option value="XXL">XXL</option>
                                                                    <option value="3XL">3XL</option>
                                                                </select>
                                                            </div>
                                                            <small v-if="form.errors.ukuran_jacket_rompi" class="text-danger text-xs mt-1 d-block">
                                                                <i class="material-symbols-rounded text-xs align-middle">error</i>
                                                                {{ form.errors.ukuran_jacket_rompi }}
                                                            </small>
                                                        </div>

                                                        <!-- Punya Kendaraan -->
                                                        <div class="col-md-6 mb-4">
                                                            <div class="input-group input-group-outline" :class="{ 'is-filled': form.punya_kendaraan, 'is-invalid': form.errors.punya_kendaraan }">
                                                                <label class="form-label">Mempunyai Kendaraan</label>
                                                                <select class="form-control" v-model="form.punya_kendaraan">
                                                                    <option value="Punya">Ya</option>
                                                                    <option value="Tidak">Tidak</option>
                                                                </select>
                                                            </div>
                                                            <small v-if="form.errors.punya_kendaraan" class="text-danger text-xs mt-1 d-block">
                                                                <i class="material-symbols-rounded text-xs align-middle">error</i>
                                                                {{ form.errors.punya_kendaraan }}
                                                            </small>
                                                        </div>

                                                        <!-- Tipe Kendaraan  -->
                                                        <div class="col-md-6 mb-4">
                                                            <div class="input-group input-group-outline" :class="{ 'is-filled': form.tipe_kendaraan, 'is-invalid': form.errors.tipe_kendaraan }">
                                                                <label class="form-label">Tipe Kendaraan</label>
                                                                <select class="form-control" v-model="form.tipe_kendaraan">
                                                                    <option value="Tidak Ada">Tidak Ada</option>
                                                                    <option value="Mobil">Mobil</option>
                                                                    <option value="Sepeda Motor">Sepeda Motor</option>
                                                                </select>
                                                            </div>
                                                            <small v-if="form.errors.tipe_kendaraan" class="text-danger text-xs mt-1 d-block">
                                                                <i class="material-symbols-rounded text-xs align-middle">error</i>
                                                                {{ form.errors.tipe_kendaraan }}
                                                            </small>
                                                        </div>

                                                        <!-- Punya Lisensi  -->
                                                        <div class="col-md-6 mb-4">
                                                            <div class="input-group input-group-outline" :class="{ 'is-filled': form.punya_lisensi, 'is-invalid': form.errors.punya_lisensi }">
                                                                <label class="form-label">Mempunyai Lisensi</label>
                                                                <select class="form-control" v-model="form.punya_lisensi">
                                                                    <option value="Tidak Ada">Tidak Ada</option>
                                                                    <option value="SIM A">SIM A</option>
                                                                    <option value="SIM B">SIM B</option>
                                                                    <option value="SIM C">SIM C</option>
                                                                </select>
                                                            </div>
                                                            <small v-if="form.errors.punya_lisensi" class="text-danger text-xs mt-1 d-block">
                                                                <i class="material-symbols-rounded text-xs align-middle">error</i>
                                                                {{ form.errors.punya_lisensi }}
                                                            </small>
                                                        </div>

                                                        <!-- Keahlian -->
                                                        <div class="col-md-6 mb-4">
                                                            <div class="input-group input-group-outline" :class="{ 'is-filled': form.keahlian, 'is-invalid': form.errors.keahlian }">
                                                                <label class="form-label">Keahlian</label>
                                                                <input type="text" :maxlength=100 class="form-control" v-model="form.keahlian"
                                                                    @focus="$event.target.parentElement.classList.add('is-focused')"
                                                                    @blur="$event.target.parentElement.classList.remove('is-focused')">
                                                            </div>
                                                            <small v-if="form.errors.keahlian" class="text-danger text-xs mt-1 d-block">
                                                                <i class="material-symbols-rounded text-xs align-middle">error</i>
                                                                {{ form.errors.keahlian }}
                                                            </small>
                                                        </div>

                                                        <!-- Foto KTP -->
                                                        <div class="col-md-6 mb-4">
                                                            <label class="form-label mb-2 ms-0 text-sm">Upload Foto KTP</label>
                                                            <div class="file-upload-container" :class="{ 'is-invalid': form.errors.foto_ktp }">
                                                                <label v-if="!previewUrl" for="foto_ktp_input" class="file-upload-label w-100 mb-0">
                                                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                                                        <i class="material-symbols-rounded text-secondary mb-2" style="font-size: 32px;">cloud_upload</i>
                                                                        <span class="text-xs font-weight-bold text-secondary">Klik untuk Upload Foto KTP</span>
                                                                        <span class="text-xxs text-secondary">PNG, JPG (Maks. 2MB)</span>
                                                                    </div>
                                                                    <input id="foto_ktp_input" type="file" accept="image/*" class="d-none" @change="handleFileUpload">
                                                                </label>
                                                                
                                                                <div v-else class="preview-container position-relative p-2">
                                                                    <img :src="previewUrl" class="img-fluid rounded-3 shadow-sm w-100" style="max-height: 180px; object-fit: contain; background: #f8f9fa;">
                                                                    <button @click="removePhoto" type="button" class="btn btn-sm btn-icon-only bg-gradient-danger position-absolute top-0 end-0 m-3 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; min-width: 24px; padding: 0;">
                                                                        <i class="material-symbols-rounded text-white" style="font-size: 14px;">close</i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <small v-if="form.errors.foto_ktp" class="text-danger text-xs mt-1 d-block">
                                                                <i class="material-symbols-rounded text-xs align-middle">error</i>
                                                                {{ form.errors.foto_ktp }}
                                                            </small>
                                                        </div>

                                                        <!-- Tombol Submit -->
                                                        <div class="col-12 text-end mt-2 mb-1">
                                                            <button type="submit" class="btn bg-gradient-dark px-4 py-2">
                                                                <i class="material-symbols-rounded text-white me-2">save</i>Simpan Biodata
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </template>
                                            <template v-else>
                                                <form @submit.prevent="confirmBiodata" id="biodataForm">
                                                    <div class="row px-3 mt-2">
                                                        <div class="col-12 mb-4">
                                                            <h5 class="font-weight-bolder">Pendaftaran KKN</h5>
                                                            <p class="text-sm text-secondary">Lengkapi biodata di bawah ini dengan benar sebelum menyimpan.</p>
                                                        </div>
                                                        
                                                        <!-- No HP -->
                                                        <div class="col-md-6 mb-4">
                                                            <div class="input-group input-group-outline" :class="{ 'is-filled': form.no_hp, 'is-invalid': form.errors.no_hp }">
                                                                <label disable class="form-label">NO HP</label>
                                                                <input type="text" class="form-control" v-model="form.no_hp"
                                                                    @focus="$event.target.parentElement.classList.add('is-focused')"
                                                                    @blur="$event.target.parentElement.classList.remove('is-focused')" disabled>
                                                            </div>
                                                            <small v-if="form.errors.no_hp" class="text-danger text-xs mt-1 d-block">
                                                                <i class="material-symbols-rounded text-xs align-middle">error</i>
                                                                {{ form.errors.no_hp }}
                                                            </small>
                                                        </div>

                                                        <!-- No HP Darurat -->
                                                        <div class="col-md-6 mb-4">
                                                            <div class="input-group input-group-outline" :class="{ 'is-filled': form.no_hp_wali, 'is-invalid': form.errors.no_hp_wali }">
                                                                <label class="form-label">NO HP Wali</label>
                                                                <input type="text" class="form-control" v-model="form.no_hp_wali"
                                                                    @focus="$event.target.parentElement.classList.add('is-focused')"
                                                                    @blur="$event.target.parentElement.classList.remove('is-focused')" disabled>
                                                            </div>
                                                            <small v-if="form.errors.no_hp_wali" class="text-danger text-xs mt-1 d-block">
                                                                <i class="material-symbols-rounded text-xs align-middle">error</i>
                                                                {{ form.errors.no_hp_wali }}
                                                            </small>
                                                        </div>

                                                        <!-- Jenis Kelamin -->
                                                        <div class="col-md-6 mb-4">
                                                            <div class="input-group input-group-outline" :class="{ 'is-filled': form.jenis_kelamin, 'is-invalid': form.errors.jenis_kelamin }">
                                                                <label class="form-label">Jenis Kelamin</label>
                                                                <select class="form-control" v-model="form.jenis_kelamin" disabled>
                                                                    <option value="L">Laki-laki</option>
                                                                    <option value="P">Perempuan</option>
                                                                </select>
                                                            </div>
                                                            <small v-if="form.errors.jenis_kelamin" class="text-danger text-xs mt-1 d-block">
                                                                <i class="material-symbols-rounded text-xs align-middle">error</i>
                                                                {{ form.errors.jenis_kelamin }}
                                                            </small>
                                                        </div>


                                                        <!-- Ukuran Jaket/Rompi -->
                                                        <div class="col-md-6 mb-4">
                                                            <div class="input-group input-group-outline" :class="{ 'is-filled': form.ukuran_jacket_rompi, 'is-invalid': form.errors.ukuran_jacket_rompi }">
                                                                <label class="form-label">Ukuran Jacket / Rompi</label>
                                                                <select class="form-control" v-model="form.ukuran_jacket_rompi" disabled>
                                                                    <option value="S">S</option>
                                                                    <option value="M">M</option>
                                                                    <option value="L">L</option>
                                                                    <option value="XL">XL</option>
                                                                    <option value="XXL">XXL</option>
                                                                    <option value="3XL">3XL</option>
                                                                </select>
                                                            </div>
                                                            <small v-if="form.errors.ukuran_jacket_rompi" class="text-danger text-xs mt-1 d-block">
                                                                <i class="material-symbols-rounded text-xs align-middle">error</i>
                                                                {{ form.errors.ukuran_jacket_rompi }}
                                                            </small>
                                                        </div>

                                                        <!-- Punya Kendaraan -->
                                                        <div class="col-md-6 mb-4">
                                                            <div class="input-group input-group-outline" :class="{ 'is-filled': form.punya_kendaraan, 'is-invalid': form.errors.punya_kendaraan }">
                                                                <label class="form-label">Mempunyai Kendaraan</label>
                                                                <select class="form-control" v-model="form.punya_kendaraan" disabled>
                                                                    <option value="Punya">Ya</option>
                                                                    <option value="Tidak">Tidak</option>
                                                                </select>
                                                            </div>
                                                            <small v-if="form.errors.punya_kendaraan" class="text-danger text-xs mt-1 d-block">
                                                                <i class="material-symbols-rounded text-xs align-middle">error</i>
                                                                {{ form.errors.punya_kendaraan }}
                                                            </small>
                                                        </div>

                                                        <!-- Tipe Kendaraan  -->
                                                        <div class="col-md-6 mb-4">
                                                            <div class="input-group input-group-outline" :class="{ 'is-filled': form.tipe_kendaraan, 'is-invalid': form.errors.tipe_kendaraan }">
                                                                <label class="form-label">Tipe Kendaraan</label>
                                                                <select class="form-control" v-model="form.tipe_kendaraan" disabled>
                                                                    <option value="Tidak Ada">Tidak Ada</option>
                                                                    <option value="Mobil">Mobil</option>
                                                                    <option value="Sepeda Motor">Sepeda Motor</option>
                                                                </select>
                                                            </div>
                                                            <small v-if="form.errors.tipe_kendaraan" class="text-danger text-xs mt-1 d-block">
                                                                <i class="material-symbols-rounded text-xs align-middle">error</i>
                                                                {{ form.errors.tipe_kendaraan }}
                                                            </small>
                                                        </div>

                                                        <!-- Punya Lisensi  -->
                                                        <div class="col-md-6 mb-4">
                                                            <div class="input-group input-group-outline" :class="{ 'is-filled': form.punya_lisensi, 'is-invalid': form.errors.punya_lisensi }">
                                                                <label class="form-label">Mempunyai Lisensi</label>
                                                                <select class="form-control" v-model="form.punya_lisensi" disabled>
                                                                    <option value="Tidak Ada">Tidak Ada</option>
                                                                    <option value="SIM A">SIM A</option>
                                                                    <option value="SIM B">SIM B</option>
                                                                    <option value="SIM C">SIM C</option>
                                                                </select>
                                                            </div>
                                                            <small v-if="form.errors.punya_lisensi" class="text-danger text-xs mt-1 d-block">
                                                                <i class="material-symbols-rounded text-xs align-middle">error</i>
                                                                {{ form.errors.punya_lisensi }}
                                                            </small>
                                                        </div>

                                                        <!-- Keahlian -->
                                                        <div class="col-md-6 mb-4">
                                                            <div class="input-group input-group-outline" :class="{ 'is-filled': form.keahlian, 'is-invalid': form.errors.keahlian }">
                                                                <label class="form-label">Keahlian</label>
                                                                <input type="text" :maxlength=100 class="form-control" v-model="form.keahlian"
                                                                    @focus="$event.target.parentElement.classList.add('is-focused')"
                                                                    @blur="$event.target.parentElement.classList.remove('is-focused')" disabled>
                                                            </div>
                                                            <small v-if="form.errors.keahlian" class="text-danger text-xs mt-1 d-block">
                                                                <i class="material-symbols-rounded text-xs align-middle">error</i>
                                                                {{ form.errors.keahlian }}
                                                            </small>
                                                        </div>

                                                        <!-- Foto KTP (Read Only) -->
                                                        <div class="col-md-6 mb-4">
                                                            <label class="form-label mb-2 ms-0 text-sm">Foto KTP</label>
                                                            <div class="file-upload-container bg-light border-0 shadow-none" style="cursor: default;">
                                                                <div v-if="previewUrl" class="preview-container p-2">
                                                                    <img :src="previewUrl" class="img-fluid rounded-3 shadow-sm w-100" style="max-height: 180px; object-fit: contain; background: #fff;">
                                                                </div>
                                                                <div v-else class="text-center py-4">
                                                                    <i class="material-symbols-rounded text-secondary mb-1" style="font-size: 24px;">image_not_supported</i>
                                                                    <p class="text-xs text-secondary mb-0">Belum ada foto KTP</p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Tombol Submit -->
                                                        <div class="col-12 text-end mt-2 mb-1">
                                                            <button type="submit" class="btn bg-gradient-dark px-4 py-2" disabled>
                                                                <i class="material-symbols-rounded text-white me-2">save</i>Simpan Biodata
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
.file-upload-container {
    border: 2px dashed #d2d6da;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    background-color: #fff;
    overflow: hidden;
}

.file-upload-container:hover {
    border-color: #e91e63;
    background-color: rgba(233, 30, 99, 0.02);
}

.file-upload-container.is-invalid {
    border-color: #fd5c70;
}

.file-upload-label {
    width: 100%;
    cursor: pointer;
    margin-bottom: 0;
}

.preview-container {
    transition: all 0.3s ease;
}

.material-symbols-rounded {
    transition: transform 0.2s ease;
}

.file-upload-label:hover .material-symbols-rounded {
    transform: translateY(-2px);
}

.btn-icon-only {
    transition: all 0.2s ease;
}

.btn-icon-only:hover {
    transform: scale(1.1);
}
</style>