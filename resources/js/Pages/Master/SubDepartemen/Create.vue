<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
    IconArrowLeft,
    IconDeviceFloppy,
    IconGitMerge,
    IconListNumbers,
    IconLoader2,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    departemens: Array<{ id: number; nama: string }>;
}>();

const form = useForm({
    departemen_id: "",
    nama: "",
    urutan: "", // Tambahkan field urutan
});

const submit = () => {
    form.post(route("sub.departemens.store"), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Tambah Sub Departemen" />
    <div class="p-4 md:p-8">
        <div class="flex items-center gap-4 mb-6">
            <Button variant="outline" size="icon" as-child>
                <Link :href="route('sub.departemens.index')">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <h2 class="text-3xl font-bold tracking-tight">
                Tambah Sub Departemen
            </h2>
        </div>

        <Card class="max-w-2xl border-none shadow-sm">
            <CardHeader>
                <CardTitle class="flex items-center gap-2 text-primary">
                    <IconGitMerge /> Informasi Sub Unit
                </CardTitle>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label
                            class="font-bold uppercase text-[10px] text-muted-foreground"
                            >Pilih Departemen Induk</Label
                        >
                        <select
                            v-model="form.departemen_id"
                            class="flex h-11 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                            :class="{
                                'border-destructive': form.errors.departemen_id,
                            }"
                        >
                            <option value="" disabled>
                                Pilih Departemen...
                            </option>
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
                            class="text-xs text-destructive font-medium"
                        >
                            {{ form.errors.departemen_id }}
                        </p>
                    </div>

                    <div class="grid gap-2">
                        <Label
                            class="font-bold uppercase text-[10px] text-muted-foreground"
                            >Nama Sub Departemen</Label
                        >
                        <div class="relative">
                            <IconGitMerge
                                class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                            />
                            <Input
                                v-model="form.nama"
                                placeholder="Contoh: LINE 1, QC PASS, dll"
                                class="h-11 pl-10 uppercase shadow-sm"
                                :class="{
                                    'border-destructive': form.errors.nama,
                                }"
                            />
                        </div>
                        <p
                            v-if="form.errors.nama"
                            class="text-xs text-destructive font-medium"
                        >
                            {{ form.errors.nama }}
                        </p>
                    </div>

                    <div class="grid gap-2">
                        <Label
                            class="font-bold uppercase text-[10px] text-muted-foreground"
                            >Urutan Proses</Label
                        >
                        <div class="relative">
                            <IconListNumbers
                                class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"
                            />
                            <Input
                                v-model="form.urutan"
                                type="number"
                                placeholder="Contoh: 1"
                                class="h-11 pl-10 shadow-sm"
                                :class="{
                                    'border-destructive': form.errors.urutan,
                                }"
                            />
                        </div>
                        <p
                            v-if="form.errors.urutan"
                            class="text-xs text-destructive font-medium"
                        >
                            {{ form.errors.urutan }}
                        </p>
                    </div>

                    <div class="pt-2">
                        <Button
                            type="submit"
                            class="w-full sm:w-auto min-w-[200px]"
                            :disabled="form.processing"
                        >
                            <IconLoader2
                                v-if="form.processing"
                                class="mr-2 size-4 animate-spin"
                            />
                            <IconDeviceFloppy v-else class="mr-2 size-4" />
                            Simpan Sub Departemen
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
