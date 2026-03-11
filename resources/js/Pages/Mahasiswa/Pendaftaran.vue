<script setup>
    import Layout from '../../Layouts/App.vue';
    import { Link, useForm, usePage } from '@inertiajs/vue3';
    import { router } from '@inertiajs/vue3';
    import { watch, onMounted } from 'vue';
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
    });

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
</script>

<template>
    <Layout>
        <div class="container-fluid py-2">
            <div class="row">
                <div class="col-12 mt-2">
                    <div class="card">
                        <div class="card-body pt-4 p-3">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body p-4">
                                            <!-- @if ($mahasiswa->status_kkn === 'Belum Daftar') -->
                                            <template v-if="mahasiswa.status_kkn == 'Belum Daftar'">
                                                <form @submit.prevent="confirmBiodata" id="biodataForm">
                                                    <div class="row px-3 mt-2">
                                                        <div class="col-12 mb-4">
                                                            <h5 class="font-weight-bolder">Pendaftaran KKN</h5>
                                                            <p class="text-sm text-secondary">Lengkapi biodata di bawah ini dengan benar sebelum menyimpan.</p>
                                                        </div>
                                                        
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