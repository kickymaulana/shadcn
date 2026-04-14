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
    IconPackage,
} from "@tabler/icons-vue";

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
    };
}>();

const showDeleteDialog = ref(false);

const form = useForm({
    kode_sample: props.sample.kode_sample,
    diajukan_oleh: props.sample.diajukan_oleh,
    kepada: props.sample.kepada,
    customer: props.sample.customer,
    model: props.sample.model,
    spesifikasi: props.sample.spesifikasi,
});

const submit = () => {
    form.put(route("samples.update", props.sample.id), {
        preserveScroll: true,
    });
};

const deleteSample = () => {
    router.delete(route("samples.destroy", props.sample.id), {
        onSuccess: () => {
            showDeleteDialog.value = false;
        },
    });
};
</script>

<template>
    <Head :title="'Edit Sample - ' + sample.kode_sample" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-4">
        <div class="flex items-center">
            <Button
                variant="ghost"
                size="sm"
                as-child
                class="-ml-2 text-muted-foreground hover:text-foreground"
            >
                <Link :href="route('samples.show', sample.id)">
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
                        <CardTitle class="text-2xl font-bold italic"
                            >Edit Data Sample</CardTitle
                        >
                        <CardDescription class="text-sm">
                            Perbarui informasi untuk kode:
                            <span class="text-foreground font-semibold">{{
                                sample.kode_sample
                            }}</span>
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
                                Hapus Sample
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </CardHeader>

                <form @submit.prevent="submit">
                    <CardContent class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="grid gap-2">
                            <Label for="kode_sample">Kode Sample</Label>
                            <Input
                                id="kode_sample"
                                v-model="form.kode_sample"
                                class="h-11"
                                :class="{
                                    'border-destructive':
                                        form.errors.kode_sample,
                                }"
                            />
                            <p
                                v-if="form.errors.kode_sample"
                                class="text-xs text-destructive"
                            >
                                {{ form.errors.kode_sample }}
                            </p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="customer">Customer</Label>
                            <Input
                                id="customer"
                                v-model="form.customer"
                                class="h-11"
                                :class="{
                                    'border-destructive': form.errors.customer,
                                }"
                            />
                            <p
                                v-if="form.errors.customer"
                                class="text-xs text-destructive"
                            >
                                {{ form.errors.customer }}
                            </p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="diajukan_oleh">Diajukan Oleh</Label>
                            <Input
                                id="diajukan_oleh"
                                v-model="form.diajukan_oleh"
                                class="h-11"
                                :class="{
                                    'border-destructive':
                                        form.errors.diajukan_oleh,
                                }"
                            />
                            <p
                                v-if="form.errors.diajukan_oleh"
                                class="text-xs text-destructive"
                            >
                                {{ form.errors.diajukan_oleh }}
                            </p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="kepada">Kepada</Label>
                            <Input
                                id="kepada"
                                v-model="form.kepada"
                                class="h-11"
                                :class="{
                                    'border-destructive': form.errors.kepada,
                                }"
                            />
                            <p
                                v-if="form.errors.kepada"
                                class="text-xs text-destructive"
                            >
                                {{ form.errors.kepada }}
                            </p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="model">Model</Label>
                            <Input
                                id="model"
                                v-model="form.model"
                                class="h-11"
                                :class="{
                                    'border-destructive': form.errors.model,
                                }"
                            />
                            <p
                                v-if="form.errors.model"
                                class="text-xs text-destructive"
                            >
                                {{ form.errors.model }}
                            </p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="spesifikasi">Spesifikasi</Label>
                            <Input
                                id="spesifikasi"
                                v-model="form.spesifikasi"
                                class="h-11"
                                :class="{
                                    'border-destructive':
                                        form.errors.spesifikasi,
                                }"
                            />
                            <p
                                v-if="form.errors.spesifikasi"
                                class="text-xs text-destructive"
                            >
                                {{ form.errors.spesifikasi }}
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
                            <Link :href="route('samples.show', sample.id)"
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
                <AlertDialogTitle>Hapus Data Sample?</AlertDialogTitle>
                <AlertDialogDescription>
                    Apakah Anda yakin ingin menghapus data
                    <strong>{{ sample.kode_sample }}</strong
                    >?
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>Batal</AlertDialogCancel>
                <AlertDialogAction
                    @click="deleteSample"
                    class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                >
                    Ya, Hapus Data
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
