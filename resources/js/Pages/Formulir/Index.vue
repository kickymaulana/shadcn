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
    IconEye,
    IconSearch,
    IconX,
    IconFileDescription,
    IconClipboardList,
} from "@tabler/icons-vue";
import { ref, watch } from "vue";

// 1. Definisikan Persistent Layout
defineOptions({ layout: AuthenticatedLayout });

// 2. Definisi Props
const props = defineProps<{
    list_formulir: {
        data: Array<{
            id: number;
            sampel: { kode_sample: string; customer: string };
            size: string;
            qty_sampel_kirim: number;
            running_ke: number;
            tanggal_permintaan: string;
            status: "Draft" | "Proses" | "Selesai" | "Ditolak";
        }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
        from: number;
        to: number;
        total: number;
    };
    filters: { search: string };
}>();

// 3. Logika Pencarian (Search)
const search = ref(props.filters.search || "");

let timeout: ReturnType<typeof setTimeout>;
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route("formulirs.index"),
            { search: value },
            { preserveState: true, replace: true },
        );
    }, 500);
});

const clearSearch = () => {
    search.value = "";
};

// 4. Helper Formatter & Styling
const getStatusVariant = (status: string) => {
    switch (status) {
        case "Selesai":
            return "default"; // Hitam/Primary
        case "Proses":
            return "secondary"; // Abu-abu/Biru
        case "Ditolak":
            return "destructive"; // Merah
        default:
            return "outline"; // Draft (Transparan/Border)
    }
};

const formatDate = (dateString: string) => {
    if (!dateString) return "-";
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
    <Head title="Daftar Formulir Permintaan" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6"
            >
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-primary/10 rounded-lg">
                        <IconClipboardList class="size-5 text-primary" />
                    </div>
                    <CardTitle
                        class="text-xl font-bold uppercase italic tracking-tight"
                    >
                        Daftar Formulir
                    </CardTitle>
                </div>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <IconSearch
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari kode atau customer..."
                            class="flex h-10 w-full rounded-md border border-input bg-background pl-10 pr-10 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        />
                        <button
                            v-if="search"
                            @click="clearSearch"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                        >
                            <IconX class="size-4" />
                        </button>
                    </div>

                    <Button as-child shadow-sm>
                        <Link :href="route('formulirs.create')">
                            <IconFileDescription class="mr-2 size-4" />
                            <span class="hidden sm:inline">Buat Formulir</span>
                        </Link>
                    </Button>
                </div>
            </CardHeader>

            <CardContent>
                <div class="rounded-md border bg-card">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50 hover:bg-muted/50">
                                <TableHead class="w-[100px]">Status</TableHead>
                                <TableHead>Kode Sample</TableHead>
                                <TableHead>Customer</TableHead>
                                <TableHead>Size / Qty</TableHead>
                                <TableHead>Tgl Permintaan</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="list_formulir.data.length === 0">
                                <TableCell
                                    colspan="6"
                                    class="h-24 text-center text-muted-foreground italic"
                                >
                                    Tidak ada data formulir yang ditemukan.
                                </TableCell>
                            </TableRow>

                            <TableRow
                                v-for="item in list_formulir.data"
                                :key="item.id"
                                class="group"
                            >
                                <TableCell>
                                    <Badge
                                        :variant="getStatusVariant(item.status)"
                                        class="uppercase text-[10px] font-bold"
                                    >
                                        {{ item.status }}
                                    </Badge>
                                </TableCell>
                                <TableCell
                                    class="font-mono text-xs font-bold text-primary"
                                >
                                    {{ item.sampel.kode_sample }}
                                </TableCell>
                                <TableCell class="font-medium">
                                    {{ item.sampel.customer }}
                                </TableCell>
                                <TableCell>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold">{{
                                            item.size
                                        }}</span>
                                        <span
                                            class="text-[10px] text-muted-foreground"
                                        >
                                            {{ item.qty_sampel_kirim }} Pcs
                                            (Run: {{ item.running_ke }})
                                        </span>
                                    </div>
                                </TableCell>
                                <TableCell class="text-sm">
                                    {{ formatDate(item.tanggal_permintaan) }}
                                </TableCell>
                                <TableCell class="text-right">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        as-child
                                        class="size-8"
                                    >
                                        <Link
                                            :href="
                                                route('formulirs.show', item.id)
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
                    <p class="text-xs text-muted-foreground font-medium">
                        Menampilkan {{ list_formulir.from ?? 0 }} -
                        {{ list_formulir.to ?? 0 }} dari
                        {{ list_formulir.total }} formulir
                    </p>

                    <nav class="flex items-center gap-1">
                        <template
                            v-for="(link, k) in list_formulir.links"
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
                                class="text-xs px-3 h-8 shadow-sm transition-all"
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
