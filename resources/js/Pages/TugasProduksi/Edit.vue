<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
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
    IconHierarchy2,
    IconClipboardCheck,
    IconDatabase,
    IconSignature,
    IconLock,
    IconLockOpen,
} from "@tabler/icons-vue";
import { computed } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    formulir: any;
    departemen_terlibat: any;
}>();

// Gunakan computed agar reaktif saat data dari back() datang
const isReceived = computed(() => !!props.departemen_terlibat.tanggal_diterima);

// Mapping Data Tambahan (Object JSON to Array for Form)
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
        route("formulirs.departemen.update", {
            formulir: props.formulir.id,
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
            preserveState: false, // Tambahkan ini untuk memaksa refresh state props
            onSuccess: () => {
                console.log("Berhasil diterima!");
            },
        },
    );
};

const parafQC = () => {
    router.patch(
        route("formulirs.departemen.paraf-qc", {
            formulir: props.formulir.id,
            departemen_terlibat: props.departemen_terlibat.id,
        }),
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
                    <h2
                        class="text-xl font-black tracking-tight text-primary uppercase italic leading-none"
                    >
                        Unit: {{ departemen_terlibat.nama_departemen }}
                    </h2>
                    <p
                        class="text-[9px] font-bold uppercase text-muted-foreground tracking-[0.2em] mt-1"
                    >
                        {{
                            isReceived
                                ? "Status: Tugas Sedang Dikerjakan"
                                : "Status: Menunggu Konfirmasi Terima"
                        }}
                    </p>
                </div>
            </div>

            <AlertDialog v-if="!isReceived">
                <AlertDialogTrigger as-child>
                    <Button
                        class="bg-orange-500 hover:bg-orange-600 font-black text-xs uppercase h-10 px-6 shadow-lg shadow-orange-100"
                    >
                        <IconLockOpen class="mr-2 size-4 font-bold" /> Terima
                        Tugas
                    </Button>
                </AlertDialogTrigger>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle
                            >Konfirmasi Terima Tugas</AlertDialogTitle
                        >
                        <AlertDialogDescription>
                            Apakah Anda yakin ingin menerima tugas untuk sampel
                            <strong>{{ formulir.sampel.kode_sample }}</strong
                            >? Sistem akan mencatat waktu penerimaan atas nama
                            Anda.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>Batal</AlertDialogCancel>
                        <AlertDialogAction
                            @click="terimaTugas"
                            class="bg-orange-500 text-white"
                            >Ya, Terima</AlertDialogAction
                        >
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </div>

        <div class="max-w-5xl space-y-4">
            <Card
                class="border-none shadow-sm bg-muted/20 border-l-4"
                :class="isReceived ? 'border-l-primary' : 'border-l-orange-400'"
            >
                <CardContent
                    class="p-4 grid grid-cols-2 md:grid-cols-4 gap-y-4 gap-x-6 text-[10px]"
                >
                    <div class="space-y-0.5">
                        <p
                            class="text-muted-foreground font-bold uppercase text-[8px]"
                        >
                            Customer
                        </p>
                        <p
                            class="font-black text-slate-900 uppercase text-xs truncate"
                        >
                            {{ formulir.sampel.customer?.nama ?? "INTERNAL" }}
                        </p>
                    </div>
                    <div class="space-y-0.5">
                        <p
                            class="text-muted-foreground font-bold uppercase text-[8px]"
                        >
                            Kode Sampel / Size
                        </p>
                        <p class="font-black text-primary text-xs">
                            {{ formulir.sampel.kode_sample }} /
                            {{ formulir.size }}
                        </p>
                    </div>
                    <div class="space-y-0.5">
                        <p
                            class="text-muted-foreground font-bold uppercase text-[8px]"
                        >
                            Running / Tgl Terima
                        </p>
                        <p class="font-bold text-slate-700">
                            Run #{{ formulir.running_ke }} —
                            {{
                                isReceived
                                    ? formatDate(
                                          departemen_terlibat.tanggal_diterima,
                                      )
                                    : "BELUM"
                            }}
                        </p>
                    </div>
                    <div class="space-y-0.5">
                        <p
                            class="text-muted-foreground font-bold uppercase text-[8px]"
                        >
                            Diterima Oleh
                        </p>
                        <p class="font-bold text-slate-700 uppercase">
                            {{ departemen_terlibat.penerima?.name ?? "-" }}
                        </p>
                    </div>
                    <div class="space-y-0.5">
                        <p
                            class="text-muted-foreground font-bold uppercase text-[8px]"
                        >
                            Paraf QC
                        </p>
                        <p class="font-bold text-green-600 uppercase">
                            {{ departemen_terlibat.qc_user?.name ?? "-" }}
                        </p>
                    </div>
                    <div class="space-y-0.5">
                        <p
                            class="text-muted-foreground font-bold uppercase text-[8px]"
                        >
                            Paraf SPV
                        </p>
                        <p class="font-bold text-blue-600 uppercase">
                            {{ departemen_terlibat.spv_user?.name ?? "-" }}
                        </p>
                    </div>
                    <div class="space-y-0.5">
                        <p
                            class="text-muted-foreground font-bold uppercase text-[8px]"
                        >
                            Tgl Selesai (Update)
                        </p>
                        <p class="font-bold text-slate-700">
                            {{
                                formatDate(departemen_terlibat.tanggal_selesai)
                            }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <form @submit.prevent="submit" class="space-y-4 relative">
                <div
                    v-if="!isReceived"
                    class="absolute inset-0 z-10 bg-white/50 backdrop-blur-[1.5px] flex items-center justify-center rounded-xl border-2 border-dashed border-muted/50"
                >
                    <div
                        class="bg-white p-5 rounded-2xl shadow-2xl border flex flex-col items-center gap-3 text-center"
                    >
                        <IconLock
                            class="size-10 text-orange-500 animate-pulse"
                        />
                        <div class="space-y-1">
                            <p
                                class="text-sm font-black uppercase text-slate-800 tracking-tight"
                            >
                                Input Terkunci
                            </p>
                            <p
                                class="text-[10px] text-muted-foreground leading-relaxed px-4"
                            >
                                Silahkan klik tombol
                                <strong>"Terima Tugas"</strong> di pojok kanan
                                atas untuk membuka form.
                            </p>
                        </div>
                    </div>
                </div>

                <Card class="border-none shadow-sm overflow-hidden">
                    <CardHeader
                        class="bg-muted/30 py-2 px-4 flex flex-row items-center justify-between border-b"
                    >
                        <CardTitle
                            class="text-[10px] font-bold uppercase flex items-center gap-2 text-primary tracking-widest"
                        >
                            <IconSettings class="size-4" /> Kuantitas Produksi
                        </CardTitle>

                        <AlertDialog
                            v-if="isReceived && !departemen_terlibat.is_qc"
                        >
                            <AlertDialogTrigger as-child>
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    class="h-7 border-primary text-primary hover:bg-primary hover:text-white font-bold text-[9px] uppercase px-4 shadow-sm"
                                >
                                    <IconSignature class="mr-1.5 size-3.5" />
                                    Paraf QC
                                </Button>
                            </AlertDialogTrigger>
                            <AlertDialogContent>
                                <AlertDialogHeader
                                    ><AlertDialogTitle
                                        >Verifikasi QC</AlertDialogTitle
                                    ></AlertDialogHeader
                                >
                                <AlertDialogFooter>
                                    <AlertDialogCancel>Batal</AlertDialogCancel>
                                    <AlertDialogAction
                                        @click="parafQC"
                                        class="bg-primary text-white"
                                        >Ya, Verifikasi</AlertDialogAction
                                    >
                                </AlertDialogFooter>
                            </AlertDialogContent>
                        </AlertDialog>
                    </CardHeader>
                    <CardContent
                        class="py-4 px-6 flex items-center gap-6 bg-primary/5"
                    >
                        <div class="flex-1">
                            <Label
                                class="text-[9px] font-black uppercase text-primary tracking-widest"
                                >Jumlah Hasil (Qty Pcs)</Label
                            >
                            <Input
                                type="number"
                                v-model="form.qty"
                                :disabled="!isReceived"
                                class="h-12 text-3xl font-black focus-visible:ring-primary border-primary/20 bg-white text-center shadow-inner"
                            />
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm overflow-hidden">
                    <CardHeader class="bg-muted/30 py-2 border-b px-4">
                        <CardTitle
                            class="text-[10px] font-bold uppercase flex items-center gap-2 text-muted-foreground"
                        >
                            <IconDatabase class="size-4" /> Parameter Pendukung
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="p-0">
                        <Table>
                            <TableBody>
                                <TableRow
                                    v-for="(data, index) in form.data_tambahan"
                                    :key="index"
                                    class="h-10 border-b last:border-0 hover:bg-transparent"
                                >
                                    <TableCell
                                        class="py-2 px-6 w-1/3 bg-muted/5 border-r font-bold uppercase text-[9px] text-slate-500 italic"
                                        >{{ data.key }}</TableCell
                                    >
                                    <TableCell
                                        class="py-1 px-6 font-bold text-sm"
                                    >
                                        <Input
                                            v-model="data.value"
                                            :disabled="!isReceived"
                                            class="h-8 border-none shadow-none focus-visible:ring-0 p-0 bg-transparent"
                                            placeholder="Ketik nilai..."
                                        />
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm overflow-hidden">
                    <CardHeader class="bg-muted/30 py-2 border-b px-4">
                        <CardTitle
                            class="text-[10px] font-bold uppercase flex items-center gap-2 text-muted-foreground"
                        >
                            <IconClipboardCheck class="size-4" /> Lembar
                            Pemeriksaan Kualitas
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="p-0">
                        <Table>
                            <TableHeader
                                class="bg-muted/50 text-[9px] uppercase font-black tracking-wider h-10"
                            >
                                <TableRow>
                                    <TableHead class="pl-8"
                                        >Parameter Item</TableHead
                                    >
                                    <TableHead>Spec Standar</TableHead>
                                    <TableHead class="w-[300px] text-primary"
                                        >Hasil Aktual (Input)</TableHead
                                    >
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="(item, index) in form.pemeriksaan"
                                    :key="index"
                                    class="h-14 border-b last:border-0 hover:bg-transparent"
                                >
                                    <TableCell
                                        class="pl-8 font-black text-xs text-slate-700 uppercase tracking-tight"
                                        >{{ item.item }}</TableCell
                                    >
                                    <TableCell
                                        class="text-[10px] font-bold italic text-muted-foreground"
                                        >{{ item.spec || "-" }}</TableCell
                                    >
                                    <TableCell class="pr-8">
                                        <Input
                                            v-model="item.actual"
                                            :disabled="!isReceived"
                                            placeholder="Input hasil..."
                                            class="h-10 bg-primary/5 border-primary/20 focus:border-primary font-black text-base text-primary shadow-sm"
                                        />
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <div
                    v-if="isReceived"
                    class="flex items-center justify-end gap-3 pt-4"
                >
                    <Button
                        variant="ghost"
                        as-child
                        class="font-bold text-[10px] uppercase h-10 px-6"
                    >
                        <Link :href="route('tugas.produksi.index')">Batal</Link>
                    </Button>
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="min-w-[200px] h-12 font-black uppercase tracking-widest shadow-xl shadow-primary/20 text-xs"
                    >
                        <IconLoader2
                            v-if="form.processing"
                            class="mr-2 size-4 animate-spin"
                        />
                        <IconDeviceFloppy
                            v-else
                            class="mr-2 size-4 font-bold"
                        />
                        Simpan Hasil
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
