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
    IconGitMerge,
    IconListNumbers, // Import icon urutan
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    sub: {
        id: number;
        nama: string;
        departemen_id: number;
        urutan: number; // Tambahkan prop urutan
    };
    departemens: Array<{
        id: number;
        nama: string;
    }>;
}>();

const showDeleteDialog = ref(false);

const form = useForm({
    nama: props.sub.nama,
    departemen_id: props.sub.departemen_id,
    urutan: props.sub.urutan, // Masukkan ke dalam form
});

const submit = () => {
    form.put(route("sub.departemens.update", props.sub.id), {
        preserveScroll: true,
    });
};

const deleteSub = () => {
    router.delete(route("sub.departemens.destroy", props.sub.id), {
        onSuccess: () => {
            showDeleteDialog.value = false;
        },
    });
};
</script>

<template>
    <Head :title="'Edit Sub Departemen - ' + sub.nama" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-4">
        <div class="flex items-center">
            <Button
                variant="ghost"
                size="sm"
                as-child
                class="-ml-2 text-muted-foreground hover:text-foreground"
            >
                <Link :href="route('sub.departemens.show', sub.id)">
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
                            >Edit Sub Departemen</CardTitle
                        >
                        <CardDescription class="text-sm">
                            Perbarui informasi untuk unit
                            <span class="text-foreground font-semibold">{{
                                sub.nama
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
                                Hapus Sub Unit
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </CardHeader>

                <form @submit.prevent="submit">
                    <CardContent class="grid gap-6">
                        <div class="grid gap-3">
                            <Label
                                for="departemen_id"
                                class="text-sm font-medium leading-none ml-0.5 text-primary uppercase text-[10px] font-bold"
                                >Departemen Induk</Label
                            >
                            <select
                                id="departemen_id"
                                v-model="form.departemen_id"
                                class="flex h-11 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                                :class="{
                                    'border-destructive':
                                        form.errors.departemen_id,
                                }"
                            >
                                <option
                                    v-for="dept in departemens"
                                    :key="dept.id"
                                    :value="dept.id"
                                >
                                    {{ dept.nama }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.departemen_id"
                                class="text-xs font-medium text-destructive"
                            >
                                {{ form.errors.departemen_id }}
                            </p>
                        </div>

                        <div class="grid gap-3">
                            <Label
                                for="nama"
                                class="text-sm font-medium leading-none ml-0.5 text-primary uppercase text-[10px] font-bold"
                                >Nama Sub Departemen</Label
                            >
                            <div class="relative">
                                <IconGitMerge
                                    class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                                />
                                <Input
                                    id="nama"
                                    v-model="form.nama"
                                    type="text"
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

                        <div class="grid gap-3">
                            <Label
                                for="urutan"
                                class="text-sm font-medium leading-none ml-0.5 text-primary uppercase text-[10px] font-bold"
                                >Urutan Proses</Label
                            >
                            <div class="relative">
                                <IconListNumbers
                                    class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                                />
                                <Input
                                    id="urutan"
                                    v-model="form.urutan"
                                    type="number"
                                    min="1"
                                    placeholder="Contoh: 1"
                                    class="h-11 pl-10 shadow-sm focus-visible:ring-primary"
                                    :class="{
                                        'border-destructive':
                                            form.errors.urutan,
                                    }"
                                />
                            </div>
                            <p
                                v-if="form.errors.urutan"
                                class="text-xs font-medium text-destructive"
                            >
                                {{ form.errors.urutan }}
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
                            <Link :href="route('sub.departemens.show', sub.id)"
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
                <AlertDialogTitle>Hapus Sub Departemen?</AlertDialogTitle>
                <AlertDialogDescription>
                    Apakah Anda yakin ingin menghapus sub departemen
                    <strong>{{ sub.nama }}</strong
                    >? Tindakan ini tidak dapat dibatalkan.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Batal</AlertDialogCancel>
                <AlertDialogAction
                    @click="deleteSub"
                    class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                >
                    Ya, Hapus
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
