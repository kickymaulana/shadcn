<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, Link, useForm, router } from "@inertiajs/vue3"
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog'
import {
  IconArrowLeft,
  IconDeviceFloppy,
  IconLoader2,
  IconTrash,
  IconHierarchy
} from "@tabler/icons-vue"

defineOptions({ layout: AuthenticatedLayout })

const props = defineProps<{
  role: {
    id: number
    name: string
    guard_name: string
  }
}>()

const form = useForm({
  name: props.role.name,
})

const submit = () => {
  form.put(route('roles.update', props.role.id))
}

const deleteRole = () => {
  router.delete(route('roles.destroy', props.role.id))
}
</script>

<template>
  <Head :title="'Edit Jabatan - ' + role.name" />

  <div class="flex flex-col gap-6 p-4 md:p-8 pt-4">
    <div class="flex items-center">
      <Button variant="ghost" size="sm" as-child class="-ml-2 text-muted-foreground hover:text-foreground">
        <Link :href="route('roles.index')">
          <IconArrowLeft class="mr-2 size-4" />
          Kembali ke Daftar
        </Link>
      </Button>
    </div>

    <div class="max-w-2xl mx-auto w-full">
      <Card class="border-none shadow-lg">
        <CardHeader class="flex flex-row items-center gap-4 pb-8">
          <div class="p-3 bg-primary/10 rounded-xl">
            <IconHierarchy class="size-6 text-primary" />
          </div>
          <div class="grid gap-1">
            <CardTitle class="text-2xl font-bold">Edit Jabatan</CardTitle>
            <CardDescription>
              Ubah nama jabatan atau otoritas akses untuk sistem.
            </CardDescription>
          </div>
        </CardHeader>

        <form @submit.prevent="submit">
          <CardContent class="grid gap-6">
            <div class="grid gap-3">
              <Label for="name" class="text-sm font-medium ml-0.5">Nama Jabatan</Label>
              <Input
                id="name"
                v-model="form.name"
                type="text"
                placeholder="Contoh: Supervisor IT"
                class="h-11 shadow-sm"
                :class="{ 'border-destructive': form.errors.name }"
                :disabled="role.name === 'admin'"
              />
              <p v-if="role.name === 'admin'" class="text-[10px] text-muted-foreground italic">
                * Nama role Administrator sistem tidak dapat diubah.
              </p>
              <p v-if="form.errors.name" class="text-xs font-medium text-destructive">{{ form.errors.name }}</p>
            </div>

            <div class="grid gap-3 opacity-70">
              <Label class="text-sm font-medium ml-0.5">Guard Name</Label>
              <Input
                :value="role.guard_name"
                readonly
                class="h-11 bg-muted cursor-not-allowed"
              />
            </div>
          </CardContent>

          <CardFooter class="flex flex-col md:flex-row justify-between gap-4 border-t bg-muted/20 px-6 py-6 rounded-b-lg">
            <AlertDialog v-if="role.name !== 'admin'">
              <AlertDialogTrigger as-child>
                <Button variant="ghost" type="button" class="text-destructive hover:bg-destructive/10 hover:text-destructive">
                  <IconTrash class="mr-2 size-4" />
                  Hapus Jabatan
                </Button>
              </AlertDialogTrigger>
              <AlertDialogContent>
                <AlertDialogHeader>
                  <AlertDialogTitle>Hapus Jabatan?</AlertDialogTitle>
                  <AlertDialogDescription>
                    Apakah Anda yakin ingin menghapus jabatan <strong>{{ role.name }}</strong>?
                    User yang memiliki jabatan ini mungkin akan kehilangan akses ke sistem.
                  </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                  <AlertDialogCancel>Batal</AlertDialogCancel>
                  <AlertDialogAction @click="deleteRole" class="bg-destructive text-destructive-foreground hover:bg-destructive/90">
                    Ya, Hapus Permanen
                  </AlertDialogAction>
                </AlertDialogFooter>
              </AlertDialogContent>
            </AlertDialog>
            <div v-else></div> <div class="flex gap-3 w-full md:w-auto">
              <Button variant="outline" type="button" as-child class="h-10 px-6 flex-1 md:flex-none">
                <Link :href="route('roles.index')">Batal</Link>
              </Button>
              <Button type="submit" :disabled="form.processing || role.name === 'admin'" class="h-10 px-6 flex-1 md:flex-none shadow-md">
                <IconLoader2 v-if="form.processing" class="mr-2 size-4 animate-spin" />
                <IconDeviceFloppy v-else class="mr-2 size-4" />
                Simpan Perubahan
              </Button>
            </div>
          </CardFooter>
        </form>
      </Card>

      <div class="mt-6 p-6 border-2 border-dashed rounded-xl text-center">
        <p class="text-sm text-muted-foreground italic">
          Modul Pengaturan Hak Akses (Permissions) akan tampil di bawah sini setelah tabel permission siap.
        </p>
      </div>
    </div>
  </div>
</template>
