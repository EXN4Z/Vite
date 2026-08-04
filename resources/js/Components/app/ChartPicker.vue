<script setup>
import { ref, computed, markRaw } from 'vue';
import { BarChart3, LineChart, AreaChart, PieChart } from 'lucide-vue-next';
import ChartBarStandard from '@/Components/app/charts/ChartBarStandard.vue';
import ChartLineStandard from '@/Components/app/charts/ChartLineStandard.vue';
import ChartAreaStandard from '@/Components/app/charts/ChartAreaStandard.vue';
import ChartPieStandard from '@/Components/app/charts/ChartPieStandard.vue';

const props = defineProps({
  modelValue: { type: String, default: 'bar' },
});
const emit = defineEmits(['update:modelValue']);

// Daftar varian chart yang tersedia. Tambahkan di sini kalau mau nambah varian baru nanti.
const variants = [
  { key: 'bar', label: 'Bar Chart', icon: BarChart3, component: markRaw(ChartBarStandard) },
  { key: 'line', label: 'Line Chart', icon: LineChart, component: markRaw(ChartLineStandard) },
  { key: 'area', label: 'Area Chart', icon: AreaChart, component: markRaw(ChartAreaStandard) },
  { key: 'pie', label: 'Pie Chart', icon: PieChart, component: markRaw(ChartPieStandard) },
];

const open = ref(false);

const selected = computed(() => variants.find((v) => v.key === props.modelValue) || variants[0]);

function selectVariant(key) {
  emit('update:modelValue', key);
  open.value = false;
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
      <component :is="selected.icon" class="h-4 w-4" />
      <span class="text-slate-600">{{ selected.label }}</span>
    </button>

    <div
      v-if="open"
      class="absolute z-50 mt-1 w-96 rounded-lg border border-slate-200 bg-white p-3 shadow-lg"
    >
      <div class="grid grid-cols-2 gap-2">
        <button
          v-for="variant in variants"
          :key="variant.key"
          type="button"
          class="rounded-lg border p-2 text-left transition-colors hover:border-slate-400"
          :class="modelValue === variant.key ? 'border-slate-900 ring-1 ring-slate-900' : 'border-slate-200'"
          @click="selectVariant(variant.key)"
        >
          <div class="pointer-events-none h-20 overflow-hidden">
            <component :is="variant.component" />
          </div>
          <div class="mt-1 flex items-center gap-1.5 text-xs font-medium text-slate-600">
            <component :is="variant.icon" class="h-3.5 w-3.5" />
            {{ variant.label }}
          </div>
        </button>
      </div>
    </div>

    <div v-if="open" class="fixed inset-0 z-40" @click="open = false"></div>
  </div>
</template>