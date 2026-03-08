<script setup>
    import Layout from '../../Layouts/App.vue';

    import { Link } from '@inertiajs/vue3';

    import { router } from '@inertiajs/vue3'

    defineProps({
        posts: Array
    })

    function deletePost(id) {
        router.delete(`/posts/${id}`)
    }
</script>

<template>
    <Layout>
        <div class="mt-5">
            <!-- flash message -->
            <div v-if="$page.props.flash.message" class="alert alert-success" role="alert">
                {{ $page.props.flash.message }}
            </div>
            <!-- flash message -->
            <div class="mb-3">
                <Link href="/posts/create" class="btn btn-md btn-primary">TAMBAH DATA</Link>
            </div>
            <div class="card border-0 rounded shadow-sm">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">TITLE</th>
                                <th scope="col">CONTENT</th>
                                <th scope="col">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="post in posts" :key="post.id" >
                                <td>{{ post.title }}</td>
                                <td>{{ post.content }}</td>
                                <td class="text-center">
                                    <Link :href="`/posts/${post.id}/edit`" class="btn btn-warning btn-sm me-2">EDIT</Link>
                                    <button @click.prevent="deletePost(`${post.id}`)" class="btn btn-danger btn-sm me-2">DELETE</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </Layout>
</template>