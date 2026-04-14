<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import {
    IconGitMerge,
    IconEye,
    IconSearch,
    IconX,
    IconBuilding,
} from "@tabler/icons-vue";
import { ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    list_sub: any;
    filters: any;
}>();

const search = ref(props.filters.search || "");
watch(search, (value) => {
    router.get(
        route("sub.departemens.index"),
        { search: value },
        { preserveState: true, replace: true },
    );
});
</script>

<template>
    <Head title="Manajemen Sub Departemen" />
    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm">
            <CardHeader
                class="flex flex-col md:flex-row items-start md:items-center justify-between pb-6"
            >
                <CardTitle class="text-xl font-bold"
                    >Daftar Sub Departemen</CardTitle
                >
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <Input
                        v-model="search"
                        placeholder="Cari sub departemen..."
                        class="w-full md:w-72"
                    />
                    <Button as-child>
                        <Link :href="route('sub.departemens.create')">
                            <IconGitMerge class="mr-2 size-4" /> Tambah Sub
                        </Link>
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Urutan</TableHead>
                            <TableHead>Nama Sub</TableHead>
                            <TableHead>Induk Departemen</TableHead>
                            <TableHead class="text-right">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="sub in list_sub.data" :key="sub.id">
                            <TableCell class="font-medium italic">{{
                                sub.urutan
                            }}</TableCell>
                            <TableCell class="font-medium italic">{{
                                sub.nama
                            }}</TableCell>
                            <TableCell>
                                <Badge variant="outline">
                                    <IconBuilding class="mr-1 size-3" />
                                    {{ sub.departemen.nama }}
                                </Badge>
                            </TableCell>
                            <TableCell class="text-right">
                                <Button variant="ghost" size="icon" as-child>
                                    <Link
                                        :href="
                                            route(
                                                'sub.departemens.show',
                                                sub.id,
                                            )
                                        "
                                    >
                                        <IconEye class="size-4 text-primary" />
                                    </Link>
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>
</template>
