<script setup>
import LayoutLogin from '@/Layouts/LayoutLogin.vue';
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import { Link, usePage } from '@inertiajs/vue3';
import { watch, onMounted } from 'vue';
import Swal from 'sweetalert2';

    defineProps({
        errors : Object
    });

    const post = reactive({
        email : '',
        password : ''
    })

    function store() {
        router.post('/auth/login/admin', post)
    }

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

    onMounted(() => {
        showFlashMessage();
    });

    watch(() => page.props.flash, () => {
        showFlashMessage();
    }, { deep: true });
</script>

<template>
    <LayoutLogin>
        <div class="position-relative min-vh-100 d-flex align-items-center">
            <!-- Background Image with Blur -->
            <div class="position-absolute w-100 h-100 top-0 start-0"
                style="background-image: url('/assets/img/Biro Abulyatama.webp'); background-size: cover; background-position: center; filter: blur(4px); z-index: 0;">
            </div>
            <!-- Dark Overlay -->
            <div class="position-absolute w-100 h-100 top-0 start-0 bg-gradient-dark opacity-6" style="z-index: 1;"></div>

            <!-- Login Form Content -->
            <div class="container my-auto position-relative p-4" style="z-index: 2;">
                <div class="row">
                    <div class="col-lg-4 col-md-8 col-12 mx-auto">
                        <div class="card z-index-0 fadeIn3 fadeInBottom">
                            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                                <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                                    <img src="/assets/img/Logo Unaya.png" alt=""
                                        style="width: 40px; height: 40px; display: block; margin: 0 auto;">
                                    <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Login Admin</h4>
                                    <h5 class="text-white font-weight-bolder text-center mt-2 mb-0">Sistem
                                        KKN Unaya</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <form @submit.prevent="store" role="form" class="text-start">
                                    <div class="input-group input-group-outline my-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" v-model="post.email">
                                    </div>
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" v-model="post.password">
                                    </div>
                                    <div class="form-check form-switch d-flex align-items-center mb-3">
                                        <input class="form-check-input" type="checkbox" id="rememberMe" checked>
                                        <label class="form-check-label mb-0 ms-3" for="rememberMe">Remember me</label>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Sign
                                            in</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </LayoutLogin>
</template>
