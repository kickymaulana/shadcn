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
    IconClock,
    IconTrash,
    IconClipboardList,
    IconTruckDelivery,
    IconHierarchy2,
    IconCheck,
    IconX,
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
            urutan: number;
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
    return new Date(dateString).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    });
};
</script>

<template>
    <Head :title="'Detail Formulir #' + formulir.id" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-4">
        <div class="flex items-center gap-2">
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
                    <Badge
                        variant="outline"
                        class="mb-2 uppercase font-bold text-primary border-primary"
                    >
                        {{ formulir.status }}
                    </Badge>
                    <h2 class="text-lg font-bold">
                        Formulir #{{ formulir.id }}
                    </h2>
                    <p class="text-sm text-muted-foreground italic">
                        Running ke-{{ formulir.running_ke }}
                    </p>

                    <div class="w-full mt-6 flex flex-col gap-2">
                        <Button class="w-full" variant="outline" as-child>
                            <Link :href="route('formulirs.edit', formulir.id)">
                                <IconPencil class="mr-2 size-4" /> Update
                                Formulir
                            </Link>
                        </Button>
                        <AlertDialog>
                            <AlertDialogTrigger as-child>
                                <Button variant="destructive" class="w-full">
                                    <IconTrash class="mr-2 size-4" /> Hapus
                                </Button>
                            </AlertDialogTrigger>
                            <AlertDialogContent>
                                <AlertDialogHeader>
                                    <AlertDialogTitle
                                        >Hapus formulir ini?</AlertDialogTitle
                                    >
                                    <AlertDialogDescription
                                        >Tindakan ini akan menghapus riwayat
                                        permintaan #{{
                                            formulir.id
                                        }}.</AlertDialogDescription
                                    >
                                </AlertDialogHeader>
                                <AlertDialogFooter>
                                    <AlertDialogCancel>Batal</AlertDialogCancel>
                                    <AlertDialogAction
                                        @click="deleteFormulir"
                                        class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                        >Ya, Hapus</AlertDialogAction
                                    >
                                </AlertDialogFooter>
                            </AlertDialogContent>
                        </AlertDialog>
                    </div>

                    <Separator class="my-6" />
                    <div class="space-y-4 text-left">
                        <div class="flex justify-between text-sm">
                            <span
                                class="text-muted-foreground flex items-center gap-2"
                                ><IconCalendar class="size-4" />Tgl Minta</span
                            >
                            <span class="font-medium">{{
                                formatDate(formulir.tanggal_permintaan)
                            }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span
                                class="text-muted-foreground flex items-center gap-2"
                                ><IconFingerprint
                                    class="size-4"
                                />Pemeriksa</span
                            >
                            <span class="font-medium">{{
                                formulir.pemeriksa
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
                            class="text-md font-bold uppercase text-primary"
                            >Detail Pengiriman</CardTitle
                        >
                        <IconTruckDelivery
                            class="size-5 text-muted-foreground"
                        />
                    </CardHeader>
                    <Separator />
                    <CardContent class="pt-6 grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="text-[10px] font-bold text-muted-foreground uppercase"
                                >Size</label
                            >
                            <p class="text-lg font-semibold">
                                {{ formulir.size }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="text-[10px] font-bold text-muted-foreground uppercase"
                                >Quantity</label
                            >
                            <p class="text-lg font-semibold">
                                {{ formulir.qty_sampel_kirim }} Pcs
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm">
                    <CardHeader
                        class="flex flex-row items-center justify-between"
                    >
                        <div>
                            <CardTitle
                                class="text-md font-bold uppercase text-primary"
                                >Alur Proses Departemen</CardTitle
                            >
                            <CardDescription
                                >Daftar departemen yang memproses sampel
                                ini.</CardDescription
                            >
                        </div>
                        <IconHierarchy2 class="size-5 text-muted-foreground" />
                    </CardHeader>
                    <Separator />
                    <CardContent class="pt-0 p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-10 text-center"
                                        >No</TableHead
                                    >
                                    <TableHead>Departemen</TableHead>
                                    <TableHead>Penerima</TableHead>
                                    <TableHead>Tgl Selesai</TableHead>
                                    <TableHead class="text-center"
                                        >QC</TableHead
                                    >
                                    <TableHead class="text-center"
                                        >SPV</TableHead
                                    >
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-if="
                                        formulir.departemen_terlibat.length ===
                                        0
                                    "
                                >
                                    <TableCell
                                        colspan="6"
                                        class="text-center py-10 text-muted-foreground italic"
                                    >
                                        Belum ada departemen yang dikaitkan
                                        dengan formulir ini.
                                    </TableCell>
                                </TableRow>
                                <TableRow
                                    v-for="dept in formulir.departemen_terlibat"
                                    :key="dept.id"
                                >
                                    <TableCell
                                        class="text-center font-bold text-muted-foreground"
                                        >{{ dept.urutan }}</TableCell
                                    >
                                    <TableCell class="font-semibold">{{
                                        dept.nama_departemen
                                    }}</TableCell>
                                    <TableCell class="text-xs">{{
                                        dept.penerima
                                    }}</TableCell>
                                    <TableCell class="text-xs">{{
                                        dept.tanggal_selesai
                                    }}</TableCell>
                                    <TableCell class="text-center">
                                        <IconCheck
                                            v-if="dept.is_qc"
                                            class="size-4 text-green-500 mx-auto"
                                        />
                                        <IconX
                                            v-else
                                            class="size-4 text-muted-foreground/30 mx-auto"
                                        />
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <IconCheck
                                            v-if="dept.is_spv"
                                            class="size-4 text-green-500 mx-auto"
                                        />
                                        <IconX
                                            v-else
                                            class="size-4 text-muted-foreground/30 mx-auto"
                                        />
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm bg-muted/30">
                    <CardContent class="pt-6 flex justify-between items-center">
                        <div>
                            <p
                                class="text-[10px] font-bold text-muted-foreground uppercase leading-none"
                            >
                                Master Sample
                            </p>
                            <p class="text-sm font-bold font-mono">
                                {{ formulir.sampel.kode_sample }} -
                                {{ formulir.sampel.customer }}
                            </p>
                        </div>
                        <Button variant="link" as-child
                            ><Link
                                :href="
                                    route('samples.show', formulir.sampel.id)
                                "
                                >Lihat Master →</Link
                            ></Button
                        >
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
