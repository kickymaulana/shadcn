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
    IconBuildingCommunity,
    IconCalendar,
    IconFingerprint,
    IconClock,
    IconGitMerge,
    IconTrash,
    IconListNumbers, // Tambah icon urutan
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

// 1. Definisikan Persistent Layout
defineOptions({ layout: AuthenticatedLayout });

// 2. Definisi Props (Tambah properti urutan)
const props = defineProps<{
    sub: {
        id: number;
        nama: string;
        urutan: number; // Tambahkan ini
        created_at: string;
        updated_at: string;
        departemen: {
            id: number;
            nama: string;
        };
    };
}>();

const deleteSub = () => {
    router.delete(route("sub.departemens.destroy", props.sub.id), {
        preserveScroll: true,
    });
};

// 3. Helper Formatter
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
    <Head :title="'Detail Sub Departemen - ' + sub.nama" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-4">
        <div class="flex items-center gap-2">
            <Button
                variant="ghost"
                size="sm"
                as-child
                class="-ml-2 text-muted-foreground hover:text-foreground"
            >
                <Link :href="route('sub.departemens.index')">
                    <IconArrowLeft class="mr-2 size-4" />
                    Kembali ke Daftar
                </Link>
            </Button>
        </div>

        <div class="max-w-3xl mx-auto w-full">
            <Card class="border-none shadow-sm h-fit">
                <CardContent class="pt-6">
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="size-24 rounded-2xl bg-primary/10 flex items-center justify-center mb-4 relative"
                        >
                            <IconGitMerge class="size-12 text-primary" />
                            <div
                                class="absolute -top-2 -right-2 size-8 bg-primary text-primary-foreground rounded-full flex items-center justify-center font-bold border-4 border-background text-xs"
                            >
                                {{ sub.urutan }}
                            </div>
                        </div>
                        <h2 class="text-xl font-bold uppercase tracking-tight">
                            {{ sub.nama }}
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            Sub Departemen dari
                            <span class="font-semibold text-foreground">{{
                                sub.departemen.nama
                            }}</span>
                        </p>

                        <div
                            class="w-full mt-6 flex flex-col sm:flex-row gap-2 justify-center"
                        >
                            <Button
                                variant="outline"
                                as-child
                                class="w-full sm:w-auto"
                            >
                                <Link
                                    :href="
                                        route('sub.departemens.edit', sub.id)
                                    "
                                >
                                    <IconPencil class="mr-2 size-4" />
                                    Edit Sub Departemen
                                </Link>
                            </Button>

                            <AlertDialog>
                                <AlertDialogTrigger as-child>
                                    <Button
                                        variant="destructive"
                                        class="w-full sm:w-auto"
                                    >
                                        <IconTrash class="mr-2 size-4" />
                                        Hapus Sub
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle
                                            >Hapus sub departemen
                                            ini?</AlertDialogTitle
                                        >
                                        <AlertDialogDescription>
                                            Apakah Anda yakin ingin menghapus
                                            sub departemen
                                            <strong>{{ sub.nama }}</strong
                                            >? Tindakan ini tidak dapat
                                            dibatalkan.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel
                                            >Batal</AlertDialogCancel
                                        >
                                        <AlertDialogAction
                                            @click="deleteSub"
                                            class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                        >
                                            Ya, Hapus
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        </div>
                    </div>

                    <Separator class="my-8" />

                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-8"
                    >
                        <div class="flex items-center gap-3 text-sm">
                            <IconListNumbers
                                class="size-5 text-muted-foreground"
                            />
                            <div class="flex flex-col">
                                <span
                                    class="text-[10px] uppercase font-bold text-muted-foreground leading-none mb-1"
                                    >Urutan Proses</span
                                >
                                <span class="font-medium"
                                    >Ke-{{ sub.urutan }}</span
                                >
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-sm">
                            <IconBuildingCommunity
                                class="size-5 text-muted-foreground"
                            />
                            <div class="flex flex-col">
                                <span
                                    class="text-[10px] uppercase font-bold text-muted-foreground leading-none mb-1"
                                    >Departemen Induk</span
                                >
                                <span class="font-medium uppercase italic">{{
                                    sub.departemen.nama
                                }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-sm">
                            <IconFingerprint
                                class="size-5 text-muted-foreground"
                            />
                            <div class="flex flex-col">
                                <span
                                    class="text-[10px] uppercase font-bold text-muted-foreground leading-none mb-1"
                                    >ID Internal</span
                                >
                                <span class="font-mono font-medium"
                                    >#{{ sub.id }}</span
                                >
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-sm">
                            <IconCalendar
                                class="size-5 text-muted-foreground"
                            />
                            <div class="flex flex-col">
                                <span
                                    class="text-[10px] uppercase font-bold text-muted-foreground leading-none mb-1"
                                    >Dibuat Pada</span
                                >
                                <span class="font-medium">{{
                                    formatDate(sub.created_at)
                                }}</span>
                            </div>
                        </div>

                        <div
                            class="flex items-center gap-3 text-sm md:col-span-2"
                        >
                            <IconClock class="size-5 text-muted-foreground" />
                            <div class="flex flex-col">
                                <span
                                    class="text-[10px] uppercase font-bold text-muted-foreground leading-none mb-1"
                                    >Terakhir Update</span
                                >
                                <span class="font-medium">{{
                                    formatDate(sub.updated_at)
                                }}</span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
