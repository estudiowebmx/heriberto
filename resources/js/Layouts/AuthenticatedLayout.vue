<script setup>
import { ref } from "vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-white">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <img
                                        src="/img/perfil.jpg"
                                        alt="Jasmin Bugarin"
                                        class="h-9 w-9 rounded-xl object-cover shadow-sm"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    v-if="$page.props.auth.user?.role === 'admin'"
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    <i class="fa-solid fa-chart-line mr-1.5"></i> Avances
                                </NavLink>
                                <NavLink
                                    v-if="
                                        $page.props.auth.user?.role === 'admin'
                                    "
                                    :href="route('admin.info.index')"
                                    :active="route().current('admin.info.*')"
                                >
                                    <i class="fa-solid fa-circle-info mr-1.5"></i> Información
                                </NavLink>
                                <NavLink
                                    v-if="
                                        $page.props.auth.user?.role === 'admin'
                                    "
                                    :href="route('admin.agenda.index')"
                                    :active="route().current('admin.agenda.*')"
                                >
                                    <i class="fa-solid fa-calendar-days mr-1.5"></i> Agenda
                                </NavLink>
                                <NavLink
                                    :href="route('admin.personas.index')"
                                    :active="
                                        route().current('admin.personas.*')
                                    "
                                >
                                    <i class="fa-solid fa-address-book mr-1.5"></i> Personas
                                </NavLink>
                                <NavLink
                                    v-if="
                                        $page.props.auth.user?.role === 'admin'
                                    "
                                    :href="route('admin.encuestas.index')"
                                    :active="
                                        route().current('admin.encuestas.*')
                                    "
                                >
                                    <i class="fa-solid fa-clipboard-question mr-1.5"></i> Conóceme
                                </NavLink>
                                <NavLink
                                    :href="route('admin.denuncias.index')"
                                    :active="
                                        route().current('admin.denuncias.*')
                                    "
                                >
                                    <i class="fa-solid fa-gavel mr-1.5"></i> Denuncias
                                </NavLink>
                                <NavLink
                                    v-if="
                                        $page.props.auth.user?.role === 'admin'
                                    "
                                    :href="route('admin.users.index')"
                                    :active="route().current('admin.users.*')"
                                >
                                    <i class="fa-solid fa-user-gear mr-1.5"></i> Usuarios
                                </NavLink>
                                <NavLink
                                    v-if="
                                        $page.props.auth.user?.role === 'admin'
                                    "
                                    :href="route('mapa.index')"
                                    :active="route().current('mapa.*')"
                                >
                                    <i class="fa-solid fa-map mr-1.5"></i> Mapa
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            v-if="$page.props.auth.user?.role === 'admin'"
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            <i class="fa-solid fa-chart-line mr-1.5"></i> Avances
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('admin.info.index')"
                            :active="route().current('admin.info.*')"
                        >
                            <i class="fa-solid fa-circle-info mr-1.5"></i> Información
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('admin.agenda.index')"
                            :active="route().current('admin.agenda.*')"
                        >
                            <i class="fa-solid fa-calendar-days mr-1.5"></i> Agenda
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('admin.personas.index')"
                            :active="route().current('admin.personas.*')"
                        >
                            <i class="fa-solid fa-address-book mr-1.5"></i> Personas
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('admin.encuestas.index')"
                            :active="route().current('admin.encuestas.*')"
                        >
                            <i class="fa-solid fa-clipboard-question mr-1.5"></i> Conóceme
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('admin.denuncias.index')"
                            :active="route().current('admin.denuncias.*')"
                        >
                            <i class="fa-solid fa-gavel mr-1.5"></i> Denuncias
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="$page.props.auth.user?.role === 'admin'"
                            :href="route('admin.users.index')"
                            :active="route().current('admin.users.*')"
                        >
                            <i class="fa-solid fa-user-gear mr-1.5"></i> Usuarios
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
