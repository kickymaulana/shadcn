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
import { usePage } from '@inertiajs/vue3'
import { computed, watch } from 'vue'
import {
  Pagination,
  PaginationEllipsis,
  PaginationFirst,
  PaginationLast,
  PaginationList,
  PaginationListItem,
  PaginationNext,
  PaginationPrev,
} from '@/components/ui/pagination'

// 1. Definisikan Persistent Layout
defineOptions({ layout: AuthenticatedLayout })

// Definisi props diperbarui sesuai struktur LengthAwarePaginator Laravel
defineProps<{
  users: {
    data: Array<{
      id: number
      name: string
      username: string
      email: string
      created_at: string
    }>
    links: Array<{
      url: string | null
      label: string
      active: boolean
    }>
    meta: {
      current_page: number
      from: number
      last_page: number
      path: string
      per_page: number
      to: number
      total: number
    }
    // Jika menggunakan simplePaginate, strukturnya lebih sederhana,
    // tapi paginate() standar biasanya mengirimkan field di bawah ini di root:
    current_page: number
    last_page: number
    total: number
    prev_page_url: string | null
    next_page_url: string | null
  }
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

  <div class="flex flex-col gap-6 p-4 md:p-8 pt-1 md:pt-1">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-3xl font-bold tracking-tight">Pengguna</h2>
        <p class="text-muted-foreground text-sm">Kelola akses pengguna sistem Sisamcus.</p>
      </div>
      <Button as-child>
        <Link :href="route('users.create')">
            <IconUserPlus class="mr-2 size-4" />
            Tambah User
        </Link>
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
            <TableRow v-for="user in users.data" :key="user.id">
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


        <div class="flex items-center justify-center space-x-1 py-4">
            <template v-for="(link, k) in users.links" :key="k">
                <div v-if="link.url === null" class="px-4 py-2 text-sm text-gray-500" v-html="link.label" />

                <Link
                v-else
                :href="link.url"
                class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring px-4 py-2"
                :class="link.active
                    ? 'bg-primary text-primary-foreground shadow hover:bg-primary/90'
                    : 'border border-input bg-background hover:bg-accent hover:text-accent-foreground'"
                v-html="link.label"
                />
            </template>
            </div>

      </CardContent>
    </Card>

  </div>
</template>
