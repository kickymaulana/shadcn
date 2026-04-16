<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { computed } from "vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
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
    IconDeviceFloppy,
    IconLoader2,
    IconSettings,
    IconClipboardCheck,
    IconDatabase,
    IconLock,
    IconLockOpen,
    IconUserCheck,
    IconEye,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    formulir: any;
    departemen_terlibat: any;
}>();

// Logika Status Reaktif
const isReceived = computed(() => !!props.departemen_terlibat.tanggal_diterima);
const isQC = computed(() => !!props.departemen_terlibat.paraf_qc);
const isSPV = computed(() => !!props.departemen_terlibat.paraf_spv);

// Mapping Data Tambahan (Object to Array)
const initialDataTambahan = Object.entries(
    props.departemen_terlibat.data_tambahan || {},
).map(([key, value]) => ({
    key: key,
    value: String(value),
}));

const form = useForm({
    qty: props.departemen_terlibat.qty,
    pemeriksaan: props.departemen_terlibat.item_pemeriksaan || [],
    data_tambahan: initialDataTambahan,
});

const submit = () => {
    const dataTambahanObj: Record<string, any> = {};
    form.data_tambahan.forEach((i) => {
        if (i.key) dataTambahanObj[i.key] = i.value;
    });

    form.transform((data) => ({
        ...data,
        sub_departemen_id: props.departemen_terlibat.sub_departemen_id,
        data_tambahan: dataTambahanObj,
        item_pemeriksaan: data.pemeriksaan,
    })).put(
        route("tugas.produksi.update", {
            departemen_terlibat: props.departemen_terlibat.id,
        }),
    );
};

const terimaTugas = () => {
    router.patch(
        route("tugas.produksi.terima", props.departemen_terlibat.id),
        {},
        {
            preserveScroll: true,
        },
    );
};

const parafSPV = () => {
    router.patch(
        route("formulirs.departemen.paraf-spv", {
            formulir: props.formulir.id,
            departemen_terlibat: props.departemen_terlibat.id,
        }),
        {},
        { preserveScroll: true },
    );
};

const formatDate = (date: string | null) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};
</script>

<template>
    <Head :title="'Input ' + departemen_terlibat.nama_departemen" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-2">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <Button
                    variant="outline"
                    size="icon"
                    as-child
                    class="rounded-full size-8 shadow-sm"
                >
                    <Link :href="route('tugas.produksi.index')">
                        <IconArrowLeft class="size-4" />
                    </Link>
                </Button>
                <div>
                    <h2 class="text-xl font-black tracking-tight text-primary uppercase italic leading-none">
                        Unit: {{ departemen_terlibat.nama_departemen }}
                    </h2>
                    <p class="text-[9px] font-bold uppercase text-muted-foreground tracking-[0.2em] mt-1">
                        {{ isReceived ? "Status: Proses Pengerjaan" : "Status: Menunggu Konfirmasi Terima" }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <Button
                    variant="outline"
                    size="sm"
                    as-child
                    class="h-9 border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-[10px] uppercase px-4 shadow-sm"
                >
                    <Link :href="route('tugas.produksi.show', { departemen_terlibat: departemen_terlibat.id })">
                        <IconEye class="mr-1.5 size-4" /> Lihat Preview Dokumen
                    </Link>
                </Button>

                <AlertDialog v-if="!isReceived">
                    <AlertDialogTrigger as-child>
                        <Button class="bg-orange-500 hover:bg-orange-600 font-black text-xs uppercase h-10 px-6 shadow-lg shadow-orange-100">
                            <IconLockOpen class="mr-2 size-4" /> Terima Tugas
                        </Button>
                    </AlertDialogTrigger>
                    <AlertDialogContent>
                        <AlertDialogHeader>
                            <AlertDialogTitle>Konfirmasi Terima Tugas</AlertDialogTitle>
                            <AlertDialogDescription>
                                Terima sampel <strong>{{ formulir.sampel.kode_sample }}</strong>? Waktu penerimaan akan dicatat secara otomatis.
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                            <AlertDialogCancel>Batal</AlertDialogCancel>
                            <AlertDialogAction @click="terimaTugas" class="bg-orange-500 text-white">
                                Ya, Terima
                            </AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            </div>
        </div>

        <div class="max-w-5xl space-y-4">
            <Card class="border-none shadow-sm bg-muted/20 border-l-4" :class="isReceived ? 'border-l-primary' : 'border-l-orange-400'">
                <CardContent class="p-4 grid grid-cols-2 md:grid-cols-4 gap-y-4 gap-x-6 text-[10px]">
                    <div class="space-y-0.5">
                        <p class="text-muted-foreground font-bold uppercase text-[8px]">Customer</p>
                        <p class="font-black text-slate-900 uppercase text-xs truncate">{{ formulir.sampel.customer ?? "INTERNAL" }}</p>
                    </div>
                    <div class="space-y-0.5">
                        <p class="text-muted-foreground font-bold uppercase text-[8px]">Kode Sampel / Size</p>
                        <p class="font-black text-primary text-xs">{{ formulir.sampel.kode_sample }} / {{ formulir.size }}</p>
                    </div>
                    <div class="space-y-0.5">
                        <p class="text-muted-foreground font-bold uppercase text-[8px]">Model</p>
                        <p class="font-black text-primary text-xs">{{ formulir.sampel.model }}</p>
                    </div>
                    <div class="space-y-0.5">
                        <p class="text-muted-foreground font-bold uppercase text-[8px]">Running / Tgl Terima</p>
                        <p class="font-bold text-slate-700 uppercase">
                            Run #{{ formulir.running_ke }} — {{ isReceived ? formatDate(departemen_terlibat.tanggal_diterima) : "BELUM" }}
                        </p>
                    </div>
                    <div class="space-y-0.5">
                        <p class="text-muted-foreground font-bold uppercase text-[8px]">Diterima Oleh</p>
                        <p class="font-bold text-slate-700 uppercase">{{ departemen_terlibat.penerima?.name ?? "-" }}</p>
                    </div>
                </CardContent>
            </Card>

            <form @submit.prevent="submit" class="space-y-4 relative">
                <div v-if="!isReceived" class="absolute inset-0 z-10 bg-white/50 backdrop-blur-[1px] flex items-center justify-center rounded-xl border-2 border-dashed border-muted/50">
                    <IconLock class="size-10 text-orange-500 opacity-20" />
                </div>

                <Card class="border-none shadow-sm overflow-hidden">
                    <CardHeader class="bg-muted/30 py-2 px-4 flex flex-row items-center justify-between border-b text-[10px] font-bold uppercase tracking-widest">
                        <div class="flex items-center gap-2 text-primary">
                            <IconSettings class="size-4" /> Kontrol Produksi
                        </div>
                        <div class="flex items-center gap-2">
                            <div v-if="isQC" class="flex items-center gap-1 text-green-600">
                                <IconClipboardCheck class="size-3" /> QC OK
                            </div>
                            <AlertDialog v-if="isReceived && !isSPV">
                                <AlertDialogTrigger as-child>
                                    <Button type="button" variant="outline" size="sm" class="h-7 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white font-bold text-[9px] uppercase px-3 shadow-sm">
                                        <IconUserCheck class="mr-1 size-3" /> Paraf SPV
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>Paraf Supervisor</AlertDialogTitle>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>Batal</AlertDialogCancel>
                                        <AlertDialogAction @click="parafSPV" class="bg-blue-600">Ya, Paraf</AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                            <div v-else-if="isSPV" class="flex items-center gap-1 text-blue-600">
                                <IconUserCheck class="size-3" /> SPV OK
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="py-4 px-6 bg-primary/5">
                        <div class="max-w-[200px] mx-auto space-y-1">
                            <Label class="text-[9px] font-black uppercase text-primary tracking-widest block text-center">Jumlah</Label>
                            <Input type="number" v-model="form.qty" :disabled="!isReceived" class="h-12 text-3xl font-black focus-visible:ring-primary border-primary/20 bg-white text-center shadow-inner" />
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm overflow-hidden">
                    <CardHeader class="bg-muted/30 py-2 border-b px-4 text-[10px] font-bold uppercase text-muted-foreground">
                        <IconDatabase class="size-4 mr-2 inline-block" /> Parameter Pendukung
                    </CardHeader>
                    <CardContent class="p-0">
                        <Table>
                            <TableBody>
                                <TableRow v-for="(data, index) in form.data_tambahan" :key="index" class="h-10 border-b last:border-0 hover:bg-transparent">
                                    <TableCell class="py-2 px-6 w-1/3 bg-muted/5 border-r font-bold uppercase text-[9px] text-slate-500 italic">{{ data.key }}</TableCell>
                                    <TableCell class="py-1 px-6 font-bold text-sm">
                                        <Input v-model="data.value" :disabled="!isReceived" class="h-8 border-none shadow-none focus-visible:ring-0 p-0 bg-transparent" placeholder="..." />
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm overflow-hidden">
                    <CardHeader class="bg-muted/30 py-2 border-b px-4 text-[10px] font-bold uppercase text-muted-foreground">
                        <IconClipboardCheck class="size-4 mr-2 inline-block" /> Pemeriksaan Kualitas
                    </CardHeader>
                    <CardContent class="p-0">
                        <Table>
                            <TableHeader class="bg-muted/50 text-[9px] uppercase font-black tracking-wider h-10">
                                <TableRow>
                                    <TableHead class="pl-8">Item</TableHead>
                                    <TableHead>Spec</TableHead>
                                    <TableHead class="w-[300px] text-primary">Hasil Aktual</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="(item, index) in form.pemeriksaan" :key="index" class="h-14 border-b last:border-0 hover:bg-transparent">
                                    <TableCell class="pl-8 font-black text-xs text-slate-700 uppercase tracking-tight">{{ item.item }}</TableCell>
                                    <TableCell class="text-[10px] font-bold italic text-muted-foreground">{{ item.spec || "-" }}</TableCell>
                                    <TableCell class="pr-8">
                                        <Input v-model="item.actual" :disabled="!isReceived" class="h-10 bg-primary/5 border-primary/20 focus:border-primary font-black text-base text-primary shadow-sm" />
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <div v-if="isReceived" class="flex items-center justify-end gap-3 pt-4">
                    <Button variant="ghost" as-child class="font-bold text-[10px] uppercase h-10 px-6 text-muted-foreground hover:text-foreground">
                        <Link :href="route('tugas.produksi.index')">Batal</Link>
                    </Button>
                    <Button type="submit" :disabled="form.processing" class="min-w-[200px] h-12 font-black uppercase tracking-widest shadow-xl shadow-primary/20 text-xs">
                        <IconLoader2 v-if="form.processing" class="mr-2 size-4 animate-spin" />
                        <IconDeviceFloppy v-else class="mr-2 size-4" /> Simpan Hasil
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
