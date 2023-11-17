import { createRouter, createWebHistory } from "vue-router";

import AuthenticatedLayout from "@/layouts/Authenticated.vue";
import GuestLayout from "@/layouts/Guest.vue";

import PostsIndex from "@/components/posts/Index.vue";
import PostsCreate from "@/components/posts/Create.vue";
import PostsEdit from "@/components/posts/Edit.vue";
import Login from "@/components/auth/Login.vue";
import Register from "@/components/auth/Register.vue";

const routes = [
    {
        component: GuestLayout,
        children: [
            {
                path: "/login",
                name: "login",
                component: Login,
            },
            {
                path: "/register",
                name: "register",
                component: Register,
            },
        ],
    },
    {
        component: AuthenticatedLayout,
        children: [
            {
                path: "/posts",
                name: "posts.index",
                component: PostsIndex,
                meta: { title: "Posts" },
            },
            {
                path: "/posts/create",
                name: "posts.create",
                component: PostsCreate,
                meta: { title: "Add new post" },
            },
            {
                path: "/posts/edit/:id",
                name: "posts.edit",
                component: PostsEdit,
                meta: { title: "Edit post" },
            },
        ],
    },
];

export default createRouter({
    history: createWebHistory(),
    routes,
});
