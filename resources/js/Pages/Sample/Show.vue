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
import {
    IconArrowLeft,
    IconPencil,
    IconPackage,
    IconCalendar,
    IconFingerprint,
    IconClock,
    IconTrash,
    IconClipboardList,
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
    sample: {
        id: number;
        kode_sample: string;
        diajukan_oleh: string;
        kepada: string;
        customer: string;
        model: string;
        spesifikasi: string;
        created_at: string;
        updated_at: string;
    };
}>();

const deleteSample = () => {
    router.delete(route("samples.destroy", props.sample.id));
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};
</script>

<template>
    <Head :title="'Detail Sample - ' + sample.kode_sample" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-4">
        <div class="flex items-center gap-2">
            <Button
                variant="ghost"
                size="sm"
                as-child
                class="-ml-2 text-muted-foreground hover:text-foreground"
            >
                <Link :href="route('samples.index')">
                    <IconArrowLeft class="mr-2 size-4" />
                    Kembali ke Daftar
                </Link>
            </Button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <Card class="border-none shadow-sm col-span-1 h-fit">
                <CardContent class="pt-6">
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="size-24 rounded-2xl bg-primary/10 flex items-center justify-center mb-4"
                        >
                            <IconPackage class="size-12 text-primary" />
                        </div>
                        <h2 class="text-xl font-bold uppercase tracking-tight">
                            {{ sample.kode_sample }}
                        </h2>
                        <p class="text-sm text-muted-foreground font-medium">
                            {{ sample.customer }}
                        </p>

                        <div class="w-full mt-6 flex flex-col gap-2">
                            <Button class="w-full" variant="outline" as-child>
                                <Link :href="route('samples.edit', sample.id)">
                                    <IconPencil class="mr-2 size-4" />
                                    Edit Data Sample
                                </Link>
                            </Button>

                            <AlertDialog>
                                <AlertDialogTrigger as-child>
                                    <Button
                                        variant="destructive"
                                        class="w-full"
                                    >
                                        <IconTrash class="mr-2 size-4" />
                                        Hapus Sample
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle
                                            >Hapus sample ini?</AlertDialogTitle
                                        >
                                        <AlertDialogDescription>
                                            Apakah Anda yakin ingin menghapus
                                            data sample
                                            <strong>{{
                                                sample.kode_sample
                                            }}</strong
                                            >? Tindakan ini permanen.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel
                                            >Batal</AlertDialogCancel
                                        >
                                        <AlertDialogAction
                                            @click="deleteSample"
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
                                <IconFingerprint class="size-4" />
                                <span>ID Database</span>
                            </div>
                            <span class="font-mono font-medium"
                                >#{{ sample.id }}</span
                            >
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <div
                                class="flex items-center text-muted-foreground gap-2"
                            >
                                <IconCalendar class="size-4" />
                                <span>Dibuat</span>
                            </div>
                            <span class="font-medium text-right">{{
                                formatDate(sample.created_at)
                            }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <div
                                class="flex items-center text-muted-foreground gap-2"
                            >
                                <IconClock class="size-4" />
                                <span>Terakhir Update</span>
                            </div>
                            <span class="font-medium text-right">{{
                                formatDate(sample.updated_at)
                            }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card class="border-none shadow-sm lg:col-span-2">
                <CardHeader class="flex flex-row items-center justify-between">
                    <div class="grid gap-1">
                        <CardTitle>Spesifikasi teknis</CardTitle>
                        <CardDescription
                            >Detail atribut dan tujuan pengiriman
                            sample.</CardDescription
                        >
                    </div>
                    <IconClipboardList class="size-5 text-muted-foreground" />
                </CardHeader>
                <Separator />
                <CardContent class="pt-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="text-xs font-bold uppercase text-muted-foreground"
                                >Model / Nama</label
                            >
                            <p class="text-lg font-semibold">
                                {{ sample.model }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="text-xs font-bold uppercase text-muted-foreground"
                                >Diajukan Oleh</label
                            >
                            <p class="text-lg font-semibold">
                                {{ sample.diajukan_oleh }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="text-xs font-bold uppercase text-muted-foreground"
                                >Kepada</label
                            >
                            <p class="text-lg font-semibold">
                                {{ sample.kepada }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <label
                            class="text-xs font-bold uppercase text-muted-foreground"
                            >Detail Spesifikasi</label
                        >
                        <div
                            class="mt-2 p-4 rounded-lg bg-muted/50 border italic text-foreground"
                        >
                            {{ sample.spesifikasi }}
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
