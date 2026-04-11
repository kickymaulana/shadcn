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
    IconBuildingCommunity,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const form = useForm({
    nama: "",
});

const submit = () => {
    form.post(route("departemens.store"), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Tambah Departemen" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1 md:pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child>
                <Link :href="route('departemens.index')">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">
                    Tambah Departemen
                </h2>
                <p class="text-muted-foreground text-sm">
                    Buat unit atau divisi kerja baru dalam sistem.
                </p>
            </div>
        </div>

        <div class="max-w-2xl">
            <Card class="border-none shadow-sm">
                <CardHeader>
                    <div class="flex items-center gap-2 mb-1">
                        <IconBuildingCommunity class="size-5 text-primary" />
                        <CardTitle>Informasi Departemen</CardTitle>
                    </div>
                    <CardDescription
                        >Masukkan nama departemen secara spesifik agar tidak
                        membingungkan.</CardDescription
                    >
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="nama">Nama Departemen</Label>
                            <Input
                                id="nama"
                                v-model="form.nama"
                                placeholder="Contoh: MOULD, OVEN, FILLING"
                                autofocus
                                :class="{
                                    'border-destructive': form.errors.nama,
                                }"
                            />
                            <p
                                v-if="form.errors.nama"
                                class="text-sm text-destructive font-medium"
                            >
                                {{ form.errors.nama }}
                            </p>
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
                                Simpan Departemen
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
