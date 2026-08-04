<script setup>
import { ref, computed } from 'vue';
import * as Icons from 'lucide-vue-next';
import { Search, Circle } from 'lucide-vue-next';

const props = defineProps({
  modelValue: { type: String, default: '' },
});
const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const search = ref('');

// Ambil semua nama export dari lucide-vue-next yang merupakan component icon.
// Lucide export tiap icon sebagai PascalCase component + beberapa util lain (createLucideIcon, dst) yang perlu difilter.
const allIconNames = Object.keys(Icons).filter((name) => {
  const type = typeof Icons[name];
  return /^[A-Z]/.test(name) && (type === 'object' || type === 'function');
});

const filteredIcons = computed(() => {
  if (!search.value) return allIconNames.slice(0, 200); // batasi render awal biar gak berat
  const q = search.value.toLowerCase();
  return allIconNames.filter((name) => name.toLowerCase().includes(q)).slice(0, 200);
});

const selectedIcon = computed(() => Icons[props.modelValue] || Circle);

function selectIcon(name) {
  emit('update:modelValue', name);
  open.value = false;
  search.value = '';
}

function toggleOpen() {
  open.value = !open.value;
}
</script>

<template>
  <div class="relative">
    <button
      type="button"
      class="flex items-center gap-2 rounded-md border border-slate-300 px-3 py-2 text-sm hover:bg-slate-50"
      @click="toggleOpen"
    >
      <component :is="selectedIcon" class="h-4 w-4" />
      <span class="text-slate-600">{{ modelValue || 'Pilih icon' }}</span>
    </button>

    <div
      v-if="open"
      class="absolute z-50 mt-1 w-72 rounded-lg border border-slate-200 bg-white p-3 shadow-lg"
    >
      <div class="mb-2 flex items-center gap-2 rounded-md border border-slate-200 px-2 py-1.5">
        <Search class="h-4 w-4 text-slate-400" />
        <input
          v-model="search"
          type="text"
          placeholder="Cari icon..."
          class="w-full text-sm outline-none"
          autofocus
        />
      </div>

      <div class="grid max-h-64 grid-cols-6 gap-1 overflow-y-auto">
        <button
          v-for="name in filteredIcons"
          :key="name"
          type="button"
          class="flex items-center justify-center rounded-md p-2 hover:bg-slate-100"
          :class="modelValue === name ? 'bg-slate-900 text-white hover:bg-slate-900' : 'text-slate-600'"
          :title="name"
          @click="selectIcon(name)"
        >
          <component :is="Icons[name]" class="h-4 w-4" />
        </button>
      </div>

      <p v-if="filteredIcons.length === 0" class="py-4 text-center text-xs text-slate-400">
        Icon tidak ditemukan.
      </p>
    </div>

    <!-- Overlay buat close pas klik di luar -->
    <div v-if="open" class="fixed inset-0 z-40" @click="open = false"></div>
  </div>
</template>