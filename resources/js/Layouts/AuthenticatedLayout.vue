<script setup>
import { ref } from "vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);
const expandedGeneral = ref(false);
const expandedChatBot = ref(false);
const expandedJuridico = ref(false);
const expandedTerritorio = ref(false);
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
                                <!-- General -->
                                <Dropdown width="48" align="left">
                                    <template #trigger>
                                        <button
                                            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none"
                                            :class="
                                                route().current('dashboard') ||
                                                route().current('admin.users.*')
                                                    ? 'border-indigo-400 text-gray-900 focus:border-indigo-700'
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300'
                                            "
                                        >
                                            <i class="fa-solid fa-layer-group mr-1.5"></i> General
                                        </button>
                                    </template>
                                    <template #content>
                                        <DropdownLink
                                            v-if="$page.props.auth.user?.role === 'admin'"
                                            :href="route('dashboard')"
                                        >
                                            <i class="fa-solid fa-chart-line mr-1.5"></i> Avances
                                        </DropdownLink>
                                        <DropdownLink
                                            v-if="$page.props.auth.user?.role === 'admin'"
                                            :href="route('admin.users.index')"
                                        >
                                            <i class="fa-solid fa-user-gear mr-1.5"></i> Usuarios
                                        </DropdownLink>
                                    </template>
                                </Dropdown>

                                <!-- ChatBot -->
                                <Dropdown width="48" align="left">
                                    <template #trigger>
                                        <button
                                            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none"
                                            :class="
                                                route().current('admin.info.*') ||
                                                route().current('admin.agenda.*')
                                                    ? 'border-indigo-400 text-gray-900 focus:border-indigo-700'
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300'
                                            "
                                        >
                                            <i class="fa-solid fa-robot mr-1.5"></i> ChatBot
                                        </button>
                                    </template>
                                    <template #content>
                                        <DropdownLink
                                            v-if="$page.props.auth.user?.role === 'admin'"
                                            :href="route('admin.info.index')"
                                        >
                                            <i class="fa-solid fa-circle-info mr-1.5"></i> Información
                                        </DropdownLink>
                                        <DropdownLink
                                            v-if="$page.props.auth.user?.role === 'admin'"
                                            :href="route('admin.agenda.index')"
                                        >
                                            <i class="fa-solid fa-calendar-days mr-1.5"></i> Agenda
                                        </DropdownLink>
                                    </template>
                                </Dropdown>

                                <!-- Jurídico -->
                                <Dropdown width="48" align="left">
                                    <template #trigger>
                                        <button
                                            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none"
                                            :class="
                                                route().current('admin.programa_conoceme.*') ||
                                                route().current('admin.denuncias.*')
                                                    ? 'border-indigo-400 text-gray-900 focus:border-indigo-700'
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300'
                                            "
                                        >
                                            <i class="fa-solid fa-scale-balanced mr-1.5"></i> Jurídico
                                        </button>
                                    </template>
                                    <template #content>
                                        <DropdownLink
                                            v-if="$page.props.auth.user?.role === 'admin'"
                                            :href="route('admin.programa_conoceme.index')"
                                        >
                                            <i class="fa-solid fa-clipboard-question mr-1.5"></i> Conóceme
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('admin.denuncias.index')"
                                        >
                                            <i class="fa-solid fa-gavel mr-1.5"></i> Denuncias
                                        </DropdownLink>
                                    </template>
                                </Dropdown>

                                <!-- Territorio -->
                                <Dropdown width="48" align="left">
                                    <template #trigger>
                                        <button
                                            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none"
                                            :class="
                                                route().current('mapa.*') ||
                                                route().current('admin.personas.*')
                                                    ? 'border-indigo-400 text-gray-900 focus:border-indigo-700'
                                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300'
                                            "
                                        >
                                            <i class="fa-solid fa-earth-americas mr-1.5"></i> Territorio
                                        </button>
                                    </template>
                                    <template #content>
                                        <DropdownLink
                                            v-if="$page.props.auth.user?.role === 'admin'"
                                            :href="route('mapa.index')"
                                        >
                                            <i class="fa-solid fa-map mr-1.5"></i> Mapa
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('admin.personas.index')"
                                        >
                                            <i class="fa-solid fa-address-book mr-1.5"></i> Personas
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
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
                        <!-- General -->
                        <button
                            @click="expandedGeneral = !expandedGeneral"
                            class="flex w-full items-center justify-between ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none"
                        >
                            <span><i class="fa-solid fa-layer-group mr-1.5"></i> General</span>
                            <i
                                class="fa-solid text-xs transition-transform"
                                :class="expandedGeneral ? 'fa-chevron-down' : 'fa-chevron-right'"
                            ></i>
                        </button>
                        <div v-show="expandedGeneral" class="space-y-1">
                            <ResponsiveNavLink
                                v-if="$page.props.auth.user?.role === 'admin'"
                                :href="route('dashboard')"
                                :active="route().current('dashboard')"
                            >
                                <i class="fa-solid fa-chart-line mr-1.5"></i> Avances
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                v-if="$page.props.auth.user?.role === 'admin'"
                                :href="route('admin.users.index')"
                                :active="route().current('admin.users.*')"
                            >
                                <i class="fa-solid fa-user-gear mr-1.5"></i> Usuarios
                            </ResponsiveNavLink>
                        </div>

                        <!-- ChatBot -->
                        <button
                            @click="expandedChatBot = !expandedChatBot"
                            class="flex w-full items-center justify-between ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none"
                        >
                            <span><i class="fa-solid fa-robot mr-1.5"></i> ChatBot</span>
                            <i
                                class="fa-solid text-xs transition-transform"
                                :class="expandedChatBot ? 'fa-chevron-down' : 'fa-chevron-right'"
                            ></i>
                        </button>
                        <div v-show="expandedChatBot" class="space-y-1">
                            <ResponsiveNavLink
                                v-if="$page.props.auth.user?.role === 'admin'"
                                :href="route('admin.info.index')"
                                :active="route().current('admin.info.*')"
                            >
                                <i class="fa-solid fa-circle-info mr-1.5"></i> Información
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                v-if="$page.props.auth.user?.role === 'admin'"
                                :href="route('admin.agenda.index')"
                                :active="route().current('admin.agenda.*')"
                            >
                                <i class="fa-solid fa-calendar-days mr-1.5"></i> Agenda
                            </ResponsiveNavLink>
                        </div>

                        <!-- Jurídico -->
                        <button
                            @click="expandedJuridico = !expandedJuridico"
                            class="flex w-full items-center justify-between ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none"
                        >
                            <span><i class="fa-solid fa-scale-balanced mr-1.5"></i> Jurídico</span>
                            <i
                                class="fa-solid text-xs transition-transform"
                                :class="expandedJuridico ? 'fa-chevron-down' : 'fa-chevron-right'"
                            ></i>
                        </button>
                        <div v-show="expandedJuridico" class="space-y-1">
                            <ResponsiveNavLink
                                v-if="$page.props.auth.user?.role === 'admin'"
                                :href="route('admin.programa_conoceme.index')"
                                :active="route().current('admin.programa_conoceme.*')"
                            >
                                <i class="fa-solid fa-clipboard-question mr-1.5"></i> Conóceme
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('admin.denuncias.index')"
                                :active="route().current('admin.denuncias.*')"
                            >
                                <i class="fa-solid fa-gavel mr-1.5"></i> Denuncias
                            </ResponsiveNavLink>
                        </div>

                        <!-- Territorio -->
                        <button
                            @click="expandedTerritorio = !expandedTerritorio"
                            class="flex w-full items-center justify-between ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none"
                        >
                            <span><i class="fa-solid fa-earth-americas mr-1.5"></i> Territorio</span>
                            <i
                                class="fa-solid text-xs transition-transform"
                                :class="expandedTerritorio ? 'fa-chevron-down' : 'fa-chevron-right'"
                            ></i>
                        </button>
                        <div v-show="expandedTerritorio" class="space-y-1">
                            <ResponsiveNavLink
                                v-if="$page.props.auth.user?.role === 'admin'"
                                :href="route('mapa.index')"
                                :active="route().current('mapa.*')"
                            >
                                <i class="fa-solid fa-map mr-1.5"></i> Mapa
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('admin.personas.index')"
                                :active="route().current('admin.personas.*')"
                            >
                                <i class="fa-solid fa-address-book mr-1.5"></i> Personas
                            </ResponsiveNavLink>
                        </div>
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
