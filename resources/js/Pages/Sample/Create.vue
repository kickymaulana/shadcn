<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from "@/components/ui/card";
import {
    IconArrowLeft,
    IconDeviceFloppy,
    IconLoader2,
    IconPackage,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const form = useForm({
    kode_sample: "",
    diajukan_oleh: "",
    kepada: "",
    customer: "",
    model: "",
    spesifikasi: "",
});

const submit = () => {
    form.post(route("samples.store"), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Tambah Sample" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child>
                <Link :href="route('samples.index')">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Tambah Sample</h2>
                <p class="text-muted-foreground text-sm">
                    Input data master sample baru ke dalam sistem.
                </p>
            </div>
        </div>

        <div class="max-w-4xl">
            <Card class="border-none shadow-sm">
                <CardHeader>
                    <div class="flex items-center gap-2 mb-1">
                        <IconPackage class="size-5 text-primary" />
                        <CardTitle>Informasi Sample</CardTitle>
                    </div>
                    <CardDescription
                        >Lengkapi formulir di bawah ini dengan
                        benar.</CardDescription
                    >
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="kode_sample">Kode Sample</Label>
                                <Input
                                    id="kode_sample"
                                    v-model="form.kode_sample"
                                    placeholder="Contoh: SMP-2024-001"
                                    :class="{
                                        'border-destructive':
                                            form.errors.kode_sample,
                                    }"
                                />
                                <p
                                    v-if="form.errors.kode_sample"
                                    class="text-sm text-destructive font-medium"
                                >
                                    {{ form.errors.kode_sample }}
                                </p>
                            </div>

                            <div class="grid gap-2">
                                <Label for="customer">Customer</Label>
                                <Input
                                    id="customer"
                                    v-model="form.customer"
                                    placeholder="Nama Perusahaan/Client"
                                    :class="{
                                        'border-destructive':
                                            form.errors.customer,
                                    }"
                                />
                                <p
                                    v-if="form.errors.customer"
                                    class="text-sm text-destructive font-medium"
                                >
                                    {{ form.errors.customer }}
                                </p>
                            </div>

                            <div class="grid gap-2">
                                <Label for="diajukan_oleh">Diajukan Oleh</Label>
                                <Input
                                    id="diajukan_oleh"
                                    v-model="form.diajukan_oleh"
                                    placeholder="Nama Pemohon"
                                    :class="{
                                        'border-destructive':
                                            form.errors.diajukan_oleh,
                                    }"
                                />
                                <p
                                    v-if="form.errors.diajukan_oleh"
                                    class="text-sm text-destructive font-medium"
                                >
                                    {{ form.errors.diajukan_oleh }}
                                </p>
                            </div>

                            <div class="grid gap-2">
                                <Label for="kepada">Kepada</Label>
                                <Input
                                    id="kepada"
                                    v-model="form.kepada"
                                    placeholder="Tujuan Sample"
                                    :class="{
                                        'border-destructive':
                                            form.errors.kepada,
                                    }"
                                />
                                <p
                                    v-if="form.errors.kepada"
                                    class="text-sm text-destructive font-medium"
                                >
                                    {{ form.errors.kepada }}
                                </p>
                            </div>

                            <div class="grid gap-2">
                                <Label for="model">Model / Nama Barang</Label>
                                <Input
                                    id="model"
                                    v-model="form.model"
                                    placeholder="Contoh: Type-X Gen 2"
                                    :class="{
                                        'border-destructive': form.errors.model,
                                    }"
                                />
                                <p
                                    v-if="form.errors.model"
                                    class="text-sm text-destructive font-medium"
                                >
                                    {{ form.errors.model }}
                                </p>
                            </div>

                            <div class="grid gap-2">
                                <Label for="spesifikasi">Spesifikasi</Label>
                                <Input
                                    id="spesifikasi"
                                    v-model="form.spesifikasi"
                                    placeholder="Contoh: Ukuran 10cm, Bahan Plastik"
                                    :class="{
                                        'border-destructive':
                                            form.errors.spesifikasi,
                                    }"
                                />
                                <p
                                    v-if="form.errors.spesifikasi"
                                    class="text-sm text-destructive font-medium"
                                >
                                    {{ form.errors.spesifikasi }}
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="min-w-[140px]"
                            >
                                <IconLoader2
                                    v-if="form.processing"
                                    class="mr-2 size-4 animate-spin"
                                />
                                <IconDeviceFloppy v-else class="mr-2 size-4" />
                                Simpan Sample
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
