<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    lokasiKkns: Array,
    jadwalKkn: Object
});

// Format tanggal
const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};

const isOpen = computed(() => {
    if (!props.jadwalKkn) return false;
    const now = new Date();
    const start = new Date(props.jadwalKkn.tanggal_dibuka);
    const end = new Date(props.jadwalKkn.tanggal_ditutup);
    return now >= start && now <= end;
});

</script>

<template>
  <!-- Navbar -->
  <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
      <div class="col-12">
        <nav
          class="navbar navbar-expand-lg blur border-radius-xl top-0 z-index-fixed shadow position-absolute my-3 py-2 start-0 end-0 mx-4">
          <div class="container-fluid px-0">
            <a class="navbar-brand font-weight-bolder ms-sm-3 d-flex align-items-center" href="#home">
              <img src="assets/img/Logo Unaya.png" alt="Logo Unaya" class="me-2" style="height: 40px;">
              KKN UNAYA
            </a>
            <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse"
              data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false"
              aria-label="Toggle navigation">
              <span class="navbar-toggler-icon mt-2">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </span>
            </button>
            <div class="collapse navbar-collapse w-100 pt-3 pb-2 py-lg-0" id="navigation">
              <ul class="navbar-nav navbar-nav-hover mx-auto">
                <li class="nav-item">
                  <a class="nav-link" href="#home">Home</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#panduan">Panduan KKN</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#lokasi">Lokasi KKN</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#jadwal">Jadwal</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#download">Download</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#kontak">Kontak</a>
                </li>
              </ul>
              <ul class="navbar-nav">
                <li class="nav-item">
                  <template v-if="$page.props.auth.user && $page.props.auth.user.email === 'operatorkkn@abulyatama.ac.id'">
                    <Link href="/admin/dashboard" class="btn btn-sm bg-gradient-dark mb-0">
                      <i class="material-symbols-rounded me-1">dashboard</i> Dashboard
                    </Link>
                  </template>
                  <template v-else-if="$page.props.authMahasiswa.user">
                    <Link href="/mahasiswa/dashboard" class="btn btn-sm bg-gradient-dark mb-0">
                      <i class="material-symbols-rounded me-1">dashboard</i> Dashboard
                    </Link>
                  </template>
                  <template v-else>
                    <Link href="/login" class="btn btn-sm bg-gradient-dark mb-0">
                      <i class="material-symbols-rounded me-1">login</i> Login
                    </Link>
                  </template>
                </li>
              </ul>
            </div>
          </div>
        </nav>
      </div>
    </div>
  </div>

  <!-- Hero Section -->
  <section class="p-4 min-vh-100 d-flex align-items-center position-relative" id="home"
    style="background-image: url('/assets/img/Biro Abulyatama.webp'); background-size: cover; background-position: center;">
    <!-- Dark Overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-dark" style="opacity: 0.85;"></div>
    <div class="container position-relative" style="z-index: 1;">
      <div class="row align-items-center py-5">
        <div class="col-lg-7 text-white mb-5 mb-lg-0">
          <h1 class="display-4 fw-bold mb-4 text-white mt-3 pt-5">
            KKN Universitas Abulyatama
          </h1>
          <p class="lead mb-5 opacity-8">
            Kuliah Kerja Nyata (KKN) adalah bentuk kegiatan pengabdian kepada masyarakat oleh mahasiswa dengan
            pendekatan lintas keilmuan dan sektoral pada waktu dan daerah tertentu.
          </p>
          <div class="d-flex flex-column flex-sm-row gap-3">
            <Link href="/login" class="btn btn-white btn-lg">
              <i class="material-symbols-rounded me-2">how_to_reg</i> Daftar KKN
            </Link>
            <Link href="#panduan" class="btn btn-outline-white btn-lg">
              <i class="material-symbols-rounded me-2">info</i> Pelajari Lebih Lanjut
            </Link>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="row g-3">
            <div class="col-3">
            </div>
            <div class="col-9">
              <div class="card text-center p-4 shadow">
                <h2 class="text-dark fw-bold mb-2">{{ jadwalKkn?.nama_periode ?? 'Tidak Ada Periode Aktif' }}</h2>
                <p class="text-secondary mb-0">Periode KKN</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Panduan Section -->
  <section class="p-3 py-7" id="panduan">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold mb-2">Panduan Pendaftaran KKN</h2>
        <p class="text-secondary">Ikuti langkah-langkah berikut untuk mendaftar KKN</p>
      </div>
      <div class="row g-4">
        <div class="col-lg-4 col-md-6">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div
                class="icon icon-shape bg-gradient-dark text-white rounded-circle mb-3 d-flex align-items-center justify-content-center">
                <span class="fw-bold">1</span>
              </div>
              <h5 class="mb-2">Cek Persyaratan</h5>
              <p class="text-secondary mb-0">
                Pastikan Anda telah memenuhi minimal 100 SKS dan tidak memiliki tunggakan pembayaran.
              </p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div
                class="icon icon-shape bg-gradient-dark text-white rounded-circle mb-3 d-flex align-items-center justify-content-center">
                <span class="fw-bold">2</span>
              </div>
              <h5 class="mb-2">Login ke Portal</h5>
              <p class="text-secondary mb-0">
                Login menggunakan NIM dan password yang telah terdaftar di sistem akademik.
              </p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div
                class="icon icon-shape bg-gradient-dark text-white rounded-circle mb-3 d-flex align-items-center justify-content-center">
                <span class="fw-bold">3</span>
              </div>
              <h5 class="mb-2">Lengkapi Biodata</h5>
              <p class="text-secondary mb-0">
                Isi formulir biodata dengan lengkap termasuk nomor HP, ukuran jas, dan keahlian.
              </p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div
                class="icon icon-shape bg-gradient-dark text-white rounded-circle mb-3 d-flex align-items-center justify-content-center">
                <span class="fw-bold">4</span>
              </div>
              <h5 class="mb-2">Lakukan Pembayaran</h5>
              <p class="text-secondary mb-0">
                Bayar biaya KKN melalui metode pembayaran yang tersedia (Transfer Bank, E-Wallet, dll).
              </p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div
                class="icon icon-shape bg-gradient-dark text-white rounded-circle mb-3 d-flex align-items-center justify-content-center">
                <span class="fw-bold">5</span>
              </div>
              <h5 class="mb-2">Tunggu Penempatan</h5>
              <p class="text-secondary mb-0">
                Setelah pembayaran terverifikasi, tunggu pengumuman lokasi penempatan KKN Anda.
              </p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div
                class="icon icon-shape bg-gradient-dark text-white rounded-circle mb-3 d-flex align-items-center justify-content-center">
                <span class="fw-bold">6</span>
              </div>
              <h5 class="mb-2">Cetak Bukti Pendaftaran</h5>
              <p class="text-secondary mb-0">
                Download dan cetak bukti pendaftaran sebagai tanda keikutsertaan KKN.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Lokasi Section -->
  <section class="p-3 py-7 bg-white" id="lokasi">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold mb-2">Lokasi KKN</h2>
        <p class="text-secondary">Peta dan daftar lokasi KKN yang tersedia</p>
      </div>

      <!-- Map Embed -->
      <div class="row mb-5">
        <div class="col-12">
          <div class="card shadow-lg border-radius-lg overflow-hidden">
            <iframe 
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31771.31915956417!2d95.31493805!3d5.5052555!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3040398672c57efb%3A0x710c7f0248a5a110!2sDarul%20Imarah%2C%20Aceh%20Besar%20Regency%2C%20Aceh!5e0!3m2!1sen!2sid!4v1773839621873!5m2!1sen!2sid" 
              width="100%" 
              height="400" 
              style="border:0;" 
              allowfullscreen="" 
              loading="lazy" 
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Jadwal Section -->
  <section class="p-3 py-7" id="jadwal">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold mb-2">Jadwal KKN</h2>
        <p class="text-secondary">Jadwal pelaksanaan KKN tahun ini</p>
      </div>
      <div class="row justify-content-center g-4">
        <!-- @forelse($jadwalKkns->take(1) as $jadwal) -->
          <div class="col-lg-4 col-md-6">
            <div class="card bg-gradient-dark text-white shadow h-100">
              <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                  <i class="material-symbols-rounded me-2">event</i>
                  <h5 class="mb-0 text-white">{{ jadwalKkn?.nama_periode ?? 'Periode KKN' }}</h5>
                </div>
                <div class="mb-3">
                  <small class="opacity-8">Pendaftaran Dibuka</small>
                  <p class="mb-0">{{ formatDate(jadwalKkn?.tanggal_dibuka ?? '-') }}</p>
                </div>
                <div class="mb-3">
                  <small class="opacity-8">Pendaftaran Ditutup</small>
                  <p class="mb-0">{{ formatDate(jadwalKkn?.tanggal_ditutup ?? '-') }}</p>
                </div>
                <div>
                  <small class="opacity-8">Status</small>
                  <p class="mb-0">
                      <template v-if="jadwalKkn">
                          <span v-if="isOpen" class="badge bg-success">Pendaftaran Dibuka</span>
                          <span v-else class="badge bg-secondary">Pendaftaran Ditutup</span>
                      </template>
                      <span v-else class="badge bg-secondary">Pendaftaran Belum Tersedia</span>
                  </p>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
  </section>

  <!-- Download Section -->
  <section class="py-7 bg-white" id="download">
    <div class="container p-4">
      <div class="text-center mb-5">
        <h2 class="fw-bold mb-2">Download</h2>
        <p class="text-secondary">Unduh dokumen dan formulir yang diperlukan</p>
      </div>
      <div class="row g-4 justify-content-center">
        <div class="col-md-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center py-4">
              <div
                class="icon icon-lg icon-shape bg-gradient-dark text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                <i class="material-symbols-rounded" style="top: 0;">description</i>
              </div>
              <h5>Renja Reguler</h5>
              <p class="text-secondary text-sm">Template Renja Mahasiswa KKN Reguler</p>
              <a href="https://docs.google.com/document/d/1AvastoyEGjX2d1GwcbGfvi_QSNIdH9et/edit" target="_blank" class="btn btn-outline-dark btn-sm">
                <i class="material-symbols-rounded me-1">download</i> Download
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center py-4">
              <div
                class="icon icon-lg icon-shape bg-gradient-dark text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                <i class="material-symbols-rounded" style="top: 0;">description</i>
              </div>
              <h5>Renja Non Reguler</h5>
              <p class="text-secondary text-sm">Template Renja Mahasiswa KKN Non Reguler</p>
              <a href="https://docs.google.com/document/d/15vCoeNdpRk5o6FcUC1qUHtKVUPa0yQQp/edit?rtpof=true&sd=true&tab=t.0" target="_blank" class="btn btn-outline-dark btn-sm">
                <i class="material-symbols-rounded me-1">download</i> Download
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center py-4">
              <div
                class="icon icon-lg icon-shape bg-gradient-dark text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                <i class="material-symbols-rounded" style="top: 0;">folder_zip</i>
              </div>
              <h5>Laporan Akhir Reguler</h5>
              <p class="text-secondary text-sm">Template Laporan Akhir Mahasiswa KKN Reguler</p>
              <a href="https://docs.google.com/document/d/16RR4xf05bIZ036OLVEmyU0_QkyBLlUfa/edit" target="_blank" class="btn btn-outline-dark btn-sm">
                <i class="material-symbols-rounded me-1">download</i> Download
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center py-4">
              <div
                class="icon icon-lg icon-shape bg-gradient-dark text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                <i class="material-symbols-rounded" style="top: 0;">folder_zip</i>
              </div>
              <h5>Laporan Akhir Non Reguler</h5>
              <p class="text-secondary text-sm">Template Laporan Akhir Mahasiswa KKN Non Reguler</p>
              <a href="https://docs.google.com/document/d/1F8uaoXmn0OAJngHryAg0TIt9ONpZDxbq/edit?rtpof=true&sd=true" target="_blank" class="btn btn-outline-dark btn-sm">
                <i class="material-symbols-rounded me-1">download</i> Download
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center py-4">
              <div
                class="icon icon-lg icon-shape bg-gradient-dark text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                <i class="material-symbols-rounded" style="top: 0;">folder_zip</i>
              </div>
              <h5>Laporan DPL</h5>
              <p class="text-secondary text-sm">Template Laporan DPL KKN Reguler dan Non Reguler</p>
              <a href="https://docs.google.com/document/d/13KYZrJr1r_qiRWRuIJUMNC1BvbPrFImL/edit" target="_blank" class="btn btn-outline-dark btn-sm">
                <i class="material-symbols-rounded me-1">download</i> Download
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Kontak Section -->
  <section class="py-7" id="kontak">
    <div class="container p-3">
      <div class="text-center mb-5">
        <h2 class="fw-bold mb-2">Kontak Kami</h2>
        <p class="text-secondary">Hubungi kami jika ada pertanyaan</p>
      </div>
      <div class="row g-4 justify-content-center">
        <div class="col-md-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center py-4">
              <div
                class="icon icon-lg icon-shape bg-gradient-dark text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                <i class="material-symbols-rounded" style="top: 0;">location_on</i>
              </div>
              <h5>Alamat</h5>
              <p class="text-secondary text-sm mb-0">
                Jl. Blang Bintang Lama No.17<br>
                Lampoh Keude, Aceh Besar
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center py-4">
              <div
                class="icon icon-lg icon-shape bg-gradient-dark text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                <i class="material-symbols-rounded" style="top: 0;">call</i>
              </div>
              <h5>Telepon</h5>
              <p class="text-secondary text-sm mb-0">
                (0651) 7551966<br>
                Senin - Jumat: 08.00 - 16.00 WIB
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center py-4">
              <div
                class="icon icon-lg icon-shape bg-gradient-dark text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                <i class="material-symbols-rounded" style="top: 0;">mail</i>
              </div>
              <h5>Email</h5>
              <p class="text-secondary text-sm mb-0">
                lppm@abulyatama.ac.id<br>
                kkn@abulyatama.ac.id
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="p-4 bg-gradient-dark py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 mb-4 mb-lg-0">
          <h4 class="text-white mb-3">
            <img src="assets/img/Logo Unaya.png" alt="Logo Unaya" class="me-2" style="height: 40px;">KKN
            UNAYA
          </h4>
          <p class="text-white opacity-8 mb-0">
            Portal Kuliah Kerja Nyata<br>
            Universitas Abulyatama
          </p>
        </div>
        <div class="col-lg-4 mb-4 mb-lg-0">
          <h6 class="text-white text-uppercase mb-3">Link Cepat</h6>
          <ul class="list-unstyled">
            <li><a href="#home" class="text-white opacity-8 text-decoration-none">Home</a></li>
            <li><a href="#panduan" class="text-white opacity-8 text-decoration-none">Panduan KKN</a></li>
            <li><a href="#lokasi" class="text-white opacity-8 text-decoration-none">Lokasi KKN</a></li>
            <li><a href="{{ route('login') }}" class="text-white opacity-8 text-decoration-none">Login</a></li>
          </ul>
        </div>
        <div class="col-lg-4">
          <h6 class="text-white text-uppercase mb-3">Sosial Media</h6>
          <a href="#" class="btn btn-icon-only btn-link text-white opacity-8">
            <i class="fab fa-facebook fa-lg"></i>
          </a>
          <a href="#" class="btn btn-icon-only btn-link text-white opacity-8">
            <i class="fab fa-instagram fa-lg"></i>
          </a>
          <a href="#" class="btn btn-icon-only btn-link text-white opacity-8">
            <i class="fab fa-youtube fa-lg"></i>
          </a>
        </div>
      </div>
      <hr class="bg-white opacity-2 my-4">
      <div class="row">
        <div class="col-12 text-center">
          <p class="text-white opacity-8 mt-md-5">
            &copy; 2026 KKN Universitas Abulyatama. All rights reserved.
          </p>
        </div>
      </div>
    </div>
  </footer>

  <!-- Core JS Files -->
  <!-- <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/material-dashboard.min.js') }}"></script> -->
</template>