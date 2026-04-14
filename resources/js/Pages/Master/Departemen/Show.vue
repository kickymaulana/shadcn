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
    IconBuildingCommunity,
    IconCalendar,
    IconFingerprint,
    IconClock,
    IconUsers,
    IconTrash,
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

// 2. Definisi Props
const props = defineProps<{
    departemen: {
        id: number;
        nama: string;
        created_at: string;
        updated_at: string;
        users: Array<{
            id: number;
            name: string;
            username: string;
            email: string;
        }>;
    };
}>();

const deleteDepartemen = () => {
    router.delete(route("departemens.destroy", props.departemen.id), {
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
    <Head :title="'Detail Departemen - ' + departemen.nama" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-4">
        <div class="flex items-center gap-2">
            <Button
                variant="ghost"
                size="sm"
                as-child
                class="-ml-2 text-muted-foreground hover:text-foreground"
            >
                <Link :href="route('departemens.index')">
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
                            <IconBuildingCommunity
                                class="size-12 text-primary"
                            />
                        </div>
                        <h2 class="text-xl font-bold uppercase tracking-tight">
                            {{ departemen.nama }}
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            Unit Departemen
                        </p>

                        <div class="w-full mt-6 flex flex-col gap-2">
                            <Button class="w-full" variant="outline" as-child>
                                <Link
                                    :href="
                                        route('departemens.edit', departemen.id)
                                    "
                                >
                                    <IconPencil class="mr-2 size-4" />
                                    Edit Departemen
                                </Link>
                            </Button>

                            <AlertDialog>
                                <AlertDialogTrigger as-child>
                                    <Button
                                        variant="destructive"
                                        class="w-full"
                                    >
                                        <IconTrash class="mr-2 size-4" />
                                        Hapus Departemen
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle
                                            >Hapus departemen
                                            ini?</AlertDialogTitle
                                        >
                                        <AlertDialogDescription>
                                            Apakah Anda yakin ingin menghapus
                                            departemen
                                            <strong>{{
                                                departemen.nama
                                            }}</strong
                                            >? User yang terkait mungkin akan
                                            kehilangan referensi departemen
                                            mereka.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel
                                            >Batal</AlertDialogCancel
                                        >
                                        <AlertDialogAction
                                            @click="deleteDepartemen"
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
                                <span>ID Internal</span>
                            </div>
                            <span class="font-mono font-medium"
                                >#{{ departemen.id }}</span
                            >
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <div
                                class="flex items-center text-muted-foreground gap-2"
                            >
                                <IconCalendar class="size-4" />
                                <span>Dibuat</span>
                            </div>
                            <span class="font-medium">{{
                                formatDate(departemen.created_at)
                            }}</span>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <div
                                class="flex items-center text-muted-foreground gap-2"
                            >
                                <IconClock class="size-4" />
                                <span>Terakhir Update</span>
                            </div>
                            <span class="font-medium">{{
                                formatDate(departemen.updated_at)
                            }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card class="border-none shadow-sm lg:col-span-2">
                <CardHeader class="flex flex-row items-center justify-between">
                    <div class="grid gap-1">
                        <CardTitle>Daftar Anggota</CardTitle>
                        <CardDescription
                            >Para pengguna yang ditugaskan di departemen
                            ini.</CardDescription
                        >
                    </div>
                    <IconUsers class="size-5 text-muted-foreground" />
                </CardHeader>
                <Separator />
                <CardContent class="pt-6">
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow class="bg-muted/50">
                                    <TableHead>Nama Pengguna</TableHead>
                                    <TableHead>Username</TableHead>
                                    <TableHead>Email</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="departemen.users.length === 0">
                                    <TableCell
                                        colspan="3"
                                        class="h-24 text-center text-muted-foreground italic"
                                    >
                                        Belum ada anggota yang terdaftar di
                                        departemen ini.
                                    </TableCell>
                                </TableRow>
                                <TableRow
                                    v-for="member in departemen.users"
                                    :key="member.id"
                                >
                                    <TableCell class="font-medium">{{
                                        member.name
                                    }}</TableCell>
                                    <TableCell>
                                        <span
                                            class="text-xs font-mono bg-muted px-1.5 py-0.5 rounded"
                                            >@{{ member.username }}</span
                                        >
                                    </TableCell>
                                    <TableCell class="text-sm">{{
                                        member.email
                                    }}</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
