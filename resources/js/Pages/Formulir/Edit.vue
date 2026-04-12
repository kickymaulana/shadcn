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
    IconClipboardList,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    formulir: {
        id: number;
        sampel_id: number;
        size: string;
        qty_sampel_kirim: number;
        running_ke: number;
        tanggal_permintaan: string;
        status: string;
    };
    list_samples: Array<{ id: number; kode_sample: string; customer: string }>;
}>();

const showDeleteDialog = ref(false);

const form = useForm({
    sampel_id: props.formulir.sampel_id,
    size: props.formulir.size,
    qty_sampel_kirim: props.formulir.qty_sampel_kirim,
    running_ke: props.formulir.running_ke,
    tanggal_permintaan: props.formulir.tanggal_permintaan,
    status: props.formulir.status,
});

const submit = () => {
    form.put(route("formulirs.update", props.formulir.id), {
        preserveScroll: true,
    });
};

const deleteFormulir = () => {
    router.delete(route("formulirs.destroy", props.formulir.id), {
        onSuccess: () => {
            showDeleteDialog.value = false;
        },
    });
};
</script>

<template>
    <Head :title="'Edit Formulir #' + formulir.id" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-4">
        <div class="flex items-center">
            <Button
                variant="ghost"
                size="sm"
                as-child
                class="-ml-2 text-muted-foreground hover:text-foreground"
            >
                <Link :href="route('formulirs.show', formulir.id)">
                    <IconArrowLeft class="mr-2 size-4" />
                    Kembali ke Detail
                </Link>
            </Button>
        </div>

        <div class="max-w-4xl mx-auto w-full">
            <Card class="border-none shadow-lg bg-card">
                <CardHeader
                    class="pb-8 flex flex-row items-start justify-between space-y-0"
                >
                    <div class="grid gap-1.5">
                        <CardTitle
                            class="text-2xl font-bold italic uppercase tracking-tight"
                            >Edit Formulir</CardTitle
                        >
                        <CardDescription class="text-sm">
                            Perbarui rincian permintaan untuk ID:
                            <span class="text-foreground font-semibold"
                                >#{{ formulir.id }}</span
                            >
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
                                Hapus Formulir
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </CardHeader>

                <form @submit.prevent="submit">
                    <CardContent class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="grid gap-2">
                            <Label for="sampel_id" class="font-bold"
                                >Referensi Master Sample</Label
                            >
                            <select
                                id="sampel_id"
                                v-model="form.sampel_id"
                                class="flex h-11 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-primary"
                                :class="{
                                    'border-destructive': form.errors.sampel_id,
                                }"
                            >
                                <option
                                    v-for="s in list_samples"
                                    :key="s.id"
                                    :value="s.id"
                                >
                                    {{ s.kode_sample }} - {{ s.customer }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.sampel_id"
                                class="text-xs text-destructive"
                            >
                                {{ form.errors.sampel_id }}
                            </p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="tanggal_permintaan" class="font-bold"
                                >Tanggal Permintaan</Label
                            >
                            <Input
                                id="tanggal_permintaan"
                                type="date"
                                v-model="form.tanggal_permintaan"
                                class="h-11"
                                :class="{
                                    'border-destructive':
                                        form.errors.tanggal_permintaan,
                                }"
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="size" class="font-bold"
                                >Size / Ukuran</Label
                            >
                            <Input
                                id="size"
                                v-model="form.size"
                                class="h-11"
                                :class="{
                                    'border-destructive': form.errors.size,
                                }"
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="qty_sampel_kirim" class="font-bold"
                                >Quantity (Pcs)</Label
                            >
                            <Input
                                id="qty_sampel_kirim"
                                type="number"
                                v-model="form.qty_sampel_kirim"
                                class="h-11"
                                :class="{
                                    'border-destructive':
                                        form.errors.qty_sampel_kirim,
                                }"
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="running_ke" class="font-bold"
                                >Running Ke-</Label
                            >
                            <Input
                                id="running_ke"
                                type="number"
                                v-model="form.running_ke"
                                class="h-11"
                                :class="{
                                    'border-destructive':
                                        form.errors.running_ke,
                                }"
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="status" class="font-bold">Status</Label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="flex h-11 w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-primary"
                                :class="{
                                    'border-destructive': form.errors.status,
                                }"
                            >
                                <option value="Draft">Draft</option>
                                <option value="Proses">Proses</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
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
                            <Link :href="route('formulirs.show', formulir.id)"
                                >Batal</Link
                            >
                        </Button>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="h-10 px-6 shadow-md"
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
                <AlertDialogTitle>Hapus Formulir?</AlertDialogTitle>
                <AlertDialogDescription
                    >Apakah Anda yakin ingin menghapus formulir
                    ini?</AlertDialogDescription
                >
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Batal</AlertDialogCancel>
                <AlertDialogAction
                    @click="deleteFormulir"
                    class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                >
                    Ya, Hapus
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
