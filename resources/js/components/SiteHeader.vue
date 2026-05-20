<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { Separator } from "@/components/ui/separator";
import { SidebarTrigger } from "@/components/ui/sidebar";
import { useDark, useToggle } from "@vueuse/core";
import { IconSun, IconMoon, IconBell } from "@tabler/icons-vue";
import { ref, computed } from "vue";
import { usePage, Link } from "@inertiajs/vue3";

// Logika dark mode: otomatis mendeteksi sistem & menambah/menghapus class 'dark'
const isDark = useDark();
const toggleDark = useToggle(isDark);

// Ambil shared props dari Inertia
const page = usePage();

const notificationCount = computed(() => page.props.auth.unreadNotificationsCount ?? 0);
</script>

<template>
    <header
        class="flex h-(--header-height) shrink-0 items-center gap-2 border-b transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-(--header-height)"
    >
        <div class="flex w-full items-center gap-1 px-4 lg:gap-2 lg:px-6">
            <SidebarTrigger class="-ml-1" />
            <Separator
                orientation="vertical"
                class="mx-2 data-[orientation=vertical]:h-4"
            />
            <div class="ml-auto flex items-center gap-2">
                <Button
                    variant="ghost"
                    as-child
                    size="sm"
                    class="hidden sm:flex"
                >
                    <a
                        href="#"
                        rel="noopener noreferrer"
                        target="_blank"
                        class="dark:text-foreground"
                    >
                        Sisamsul
                    </a>
                </Button>

                <!-- Tombol Notifikasi dengan Link Inertia -->
                <Button variant="ghost" size="icon" class="relative" as-child>
                    <Link :href="route('notifikasi.index')">
                        <IconBell class="size-5 text-fuchsia-600 dark:text-fuchsia-400" />

                        <!-- Badge Jumlah Notifikasi -->
                        <span
                            v-if="notificationCount > 0"
                            class="absolute -top-1 -right-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-medium text-white ring-2 ring-background"
                        >
                            {{ notificationCount }}
                        </span>
                        <span class="sr-only">Notifications</span>
                    </Link>
                </Button>
                <Button variant="ghost" size="icon" @click="toggleDark()">
                    <IconSun v-if="isDark" class="size-5 text-fuchsia-400" />
                    <IconMoon v-else class="size-5 text-fuchsia-600" />
                    <span class="sr-only">Toggle Theme</span>
                </Button>
            </div>
        </div>
    </header>
</template>
