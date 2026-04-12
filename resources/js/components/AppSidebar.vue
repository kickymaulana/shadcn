<script setup lang="ts">
import {
    IconCamera,
    IconDatabase,
    IconFileAi,
    IconFileDescription,
    IconHelp,
    IconInnerShadowTop,
    IconPackage,
    IconReport,
    IconSearch,
    IconSettings,
    IconUsers,
    IconHierarchy,
    IconBuilding,
    IconGitMerge,
    IconClipboardList,
} from "@tabler/icons-vue";

import NavDocuments from "@/components/NavDocuments.vue";
import Master from "@/components/Master.vue";
import NavMain from "@/components/NavMain.vue";
import NavSecondary from "@/components/NavSecondary.vue";
import NavUser from "@/components/NavUser.vue";
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from "@/components/ui/sidebar";
import { Link } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";

const data = {
    user: {
        name: "shadcn",
        email: "m@example.com",
        avatar: "/avatars/shadcn.jpg",
    },
    navMain: [
        {
            title: "Sampel",
            url: route("samples.index"),
            icon: IconPackage,
            root: "Sample",
        },
        {
            title: "Formulir Permintaan",
            url: route("formulirs.index"),
            icon: IconFileDescription,
            root: "Formulir",
        },
        {
            title: "Tugas Produksi",
            url: route("tugas.produksi.index"),
            icon: IconClipboardList, // <--- Ini
            root: "TugasProduksi",
        },
    ],
    master: [
        {
            name: "Pengguna",
            url: route("users.index"),
            icon: IconUsers,
            root: "Master/Users",
        },
        {
            name: "Jabatan",
            url: route("roles.index"),
            icon: IconHierarchy,
            root: "Master/Roles",
        },
        {
            name: "Departemen",
            url: route("departemens.index"),
            icon: IconBuilding,
            root: "Master/Departemen",
        },
        {
            name: "Sub Departemen",
            url: route("sub.departemens.index"),
            icon: IconGitMerge,
            root: "Master/SubDepartemen",
        },
    ],
};

const user = usePage().props.auth.user;
</script>

<template>
    <Sidebar collapsible="offcanvas">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        as-child
                        class="data-[slot=sidebar-menu-button]:!p-1.5"
                    >
                        <Link
                            :href="route('dashboard')"
                            as="button"
                            class="w-full text-left"
                        >
                            <IconInnerShadowTop class="!size-5" />
                            <span>Sisamcus</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>
        <SidebarContent>
            <NavMain :items="data.navMain" />
            <Master :items="data.master" />
        </SidebarContent>
        <SidebarFooter>
            <NavUser :user="user" />
        </SidebarFooter>
    </Sidebar>
</template>
