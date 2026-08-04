<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import BlockRenderer from '@/Components/app/BlockRenderer.vue';

const props = defineProps({
  page: { type: Object, required: true },
});
</script>

<template>
  <AppLayout>
    <template #header>
      <h1 class="text-lg font-semibold text-slate-900">{{ page.name }}</h1>
    </template>

    <div class="mx-auto max-w-3xl space-y-3">
      <BlockRenderer v-for="block in page.blocks" :key="block.id" :block="block" />

      <div v-if="page.blocks.length === 0" class="rounded-lg border border-dashed border-slate-300 py-12 text-center text-sm text-slate-400">
        Halaman ini belum punya blok.
      </div>
    </div>
  </AppLayout>
</template>