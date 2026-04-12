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
    IconFilePlus,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    list_samples: Array<{ id: number; kode_sample: string; customer: string }>;
}>();

const form = useForm({
    sampel_id: "",
    size: "",
    qty_sampel_kirim: 1,
    running_ke: 1,
    tanggal_permintaan: new Date().toISOString().split("T")[0],
    status: "Draft",
});

const submit = () => {
    form.post(route("formulirs.store"));
};
</script>

<template>
    <Head title="Buat Formulir" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child>
                <Link :href="route('formulirs.index')">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <div>
                <h2
                    class="text-3xl font-bold tracking-tight text-primary uppercase italic"
                >
                    New Form
                </h2>
                <p class="text-muted-foreground text-sm italic">
                    SISAMCUS - Permintaan Sampel Customer
                </p>
            </div>
        </div>

        <div class="max-w-4xl">
            <Card class="border-none shadow-sm">
                <CardHeader>
                    <div class="flex items-center gap-2 mb-1">
                        <IconFilePlus class="size-5 text-primary" />
                        <CardTitle>Formulir Pengajuan</CardTitle>
                    </div>
                    <CardDescription
                        >Input detail pengiriman sampel ke
                        customer.</CardDescription
                    >
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4"
                        >
                            <div class="grid gap-2">
                                <Label for="sampel_id" class="font-bold"
                                    >Master Sampel</Label
                                >
                                <select
                                    id="sampel_id"
                                    v-model="form.sampel_id"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                    :class="{
                                        'border-destructive':
                                            form.errors.sampel_id,
                                    }"
                                >
                                    <option value="" disabled>
                                        -- Pilih Kode Sample --
                                    </option>
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
                                <Label
                                    for="tanggal_permintaan"
                                    class="font-bold"
                                    >Tanggal Permintaan</Label
                                >
                                <Input
                                    id="tanggal_permintaan"
                                    type="date"
                                    v-model="form.tanggal_permintaan"
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
                                    placeholder="Contoh: 38-42 atau L"
                                    :class="{
                                        'border-destructive': form.errors.size,
                                    }"
                                />
                            </div>

                            <div class="grid gap-2">
                                <Label for="qty_sampel_kirim" class="font-bold"
                                    >QTY Kirim (Pcs)</Label
                                >
                                <Input
                                    id="qty_sampel_kirim"
                                    type="number"
                                    v-model="form.qty_sampel_kirim"
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
                                    :class="{
                                        'border-destructive':
                                            form.errors.running_ke,
                                    }"
                                />
                            </div>

                            <div class="grid gap-2 opacity-70">
                                <Label for="status" class="font-bold"
                                    >Status Awal</Label
                                >
                                <Input
                                    id="status"
                                    v-model="form.status"
                                    readonly
                                    class="bg-muted font-bold text-primary"
                                />
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="min-w-[150px]"
                            >
                                <IconLoader2
                                    v-if="form.processing"
                                    class="mr-2 size-4 animate-spin"
                                />
                                <IconDeviceFloppy v-else class="mr-2 size-4" />
                                Simpan Formulir
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
