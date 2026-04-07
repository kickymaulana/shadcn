<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, Link } from "@inertiajs/vue3"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { IconUserPlus, IconPencil, IconTrash } from "@tabler/icons-vue"

// 1. Definisikan Persistent Layout
defineOptions({ layout: AuthenticatedLayout })

// Ambil data users dari props
defineProps<{
  users: Array<{
    id: number
    name: string
    username: string
    email: string
    created_at: string
  }>
}>()

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}
</script>

<template>
  <Head title="Manajemen Pengguna" />

  <div class="flex flex-col gap-6 p-4 md:p-8">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-3xl font-bold tracking-tight">Pengguna</h2>
        <p class="text-muted-foreground text-sm">Kelola akses pengguna sistem Sisamcus.</p>
      </div>
      <Button>
        <IconUserPlus class="mr-2 size-4" />
        Tambah User
      </Button>
    </div>

    <Card class="border-none shadow-sm">
      <CardHeader>
        <CardTitle>Daftar Pengguna</CardTitle>
      </CardHeader>
      <CardContent>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Nama</TableHead>
              <TableHead>Username</TableHead>
              <TableHead>Email</TableHead>
              <TableHead>Tgl Terdaftar</TableHead>
              <TableHead class="text-right">Aksi</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="user in users" :key="user.id">
              <TableCell class="font-medium">{{ user.name }}</TableCell>
              <TableCell>{{ user.username }}</TableCell>
              <TableCell>{{ user.email }}</TableCell>
              <TableCell>{{ formatDate(user.created_at) }}</TableCell>
              <TableCell class="text-right space-x-2">
                <Button variant="outline" size="icon">
                  <IconPencil class="size-4" />
                </Button>
                <Button variant="destructive" size="icon">
                  <IconTrash class="size-4" />
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </CardContent>
    </Card>

  </div>
</template>
