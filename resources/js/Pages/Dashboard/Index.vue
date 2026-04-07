<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Link } from '@inertiajs/vue3'
import SectionCards from "@/components/SectionCards.vue"

// Mendefinisikan layout yang digunakan halaman ini
defineOptions({ layout: AuthenticatedLayout })

</script>

<template>
    <SectionCards />
</template>
