<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { ref } from "vue";
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
    CardFooter,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
    DropdownMenuSeparator,
} from "@/components/ui/dropdown-menu";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import {
    IconArrowLeft,
    IconDeviceFloppy,
    IconLoader2,
    IconDotsVertical,
    IconTrash,
    IconBuildingCommunity,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    departemen: {
        id: number;
        nama: string;
    };
}>();

const showDeleteDialog = ref(false);

const form = useForm({
    nama: props.departemen.nama,
});

const submit = () => {
    form.put(route("departemens.update", props.departemen.id), {
        preserveScroll: true,
    });
};

const deleteDepartemen = () => {
    router.delete(route("departemens.destroy", props.departemen.id), {
        onSuccess: () => {
            showDeleteDialog.value = false;
        },
    });
};
</script>

<template>
    <Head :title="'Edit Departemen - ' + departemen.nama" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-4">
        <div class="flex items-center">
            <Button
                variant="ghost"
                size="sm"
                as-child
                class="-ml-2 text-muted-foreground hover:text-foreground"
            >
                <Link :href="route('departemens.show', departemen.id)">
                    <IconArrowLeft class="mr-2 size-4" />
                    Kembali ke Detail
                </Link>
            </Button>
        </div>

        <div class="max-w-2xl mx-auto w-full">
            <Card class="border-none shadow-lg bg-card">
                <CardHeader
                    class="pb-8 flex flex-row items-start justify-between space-y-0"
                >
                    <div class="grid gap-1.5">
                        <CardTitle class="text-2xl font-bold italic"
                            >Edit Departemen</CardTitle
                        >
                        <CardDescription class="text-sm">
                            Perbarui nama departemen
                            <span class="text-foreground font-semibold">{{
                                departemen.nama
                            }}</span
                            >.
                        </CardDescription>
                    </div>

                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button
                                variant="ghost"
                                size="icon"
                                class="h-8 w-8 rounded-full"
                            >
                                <IconDotsVertical
                                    class="size-5 text-muted-foreground"
                                />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-48">
                            <div
                                class="px-2 py-1.5 text-xs font-semibold text-muted-foreground uppercase tracking-wider"
                            >
                                Opsi Lanjutan
                            </div>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem
                                @select="showDeleteDialog = true"
                                class="text-destructive focus:bg-destructive/10 focus:text-destructive cursor-pointer"
                            >
                                <IconTrash class="mr-2 size-4" />
                                Hapus Departemen
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </CardHeader>

                <form @submit.prevent="submit">
                    <CardContent class="grid gap-6">
                        <div class="grid gap-3">
                            <Label
                                for="nama"
                                class="text-sm font-medium leading-none ml-0.5 text-primary uppercase text-[10px] font-bold"
                                >Nama Departemen</Label
                            >
                            <div class="relative">
                                <IconBuildingCommunity
                                    class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                                />
                                <Input
                                    id="nama"
                                    v-model="form.nama"
                                    type="text"
                                    placeholder="Masukkan nama departemen"
                                    class="h-11 pl-10 shadow-sm focus-visible:ring-primary uppercase"
                                    :class="{
                                        'border-destructive': form.errors.nama,
                                    }"
                                />
                            </div>
                            <p
                                v-if="form.errors.nama"
                                class="text-xs font-medium text-destructive"
                            >
                                {{ form.errors.nama }}
                            </p>
                        </div>
                    </CardContent>

                    <CardFooter
                        class="flex justify-end gap-3 border-t bg-muted/20 px-6 py-6 rounded-b-lg mt-4"
                    >
                        <Button
                            variant="outline"
                            type="button"
                            as-child
                            :disabled="form.processing"
                            class="h-10 px-6"
                        >
                            <Link :href="route('departemens.index')"
                                >Batal</Link
                            >
                        </Button>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="h-10 px-6 shadow-md transition-all active:scale-95"
                        >
                            <IconLoader2
                                v-if="form.processing"
                                class="mr-2 size-4 animate-spin"
                            />
                            <IconDeviceFloppy v-else class="mr-2 size-4" />
                            Simpan Perubahan
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </div>

    <AlertDialog
        :open="showDeleteDialog"
        @update:open="showDeleteDialog = $event"
    >
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Hapus Departemen?</AlertDialogTitle>
                <AlertDialogDescription>
                    Apakah Anda yakin ingin menghapus departemen
                    <strong>{{ departemen.nama }}</strong
                    >? Tindakan ini tidak dapat dibatalkan.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Batal</AlertDialogCancel>
                <AlertDialogAction
                    @click="deleteDepartemen"
                    class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                >
                    Ya, Hapus Departemen
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
