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
    IconClipboardList,
    IconPencil,
    IconSearch,
    IconX,
} from "@tabler/icons-vue";
import { ref, watch } from "vue";

// 1. Definisikan Persistent Layout
defineOptions({ layout: AuthenticatedLayout });

// 2. Definisi Props sesuai data dari Controller
const props = defineProps<{
    tugas_list: {
        data: Array<{
            id: number;
            formulir: {
                id: number;
                size: string;
                running_ke: number;
                sampel: {
                    kode_sample: string;
                };
            };
            sub_departemen: {
                nama: string;
            };
            penerima: { name: string } | null;
            tanggal_diterima: string | null;
            qty: number;
            qc_user: { name: string } | null;
            spv_user: { name: string } | null;
            is_qc: boolean;
            is_spv: boolean;
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
            route("tugas.produksi.index"),
            { search: value },
            { preserveState: true, replace: true },
        );
    }, 500);
});

const clearSearch = () => {
    search.value = "";
};

// 4. Helper Formatter
const formatDate = (dateString: string | null) => {
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
    <Head title="Tugas Produksi" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm overflow-hidden">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6"
            >
                <CardTitle
                    class="text-xl font-black uppercase tracking-tight flex items-center gap-2"
                >
                    <IconClipboardList class="size-6 text-primary" />
                    Tugas Produksi
                </CardTitle>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <IconSearch
                            class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            placeholder="Cari Kode Sample..."
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
                            <TableRow
                                class="bg-muted/50 text-[10px] uppercase whitespace-nowrap"
                            >
                                <TableHead class="font-bold"
                                    >Kode Sample</TableHead
                                >
                                <TableHead class="font-bold"
                                    >Model</TableHead
                                >
                                <TableHead class="font-bold">Size</TableHead>
                                <TableHead class="font-bold">Sub</TableHead>
                                <TableHead class="font-bold text-center"
                                    >Run Ke</TableHead
                                >
                                <TableHead class="font-bold text-center"
                                    >Qty</TableHead
                                >
                                <TableHead class="font-bold"
                                    >Diterima Oleh</TableHead
                                >
                                <TableHead class="font-bold"
                                    >Tgl Terima</TableHead
                                >
                                <TableHead class="font-bold"
                                    >Paraf QC</TableHead
                                >
                                <TableHead class="font-bold"
                                    >Paraf SPV</TableHead
                                >
                                <TableHead class="font-bold"
                                    >Tgl Selesai</TableHead
                                >
                                <TableHead class="text-right font-bold"
                                    >Aksi</TableHead
                                >
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="tugas_list.data.length === 0">
                                <TableCell
                                    colspan="9"
                                    class="h-24 text-center text-muted-foreground italic"
                                >
                                    Belum ada tugas produksi untuk departemen
                                    anda.
                                </TableCell>
                            </TableRow>

                            <TableRow
                                v-for="tugas in tugas_list.data"
                                :key="tugas.id"
                                class="whitespace-nowrap hover:bg-muted/30 transition-colors"
                            >
                                <TableCell
                                    class="font-mono font-bold text-primary italic"
                                >
                                    {{ tugas.formulir.sampel.kode_sample }}
                                </TableCell>
                                <TableCell
                                    class="font-mono font-bold text-primary italic"
                                >
                                    {{ tugas.formulir.sampel.model }}
                                </TableCell>

                                <TableCell class="font-medium">
                                    {{ tugas.formulir.size }}
                                </TableCell>
                                <TableCell class="font-medium">
                                    <Badge variant="outline" class="bg-blue-50 text-blue-700 border-blue-200 uppercase text-[10px] font-bold">
                                        {{ tugas.sub_departemen?.nama }}
                                    </Badge>
                                </TableCell>

                                <TableCell class="text-center">
                                    <Badge
                                        variant="secondary"
                                        class="font-bold"
                                    >
                                        {{ tugas.formulir.running_ke }}
                                    </Badge>
                                </TableCell>

                                <TableCell
                                    class="text-center font-black text-sm"
                                >
                                    {{ tugas.qty }}
                                </TableCell>

                                <TableCell
                                    class="text-xs font-medium uppercase"
                                >
                                    {{ tugas.penerima?.name ?? "-" }}
                                </TableCell>

                                <TableCell class="text-xs">
                                    {{ formatDate(tugas.tanggal_diterima) }}
                                </TableCell>

                                <TableCell>
                                    <div class="flex flex-col gap-0.5">
                                        <span
                                            v-if="tugas.qc_user"
                                            class="text-[10px] font-bold text-green-600 uppercase"
                                        >
                                            {{ tugas.qc_user.name }}
                                        </span>
                                        <span
                                            v-else
                                            class="text-[10px] text-muted-foreground/40 italic"
                                            >Belum Paraf</span
                                        >
                                    </div>
                                </TableCell>

                                <TableCell>
                                    <div class="flex flex-col gap-0.5">
                                        <span
                                            v-if="tugas.spv_user"
                                            class="text-[10px] font-bold text-blue-600 uppercase"
                                        >
                                            {{ tugas.spv_user.name }}
                                        </span>
                                        <span
                                            v-else
                                            class="text-[10px] text-muted-foreground/40 italic"
                                            >Belum Paraf</span
                                        >
                                    </div>
                                </TableCell>

                                <TableCell class="text-xs">
                                    {{ formatDate(tugas.tanggal_selesai) }}
                                </TableCell>

                                <TableCell class="text-right">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="size-8 rounded-full hover:bg-orange-100"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                route(
                                                    'tugas.produksi.edit',
                                                    tugas.id,
                                                )
                                            "
                                        >
                                            <IconPencil
                                                class="size-4 text-orange-500"
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
                        Menampilkan {{ tugas_list.from ?? 0 }} -
                        {{ tugas_list.to ?? 0 }} dari
                        {{ tugas_list.total }} data
                    </p>
                    <nav class="flex items-center gap-1">
                        <template
                            v-for="(link, k) in tugas_list.links"
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
