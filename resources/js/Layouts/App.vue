<script setup>
    import { Link } from '@inertiajs/vue3';
    import Aside from './Aside.vue';
    import Footer from './Footer.vue';
    import Navbar from './Navbar.vue';
</script>

<template>
    <!-- Aside -->
    <Aside />
    <!-- Main -->
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <Navbar />
        <!-- Content -->
        <slot />
    </main>
    <!-- Footer -->
    <Footer />
</template>
