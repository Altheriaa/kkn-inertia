<script setup>

    import Layout from '../../Layouts/App.vue';
    import { router } from '@inertiajs/vue3';
    import { reactive } from 'vue'

    // get props "post" dan "errors" dari controller
    const props = defineProps({
        post: Object,
        errors: Object
    })

    const post = reactive({
        title : props.post.title,
        content : props.post.content
    })

    function updatePost() {
        router.put(`/posts/${props.post.id}`, post)
    }
</script>

<template>
    <Layout>
        <div class="card border-0 rounded shadow">
            <div class="card-body">
                <h4>EDIT POST</h4>
                <hr>
                <form @submit.prevent="updatePost">
                    <div class="mb-3">
                        <label class="form-label">TITLE POST</label>
                        <input type="text" class="form-control" v-model="post.title" placeholder="Masukkan Title Post">
                        <div v-if="errors.title" class="mt-2 alert alert-danger">
                            {{ errors.title }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CONTENT</label>
                        <textarea class="form-control" rows="5" v-model="post.content" placeholder="Masukkan Content Post"></textarea>
                        <div v-if="errors.content" class="mt-2 alert alert-danger">
                            {{ errors.content }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary btn-md shadow-sm me-2">UPDATE</button>
                        <button type="reset" class="btn btn-warning btn-md shadow-sm">RESET</button>
                    </div>
                </form>
            </div>
        </div>
    </Layout>
</template>