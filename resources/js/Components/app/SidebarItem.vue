<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  href: { type: String, required: true },
  icon: { type: [Object, Function], required: true },
  label: { type: String, required: true },
  active: { type: Boolean, default: false },
  collapsed: { type: Boolean, default: false },
});
</script>

<template>
  <Link
    :href="href"
    class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors"
    :class="[
      active
        ? 'bg-slate-900 text-white'
        : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900',
      collapsed ? 'justify-center' : '',
    ]"
  >
    <component :is="icon" class="h-5 w-5 shrink-0" />
    <span v-if="!collapsed" class="truncate">{{ label }}</span>
  </Link>
</template>