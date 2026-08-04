<script setup>
import { computed } from 'vue';
import { Table2, BarChart3, CreditCard, Type } from 'lucide-vue-next';
import ChartBarStandard from '@/Components/app/charts/ChartBarStandard.vue';
import ChartLineStandard from '@/Components/app/charts/ChartLineStandard.vue';
import ChartAreaStandard from '@/Components/app/charts/ChartAreaStandard.vue';
import ChartPieStandard from '@/Components/app/charts/ChartPieStandard.vue';

const props = defineProps({
  block: { type: Object, required: true },
});

const chartComponents = {
  bar: ChartBarStandard,
  line: ChartLineStandard,
  area: ChartAreaStandard,
  pie: ChartPieStandard,
};

const chartVariant = computed(() => props.block.config?.variant || 'bar');
const chartComponent = computed(() => chartComponents[chartVariant.value] || ChartBarStandard);
</script>

<template>
  <div class="rounded-lg border border-slate-200 bg-white p-4">
    <!-- Text block -->
    <div v-if="block.type === 'text'" class="flex items-start gap-2">
      <Type class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" />
      <div>
        <p class="text-xs font-medium text-slate-400">Blok Teks</p>
        <p class="text-sm text-slate-600">Lorem ipsum dolor sit amet, teks placeholder di sini.</p>
      </div>
    </div>

    <!-- Table block -->
    <div v-else-if="block.type === 'table'" class="space-y-2">
      <div class="flex items-center gap-2">
        <Table2 class="h-4 w-4 text-slate-400" />
        <p class="text-xs font-medium text-slate-400">Blok Tabel</p>
      </div>
      <div class="overflow-hidden rounded-md border border-slate-100">
        <table class="w-full text-xs">
          <thead>
            <tr class="bg-slate-50">
              <th class="px-2 py-1.5 text-left text-slate-500">Kolom A</th>
              <th class="px-2 py-1.5 text-left text-slate-500">Kolom B</th>
              <th class="px-2 py-1.5 text-left text-slate-500">Kolom C</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="i in 2" :key="i" class="border-t border-slate-100">
              <td class="px-2 py-1.5 text-slate-400">—</td>
              <td class="px-2 py-1.5 text-slate-400">—</td>
              <td class="px-2 py-1.5 text-slate-400">—</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Chart block -->
    <div v-else-if="block.type === 'chart'" class="space-y-2">
      <div class="flex items-center gap-2">
        <BarChart3 class="h-4 w-4 text-slate-400" />
        <p class="text-xs font-medium text-slate-400">Blok Chart</p>
      </div>
      <component :is="chartComponent" />
    </div>

    <!-- Card block -->
    <div v-else-if="block.type === 'card'" class="space-y-1">
      <div class="flex items-center gap-2">
        <CreditCard class="h-4 w-4 text-slate-400" />
        <p class="text-xs font-medium text-slate-400">Blok Card</p>
      </div>
      <div class="rounded-md bg-slate-50 p-3">
        <p class="text-xs text-slate-400">Total Sesuatu</p>
        <p class="text-lg font-semibold text-slate-700">123</p>
      </div>
    </div>
  </div>
</template>