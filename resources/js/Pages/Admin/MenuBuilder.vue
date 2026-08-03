<script setup>
import { ref, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import draggable from 'vuedraggable';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Trash2, GripVertical, Plus, ChevronDown, ChevronRight } from 'lucide-vue-next';

const props = defineProps({
  menus: { type: Array, required: true },
});

// Local reactive copy biar drag-drop responsif tanpa nunggu round-trip server
const localMenus = ref(structuredClone(props.menus));

// Track parent mana yang lagi expanded (buat lihat children-nya)
const expanded = ref({});
function toggleExpand(id) {
  expanded.value[id] = !expanded.value[id];
}

// Form tambah menu baru
const showAddForm = ref(false);
const addForm = useForm({
  label: '',
  icon: '',
  type: 'link',
  route: '',
  parent_id: null,
});

function submitAdd() {
  addForm.post(route('admin.menus.store'), {
    preserveScroll: true,
    onSuccess: () => {
      addForm.reset();
      showAddForm.value = false;
      router.reload({ only: ['menus'] });
    },
  });
}

function deleteMenu(id) {
  if (!confirm('Yakin mau hapus menu ini? Semua sub-menu di dalamnya juga akan terhapus.')) return;
  router.delete(route('admin.menus.destroy', id), {
    preserveScroll: true,
  });
}

// Setiap kali urutan parent-level berubah (drag-drop selesai), kirim ke server
function onParentReorder() {
  saveOrder();
}

function onChildReorder(parentId) {
  saveOrder();
}

function saveOrder() {
  const payload = [];

  localMenus.value.forEach((parent, parentIndex) => {
    payload.push({ id: parent.id, order: parentIndex, parent_id: null });
    (parent.children || []).forEach((child, childIndex) => {
      payload.push({ id: child.id, order: childIndex, parent_id: parent.id });
    });
  });

  router.post(route('admin.menus.reorder'), { menus: payload }, {
    preserveScroll: true,
    preserveState: true,
  });
}
</script>

<template>
  <AppLayout>
    <template #header>
      <h1 class="text-lg font-semibold text-slate-900">Menu Builder</h1>
    </template>

    <div class="mx-auto max-w-2xl space-y-4">
      <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">
          Drag untuk atur urutan. Menu bisa punya maksimal 1 level sub-menu.
        </p>
        <Button size="sm" @click="showAddForm = !showAddForm">
          <Plus class="mr-1 h-4 w-4" />
          Tambah Menu
        </Button>
      </div>

      <!-- Form tambah menu -->
      <div v-if="showAddForm" class="rounded-lg border border-slate-200 bg-white p-4">
        <form class="space-y-3" @submit.prevent="submitAdd">
          <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">Label</label>
            <Input v-model="addForm.label" placeholder="Contoh: Laporan Penjualan" required />
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">Icon (nama lucide-icon)</label>
            <Input v-model="addForm.icon" placeholder="Contoh: FileText" />
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">Route / URL</label>
            <Input v-model="addForm.route" placeholder="/laporan-penjualan" />
          </div>
          <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">Induk Menu (opsional)</label>
            <select
              v-model="addForm.parent_id"
              class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
            >
              <option :value="null">— Tidak ada (jadi menu utama) —</option>
              <option v-for="parent in localMenus" :key="parent.id" :value="parent.id">
                {{ parent.label }}
              </option>
            </select>
          </div>
          <div class="flex gap-2">
            <Button type="submit" size="sm" :disabled="addForm.processing">Simpan</Button>
            <Button type="button" variant="ghost" size="sm" @click="showAddForm = false">Batal</Button>
          </div>
        </form>
      </div>

      <!-- List menu drag-drop -->
      <draggable
        v-model="localMenus"
        item-key="id"
        handle=".drag-handle"
        class="space-y-2"
        @end="onParentReorder"
      >
        <template #item="{ element: parent }">
          <div class="rounded-lg border border-slate-200 bg-white">
            <div class="flex items-center gap-2 px-3 py-2">
              <span class="drag-handle cursor-grab text-slate-400">
                <GripVertical class="h-4 w-4" />
              </span>

              <button
                v-if="parent.children && parent.children.length"
                class="text-slate-400"
                @click="toggleExpand(parent.id)"
              >
                <ChevronDown v-if="expanded[parent.id]" class="h-4 w-4" />
                <ChevronRight v-else class="h-4 w-4" />
              </button>
              <span v-else class="w-4"></span>

              <span class="flex-1 text-sm font-medium text-slate-800">{{ parent.label }}</span>
              <span class="text-xs text-slate-400">{{ parent.route }}</span>

              <button class="text-slate-400 hover:text-red-500" @click="deleteMenu(parent.id)">
                <Trash2 class="h-4 w-4" />
              </button>
            </div>

            <!-- Children (nested, max 1 level lagi) -->
            <div v-if="expanded[parent.id] && parent.children && parent.children.length" class="border-t border-slate-100 pl-8 pr-3">
              <draggable
                v-model="parent.children"
                item-key="id"
                handle=".drag-handle-child"
                class="space-y-1 py-2"
                @end="() => onChildReorder(parent.id)"
              >
                <template #item="{ element: child }">
                  <div class="flex items-center gap-2 rounded-md bg-slate-50 px-3 py-2">
                    <span class="drag-handle-child cursor-grab text-slate-400">
                      <GripVertical class="h-3.5 w-3.5" />
                    </span>
                    <span class="flex-1 text-sm text-slate-700">{{ child.label }}</span>
                    <span class="text-xs text-slate-400">{{ child.route }}</span>
                    <button class="text-slate-400 hover:text-red-500" @click="deleteMenu(child.id)">
                      <Trash2 class="h-3.5 w-3.5" />
                    </button>
                  </div>
                </template>
              </draggable>
            </div>
          </div>
        </template>
      </draggable>

      <div v-if="localMenus.length === 0" class="rounded-lg border border-dashed border-slate-300 py-12 text-center text-sm text-slate-400">
        Belum ada menu. Klik "Tambah Menu" untuk mulai.
      </div>
    </div>
  </AppLayout>
</template>