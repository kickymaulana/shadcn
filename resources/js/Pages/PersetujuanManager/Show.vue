<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
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

// Helper pengecekan hak akses
const canParafPemeriksa = () =>
    userRoles.includes("QC Manager") || userRoles.includes("admin");
const canParafPenyetuju = () =>
    userRoles.includes("Factory Manager") || userRoles.includes("admin");

const submitParaf = (routeName: string) => {
    router.post(
        route(routeName, { formulir: props.formulir.id }),
        {},
        { preserveScroll: true },
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
        <div
            class="flex items-center justify-between w-full max-w-[210mm] mx-auto"
        >
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

            <Button
                variant="destructive"
                size="sm"
                class="rounded-full shadow-md h-8 text-[10px] font-bold uppercase"
                as-child
            >
                <a
                    :href="route('pdf.formulir', { formulir: formulir.id })"
                    target="_blank"
                    class="flex items-center text-white"
                >
                    <IconFileTypePdf class="mr-1.5 size-3.5" /> Export PDF
                </a>
            </Button>
        </div>

        <div class="w-full flex justify-center">
            <Card
                class="border-none shadow-2xl overflow-hidden bg-slate-200 flex justify-center py-10 px-4 w-full"
            >
                <div
                    class="bg-white w-[210mm] min-h-[297mm] p-[12mm] shadow-sm text-slate-900 font-sans border border-slate-300 mx-auto relative"
                >
                    <div
                        class="border-2 border-slate-900 grid grid-cols-12 mb-6 text-center italic font-bold uppercase"
                    >
                        <div
                            class="col-span-3 border-r-2 border-slate-900 p-2 flex items-center justify-center text-[10px] text-left leading-tight"
                        >
                            PT Mark Dynamics Indonesia Tbk
                        </div>
                        <div
                            class="col-span-6 border-r-2 border-slate-900 p-2 flex flex-col justify-center leading-tight"
                        >
                            <h1
                                class="text-sm tracking-tighter italic uppercase"
                            >
                                Formulir Pembuatan<br />Sample Customer
                            </h1>
                        </div>
                        <div
                            class="col-span-3 text-[8px] p-2 text-left space-y-0.5 italic"
                        >
                            <div>No. Dok : MDDFM-QC37</div>
                            <div>Revisi : 02</div>
                            <div>
                                Efektif : {{ formatDate(formulir.created_at) }}
                            </div>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-2 gap-x-10 gap-y-1 text-[10px] mb-8 border-b-2 border-slate-100 pb-4 italic font-medium uppercase"
                    >
                        <div class="flex justify-between">
                            <span class="w-24 text-slate-500 font-bold"
                                >Kode Sample</span
                            >:
                            <span class="flex-1 font-black px-2">{{
                                sampel.kode_sample
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="w-24 text-slate-500 font-bold"
                                >Customer</span
                            >:
                            <span class="flex-1 font-black px-2">{{
                                sampel.customer
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="w-24 text-slate-500 font-bold"
                                >Model / Size</span
                            >:
                            <span class="flex-1 px-2"
                                >{{ sampel.model }} / {{ formulir.size }}</span
                            >
                        </div>
                        <div class="flex justify-between">
                            <span class="w-24 text-slate-500 font-bold"
                                >Tgl Minta</span
                            >:
                            <span class="flex-1 px-2">{{
                                formatDate(formulir.tanggal_permintaan)
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="w-24 text-slate-500 font-bold"
                                >Spesifikasi</span
                            >:
                            <span class="flex-1 px-2">{{
                                sampel.spesifikasi
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="w-24 text-slate-500 font-bold"
                                >Qty Minta</span
                            >:
                            <span class="flex-1 font-black underline px-2"
                                >{{ formulir.qty_sampel_kirim }} Pcs</span
                            >
                        </div>
                    </div>

                    <div
                        v-for="(dept, index) in formulir.departemen_terlibat"
                        :key="dept.id"
                        class="mb-8 italic leading-tight uppercase"
                    >
                        <div class="flex items-center gap-2 mb-1.5">
                            <div
                                class="bg-slate-900 text-white size-5 flex items-center justify-center font-bold text-[10px]"
                            >
                                {{ index + 1 }}
                            </div>
                            <div
                                class="flex-1 border-b-2 border-slate-900 font-black text-[11px] tracking-tight text-slate-900 uppercase"
                            >
                                {{ dept.sub_departemen?.nama }}
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-3 text-[9px] px-1 mb-2 font-medium uppercase"
                        >
                            <div>
                                Diterima:
                                {{ formatDate(dept.tanggal_diterima) }}
                                <span v-if="dept.penerima" class="font-bold"
                                    >({{ dept.penerima.name }})</span
                                >
                            </div>
                            <div class="text-center">
                                Hasil:
                                <span class="font-black underline"
                                    >{{ dept.qty || 0 }} Pcs</span
                                >
                                (Selesai:
                                {{ formatDate(dept.tanggal_selesai) }})
                            </div>
                            <div class="text-right font-bold">
                                QC:
                                {{
                                    dept.qc_user?.name ||
                                    dept.qcUser?.name ||
                                    "-"
                                }}
                                | SPV:
                                {{
                                    dept.spv_user?.name ||
                                    dept.spvUser?.name ||
                                    "-"
                                }}
                            </div>
                        </div>

                        <div
                            v-if="dept.data_tambahan && Object.keys(dept.data_tambahan).length > 0"
                            class="border-2 border-slate-900 overflow-hidden mb-3"
                        >
                            <table class="w-full text-slate-900 border-collapse text-[9px]">
                                <thead class="bg-slate-50 border-b-2 border-slate-900 uppercase font-black italic">
                                    <tr class="h-8">
                                        <th class="px-3 text-left border-r-2 border-slate-900">
                                            Informasi
                                        </th>
                                        <th class="px-3 text-center w-1/2">
                                            Detail / Nilai
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(value, key) in dept.data_tambahan"
                                        :key="key"
                                        class="border-b border-slate-900 last:border-0 h-7"
                                    >
                                        <td class="px-3 font-bold uppercase bg-slate-50/50 border-r-2 border-slate-900 w-[140px]">
                                            {{ key }}
                                        </td>
                                        <td class="px-3 text-center font-black text-slate-900">
                                            {{ value || "-" }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        <div class="border-2 border-slate-900 overflow-hidden">
                            <table class="w-full text-slate-900 border-collapse text-[9px] table-fixed">
                                <thead class="bg-slate-50 border-b-2 border-slate-900 uppercase font-black italic">
                                    <tr class="h-8">
                                        <th class="px-3 text-left border-r-2 border-slate-900 w-1/2">
                                            Item Pemeriksaan
                                        </th>
                                        <th class="px-3 text-center border-r-2 border-slate-900 w-1/4">
                                            Spec
                                        </th>
                                        <th class="px-3 text-center w-1/4">
                                            Actual
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(row, idx) in dept.item_pemeriksaan || []"
                                        :key="idx"
                                        class="border-b border-slate-900 last:border-0 min-h-7"
                                    >
                                        <td class="px-3 py-1 font-bold uppercase break-words border-r-2 border-slate-900">
                                            {{ row.item }}
                                        </td>
                                        <td class="px-3 py-1 text-center border-r-2 border-slate-900 font-mono font-bold break-words">
                                            {{ row.spec }}
                                        </td>
                                        <td class="px-3 py-1 text-center font-black text-primary break-words">
                                            {{ row.actual || "-" }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                    </div>

                    <div
                        class="mt-16 grid grid-cols-2 border-2 border-slate-900 text-center text-[10px] italic font-bold uppercase leading-tight"
                    >
                        <div class="flex flex-col border-r-2 border-slate-900">
                            <div
                                class="bg-slate-50 py-1 border-b-2 border-slate-900 text-slate-500"
                            >
                                Di Periksa Oleh :
                            </div>
                            <div
                                class="h-32 flex flex-col items-center justify-center gap-1.5 relative px-4"
                            >
                                <template v-if="formulir.pemeriksa">
                                    <IconCircleCheck
                                        class="size-10 text-green-600"
                                    />
                                    <span
                                        class="text-[7px] text-muted-foreground tracking-widest font-black italic uppercase"
                                        >Digitally Signed</span
                                    >
                                </template>
                                <template v-else-if="canParafPemeriksa()">
                                    <AlertDialog>
                                        <AlertDialogTrigger as-child>
                                            <Button
                                                size="sm"
                                                class="h-8 text-[10px] font-black uppercase bg-slate-900 hover:bg-slate-800"
                                            >
                                                <IconSignature
                                                    class="mr-1 size-3"
                                                />
                                                Paraf QC Manager
                                            </Button>
                                        </AlertDialogTrigger>
                                        <AlertDialogContent>
                                            <AlertDialogHeader>
                                                <AlertDialogTitle
                                                    >Konfirmasi
                                                    Paraf</AlertDialogTitle
                                                >
                                                <AlertDialogDescription
                                                    >Bubuhkan paraf digital Anda
                                                    sebagai
                                                    <strong>QC Manager</strong
                                                    >?</AlertDialogDescription
                                                >
                                            </AlertDialogHeader>
                                            <AlertDialogFooter>
                                                <AlertDialogCancel
                                                    >Batal</AlertDialogCancel
                                                >
                                                <AlertDialogAction
                                                    @click="
                                                        submitParaf(
                                                            'persetujuan.manager.paraf_pemeriksa',
                                                        )
                                                    "
                                                    >Ya, Paraf
                                                    Sekarang</AlertDialogAction
                                                >
                                            </AlertDialogFooter>
                                        </AlertDialogContent>
                                    </AlertDialog>
                                </template>
                                <IconClock
                                    v-else
                                    class="size-10 text-slate-100"
                                />
                            </div>
                            <div class="p-3 border-t-2 border-slate-900">
                                <div class="underline">
                                    {{
                                        formulir.pemeriksa?.name ??
                                        "................................"
                                    }}
                                </div>
                                <div
                                    class="text-[8px] text-muted-foreground mt-0.5 tracking-tighter"
                                >
                                    QC Manager
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <div
                                class="bg-slate-50 py-1 border-b-2 border-slate-900 text-slate-500"
                            >
                                Di Setujui Oleh :
                            </div>
                            <div
                                class="h-32 flex flex-col items-center justify-center gap-1.5 relative px-4"
                            >
                                <template v-if="formulir.penyetuju">
                                    <IconCircleCheck
                                        class="size-10 text-blue-600"
                                    />
                                    <span
                                        class="text-[7px] text-muted-foreground tracking-widest font-black italic uppercase"
                                        >Digitally Signed</span
                                    >
                                </template>
                                <template
                                    v-else-if="
                                        formulir.diperiksa_oleh &&
                                        canParafPenyetuju()
                                    "
                                >
                                    <AlertDialog>
                                        <AlertDialogTrigger as-child>
                                            <Button
                                                size="sm"
                                                class="h-8 text-[10px] font-black uppercase bg-blue-600 hover:bg-blue-700"
                                            >
                                                <IconUserCheck
                                                    class="mr-1 size-3"
                                                />
                                                Setujui Dokumen
                                            </Button>
                                        </AlertDialogTrigger>
                                        <AlertDialogContent>
                                            <AlertDialogHeader>
                                                <AlertDialogTitle
                                                    >Konfirmasi
                                                    Persetujuan</AlertDialogTitle
                                                >
                                                <AlertDialogDescription
                                                    >Bubuhkan persetujuan
                                                    digital sebagai
                                                    <strong
                                                        >Factory Manager /
                                                        GM</strong
                                                    >?</AlertDialogDescription
                                                >
                                            </AlertDialogHeader>
                                            <AlertDialogFooter>
                                                <AlertDialogCancel
                                                    >Batal</AlertDialogCancel
                                                >
                                                <AlertDialogAction
                                                    @click="
                                                        submitParaf(
                                                            'persetujuan.manager.paraf_penyetuju',
                                                        )
                                                    "
                                                    class="bg-blue-600 font-bold uppercase text-white hover:bg-blue-700"
                                                    >Ya, Setujui
                                                    Sekarang</AlertDialogAction
                                                >
                                            </AlertDialogFooter>
                                        </AlertDialogContent>
                                    </AlertDialog>
                                </template>
                                <IconClock
                                    v-else
                                    class="size-10 text-slate-100"
                                />
                            </div>
                            <div class="p-3 border-t-2 border-slate-900">
                                <div class="underline">
                                    {{
                                        formulir.penyetuju?.name ??
                                        "................................"
                                    }}
                                </div>
                                <div
                                    class="text-[8px] text-muted-foreground mt-0.5 tracking-tighter"
                                >
                                    Factory Manager / GM
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-8 flex justify-between items-center text-[8px] font-black text-slate-300 uppercase italic"
                    >
                        <span>PT Mark Dynamics Indonesia Tbk</span>
                        <span
                            >SISAMCUS Digital Archive
                            {{ new Date().getFullYear() }}</span
                        >
                    </div>
                </div>
            </Card>
        </div>
    </div>
</template>
