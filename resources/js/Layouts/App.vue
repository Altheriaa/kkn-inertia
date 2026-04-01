<script setup>
    import { onMounted, onUnmounted } from 'vue';
    import { router, Link } from '@inertiajs/vue3';
    import Aside from './Aside.vue';
    import Footer from './Footer.vue';
    import Navbar from './Navbar.vue';

    onMounted(() => {
        const handleClickOutside = (e) => {
            const body = document.getElementsByTagName('body')[0];
            const sidenav = document.getElementById('sidenav-main');
            const iconSidenav = document.getElementById('iconNavbarSidenav');

            if (body && body.classList.contains('g-sidenav-pinned')) {
                if (sidenav && !sidenav.contains(e.target) && (!iconSidenav || !iconSidenav.contains(e.target))) {
                    body.classList.remove('g-sidenav-pinned');
                }
            }
        };

        document.addEventListener('click', handleClickOutside, true);

        const removeNavigateListener = router.on('navigate', () => {
            const body = document.getElementsByTagName('body')[0];
            if (body && body.classList.contains('g-sidenav-pinned')) {
                body.classList.remove('g-sidenav-pinned');
            }
        });

        onUnmounted(() => {
            document.removeEventListener('click', handleClickOutside, true);
            if (removeNavigateListener) removeNavigateListener();
        });
    });
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
