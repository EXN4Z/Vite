<script setup>
import { ref } from 'vue';
import draggable from 'vuedraggable';
import BlockRenderer from '@/Components/app/BlockRenderer.vue';
import ChartPicker from '@/Components/app/ChartPicker.vue';
import { Type, Table2, BarChart3, CreditCard, GripVertical, Trash2 } from 'lucide-vue-next';

const props = defineProps({
  modelValue: { type: Array, required: true }, // array of { type, config }
});
const emit = defineEmits(['update:modelValue']);

const availableBlocks = [
  { type: 'text', label: 'Teks', icon: Type },
  { type: 'table', label: 'Tabel', icon: Table2 },
  { type: 'chart', label: 'Chart', icon: BarChart3 },
  { type: 'card', label: 'Card', icon: CreditCard },
];

// vuedraggable butuh 2 list terpisah kalau mau drag ANTAR list (clone dari sumber ke tujuan).
// "source" cuma dipakai buat ditarik keluar (clone: true, tidak ikut berkurang).
// "canvas" adalah list asli yang di-emit balik ke parent.
const canvas = ref([...props.modelValue]);

function onCanvasChange() {
  emit('update:modelValue', canvas.value);
}

function removeBlock(index) {
  canvas.value.splice(index, 1);
  onCanvasChange();
}

function cloneBlock(block) {
  // Setiap block yang di-drag dari panel sumber harus di-clone (bukan reference sama),
  // biar tiap instance di canvas independent config-nya.
  const config = block.type === 'chart' ? { variant: 'bar' } : {};
  return { type: block.type, config };
}

function updateChartVariant(index, variant) {
  canvas.value[index].config = { ...canvas.value[index].config, variant };
  onCanvasChange();
}
</script>

<template>
  <div class="grid grid-cols-3 gap-4">
    <!-- Canvas -->
    <div class="col-span-2">
      <p class="mb-2 text-xs font-medium text-slate-500">Susunan Halaman</p>
      <draggable
        v-model="canvas"
        item-key="__key"
        group="blocks"
        class="min-h-[200px] space-y-2 rounded-lg border-2 border-dashed border-slate-200 p-3"
        ghost-class="opacity-40"
        @change="onCanvasChange"
      >
        <template #item="{ element: block, index }">
          <div class="group relative">
            <div class="absolute -left-1 top-1/2 -translate-x-full -translate-y-1/2 cursor-grab text-slate-300 opacity-0 group-hover:opacity-100">
              <GripVertical class="h-4 w-4" />
            </div>

            <div v-if="block.type === 'chart'" class="mb-2">
              <ChartPicker
                :model-value="block.config?.variant || 'bar'"
                @update:model-value="(v) => updateChartVariant(index, v)"
              />
            </div>

            <BlockRenderer :block="block" />
            <button
              type="button"
              class="absolute right-2 top-2 rounded-md bg-white p-1 text-slate-300 opacity-0 shadow-sm hover:text-red-500 group-hover:opacity-100"
              @click="removeBlock(index)"
            >
              <Trash2 class="h-3.5 w-3.5" />
            </button>
          </div>
        </template>

        <template #footer>
          <p v-if="canvas.length === 0" class="py-8 text-center text-xs text-slate-400">
            Drag blok dari panel kanan ke sini.
          </p>
        </template>
      </draggable>
    </div>

    <!-- Panel sumber blok -->
    <div>
      <p class="mb-2 text-xs font-medium text-slate-500">Blok Tersedia</p>
      <draggable
        :model-value="availableBlocks"
        :group="{ name: 'blocks', pull: 'clone', put: false }"
        :clone="cloneBlock"
        item-key="type"
        class="space-y-2"
        :sort="false"
      >
        <template #item="{ element: block }">
          <div class="flex cursor-grab items-center gap-2 rounded-lg border border-slate-200 bg-white p-3 text-sm text-slate-600 hover:border-slate-300">
            <component :is="block.icon" class="h-4 w-4 text-slate-400" />
            {{ block.label }}
          </div>
        </template>
      </draggable>
    </div>
  </div>
</template>