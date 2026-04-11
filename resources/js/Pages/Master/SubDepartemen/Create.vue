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
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    departemens: Array<{ id: number; nama: string }>;
}>();

const form = useForm({
    departemen_id: "",
    nama: "",
});

const submit = () => {
    form.post(route("sub.departemens.store"));
};
</script>

<template>
    <Head title="Tambah Sub Departemen" />
    <div class="p-4 md:p-8">
        <div class="flex items-center gap-4 mb-6">
            <Button variant="outline" size="icon" as-child>
                <Link :href="route('sub.departemens.index')"
                    ><IconArrowLeft class="size-4"
                /></Link>
            </Button>
            <h2 class="text-3xl font-bold">Tambah Sub Departemen</h2>
        </div>

        <Card class="max-w-2xl border-none shadow-sm">
            <CardHeader>
                <CardTitle class="flex items-center gap-2"
                    ><IconGitMerge class="text-primary" /> Informasi Sub
                    Unit</CardTitle
                >
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label>Pilih Departemen Induk</Label>
                        <select
                            v-model="form.departemen_id"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background"
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
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.departemen_id }}
                        </p>
                    </div>
                    <div class="grid gap-2">
                        <Label>Nama Sub Departemen</Label>
                        <Input
                            v-model="form.nama"
                            placeholder="Contoh: LINE 1, QC PASS, dll"
                        />
                        <p
                            v-if="form.errors.nama"
                            class="text-sm text-destructive"
                        >
                            {{ form.errors.nama }}
                        </p>
                    </div>
                    <Button type="submit" :disabled="form.processing">
                        <IconDeviceFloppy class="mr-2 size-4" /> Simpan Sub
                        Departemen
                    </Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
