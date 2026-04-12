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
    IconArrowLeft,
    IconPencil,
    IconFileDescription,
    IconCalendar,
    IconFingerprint,
    IconClock,
    IconTrash,
    IconClipboardList,
    IconTruckDelivery,
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
                <CardContent class="pt-6">
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="size-20 rounded-full bg-primary/10 flex items-center justify-center mb-4"
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
                                <Link
                                    :href="route('formulirs.edit', formulir.id)"
                                >
                                    <IconPencil class="mr-2 size-4" />
                                    Update Formulir
                                </Link>
                            </Button>

                            <AlertDialog>
                                <AlertDialogTrigger as-child>
                                    <Button
                                        variant="destructive"
                                        class="w-full"
                                    >
                                        <IconTrash class="mr-2 size-4" />
                                        Hapus Formulir
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle
                                            >Hapus formulir
                                            ini?</AlertDialogTitle
                                        >
                                        <AlertDialogDescription>
                                            Menghapus formulir
                                            <strong>#{{ formulir.id }}</strong>
                                            akan menghilangkan riwayat
                                            permintaan ini.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel
                                            >Batal</AlertDialogCancel
                                        >
                                        <AlertDialogAction
                                            @click="deleteFormulir"
                                            class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                        >
                                            Ya, Hapus
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        </div>
                    </div>

                    <Separator class="my-6" />

                    <div class="space-y-4">
                        <div class="flex items-center justify-between text-sm">
                            <div
                                class="flex items-center text-muted-foreground gap-2"
                            >
                                <IconCalendar class="size-4" />
                                <span>Tgl Permintaan</span>
                            </div>
                            <span class="font-medium">{{
                                formatDate(formulir.tanggal_permintaan)
                            }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <div
                                class="flex items-center text-muted-foreground gap-2"
                            >
                                <IconFingerprint class="size-4" />
                                <span>Pemeriksa</span>
                            </div>
                            <span class="font-medium text-xs">{{
                                formulir.pemeriksa
                            }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <div
                                class="flex items-center text-muted-foreground gap-2"
                            >
                                <IconFingerprint class="size-4" />
                                <span>Penyetuju</span>
                            </div>
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
                            class="text-md font-bold uppercase tracking-wider text-primary"
                            >Detail Pengiriman</CardTitle
                        >
                        <IconTruckDelivery
                            class="size-5 text-muted-foreground"
                        />
                    </CardHeader>
                    <Separator />
                    <CardContent class="pt-6 grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label
                                class="text-[10px] font-bold text-muted-foreground uppercase"
                                >Size / Ukuran</label
                            >
                            <p class="text-lg font-semibold">
                                {{ formulir.size }}
                            </p>
                        </div>
                        <div class="space-y-1">
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

                <Card class="border-none shadow-sm bg-muted/30">
                    <CardHeader class="pb-2">
                        <CardTitle
                            class="text-md font-bold uppercase tracking-wider text-muted-foreground"
                            >Referensi Master Sample</CardTitle
                        >
                    </CardHeader>
                    <CardContent class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label
                                class="text-[10px] font-bold text-muted-foreground uppercase"
                                >Kode Sample</label
                            >
                            <p class="text-sm font-bold font-mono">
                                {{ formulir.sampel.kode_sample }}
                            </p>
                        </div>
                        <div class="space-y-1">
                            <label
                                class="text-[10px] font-bold text-muted-foreground uppercase"
                                >Customer</label
                            >
                            <p class="text-sm font-medium">
                                {{ formulir.sampel.customer }}
                            </p>
                        </div>
                        <div class="space-y-1 col-span-2">
                            <label
                                class="text-[10px] font-bold text-muted-foreground uppercase"
                                >Model</label
                            >
                            <p class="text-sm font-medium">
                                {{ formulir.sampel.model }}
                            </p>
                        </div>
                        <div class="col-span-2 pt-2">
                            <Button
                                variant="link"
                                class="p-0 h-auto text-xs"
                                as-child
                            >
                                <Link
                                    :href="
                                        route(
                                            'samples.show',
                                            formulir.sampel.id,
                                        )
                                    "
                                >
                                    Lihat Master Sample Lengkap →
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
