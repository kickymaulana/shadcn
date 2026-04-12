<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
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
    IconDeviceFloppy,
    IconLoader2,
    IconPlus,
    IconTrash,
    IconSettings,
    IconHierarchy2,
    IconClipboardCheck,
    IconDatabase,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    formulir: any;
    departemen_terlibat: any;
    list_sub_departemen: Array<any>;
}>();

// LOGIC: Inisialisasi Data Tambahan (Object ke Array agar bisa di-map ke input)
const initialDataTambahan = Object.entries(
    props.departemen_terlibat.data_tambahan || {},
).map(([key, value]) => ({
    key: key,
    value: String(value),
}));

const form = useForm({
    qty: props.departemen_terlibat.qty,
    pemeriksaan: props.departemen_terlibat.item_pemeriksaan || [],
    data_tambahan: initialDataTambahan,
});

const addItem = () => form.pemeriksaan.push({ item: "", spec: "", actual: "" });
const removeItem = (index: number) => form.pemeriksaan.splice(index, 1);
const addData = () => form.data_tambahan.push({ key: "", value: "" });
const removeData = (index: number) => form.data_tambahan.splice(index, 1);

const submit = () => {
    // Transformasi kembali array ke object untuk simpan ke JSON database
    const dataTambahanObj: Record<string, any> = {};
    form.data_tambahan.forEach((i) => {
        if (i.key) dataTambahanObj[i.key] = i.value;
    });

    form.transform((data) => ({
        ...data,
        data_tambahan: dataTambahanObj,
        item_pemeriksaan: data.pemeriksaan,
    })).put(
        route("formulirs.departemen.update", {
            formulir: props.formulir.id,
            departemen_terlibat: props.departemen_terlibat.id,
        }),
    );
};
</script>

<template>
    <Head :title="'Edit ' + departemen_terlibat.nama_departemen" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('formulirs.show', formulir.id)">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <div>
                <h2
                    class="text-2xl font-black tracking-tight text-primary uppercase italic"
                >
                    Edit Process Data
                </h2>
                <p
                    class="text-muted-foreground text-xs font-bold uppercase tracking-widest"
                >
                    SISAMCUS - {{ formulir.sampel.kode_sample }}
                </p>
            </div>
        </div>

        <div class="max-w-5xl">
            <form @submit.prevent="submit" class="space-y-6">
                <Card class="border-none shadow-sm overflow-hidden">
                    <CardHeader class="bg-muted/30 border-b py-4">
                        <CardTitle
                            class="text-sm font-bold uppercase flex items-center gap-2 text-primary"
                        >
                            <IconSettings class="size-4" /> Konfigurasi Utama
                        </CardTitle>
                    </CardHeader>
                    <CardContent
                        class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6"
                    >
                        <div class="space-y-2">
                            <Label
                                class="text-[10px] font-black uppercase text-muted-foreground tracking-widest"
                                >Departemen / Unit</Label
                            >
                            <div
                                class="flex items-center gap-3 p-4 rounded-xl bg-primary/5 border border-primary/10"
                            >
                                <div
                                    class="p-2 bg-primary rounded-lg shadow-sm"
                                >
                                    <IconHierarchy2 class="size-5 text-white" />
                                </div>
                                <span
                                    class="text-lg font-black uppercase tracking-tight"
                                    >{{
                                        departemen_terlibat.nama_departemen
                                    }}</span
                                >
                            </div>
                        </div>
                        <div class="space-y-2">
                            <Label
                                class="text-[10px] font-black uppercase text-muted-foreground tracking-widest"
                                >Kuantitas (Qty Pcs)</Label
                            >
                            <Input
                                type="number"
                                v-model="form.qty"
                                class="h-14 text-2xl font-black focus-visible:ring-primary border-2"
                            />
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm overflow-hidden">
                    <CardHeader
                        class="flex flex-row items-center justify-between bg-muted/30 py-3 border-b px-6"
                    >
                        <CardTitle
                            class="text-[11px] font-black uppercase flex items-center gap-2 text-muted-foreground"
                        >
                            <IconDatabase class="size-4" /> Data Tambahan (JSON)
                        </CardTitle>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="addData"
                            class="h-8 font-bold text-[10px] uppercase"
                        >
                            <IconPlus class="mr-1 size-3" /> Tambah Baris
                        </Button>
                    </CardHeader>
                    <CardContent class="p-0">
                        <Table>
                            <TableBody>
                                <TableRow
                                    v-if="form.data_tambahan.length === 0"
                                >
                                    <TableCell
                                        class="text-center py-6 text-muted-foreground italic text-xs"
                                        >Tidak ada data tambahan.</TableCell
                                    >
                                </TableRow>
                                <TableRow
                                    v-for="(data, index) in form.data_tambahan"
                                    :key="index"
                                    class="hover:bg-transparent border-b"
                                >
                                    <TableCell
                                        class="p-4 w-1/3 bg-muted/5 border-r"
                                    >
                                        <Input
                                            v-model="data.key"
                                            placeholder="Nama Label"
                                            class="border-none shadow-none font-bold uppercase text-xs focus-visible:ring-0 bg-transparent"
                                        />
                                    </TableCell>
                                    <TableCell
                                        class="p-4 text-primary font-medium"
                                    >
                                        <Input
                                            v-model="data.value"
                                            placeholder="Masukkan nilai data..."
                                            class="border-none shadow-none focus-visible:ring-0 text-sm bg-transparent"
                                        />
                                    </TableCell>
                                    <TableCell class="p-4 w-12">
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon"
                                            @click="removeData(index)"
                                            class="size-8 text-destructive/40 hover:text-destructive"
                                        >
                                            <IconTrash class="size-4" />
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm overflow-hidden">
                    <CardHeader
                        class="flex flex-row items-center justify-between bg-muted/30 py-3 border-b px-6"
                    >
                        <CardTitle
                            class="text-[11px] font-black uppercase flex items-center gap-2 text-muted-foreground"
                        >
                            <IconClipboardCheck class="size-4" /> Lembar
                            Pemeriksaan Kualitas
                        </CardTitle>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="addItem"
                            class="h-8 font-bold text-[10px] uppercase"
                        >
                            <IconPlus class="mr-1 size-3" /> Tambah Item
                        </Button>
                    </CardHeader>
                    <CardContent class="p-0">
                        <Table>
                            <TableHeader
                                class="bg-muted/50 text-[9px] uppercase font-black tracking-widest"
                            >
                                <TableRow>
                                    <TableHead class="pl-6"
                                        >Item Pemeriksaan</TableHead
                                    >
                                    <TableHead>Spec QC</TableHead>
                                    <TableHead class="w-[200px]"
                                        >Actual (Hasil)</TableHead
                                    >
                                    <TableHead class="w-12"></TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="(item, index) in form.pemeriksaan"
                                    :key="index"
                                >
                                    <TableCell class="p-2 pl-6">
                                        <Input
                                            v-model="item.item"
                                            class="h-9 border-none shadow-none focus-visible:ring-0 text-xs font-semibold"
                                        />
                                    </TableCell>
                                    <TableCell class="p-2">
                                        <Input
                                            v-model="item.spec"
                                            class="h-9 border-none shadow-none focus-visible:ring-0 text-xs italic text-muted-foreground"
                                        />
                                    </TableCell>
                                    <TableCell class="p-2">
                                        <Input
                                            v-model="item.actual"
                                            placeholder="Isi hasil..."
                                            class="h-9 bg-primary/5 border-primary/20 focus:border-primary font-bold text-sm text-primary"
                                        />
                                    </TableCell>
                                    <TableCell class="p-2 pr-6 text-right">
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon"
                                            @click="removeItem(index)"
                                            class="size-8 text-destructive/40 hover:text-destructive"
                                        >
                                            <IconTrash class="size-4" />
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <Button
                        variant="ghost"
                        as-child
                        class="font-bold text-xs uppercase"
                    >
                        <Link :href="route('formulirs.show', formulir.id)"
                            >Batal</Link
                        >
                    </Button>
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="min-w-[200px] font-black uppercase tracking-widest shadow-lg shadow-primary/20"
                    >
                        <IconLoader2
                            v-if="form.processing"
                            class="mr-2 size-4 animate-spin"
                        />
                        <IconDeviceFloppy v-else class="mr-2 size-4" /> Simpan
                        Perubahan
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
