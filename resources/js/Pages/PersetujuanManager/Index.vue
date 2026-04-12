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
    IconChecklist,
    IconEye,
    IconSearch,
    IconX,
    IconUserCheck,
} from "@tabler/icons-vue";
import { ref, watch } from "vue";

// 1. Definisikan Persistent Layout
defineOptions({ layout: AuthenticatedLayout });

// 2. Definisi Props
const props = defineProps<{
    list_persetujuan: {
        data: Array<{
            id: number;
            sampel: {
                kode_sample: string;
                customer: string;
            };
            size: string;
            running_ke: number;
            tanggal_permintaan: string;
            status: "Draft" | "Proses" | "Selesai" | "Ditolak";
            pemeriksa: { name: string } | null;
            penyetuju: { name: string } | null;
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
            route("persetujuan.manager.index"),
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
    if (!dateString) return "-";
    return new Date(dateString).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
};

const getStatusVariant = (status: string) => {
    switch (status) {
        case "Selesai":
            return "default"; // Hijau/Hitam
        case "Proses":
            return "secondary"; // Abu-abu/Biru
        case "Ditolak":
            return "destructive"; // Merah
        default:
            return "outline";
    }
};

const cleanLabel = (label: string) => {
    if (label.includes("Previous")) return "Sebelumnya";
    if (label.includes("Next")) return "Selanjutnya";
    return label;
};
</script>

<template>
    <Head title="Persetujuan Manager" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6"
            >
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconChecklist class="size-6 text-primary" />
                    Persetujuan Manager
                </CardTitle>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <IconSearch
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            placeholder="Cari kode atau customer..."
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
                </div>
            </CardHeader>

            <CardContent>
                <div class="rounded-md border overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50 whitespace-nowrap">
                                <TableHead>Sampel / Customer</TableHead>
                                <TableHead>Size / Run</TableHead>
                                <TableHead>Tgl Permintaan</TableHead>
                                <TableHead class="text-center"
                                    >Status</TableHead
                                >
                                <TableHead>Diperiksa Oleh</TableHead>
                                <TableHead>Disetujui Oleh</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="list_persetujuan.data.length === 0">
                                <TableCell
                                    colspan="7"
                                    class="h-24 text-center text-muted-foreground italic"
                                >
                                    Tidak ada data persetujuan ditemukan.
                                </TableCell>
                            </TableRow>

                            <TableRow
                                v-for="form in list_persetujuan.data"
                                :key="form.id"
                                class="whitespace-nowrap"
                            >
                                <TableCell>
                                    <div class="font-bold text-primary">
                                        {{ form.sampel.kode_sample }}
                                    </div>
                                    <div
                                        class="text-[10px] text-muted-foreground uppercase font-medium"
                                    >
                                        {{ form.sampel.customer }}
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div class="font-medium">
                                        {{ form.size }}
                                    </div>
                                    <div
                                        class="text-[10px] text-muted-foreground"
                                    >
                                        Running Ke-{{ form.running_ke }}
                                    </div>
                                </TableCell>
                                <TableCell>
                                    {{ formatDate(form.tanggal_permintaan) }}
                                </TableCell>
                                <TableCell class="text-center">
                                    <Badge
                                        :variant="getStatusVariant(form.status)"
                                        class="font-bold"
                                    >
                                        {{ form.status }}
                                    </Badge>
                                </TableCell>
                                <TableCell
                                    class="text-xs italic text-muted-foreground"
                                >
                                    {{ form.pemeriksa?.name ?? "-" }}
                                </TableCell>
                                <TableCell
                                    class="text-xs italic text-muted-foreground"
                                >
                                    {{ form.penyetuju?.name ?? "-" }}
                                </TableCell>
                                <TableCell class="text-right">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="size-8"
                                        as-child
                                        title="Detail Persetujuan"
                                    >
                                        <Link
                                            :href="
                                                route(
                                                    'persetujuan.manager.show',
                                                    form.id,
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
                        Menampilkan {{ list_persetujuan.from ?? 0 }} -
                        {{ list_persetujuan.to ?? 0 }} dari
                        {{ list_persetujuan.total }} data
                    </p>

                    <nav class="flex items-center gap-1">
                        <template
                            v-for="(link, k) in list_persetujuan.links"
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
                                    'bg-primary text-primary-foreground':
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
