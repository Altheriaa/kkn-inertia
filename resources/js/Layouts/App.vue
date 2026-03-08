<script setup>
    import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div>
        <!-- Nav -->
        <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
            <div class="container">
                <Link class="navbar-brand" href="/">LARAVEL + VUE + INERTIA.JS</Link>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <li class="nav-item">
                    <Link class="nav-link" href="/posts/">POSTS</Link>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="https://santrikoding.com/ebook" target="_blank">EBOOK</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="https://santrikoding.com/tutorial-set" target="_blank">TUTORIAL SET</a>
                    </li>
                </ul>
                <form class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-success" type="submit">Search</button>
                </form>
                </div>
            </div>
        </nav>
    <!-- Content -->
    <main class="container mt-5">
      <slot />
    </main>

    <!-- Footer -->
    <!-- <footer class="footer mt-auto py-3 bg-light">
        <div class="container">
            <span class="text-muted">Place sticky footer content here.</span>
        </div>
    </footer> -->
  </div>
</template>

<style scoped>
    .mt-5 {
        margin-top: 100px!important;
    }
</style>