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
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import {
    IconArrowLeft,
    IconPencil,
    IconTrash,
    IconHierarchy2,
    IconPlus,
    IconDotsVertical,
    IconInfoCircle,
    IconEye,
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
        pemeriksa: any;
        penyetuju: any;
        created_at: string;
        departemen_terlibat: Array<any>;
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

const getStatusBadge = (status: string) => {
    const s = status.toLowerCase();
    if (s === "draft") return "bg-slate-100 text-slate-700 border-slate-300";
    if (s === "selesai" || s === "approved")
        return "bg-green-100 text-green-700 border-green-300";
    if (s === "ditolak") return "bg-red-100 text-red-700 border-red-300";
    return "bg-blue-100 text-blue-700 border-blue-300";
};
</script>

<template>
    <Head :title="'Detail Formulir #' + formulir.id" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-4">
        <div class="flex items-center justify-between">
            <Button
                variant="ghost"
                size="sm"
                as-child
                class="-ml-2 text-muted-foreground hover:text-foreground"
            >
                <Link :href="route('formulirs.index')">
                    <IconArrowLeft class="mr-2 size-4" /> Kembali
                </Link>
            </Button>

            <div class="flex items-center gap-2">
                <Button variant="outline" size="sm" class="gap-2 shadow-sm border-primary/20 hover:bg-primary/5 text-primary" as-child>
                    <Link :href="route('formulirs.preview', formulir.id)">
                        <IconEye class="size-4" /> Preview Dokumen A4
                    </Link>
                </Button>

                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="ghost" size="icon" class="rounded-full">
                            <IconDotsVertical class="size-5" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-40">
                        <DropdownMenuItem as-child>
                            <Link :href="route('formulirs.edit', formulir.id)" class="flex items-center">
                                <IconPencil class="mr-2 size-4 text-orange-500" /> Edit Data
                            </Link>
                        </DropdownMenuItem>
                        <Separator class="my-1" />
                        <AlertDialog>
                            <AlertDialogTrigger as-child>
                                <div class="relative flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-destructive hover:text-destructive-foreground">
                                    <IconTrash class="mr-2 size-4" /> Hapus Data
                                </div>
                            </AlertDialogTrigger>
                            <AlertDialogContent>
                                <AlertDialogHeader>
                                    <AlertDialogTitle>Hapus Formulir?</AlertDialogTitle>
                                    <AlertDialogDescription>Data akan hilang permanen dari database SISAMCUS.</AlertDialogDescription>
                                </AlertDialogHeader>
                                <AlertDialogFooter>
                                    <AlertDialogCancel>Batal</AlertDialogCancel>
                                    <AlertDialogAction @click="deleteFormulir" class="bg-destructive">Ya, Hapus</AlertDialogAction>
                                </AlertDialogFooter>
                            </AlertDialogContent>
                        </AlertDialog>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>

        <Card class="border-none shadow-sm overflow-hidden">
            <CardHeader class="flex flex-row items-center gap-3 bg-muted/20 pb-4 border-b">
                <div class="p-2 bg-primary/10 rounded-lg">
                    <IconInfoCircle class="size-5 text-primary" />
                </div>
                <div>
                    <CardTitle class="text-lg font-black uppercase tracking-tight">Formulir #{{ formulir.id }}</CardTitle>
                    <CardDescription class="text-xs">Informasi Lengkap Pengajuan Sampel</CardDescription>
                </div>
            </CardHeader>
            <CardContent class="p-0">
                <Table>
                    <TableBody>
                        <TableRow class="hover:bg-transparent border-b">
                            <TableCell class="bg-muted/10 font-bold text-[10px] uppercase text-muted-foreground w-1/6">Status</TableCell>
                            <TableCell class="w-2/6">
                                <Badge :class="['uppercase font-black text-[9px] tracking-widest', getStatusBadge(formulir.status)]">
                                    {{ formulir.status }}
                                </Badge>
                            </TableCell>
                            <TableCell class="bg-muted/10 font-bold text-[10px] uppercase text-muted-foreground w-1/6">Kode Sample</TableCell>
                            <TableCell class="w-2/6 font-mono font-bold text-primary">{{ formulir.sampel.kode_sample }}</TableCell>
                        </TableRow>
                        <TableRow class="hover:bg-transparent border-b">
                            <TableCell class="bg-muted/10 font-bold text-[10px] uppercase text-muted-foreground">Running Ke-</TableCell>
                            <TableCell><Badge class="bg-black text-white">{{ formulir.running_ke }}</Badge></TableCell>
                            <TableCell class="bg-muted/10 font-bold text-[10px] uppercase text-muted-foreground">Customer</TableCell>
                            <TableCell class="font-semibold">{{ formulir.sampel.customer }}</TableCell>
                        </TableRow>
                        <TableRow class="hover:bg-transparent border-b">
                            <TableCell class="bg-muted/10 font-bold text-[10px] uppercase text-muted-foreground">Tgl Minta</TableCell>
                            <TableCell class="font-medium">{{ formatDate(formulir.tanggal_permintaan) }}</TableCell>
                            <TableCell class="bg-muted/10 font-bold text-[10px] uppercase text-muted-foreground">Model</TableCell>
                            <TableCell>{{ formulir.sampel.model }}</TableCell>
                        </TableRow>
                        <TableRow class="hover:bg-transparent border-b">
                            <TableCell class="bg-muted/10 font-bold text-[10px] uppercase text-muted-foreground">Pemeriksa (QC)</TableCell>
                            <TableCell class="uppercase text-xs font-semibold">{{ formulir.pemeriksa?.name || '-' }}</TableCell>
                            <TableCell class="bg-muted/10 font-bold text-[10px] uppercase text-muted-foreground">Size / Ukuran</TableCell>
                            <TableCell class="font-bold">{{ formulir.size }}</TableCell>
                        </TableRow>
                        <TableRow class="hover:bg-transparent">
                            <TableCell class="bg-muted/10 font-bold text-[10px] uppercase text-muted-foreground">Penyetuju (FM)</TableCell>
                            <TableCell class="uppercase text-xs font-semibold">{{ formulir.penyetuju?.name || '-' }}</TableCell>
                            <TableCell class="bg-muted/10 font-bold text-[10px] uppercase text-muted-foreground">Qty Kirim</TableCell>
                            <TableCell class="font-black text-primary">{{ formulir.qty_sampel_kirim }} Pcs</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>

        <Card class="border-none shadow-sm overflow-hidden mt-2">
            <CardHeader class="flex flex-row items-center justify-between bg-muted/30 py-4 border-b">
                <div class="space-y-1">
                    <CardTitle class="text-lg font-bold uppercase text-primary flex items-center gap-2">
                        <IconHierarchy2 class="size-6" /> Alur Proses
                    </CardTitle>
                    <CardDescription class="text-xs">Urutan pengerjaan sampel antar unit produksi.</CardDescription>
                </div>
                <Button size="sm" class="gap-2 shadow-md px-4" as-child>
                    <Link :href="route('formulirs.departemen.create', formulir.id)">
                        <IconPlus class="size-4" /> Tambah Departemen
                    </Link>
                </Button>
            </CardHeader>
            <CardContent class="p-0">
                <Table>
                    <TableHeader class="bg-muted/50 text-[10px] uppercase">
                        <TableRow>
                            <TableHead class="w-[60px] text-center">No</TableHead>
                            <TableHead>Departemen / Unit</TableHead>
                            <TableHead>Penerima</TableHead>
                            <TableHead class="text-center">Tgl Terima</TableHead>
                            <TableHead class="text-center">Tgl Selesai</TableHead>
                            <TableHead class="text-center">Qty</TableHead>
                            <TableHead class="text-center w-[100px]">Paraf QC</TableHead>
                            <TableHead class="text-center w-[100px]">Paraf SPV</TableHead>
                            <TableHead class="text-center w-[100px]">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="formulir.departemen_terlibat.length === 0">
                            <TableCell colspan="9" class="text-center py-16 text-muted-foreground italic">Belum ada data alur proses.</TableCell>
                        </TableRow>
                        <TableRow
                            v-for="(dept, index) in formulir.departemen_terlibat"
                            :key="dept.id"
                            class="hover:bg-muted/5 transition-colors"
                        >
                            <TableCell class="text-center font-bold text-muted-foreground/50 text-xs">{{ index + 1 }}</TableCell>
                            <TableCell>
                                <div class="font-bold text-sm uppercase leading-tight">{{ dept.nama_departemen }}</div>
                                <div class="text-[9px] text-muted-foreground tracking-tighter uppercase font-medium">Master Urutan: {{ dept.urutan }}</div>
                            </TableCell>
                            <TableCell class="text-xs">{{ dept.penerima }}</TableCell>
                            <TableCell class="text-center text-[11px] font-medium">{{ dept.tanggal_terima }}</TableCell>
                            <TableCell class="text-center text-[11px] font-medium">{{ dept.tanggal_selesai }}</TableCell>
                            <TableCell class="text-center"><Badge variant="outline" class="font-mono text-[10px]">{{ dept.qty }}</Badge></TableCell>
                            <TableCell class="text-xs text-center font-bold text-green-600">
                                <span v-if="dept.is_qc" class="flex items-center justify-center gap-1"><IconPlus class="size-3" rotate="45"/> {{ dept.nama_qc }}</span>
                                <span v-else class="text-slate-300">-</span>
                            </TableCell>
                            <TableCell class="text-xs text-center font-bold text-blue-600">
                                <span v-if="dept.is_spv" class="flex items-center justify-center gap-1"><IconPlus class="size-3" rotate="45"/> {{ dept.nama_spv }}</span>
                                <span v-else class="text-slate-300">-</span>
                            </TableCell>
                            <TableCell class="text-center">
                                <div class="flex justify-center gap-2">
                                    <Button variant="ghost" size="icon" as-child class="size-8 text-orange-500">
                                        <Link :href="route('formulirs.departemen.edit', { formulir: formulir.id, departemen_terlibat: dept.id })">
                                            <IconPencil class="size-4" />
                                        </Link>
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>
</template>
