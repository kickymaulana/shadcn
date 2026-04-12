<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Separator } from "@/components/ui/separator";
import { Badge } from "@/components/ui/badge";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import {
    IconArrowLeft,
    IconPencil,
    IconFileDescription,
    IconCalendar,
    IconFingerprint,
    IconTrash,
    IconTruckDelivery,
    IconHierarchy2,
    IconCheck,
    IconX,
    IconPlus,
    IconClipboardCheck,
    IconUserCheck,
} from "@tabler/icons-vue";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/components/ui/alert-dialog";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    formulir: {
        id: number;
        sampel: {
            id: number;
            kode_sample: string;
            customer: string;
            model: string;
        };
        size: string;
        qty_sampel_kirim: number;
        running_ke: number;
        tanggal_permintaan: string;
        status: string;
        pemeriksa: string;
        penyetuju: string;
        created_at: string;
        updated_at: string;
        departemen_terlibat: Array<{
            id: number;
            urutan: number | string;
            nama_departemen: string;
            penerima: string;
            tanggal_terima: string;
            tanggal_selesai: string;
            qty: number;
            is_qc: boolean;
            is_spv: boolean;
        }>;
    };
}>();

const deleteFormulir = () => {
    router.delete(route("formulirs.destroy", props.formulir.id));
};

const formatDate = (dateString: string) => {
    if (!dateString || dateString === "-") return "-";
    return new Date(dateString).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
};

// Fungsi untuk menentukan warna status
const getStatusBadge = (status: string) => {
    const s = status.toLowerCase();
    if (s === "draft") return "bg-slate-100 text-slate-700 border-slate-300";
    if (s === "selesai" || s === "approved")
        return "bg-green-100 text-green-700 border-green-300";
    return "bg-blue-100 text-blue-700 border-blue-300";
};
</script>

<template>
    <Head :title="'Detail Formulir #' + formulir.id" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-4">
        <div class="flex items-center justify-between gap-2">
            <Button
                variant="ghost"
                size="sm"
                as-child
                class="-ml-2 text-muted-foreground hover:text-foreground"
            >
                <Link :href="route('formulirs.index')">
                    <IconArrowLeft class="mr-2 size-4" />
                    Kembali ke Daftar Formulir
                </Link>
            </Button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <Card class="border-none shadow-sm col-span-1 h-fit">
                <CardContent class="pt-6 text-center">
                    <div
                        class="size-20 rounded-full bg-primary/10 flex items-center justify-center mb-4 mx-auto"
                    >
                        <IconFileDescription class="size-10 text-primary" />
                    </div>

                    <div
                        :class="[
                            'inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border mb-4 uppercase tracking-wider',
                            getStatusBadge(formulir.status),
                        ]"
                    >
                        {{ formulir.status }}
                    </div>

                    <h2 class="text-lg font-bold">
                        Formulir #{{ formulir.id }}
                    </h2>
                    <p class="text-sm text-muted-foreground italic mb-6">
                        Running ke-{{ formulir.running_ke }}
                    </p>

                    <div class="w-full flex flex-col gap-2">
                        <Button
                            class="w-full justify-start"
                            variant="outline"
                            as-child
                        >
                            <Link :href="route('formulirs.edit', formulir.id)">
                                <IconPencil class="mr-2 size-4" /> Edit Data
                                Utama
                            </Link>
                        </Button>
                        <AlertDialog>
                            <AlertDialogTrigger as-child>
                                <Button
                                    variant="destructive"
                                    class="w-full justify-start"
                                >
                                    <IconTrash class="mr-2 size-4" /> Hapus
                                    Formulir
                                </Button>
                            </AlertDialogTrigger>
                            <AlertDialogContent>
                                <AlertDialogHeader>
                                    <AlertDialogTitle
                                        >Hapus formulir ini?</AlertDialogTitle
                                    >
                                    <AlertDialogDescription
                                        >Tindakan ini permanen. Seluruh alur
                                        departemen terkait juga akan
                                        terhapus.</AlertDialogDescription
                                    >
                                </AlertDialogHeader>
                                <AlertDialogFooter>
                                    <AlertDialogCancel>Batal</AlertDialogCancel>
                                    <AlertDialogAction
                                        @click="deleteFormulir"
                                        class="bg-destructive"
                                        >Ya, Hapus</AlertDialogAction
                                    >
                                </AlertDialogFooter>
                            </AlertDialogContent>
                        </AlertDialog>
                    </div>

                    <Separator class="my-6" />

                    <div class="space-y-4 text-left text-sm px-2">
                        <div class="flex justify-between items-center">
                            <span
                                class="text-muted-foreground flex items-center gap-2"
                                ><IconCalendar class="size-4" />Tgl Minta</span
                            >
                            <span class="font-medium">{{
                                formatDate(formulir.tanggal_permintaan)
                            }}</span>
                        </div>
                        <div
                            class="flex justify-between items-center pt-2 border-t border-dashed"
                        >
                            <span
                                class="text-muted-foreground flex items-center gap-2"
                                ><IconFingerprint
                                    class="size-4"
                                />Pemeriksa</span
                            >
                            <span class="font-medium text-xs">{{
                                formulir.pemeriksa
                            }}</span>
                        </div>
                        <div
                            class="flex justify-between items-center pt-2 border-t border-dashed"
                        >
                            <span
                                class="text-muted-foreground flex items-center gap-2"
                                ><IconUserCheck
                                    class="size-4 text-primary"
                                />Penyetuju</span
                            >
                            <span class="font-medium text-xs">{{
                                formulir.penyetuju
                            }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <div class="lg:col-span-2 space-y-6">
                <Card class="border-none shadow-sm">
                    <CardHeader
                        class="flex flex-row items-center justify-between pb-2"
                    >
                        <CardTitle
                            class="text-md font-bold uppercase text-primary flex items-center gap-2"
                        >
                            <IconTruckDelivery class="size-5" /> Ringkasan
                            Pengiriman
                        </CardTitle>
                    </CardHeader>
                    <Separator />
                    <CardContent
                        class="pt-6 grid grid-cols-2 md:grid-cols-4 gap-6"
                    >
                        <div class="space-y-1">
                            <label
                                class="text-[10px] font-bold text-muted-foreground uppercase"
                                >Size</label
                            >
                            <p class="text-lg font-semibold">
                                {{ formulir.size }}
                            </p>
                        </div>
                        <div class="space-y-1 border-r pr-4">
                            <label
                                class="text-[10px] font-bold text-muted-foreground uppercase"
                                >Qty Kirim</label
                            >
                            <p class="text-lg font-semibold text-primary">
                                {{ formulir.qty_sampel_kirim }} Pcs
                            </p>
                        </div>
                        <div class="space-y-1 col-span-2 pl-2">
                            <label
                                class="text-[10px] font-bold text-muted-foreground uppercase"
                                >Master Sample Reference</label
                            >
                            <p class="text-sm font-bold font-mono">
                                {{ formulir.sampel.kode_sample }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ formulir.sampel.customer }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm bg-muted/20">
                    <CardContent class="py-4 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-background rounded-lg shadow-sm">
                                <IconClipboardCheck
                                    class="size-5 text-primary"
                                />
                            </div>
                            <div>
                                <p
                                    class="text-xs font-bold text-muted-foreground uppercase"
                                >
                                    Model Sample
                                </p>
                                <p class="text-sm font-medium">
                                    {{ formulir.sampel.model }}
                                </p>
                            </div>
                        </div>
                        <Button variant="link" size="sm" as-child>
                            <Link
                                :href="
                                    route('samples.show', formulir.sampel.id)
                                "
                                >Detail Master Sample →</Link
                            >
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </div>

        <Card class="border-none shadow-sm overflow-hidden mt-2">
            <CardHeader
                class="flex flex-row items-center justify-between bg-muted/30 py-4"
            >
                <div class="space-y-1">
                    <CardTitle
                        class="text-lg font-bold uppercase text-primary flex items-center gap-2"
                    >
                        <IconHierarchy2 class="size-6" />
                        Alur Proses Departemen
                    </CardTitle>
                    <CardDescription
                        >Daftar urutan pengerjaan sampel di setiap
                        departemen.</CardDescription
                    >
                </div>
                <Button size="default" class="gap-2 shadow-md" as-child>
                    <Link
                        :href="
                            route('formulirs.departemen.create', formulir.id)
                        "
                    >
                        <IconPlus class="size-4" />
                        Tambah Departemen
                    </Link>
                </Button>
            </CardHeader>
            <CardContent class="p-0">
                <Table>
                    <TableHeader class="bg-muted/50">
                        <TableRow>
                            <TableHead class="w-[60px] text-center"
                                >No</TableHead
                            >
                            <TableHead>Departemen / Unit</TableHead>
                            <TableHead>Penerima</TableHead>
                            <TableHead class="text-center"
                                >Tgl Terima</TableHead
                            >
                            <TableHead class="text-center"
                                >Tgl Selesai</TableHead
                            >
                            <TableHead class="text-center">Qty</TableHead>
                            <TableHead class="text-center w-[100px]"
                                >Paraf QC</TableHead
                            >
                            <TableHead class="text-center w-[100px]"
                                >Paraf SPV</TableHead
                            >
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-if="formulir.departemen_terlibat.length === 0"
                        >
                            <TableCell
                                colspan="8"
                                class="text-center py-16 text-muted-foreground italic"
                            >
                                Belum ada departemen yang terlibat dalam proses
                                ini.
                            </TableCell>
                        </TableRow>
                        <TableRow
                            v-for="(
                                dept, index
                            ) in formulir.departemen_terlibat"
                            :key="dept.id"
                            class="hover:bg-muted/30 transition-colors"
                        >
                            <TableCell
                                class="text-center font-bold text-muted-foreground/50"
                            >
                                {{ index + 1 }}
                            </TableCell>
                            <TableCell>
                                <div
                                    class="font-bold text-sm uppercase leading-tight"
                                >
                                    {{ dept.nama_departemen }}
                                </div>
                                <div
                                    class="text-[9px] text-muted-foreground tracking-tighter uppercase"
                                >
                                    Urutan Master: {{ dept.urutan }}
                                </div>
                            </TableCell>
                            <TableCell class="text-sm">{{
                                dept.penerima
                            }}</TableCell>
                            <TableCell
                                class="text-center text-sm font-medium"
                                >{{
                                    formatDate(dept.tanggal_terima)
                                }}</TableCell
                            >
                            <TableCell
                                class="text-center text-sm font-medium"
                                >{{
                                    formatDate(dept.tanggal_selesai)
                                }}</TableCell
                            >
                            <TableCell class="text-center">
                                <Badge
                                    variant="outline"
                                    class="font-mono bg-background"
                                    >{{ dept.qty }}</Badge
                                >
                            </TableCell>
                            <TableCell class="text-center">
                                <div class="flex justify-center">
                                    <div
                                        v-if="dept.is_qc"
                                        class="bg-green-100 p-1 rounded-full"
                                    >
                                        <IconCheck
                                            class="size-3.5 text-green-600"
                                        />
                                    </div>
                                    <IconX
                                        v-else
                                        class="size-4 text-muted-foreground/20"
                                    />
                                </div>
                            </TableCell>
                            <TableCell class="text-center">
                                <div class="flex justify-center">
                                    <div
                                        v-if="dept.is_spv"
                                        class="bg-green-100 p-1 rounded-full"
                                    >
                                        <IconCheck
                                            class="size-3.5 text-green-600"
                                        />
                                    </div>
                                    <IconX
                                        v-else
                                        class="size-4 text-muted-foreground/20"
                                    />
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>
</template>
