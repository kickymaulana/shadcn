<script setup lang="ts">

import { Switch } from '@/Components/ui/switch'

interface Event {
    title: string;
    description: string;
}

defineProps<{
    event: Event
}>()

</script>

<template>
    <div>
        <h1>{{ event.title }} </h1>
        <Switch />
    </div>
</template>
