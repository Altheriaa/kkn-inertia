<script setup>
    import Layout from '../../Layouts/App.vue';
    import { Link, useForm, usePage, router } from '@inertiajs/vue3';
    import axios from 'axios';
    import { ref, onMounted, reactive, watch } from 'vue';
    import Swal from 'sweetalert2';

    const props = defineProps({
        jenisKknList: Array,
        mahasiswa: Object,
        errors: Object,
        pendingPayment: Object,
        midtransClientKey: String,
    });

    const loading = ref(false);
    const form = reactive({
        jenis_kkn_id: ''
    });

    // format currency
    const formatCurrency = (value) => {
        if (value === undefined || value === null) return 'Rp 0';
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(value);
    };

    onMounted(() => {
        const script = document.createElement('script');
        script.src = "https://app.sandbox.midtrans.com/snap/snap.js";
        script.setAttribute('data-client-key', props.midtransClientKey);
        document.head.appendChild(script);
    });

    // fungsi checkout
    const handleCheckout = async () => {
        if (!form.jenis_kkn_id) {
            return Swal.fire('Peringatan', 'Silakan pilih jenis KKN terlebih dahulu.', 'warning')
        }

        const confirm = await Swal.fire({
            title: 'Konfirmasi Pembayaran',
            text: "Sistem akan membuat tagihan pembayaran untuk pendaftaran KKN Anda.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            confirmButtonColor: '#344767'
        });

        if (confirm.isConfirmed) {
            loading.value = true;
            try {
                // panggil methode createTransaction controller 
                const response = await axios.post('/mahasiswa/pembayaran/create-transaction', {
                    jenis_kkn_id: form.jenis_kkn_id
                }); 

                // buka popup Midtrans
                openSnap(response.data.snap_token);
                
            } catch (error) {
                const message = error.response?.data?.message || 'Terjadi kesalahan sistem.';
                Swal.fire('Gagal', message, 'error');
            } finally {
                loading.value = false;
            }
        }
    }

    // fungsi membuka pop up midtrans (snap)
    const openSnap = (token) => {
        window.snap.pay(token, {
            onSuccess: (result) => {
                Swal.fire('Berhasil', 'Pembayaran Anda telah diterima', 'success')
                    .then(() => router.reload());
            },
            onPending: (result) => {
                Swal.fire('Menunggu Pembayaran', 'Silakan selesaikan pembayaran sesuai instruksi', 'info')
                    .then(() => router.reload());
            },
            onError: (result) => {
                Swal.fire('Gagal', 'Pembayaran Anda gagal', 'error');
            },
            onClose: () => {
                router.reload({ only: ['pendingPayment'] });
            }
        })
    }

    // handle cancle transaction
    const handleCancel = (orderId) => {
        Swal.fire({
            title: 'Konfirmasi',
            text: `Anda yakin ingin membatalkan transaksi ${orderId}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#344767',
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Kembali'
        }).then((result) => {
            if (result.isConfirmed) {
                router.post(`/mahasiswa/pembayaran/${orderId}/cancel`, {}, {
                onBefore: () => {
                    loading.value = true;
                },
                onSuccess: () => {
                    Swal.fire('Berhasil', 'Transaksi telah dibatalkan.', 'success');
                },
                onError: (errors) => {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat membatalkan transaksi.', 'error');
                },
                onFinish: () => {
                    loading.value = false;
                }
            });
            }
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
                                    <i class="material-symbols-rounded text-white me-2" style="font-size: 28px;">wallet</i>
                                    <div>
                                        <h4 class="text-white text-capitalize mb-0">Pembayaran KKN Unaya</h4>
                                        <p class="text-white text-sm mb-0 opacity-8">Pilih KKN dan Lakukan Pembayaran</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Jika Pembayaran Pending -->
                        <div v-if="pendingPayment" class="card-body p-4 pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                                <span class="badge bg-gradient-dark py-2 px-3">
                                    <i class="material-symbols-rounded text-sm me-1 align-middle">edit_note</i>
                                    Form Pembayaran KKN
                                </span>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="card text-center p-4 shadow-none border">
                                        <div class="card-body">
                                            <h5 class="font-weight-bolder mb-3">Anda Memiliki Transaksi Tertunda</h5>
                                            <p class="text-secondary mb-4">
                                                Selesaikan pembayaran untuk <strong>{{ pendingPayment.order_id }}</strong>
                                            </p>

                                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                                <button type="button" @click="openSnap(pendingPayment.snap_token)" class="btn btn-success px-3">
                                                    <i class="material-symbols-rounded">credit_card</i> Bayar Sekarang
                                                </button>

                                                <button type="button" @click="handleCancel(pendingPayment.order_id)" class="btn btn-danger px-3">
                                                    <i class="material-symbols-rounded">close</i> Batalkan Pembayaran
                                                </button>

                                                <!-- <Link :href="route('mahasiswa.riwayat')" class="btn btn-outline-dark px-3">
                                                    <i class="material-symbols-rounded">history</i> Lihat Riwayat
                                                </Link> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Jika Belum Daftar/Bayar -->
                        <div v-else-if="mahasiswa.status_kkn === 'Belum Daftar'" class="card-body p-4 pt-3">
                             <!-- Action Bar -->
                            <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                                <span class="badge bg-gradient-dark py-2 px-3">
                                    <i class="material-symbols-rounded text-sm me-1 align-middle">edit_note</i>
                                    Form Pembayaran KKN
                                </span>
                            </div>

                            <div class="table-responsive mb-2">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Jenis KKN</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Biaya</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-if="jenisKknList && jenisKknList.length > 0">
                                            <tr v-for="jenis in jenisKknList" :key="jenis.id">
                                                <!-- Kolom Prodi -->
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">
                                                                {{ jenis.nama_jenis }}
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <h6 class="mb-0 text-sm text">
                                                        {{ formatCurrency(jenis.biaya) }}
                                                    </h6>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span v-if="jenis.is_active" class="badge badge-sm bg-gradient-success">Aktif</span>
                                                    <span v-else class="badge badge-sm bg-gradient-secondary">Nonaktif</span>
                                                </td>
                                            </tr>
                                        </template>
                                        <template v-else>
                                            <tr>
                                                <td colspan="3" class="text-center text-secondary">
                                                    Tidak ada jenis KKN tersedia.
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <hr class="horizontal dark mt-0 mb-4">
                            <!-- Form -->
                            <form>
                                <div class="row">
                                    <!-- Pilih KKN -->
                                    <div class="col-md-12 mb-4">
                                        <div class="input-group input-group-outline">
                                            <label class="form-label pt-1" style="z-index: 1;"></label>
                                            <select v-model="form.jenis_kkn_id" class="form-control">
                                                <option value="" disabled selected> Pilih Jenis KKN </option>
                                                <option v-for="jenis in jenisKknList" :key="jenis.id" :value="jenis.id">
                                                    {{ jenis.nama_jenis }} - ({{ formatCurrency(jenis.biaya) }})
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <p class="text-xs text-secondary mb-0">
                                        <i class="material-symbols-rounded text-xs align-middle me-1">info</i>
                                        Pastikan memilih KKN dengan benar sebelum melakukan pembayaran.
                                    </p>
                                    <div class="d-flex gap-2">
                                        <!-- <button type="reset" class="btn btn-outline-secondary px-3" @click="form.reset()">
                                            <i class="material-symbols-rounded me-1">restart_alt</i>
                                            Reset
                                        </button> -->
                                        <button type="button" @click="handleCheckout" :disabled="loading" class="btn btn bg-gradient-dark px-3">
                                            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                                            <i v-else class="material-symbols-rounded me-1">credit_card</i>
                                            {{ loading ? 'Memproses...' : 'Proses Pembayaran' }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Jika Sudah Bayar -->
                        <div v-else class="card-body p-4">
                            <div class="text-center alert alert-success pt-4">
                                <h4 class="text-white text-md"><i class="material-symbols-rounded text-white text-md">check_circle</i> Anda sudah terdaftar KKN.</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>