<script lang="ts">
export const description = "A simple login form."
import { useDark, useToggle } from '@vueuse/core'
import { IconSun, IconMoon } from '@tabler/icons-vue'

// Menginisialisasi dark mode
const isDark = useDark()
const toggleDark = useToggle(isDark)

</script>

<script setup lang="ts">
import LoginForm from "@/components/LoginForm.vue"
</script>

<template>
  <div class="flex min-h-svh w-full items-center justify-center p-6 md:p-10">
      <div class="absolute right-4 top-4 md:right-10 md:top-10">
      <Button variant="ghost" size="icon" @click="toggleDark()">
        <IconSun v-if="isDark" class="size-5 text-yellow-400" />
        <IconMoon v-else class="size-5 text-fuchsia-600" />
        <span class="sr-only">Toggle Theme</span>
      </Button>
    </div>
    <div class="w-full max-w-sm">
      <LoginForm />
    </div>
  </div>
</template>

