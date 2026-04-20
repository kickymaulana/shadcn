<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { watch } from "vue";
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
import { Separator } from "@/components/ui/separator";
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
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    formulir: any;
    list_sub_departemen: Array<any>;
    list_users: Array<any>;
}>();

// Objek Template (Pindahkan ke file terpisah jika terlalu panjang)
const departmentTemplates: Record<string, { items: any[]; data: any[] }> = {
    "MOULD DESIGN": {
        items: [
            { item: "Berat Basah Mould", spec: "Mengikuti" },
            { item: "Berat Kering Mould", spec: "Mengikuti" },
        ],
        data: [],
    },
    "WORKING MOULD": {
        items: [
            { item: "Berat Basah Mould", spec: "" },
            { item: "Berat Kering Mould", spec: "" },
        ],
        data: [],
    },
    "FILLING": {
        items: [
            { item: "Berat Basah", spec: "1200 - 1300" },
            { item: "Jenis Tapak", spec: "Kossan" },
            { item: "Visual Rejection", spec: "-" },
        ],
        data: [],
    },
    "WASHING": {
        items: [
            { item: "Berat Kering Sebelum Cuci", spec: "Mengikuti" },
            { item: "Berat Kering Sesudah Cuci", spec: "Mengikuti" },
            { item: "Dimensi Sebelum Cuci", spec: "-" },
            { item: "Dimensi Setelah Cuci", spec: "Mengikuti Spec Inproses" },
            { item: "Visual Rejection", spec: "-" },
            { item: "Susut Former", spec: "15% - 15,5%" },
        ],
        data: [],
    },
    "TEXTURE": {
        items: [
            { item: "CB", spec: "1,0 - 1,1" },
            { item: "Tinggi Texture", spec: "Ikut Sample std Inproses" },
            { item: "Tekanan Angin", spec: "-" },
            { item: "Texturan", spec: "Ikut Sample std Inproses" },
        ],
        data: [],
    },
    "SPRAY ON KASAR": {
        items: [
            { item: "Tekanan Angin", spec: "" },
            { item: "Jumlah Tembakan", spec: "" },
            { item: "Spray on mengikuti sample standard", spec: "" },
        ],
        data: [
            { key: "Specification", value: "" },
            { key: "Proses mesin", value: "" },
            { key: "Formula yang digunakan", value: "" },
            { key: "Berat Formula", value: "" },
            { key: "Lampiran", value: "" },
        ],
    },
    "CUCI CELUP": {
        items: [
            { item: "Warna Base", spec: "Yellow" },
            { item: "Tinggi Warna Base", spec: "50mm matang (55mm mentah)" },
            { item: "Visual Rejection", spec: "-" },
        ],
        data: [],
    },
    "GLAZE": {
        items: [
            { item: "Jenis Formula", spec: "-" },
            { item: "Berat Formula", spec: "-" },
            { item: "Residu", spec: "-" },
            { item: "Visco", spec: "-" },
        ],
        data: [],
    },
    "SPRAY ON HALUS": {
        items: [
            { item: "Tekanan Angin", spec: "" },
            { item: "Jumlah Tembakan", spec: "" },
            { item: "Spray On Mengikuti Sample Standard", spec: "Sample std Kossan" },
        ],
        data: [
            { key: "Proses mesin", value: "" },
            { key: "Formula yang digunakan", value: "" },
            { key: "Berat Formula", value: "" },
        ],
    },
    "QS / SUSUN": {
        items: [],
        data: [
            { key: "Kode Stempel", value: "" },
            { key: "Hasil Stempel", value: "" },
        ],
    },
    "OVEN": {
        items: [],
        data: [
            { key: "No Oven", value: "" },
            { key: "Tgl Bakar", value: "" },
            { key: "Tgl Selesai Bakar", value: "" },
        ],
    },
    "BONGKAR": {
        items: [],
        data: [
            { key: "Tanggal Bongkar", value: "" },
            { key: "Jam Bongkar", value: "" },
        ],
    },
    "ASAH / GRATING": {
        items: [
            { item: "Asah", spec: "Asah Full Body" },
            { item: "Grating", spec: "Grating (Hanya senget diatas 7mm)" },
        ],
        data: []
    },
    "FQC": {
        items: [
            { item: "Berat Former", spec: "850 - 950" },
            { item: "Visual Rejection", spec: "-" },
            { item: "Dimensi", spec: "Mengikuti spec Kossan" },
            { item: "Texture", spec: "Ikut std Kossan" },
            { item: "Spray On", spec: "Ikut std Kossan" },
            { item: "Roughness 120", spec: "3 - 4" },
            { item: "Roughness 145", spec: "3 - 4" },
            { item: "Roughness 250", spec: "3 - 4" },
            { item: "Glossy", spec: "-" },
            { item: "Thermalshock 180 c", spec: "Pass 100%" },
            { item: "Thermalshock 200 c", spec: "Pass 75%" },
            { item: "Chemical Test Piece", spec: "Max 1.7 gr/dm2 (internal control)" },
            { item: "Chemical visual", spec: "Max 25 titik (internal control)" },
        ],
        data: [
            { key: "Temperatur Bullerings", value: "" },
            { key: "Lampiran", value: "" },
        ],
    },
};

const form = useForm({
    departemen_id: null,
    sub_departemen_id: null,
    qty: props.formulir.qty_sampel_kirim,
    pemeriksaan: [] as any[],
    data_tambahan: [] as any[],
});

// LOGIC: Pantau perubahan Sub Departemen
watch(
    () => form.sub_departemen_id,
    (newVal) => {
        const selectedSub = props.list_sub_departemen.find(
            (s) => s.id == newVal,
        );
        if (selectedSub) {
            form.departemen_id = selectedSub.departemen_id; // Set otomatis ID departemen induk
            const template =
                departmentTemplates[selectedSub.nama.toUpperCase()];

            if (template) {
                form.pemeriksaan = JSON.parse(JSON.stringify(template.items));
                form.data_tambahan = JSON.parse(JSON.stringify(template.data));
            } else {
                form.pemeriksaan = [];
                form.data_tambahan = [];
            }
        }
    },
);

const addItem = () => form.pemeriksaan.push({ item: "", spec: "" });
const removeItem = (index: number) => form.pemeriksaan.splice(index, 1);
const addData = () => form.data_tambahan.push({ key: "", value: "" });
const removeData = (index: number) => form.data_tambahan.splice(index, 1);

const submit = () => {
    // Transformasi data tambahan dari array ke object untuk simpan ke JSON
    const dataTambahanObj: Record<string, any> = {};
    form.data_tambahan.forEach((i) => {
        if (i.key) dataTambahanObj[i.key] = i.value;
    });

    form.transform((data) => ({
        ...data,
        data_tambahan: dataTambahanObj,
        item_pemeriksaan: data.pemeriksaan,
    })).post(route("formulirs.departemen.store", props.formulir.id));
};
</script>

<template>
    <Head title="Tambah Departemen Terlibat" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child>
                <Link :href="route('formulirs.show', formulir.id)">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <div>
                <h2
                    class="text-3xl font-bold tracking-tight text-primary uppercase italic"
                >
                    Add Department
                </h2>
                <p class="text-muted-foreground text-sm italic">
                    SISAMCUS - {{ formulir.sampel.kode_sample }}
                </p>
            </div>
        </div>

        <div class="max-w-5xl">
            <form @submit.prevent="submit" class="space-y-6">
                <Card class="border-none shadow-sm">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2"
                            ><IconSettings class="size-5" /> Konfigurasi
                            Departemen</CardTitle
                        >
                    </CardHeader>
                    <CardContent class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="grid gap-2">
                            <Label class="font-bold"
                                >Pilih Unit (Sub Departemen)</Label
                            >
                            <select
                                v-model="form.sub_departemen_id"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:ring-2 focus:ring-primary"
                            >
                                <option :value="null" disabled>
                                    -- Pilih Sub Departemen --
                                </option>
                                <option
                                    v-for="sub in list_sub_departemen"
                                    :key="sub.id"
                                    :value="sub.id"
                                >
                                    {{ sub.nama }} ({{ sub.departemen.nama }})
                                </option>
                            </select>
                        </div>
                        <div class="grid gap-2">
                            <Label class="font-bold">Quantity</Label>
                            <Input type="number" v-model="form.qty" />
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm">
                    <CardHeader
                        class="flex flex-row items-center justify-between"
                    >
                        <CardTitle class="text-sm uppercase tracking-wider"
                            >Data Tambahan (JSON)</CardTitle
                        >
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="addData"
                            ><IconPlus class="mr-1 size-4" /> Tambah
                            Field</Button
                        >
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="form.data_tambahan.length === 0"
                            class="text-center py-4 text-muted-foreground italic text-sm border-2 border-dashed rounded-lg"
                        >
                            Tidak ada data tambahan.
                        </div>
                        <div
                            v-for="(data, index) in form.data_tambahan"
                            :key="index"
                            class="flex gap-4 mb-3"
                        >
                            <Input
                                v-model="data.key"
                                placeholder="Label (ex: No Oven)"
                                class="flex-1"
                            />
                            <Input
                                v-model="data.value"
                                placeholder="Value"
                                class="flex-1"
                            />
                            <Button
                                type="button"
                                variant="destructive"
                                size="icon"
                                @click="removeData(index)"
                                ><IconTrash class="size-4"
                            /></Button>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-none shadow-sm">
                    <CardHeader
                        class="flex flex-row items-center justify-between"
                    >
                        <CardTitle class="text-sm uppercase tracking-wider"
                            >Item Pemeriksaan QC (JSON)</CardTitle
                        >
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="addItem"
                            ><IconPlus class="mr-1 size-4" /> Tambah
                            Item</Button
                        >
                    </CardHeader>
                    <CardContent>
                        <Table class="border rounded-md">
                            <TableHeader
                                ><TableRow>
                                    <TableHead>Item Pemeriksaan</TableHead>
                                    <TableHead>Spec QC</TableHead>
                                    <TableHead
                                        class="w-10"
                                    ></TableHead> </TableRow
                            ></TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="(item, index) in form.pemeriksaan"
                                    :key="index"
                                >
                                    <TableCell
                                        ><Input
                                            v-model="item.item"
                                            class="border-none shadow-none focus-visible:ring-0"
                                            placeholder="Nama item..."
                                    /></TableCell>
                                    <TableCell
                                        ><Input
                                            v-model="item.spec"
                                            class="border-none shadow-none focus-visible:ring-0"
                                            placeholder="Spesifikasi..."
                                    /></TableCell>
                                    <TableCell
                                        ><Button
                                            type="button"
                                            variant="ghost"
                                            size="icon"
                                            @click="removeItem(index)"
                                            ><IconTrash
                                                class="size-4 text-destructive" /></Button
                                    ></TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <div class="flex justify-end gap-3">
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="min-w-[200px]"
                    >
                        <IconLoader2
                            v-if="form.processing"
                            class="mr-2 size-4 animate-spin"
                        />
                        <IconDeviceFloppy v-else class="mr-2 size-4" /> Simpan
                        Alur Proses
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
