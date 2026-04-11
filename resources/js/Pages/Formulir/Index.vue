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
    IconEye,
    IconSearch,
    IconX,
    IconFileDescription,
} from "@tabler/icons-vue";
import { ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

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

const getStatusVariant = (status: string) => {
    switch (status) {
        case "Selesai":
            return "default"; // Hijau/Hitam
        case "Proses":
            return "secondary"; // Abu/Biru
        case "Ditolak":
            return "destructive"; // Merah
        default:
            return "outline"; // Draft
    }
};

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
    <Head title="Daftar Formulir" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6"
            >
                <CardTitle class="text-xl font-bold italic"
                    >Daftar Formulir Permintaan</CardTitle
                >

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
                            @click="search = ''"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground"
                        >
                            <IconX class="size-4" />
                        </button>
                    </div>

                    <Button as-child>
                        <Link :href="route('samples.index')">
                            <IconFileDescription class="mr-2 size-4" />
                            Buat Baru
                        </Link>
                    </Button>
                </div>
            </CardHeader>

            <CardContent>
                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50">
                                <TableHead>Status</TableHead>
                                <TableHead>Kode Sample</TableHead>
                                <TableHead>Customer</TableHead>
                                <TableHead>Size / Qty</TableHead>
                                <TableHead>Tgl Permintaan</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="form in list_formulir.data"
                                :key="form.id"
                            >
                                <TableCell>
                                    <Badge
                                        :variant="getStatusVariant(form.status)"
                                        >{{ form.status }}</Badge
                                    >
                                </TableCell>
                                <TableCell
                                    class="font-mono text-xs font-bold"
                                    >{{ form.sampel.kode_sample }}</TableCell
                                >
                                <TableCell>{{
                                    form.sampel.customer
                                }}</TableCell>
                                <TableCell>
                                    <span class="text-sm font-medium">{{
                                        form.size
                                    }}</span>
                                    <span
                                        class="text-xs text-muted-foreground ml-1"
                                        >({{ form.qty_sampel_kirim }} pcs)</span
                                    >
                                </TableCell>
                                <TableCell>{{
                                    formatDate(form.tanggal_permintaan)
                                }}</TableCell>
                                <TableCell class="text-right">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        as-child
                                    >
                                        <Link
                                            :href="
                                                route('formulirs.show', form.id)
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
            </CardContent>
        </Card>
    </div>
</template>
