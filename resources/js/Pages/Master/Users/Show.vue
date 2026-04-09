<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, Link } from "@inertiajs/vue3"
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Separator } from "@/components/ui/separator"
import {
  IconArrowLeft,
  IconPencil,
  IconUser,
  IconMail,
  IconCalendar,
  IconFingerprint,
  IconClock
} from "@tabler/icons-vue"
import { router } from "@inertiajs/vue3"
import { IconTrash } from "@tabler/icons-vue"

const deleteUser = () => {
  if (confirm('Apakah Anda yakin ingin menghapus pengguna ini? Data yang dihapus tidak bisa dikembalikan.')) {
    router.delete(route('users.destroy', props.user.id))
  }
}

// 1. Definisikan Persistent Layout
defineOptions({ layout: AuthenticatedLayout })

// 2. Definisi Props
const props = defineProps<{
  user: {
    id: number
    name: string
    username: string
    email: string
    created_at: string
    updated_at: string
  }
}>()

// 3. Helper Formatter
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<template>
  <Head :title="'Detail User - ' + user.name" />

  <div class="flex flex-col gap-6 p-4 md:p-8 pt-4">
    <div class="flex items-center gap-2">
      <Button variant="ghost" size="sm" as-child class="-ml-2">
        <Link :href="route('users.index')">
          <IconArrowLeft class="mr-2 size-4" />
          Kembali ke Daftar
        </Link>
      </Button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <Card class="border-none shadow-sm col-span-1">
        <CardContent class="pt-6">
          <div class="flex flex-col items-center text-center">
            <div class="size-24 rounded-full bg-primary/10 flex items-center justify-center mb-4">
              <IconUser class="size-12 text-primary" />
            </div>
            <h2 class="text-xl font-bold">{{ user.name }}</h2>
            <p class="text-sm text-muted-foreground">@{{ user.username }}</p>

            <div class="w-full mt-6 flex flex-col gap-2">
              <Button class="w-full" variant="outline" as-child>
                <Link :href="route('users.edit', user.id)">
                  <IconPencil class="mr-2 size-4" />
                  Edit Profil
                </Link>
              </Button>
              <Button variant="destructive" @click="deleteUser">
                <IconTrash class="mr-2 size-4" />
                Hapus Pengguna
                </Button>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card class="border-none shadow-sm lg:col-span-2">
        <CardHeader>
          <CardTitle>Informasi Pengguna</CardTitle>
          <CardDescription>Detail lengkap mengenai akun pengguna ini.</CardDescription>
        </CardHeader>
        <Separator />
        <CardContent class="pt-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="space-y-1">
              <div class="flex items-center text-sm text-muted-foreground gap-2">
                <IconFingerprint class="size-4" />
                <span>ID User</span>
              </div>
              <p class="font-medium">#{{ user.id }}</p>
            </div>

            <div class="space-y-1">
              <div class="flex items-center text-sm text-muted-foreground gap-2">
                <IconMail class="size-4" />
                <span>Alamat Email</span>
              </div>
              <p class="font-medium">{{ user.email }}</p>
            </div>

            <div class="space-y-1">
              <div class="flex items-center text-sm text-muted-foreground gap-2">
                <IconCalendar class="size-4" />
                <span>Tanggal Terdaftar</span>
              </div>
              <p class="font-medium">{{ formatDate(user.created_at) }}</p>
            </div>

            <div class="space-y-1">
              <div class="flex items-center text-sm text-muted-foreground gap-2">
                <IconClock class="size-4" />
                <span>Terakhir Diperbarui</span>
              </div>
              <p class="font-medium">{{ formatDate(user.updated_at) }}</p>
            </div>

          </div>

          <div class="mt-8 p-4 rounded-lg bg-muted/50 border border-dashed border-muted-foreground/20">
            <p class="text-xs text-muted-foreground italic text-center">
              Informasi log aktivitas atau hak akses dapat ditambahkan di sini.
            </p>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
