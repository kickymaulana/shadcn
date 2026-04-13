<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
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
import {
    IconArrowLeft,
    IconFileTypePdf,
    IconCircleCheck,
    IconSignature,
    IconUserCheck,
    IconClock,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    sampel: any;
    formulir: any;
}>();

const page = usePage();
const userRoles: string[] = (page.props.auth as any).roles || [];

// Helper pengecekan hak akses (Role Spatie)
const canParafPemeriksa = () => userRoles.includes("QC Manager") || userRoles.includes("admin");
const canParafPenyetuju = () => userRoles.includes("Factory Manager") || userRoles.includes("admin");

const submitParaf = (routeName: string) => {
    router.post(
        route(routeName, { formulir: props.formulir.id }),
        {},
        {
            preserveScroll: true,
        },
    );
};

const formatDate = (dateString: string | null) => {
    if (!dateString) return "-";
    return new Date(dateString).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    });
};
</script>

<template>
    <Head :title="'Persetujuan - ' + sampel.kode_sample" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-4">
        <div class="flex items-center justify-between w-full max-w-[210mm] mx-auto">
            <Button
                variant="ghost"
                size="sm"
                as-child
                class="-ml-2 text-muted-foreground hover:text-foreground"
            >
                <Link :href="route('persetujuan.manager.index')">
                    <IconArrowLeft class="mr-2 size-4" /> Kembali
                </Link>
            </Button>

            <Button variant="destructive" size="sm" class="rounded-full shadow-md h-8 text-[10px] font-bold uppercase">
                <IconFileTypePdf class="mr-1.5 size-3.5" /> Export PDF
            </Button>
        </div>

        <div class="w-full flex justify-center">
            <Card class="border-none shadow-2xl overflow-hidden bg-slate-200 flex justify-center py-10 px-4 w-full">
                <div class="bg-white w-[210mm] min-h-[297mm] p-[12mm] shadow-sm text-slate-900 font-sans border border-slate-300 mx-auto">

                    <div class="border-2 border-slate-900 grid grid-cols-12 mb-6 text-center italic font-bold">
                        <div class="col-span-3 border-r-2 border-slate-900 p-2 flex items-center justify-center text-[10px] uppercase text-left">
                            PT Mark Dynamics Indonesia Tbk
                        </div>
                        <div class="col-span-6 border-r-2 border-slate-900 p-2 flex flex-col justify-center leading-tight">
                            <h1 class="text-sm uppercase tracking-tighter italic">Formulir Pembuatan<br />Sample Customer</h1>
                        </div>
                        <div class="col-span-3 text-[8px] p-2 text-left space-y-0.5 uppercase italic">
                            <div>No. Dok : MDDFM-QC37</div>
                            <div>Revisi : 02</div>
                            <div>Efektif : {{ formatDate(formulir.created_at) }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-x-10 gap-y-1 text-[10px] mb-8 border-b-2 border-slate-100 pb-4 italic font-medium">
                        <div class="flex justify-between">
                            <span class="w-24 text-slate-500 uppercase font-bold">Kode Sample</span>:
                            <span class="flex-1 font-black px-2">{{ sampel.kode_sample }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="w-24 text-slate-500 uppercase font-bold">Customer</span>:
                            <span class="flex-1 font-black px-2">{{ sampel.customer }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="w-24 text-slate-500 uppercase font-bold">Model / Size</span>:
                            <span class="flex-1 px-2">{{ sampel.model }} / {{ formulir.size }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="w-24 text-slate-500 uppercase font-bold">Tgl Minta</span>:
                            <span class="flex-1 px-2">{{ formatDate(formulir.tanggal_permintaan) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="w-24 text-slate-500 uppercase font-bold">Spesifikasi</span>:
                            <span class="flex-1 px-2">{{ sampel.spesifikasi }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="w-24 text-slate-500 uppercase font-bold">Qty Minta</span>:
                            <span class="flex-1 font-black underline px-2">{{ formulir.qty_sampel_kirim }} Pcs</span>
                        </div>
                    </div>

                    <div v-for="dept in formulir.departemen_terlibat" :key="dept.id" class="mb-8 italic leading-tight">
                        <div class="flex items-center gap-2 mb-1.5">
                            <div class="bg-slate-900 text-white size-5 flex items-center justify-center font-bold text-[10px]">
                                {{ dept.sub_departemen?.urutan }}
                            </div>
                            <div class="flex-1 border-b-2 border-slate-900 font-black uppercase text-[11px] tracking-tight text-slate-900">
                                {{ dept.sub_departemen?.nama }}
                            </div>
                        </div>

                        <div class="grid grid-cols-3 text-[9px] px-1 mb-2 font-medium">
                            <div>Diterima: {{ formatDate(dept.tanggal_diterima) }} <span v-if="dept.penerima">({{ dept.penerima.name }})</span></div>
                            <div class="text-center font-black">Hasil: <span class="underline">{{ dept.qty || 0 }} Pcs</span> (Selesai: {{ formatDate(dept.tanggal_selesai) }})</div>
                            <div class="text-right uppercase font-bold">QC: {{ dept.qcUser?.name ?? "-" }} | SPV: {{ dept.spvUser?.name ?? "-" }}</div>
                        </div>

                        <div class="border-2 border-slate-900 overflow-hidden">
                            <table class="w-full text-slate-900 border-collapse">
                                <thead class="bg-slate-50 border-b-2 border-slate-900">
                                    <tr class="text-[9px] font-black uppercase italic h-8">
                                        <th class="px-3 text-left border-r-2 border-slate-900">Item Pemeriksaan</th>
                                        <th class="px-3 text-center w-24 border-r-2 border-slate-900">Spec</th>
                                        <th class="px-3 text-center w-32">Actual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="row in (dept.item_pemeriksaan || [])" :key="row.item" class="border-b border-slate-900 last:border-0 h-7 text-[9px]">
                                        <td class="px-3 font-bold uppercase">{{ row.item }}</td>
                                        <td class="px-3 text-center border-x-2 border-slate-900 font-mono font-bold">{{ row.spec }}</td>
                                        <td class="px-3 text-center font-black text-primary">{{ row.actual || "-" }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-12 grid grid-cols-2 border-2 border-slate-900 italic font-bold leading-tight">

                        <div class="border-r-2 border-slate-900 flex flex-col">
                            <div class="bg-slate-50 py-1 text-[9px] font-black uppercase border-b-2 border-slate-900 text-center text-slate-500 italic">
                                Di Periksa Oleh :
                            </div>
                            <div class="h-32 flex flex-col items-center justify-center gap-1.5 relative px-4 text-center">
                                <template v-if="formulir.pemeriksa">
                                    <IconCircleCheck class="size-10 text-green-600" />
                                    <span class="text-[7px] text-muted-foreground uppercase tracking-widest font-black italic">Digitally Signed</span>
                                </template>

                                <AlertDialog v-else-if="canParafPemeriksa()">
                                    <AlertDialogTrigger as-child>
                                        <Button size="sm" class="h-8 text-[10px] font-black uppercase bg-slate-900 hover:bg-slate-800">
                                            <IconSignature class="mr-1 size-3" /> Paraf QC Manager
                                        </Button>
                                    </AlertDialogTrigger>
                                    <AlertDialogContent>
                                        <AlertDialogHeader>
                                            <AlertDialogTitle>Konfirmasi Paraf</AlertDialogTitle>
                                            <AlertDialogDescription>Bubuhkan paraf digital Anda sebagai <strong>QC Manager</strong> pada dokumen ini?</AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter>
                                            <AlertDialogCancel>Batal</AlertDialogCancel>
                                            <AlertDialogAction @click="submitParaf('persetujuan.manager.paraf_pemeriksa')">Ya, Paraf Sekarang</AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>

                                <IconClock v-else class="size-10 text-slate-100" />
                            </div>
                            <div class="p-3 border-t-2 border-slate-900 text-center">
                                <div class="text-[11px] font-black underline uppercase">
                                    {{ formulir.pemeriksa?.name ?? "................................" }}
                                </div>
                                <div class="text-[8px] text-muted-foreground mt-0.5 uppercase tracking-tighter">QC Manager</div>
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <div class="bg-slate-50 py-1 text-[9px] font-black uppercase border-b-2 border-slate-900 text-center text-slate-500 italic">
                                Di Setujui Oleh :
                            </div>
                            <div class="h-32 flex flex-col items-center justify-center gap-1.5 relative px-4 text-center">
                                <template v-if="formulir.penyetuju">
                                    <IconCircleCheck class="size-10 text-blue-600" />
                                    <span class="text-[7px] text-muted-foreground uppercase tracking-widest font-black italic">Digitally Signed</span>
                                </template>

                                <AlertDialog v-else-if="formulir.diperiksa_oleh && canParafPenyetuju()">
                                    <AlertDialogTrigger as-child>
                                        <Button size="sm" class="h-8 text-[10px] font-black uppercase bg-blue-600 hover:bg-blue-700">
                                            <IconUserCheck class="mr-1 size-3" /> Setujui Dokumen
                                        </Button>
                                    </AlertDialogTrigger>
                                    <AlertDialogContent>
                                        <AlertDialogHeader>
                                            <AlertDialogTitle>Konfirmasi Persetujuan</AlertDialogTitle>
                                            <AlertDialogDescription>Bubuhkan persetujuan digital sebagai <strong>Factory Manager / GM</strong>?</AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter>
                                            <AlertDialogCancel>Batal</AlertDialogCancel>
                                            <AlertDialogAction @click="submitParaf('persetujuan.manager.paraf_penyetuju')" class="bg-blue-600">Ya, Setujui Sekarang</AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>

                                <IconClock v-else class="size-10 text-slate-100" />
                            </div>
                            <div class="p-3 border-t-2 border-slate-900 text-center">
                                <div class="text-[11px] font-black underline uppercase">
                                    {{ formulir.penyetuju?.name ?? "................................" }}
                                </div>
                                <div class="text-[8px] text-muted-foreground mt-0.5 uppercase tracking-tighter">Factory Manager / GM</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-between items-center text-[8px] font-black text-slate-300 uppercase italic">
                        <span>PT Mark Dynamics Indonesia Tbk</span>
                        <span>SISAMCUS Digital Archive {{ new Date().getFullYear() }}</span>
                    </div>
                </div>
            </Card>
        </div>
    </div>
</template>
