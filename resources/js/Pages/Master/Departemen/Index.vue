<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import {
    IconBuildingCommunity,
    IconEye,
    IconSearch,
    IconX,
    IconUsers,
} from "@tabler/icons-vue";
import { ref, watch } from "vue";

// 1. Definisikan Persistent Layout
defineOptions({ layout: AuthenticatedLayout });

// 2. Definisi Props
const props = defineProps<{
    list_departemen: {
        data: Array<{
            id: number;
            nama: string;
            users_count: number;
            created_at: string;
        }>;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
        from: number;
        to: number;
        total: number;
    };
    filters: {
        search: string;
    };
}>();

// 3. Logika Pencarian (Search)
const search = ref(props.filters.search || "");

let timeout: ReturnType<typeof setTimeout>;
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route("departemen.index"), // Pastikan nama route sesuai
            { search: value },
            { preserveState: true, replace: true },
        );
    }, 500);
});

const clearSearch = () => {
    search.value = "";
};

// 4. Helper Formatter
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
};

const cleanLabel = (label: string) => {
    if (label.includes("Previous")) return "Sebelumnya";
    if (label.includes("Next")) return "Selanjutnya";
    return label;
};
</script>

<template>
    <Head title="Manajemen Departemen" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6"
            >
                <CardTitle class="text-xl font-bold"
                    >Daftar Departemen</CardTitle
                >

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <IconSearch
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            placeholder="Cari departemen..."
                            class="pl-10 pr-10"
                        />
                        <button
                            v-if="search"
                            @click="clearSearch"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                        >
                            <IconX class="size-4" />
                        </button>
                    </div>

                    <Button as-child>
                        <Link :href="route('departemens.create')">
                            <IconBuildingCommunity class="mr-2 size-4" />
                            <span class="hidden sm:inline"
                                >Tambah Departemen</span
                            >
                        </Link>
                    </Button>
                </div>
            </CardHeader>

            <CardContent>
                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50">
                                <TableHead class="w-[50px]">ID</TableHead>
                                <TableHead>Nama Departemen</TableHead>
                                <TableHead>Jumlah Anggota</TableHead>
                                <TableHead>Tgl Dibuat</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="list_departemen.data.length === 0">
                                <TableCell
                                    colspan="5"
                                    class="h-24 text-center text-muted-foreground"
                                >
                                    Departemen tidak ditemukan.
                                </TableCell>
                            </TableRow>

                            <TableRow
                                v-for="dept in list_departemen.data"
                                :key="dept.id"
                            >
                                <TableCell
                                    class="text-muted-foreground font-mono text-xs"
                                    >#{{ dept.id }}</TableCell
                                >
                                <TableCell
                                    class="font-medium text-foreground italic"
                                >
                                    {{ dept.nama }}
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        variant="secondary"
                                        class="font-medium"
                                    >
                                        <IconUsers
                                            class="mr-1.5 size-3 text-muted-foreground"
                                        />
                                        {{ dept.users_count }} Anggota
                                    </Badge>
                                </TableCell>
                                <TableCell>{{
                                    formatDate(dept.created_at)
                                }}</TableCell>
                                <TableCell class="text-right space-x-1">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="size-8"
                                        as-child
                                        title="Edit"
                                    >
                                        <Link
                                            :href="
                                                route(
                                                    'departemens.show',
                                                    dept.id,
                                                )
                                            "
                                        >
                                            <IconEye
                                                class="size-4 text-primary"
                                            />
                                        </Link>
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div
                    class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6"
                >
                    <p class="text-xs text-muted-foreground">
                        Menampilkan {{ list_departemen.from ?? 0 }} -
                        {{ list_departemen.to ?? 0 }} dari
                        {{ list_departemen.total }} departemen
                    </p>

                    <nav class="flex items-center gap-1">
                        <template
                            v-for="(link, k) in list_departemen.links"
                            :key="k"
                        >
                            <Button
                                v-if="link.url === null"
                                variant="outline"
                                size="sm"
                                disabled
                                class="opacity-50 text-xs px-3 h-8"
                                v-html="cleanLabel(link.label)"
                            />
                            <Button
                                v-else
                                as-child
                                variant="outline"
                                size="sm"
                                class="text-xs px-3 h-8"
                                :class="{
                                    'bg-primary text-primary-foreground hover:bg-primary/90 hover:text-primary-foreground':
                                        link.active,
                                }"
                            >
                                <Link
                                    :href="link.url"
                                    v-html="cleanLabel(link.label)"
                                />
                            </Button>
                        </template>
                    </nav>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
