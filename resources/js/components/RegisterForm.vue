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
}>()

const form = useForm({
  name: '',
  username: '',
  email: '',
  departemen_id: '',
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
    <Card class="bg-white/30 dark:bg-black/20 backdrop-blur-xl border-white/20 dark:border-white/5">
      <CardHeader>
        <CardTitle>Daftar Akun</CardTitle>
        <CardDescription>Lengkapi data di bawah untuk registrasi</CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="submit">
          <FieldGroup class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <Field class="md:col-span-2">
              <FieldLabel>Nama Lengkap</FieldLabel>
              <Input v-model="form.name" type="text" placeholder="John Doe" required />
              <p v-if="form.errors.name" class="text-destructive text-xs mt-1">{{ form.errors.name }}</p>
            </Field>

            <Field>
              <FieldLabel>Username</FieldLabel>
              <Input v-model="form.username" type="text" placeholder="johndoe" required />
              <p v-if="form.errors.username" class="text-destructive text-xs mt-1">{{ form.errors.username }}</p>
            </Field>

            <Field>
              <FieldLabel>Email</FieldLabel>
              <Input v-model="form.email" type="email" placeholder="m@example.com" required />
              <p v-if="form.errors.email" class="text-destructive text-xs mt-1">{{ form.errors.email }}</p>
            </Field>

            <Field class="md:col-span-2">
              <FieldLabel>Departemen</FieldLabel>
              <select
                v-model="form.departemen_id"
                class="flex h-10 w-full rounded-md border border-input bg-background/50 px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
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
              <FieldLabel>Password</FieldLabel>
              <Input v-model="form.password" type="password" required />
            </Field>

            <Field>
              <FieldLabel>Konfirmasi Password</FieldLabel>
              <Input v-model="form.password_confirmation" type="password" required />
              <p v-if="form.errors.password" class="text-destructive text-xs mt-1">{{ form.errors.password }}</p>
            </Field>

            <Field class="md:col-span-2">
              <Button type="submit" :disabled="form.processing" class="w-full">
                {{ form.processing ? 'Mendaftarkan...' : 'Register' }}
              </Button>
            </Field>
          </FieldGroup>
        </form>

        <div class="mt-4 text-center text-sm">
          Sudah punya akun?
          <Link :href="route('login')" class="underline underline-offset-4">Login di sini</Link>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
