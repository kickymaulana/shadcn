<script setup lang="ts">
import { cn } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Field, FieldGroup, FieldLabel } from "@/components/ui/field"
import { Input } from "@/components/ui/input"
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps<{
  class?: string
  departemens: Array<{ id: number, nama: string }>
  roles: Array<{ id: number, name: string }>
}>()

const form = useForm({
  name: '',
  username: '',
  email: '',
  departemen_id: '',
  role: '', // State untuk Spatie Role
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post(route('register.store'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>

<template>
  <div :class="cn('flex flex-col gap-6', props.class)">
    <Card class="bg-white/30 dark:bg-black/20 backdrop-blur-xl border-white/20 dark:border-white/5 shadow-2xl">
      <CardHeader>
        <CardTitle>Daftar Akun</CardTitle>
        <CardDescription>
          Lengkapi data di bawah untuk mendaftar ke aplikasi Sisamcus
        </CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="submit">
          <FieldGroup class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <Field class="md:col-span-2">
              <FieldLabel for="name">Nama Lengkap</FieldLabel>
              <Input
                id="name"
                v-model="form.name"
                type="text"
                placeholder="Masukkan nama lengkap"
                required
              />
              <p v-if="form.errors.name" class="text-destructive text-xs mt-1">{{ form.errors.name }}</p>
            </Field>

            <Field>
              <FieldLabel for="username">Username</FieldLabel>
              <Input
                id="username"
                v-model="form.username"
                type="text"
                placeholder="username123"
                required
              />
              <p v-if="form.errors.username" class="text-destructive text-xs mt-1">{{ form.errors.username }}</p>
            </Field>

            <Field>
              <FieldLabel for="email">Email</FieldLabel>
              <Input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="name@example.com"
                required
              />
              <p v-if="form.errors.email" class="text-destructive text-xs mt-1">{{ form.errors.email }}</p>
            </Field>

            <Field>
              <FieldLabel for="departemen">Departemen</FieldLabel>
              <select
                id="departemen"
                v-model="form.departemen_id"
                class="flex h-10 w-full rounded-md border border-input bg-background/50 px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                required
              >
                <option value="" disabled>Pilih Departemen</option>
                <option v-for="dept in departemens" :key="dept.id" :value="dept.id">
                  {{ dept.nama }}
                </option>
              </select>
              <p v-if="form.errors.departemen_id" class="text-destructive text-xs mt-1">{{ form.errors.departemen_id }}</p>
            </Field>

            <Field>
              <FieldLabel for="role">Role / Jabatan</FieldLabel>
              <select
                id="role"
                v-model="form.role"
                class="flex h-10 w-full rounded-md border border-input bg-background/50 px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                required
              >
                <option value="" disabled>Pilih Role</option>
                <option v-for="role in roles" :key="role.id" :value="role.name">
                  {{ role.name }}
                </option>
              </select>
              <p v-if="form.errors.role" class="text-destructive text-xs mt-1">{{ form.errors.role }}</p>
            </Field>

            <Field>
              <FieldLabel for="password">Password</FieldLabel>
              <Input
                id="password"
                v-model="form.password"
                type="password"
                required
              />
            </Field>

            <Field>
              <FieldLabel for="password_confirmation">Konfirmasi Password</FieldLabel>
              <Input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                required
              />
              <p v-if="form.errors.password" class="text-destructive text-xs mt-1">{{ form.errors.password }}</p>
            </Field>

            <Field class="md:col-span-2 mt-2">
              <Button type="submit" :disabled="form.processing" class="w-full">
                {{ form.processing ? 'Sedang mendaftar...' : 'Daftar Sekarang' }}
              </Button>
            </Field>
          </FieldGroup>
        </form>

        <div class="mt-6 text-center text-sm">
          Sudah memiliki akun?
          <Link :href="route('login')" class="font-medium underline underline-offset-4 hover:text-primary">
            Masuk di sini
          </Link>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
