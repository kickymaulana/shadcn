<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, Link, router } from "@inertiajs/vue3"
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
import { Input } from "@/components/ui/input"
import {
  IconSearch,
  IconX,
  IconEye,
  IconBell,
  IconBellOff
} from "@tabler/icons-vue"
import { ref, watch } from 'vue'
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/components/ui/alert-dialog"

// 1. Persistent Layout
defineOptions({ layout: AuthenticatedLayout })

// 2. Definisi Props sesuai kembalian Pagination Laravel
const props = defineProps<{
  notifikasi: {
    data: Array<{
      id: string
      type: string
      data: {
        pesan: string
        nomor_sampel: string
        url: string
      }
      read_at: string | null
      created_at: string
    }>
    links: Array<{
      url: string | null
      label: string
      active: boolean
    }>
    from: number
    to: number
    total: number
  },
  filters: {
    search: string
  }
}>()

// 3. Logika Pencarian (Search)
const search = ref(props.filters.search || '')

let timeout: ReturnType<typeof setTimeout>
watch(search, (value) => {
  clearTimeout(timeout)
  timeout = setTimeout(() => {
    router.get(
      route('notifikasi.index'),
      { search: value },
      { preserveState: true, replace: true }
    )
  }, 500)
})

const clearSearch = () => {
  search.value = ''
}

// 4. Helper Formatter Waktu & Label
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const cleanLabel = (label: string) => {
  if (label.includes('Previous')) return 'Sebelumnya'
  if (label.includes('Next')) return 'Selanjutnya'
  return label
}


// 2. Tambahkan ref baru untuk mengontrol Dialog
const showMarkAllDialog = ref(false)

// 3. Modifikasi fungsi markAllAsRead (Hapus fungsi confirm bawaan)
const markAllAsRead = () => {
  router.post(route('notifikasi.tandai-semua-dibaca'), {}, {
    preserveScroll: true,
    onSuccess: () => {
      showMarkAllDialog.value = false // Tutup dialog setelah berhasil
    }
  })
}
</script>

<template>
  <Head title="Pusat Notifikasi" />

  <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
    <Card class="border-none shadow-sm">
      <CardHeader class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6">
        <CardTitle class="text-xl font-bold flex items-center gap-2">
          <IconBell class="size-5 text-primary" />
          Pusat Notifikasi
        </CardTitle>

        <div class="flex items-center gap-2 w-full md:w-auto">


          <!-- Tombol Pemicu Dialog -->
          <Button
            variant="secondary"
            size="sm"
            class="w-full sm:w-auto h-9 gap-1.5 order-2 sm:order-1"
            @click="showMarkAllDialog = true"
          >
            <IconBellOff class="size-4 text-muted-foreground" />
            <span>Tandai Semua Dibaca</span>
          </Button>

          <div class="relative w-full md:w-72">
            <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
            <Input
              v-model="search"
              placeholder="Cari notifikasi atau sampel..."
              class="pl-10 pr-10"
            />
            <button
              v-if="search"
              @click="clearSearch"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
            >
              <IconX class="size-4" />
            </button>
          </div>
        </div>
      </CardHeader>

      <CardContent>
        <div class="rounded-md border">
          <Table>
            <TableHeader>
              <TableRow class="bg-muted/50">
                <TableHead class="w-[100px]">Status</TableHead>
                <TableHead>Pesan</TableHead>
                <TableHead>Tanggal Masuk</TableHead>
                <TableHead class="text-right">Buka</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-if="notifikasi.data.length === 0">
                <TableCell colspan="5" class="h-24 text-center text-muted-foreground">
                  Tidak ada notifikasi untuk ditampilkan.
                </TableCell>
              </TableRow>

              <TableRow
                v-for="item in notifikasi.data"
                :key="item.id"
                :class="{ 'bg-primary/5 hover:bg-primary/10 font-medium': !item.read_at }"
              >
                <!-- Status Belum/Sudah Dibaca -->
                <TableCell>
                  <span
                    if="!item.read_at"
                    :class="[
                      'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium',
                      !item.read_at ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-500'
                    ]"
                  >
                    {{ !item.read_at ? 'Baru' : 'Dibaca' }}
                  </span>
                </TableCell>

                <!-- Pesan Notifikasi -->
                <TableCell>{{ item.data.pesan }}</TableCell>

                <!-- Waktu Terbuat -->
                <TableCell class="text-muted-foreground text-sm">
                  {{ formatDate(item.created_at) }}
                </TableCell>

                <!-- Tombol Aksi Langsung Proses -->
                <TableCell class="text-right">
                  <Button size="sm" variant="outline" class="h-8 gap-1.5" as-child>
                    <Link :href="route('notifikasi.baca', item.id)">
                      <IconEye class="size-4" />
                      <span>Baca</span>
                    </Link>
                  </Button>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>

        <!-- Pagination Footer -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6">
          <p class="text-xs text-muted-foreground">
            Menampilkan {{ notifikasi.from ?? 0 }} - {{ notifikasi.to ?? 0 }} dari {{ notifikasi.total }} notifikasi
          </p>

          <nav class="flex items-center gap-1">
            <template v-for="(link, k) in notifikasi.links" :key="k">
              <Button
                v-if="link.url === null"
                variant="outline"
                size="sm"
                disabled
                class="opacity-50 text-xs px-3 h-8"
                v-html="cleanLabel(link.label)"
              />
              <Button
                v-else
                as-child
                variant="outline"
                size="sm"
                class="text-xs px-3 h-8"
                :class="{ 'bg-primary text-primary-foreground hover:bg-primary/90 hover:text-primary-foreground': link.active }"
              >
                <Link :href="link.url" v-html="cleanLabel(link.label)" />
              </Button>
            </template>
          </nav>
        </div>
      </CardContent>
    </Card>
  </div>


  <!-- Komponen AlertDialog untuk Konfirmasi Pembacaan Masul -->
  <AlertDialog
    :open="showMarkAllDialog"
    @update:open="showMarkAllDialog = $event"
  >
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>Tandai Semua Telah Dibaca?</AlertDialogTitle>
        <AlertDialogDescription>
          Tindakan ini akan mengubah status seluruh notifikasi baru Anda menjadi <strong>Sudah Dibaca</strong> secara sekaligus.
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel>Batal</AlertDialogCancel>
        <AlertDialogAction
          @click="markAllAsRead"
          class="bg-primary text-primary-foreground hover:bg-primary/90"
        >
          Ya, Tandai Semua
        </AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>
