<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Button } from '@/Components/ui/button';
import { Plus, Table2, Trash2, ArrowRight } from 'lucide-vue-next';

defineProps({
  dataModels: { type: Array, required: true },
});

function deleteModel(id, name) {
  if (!confirm(`Yakin mau hapus tabel "${name}"? Semua data di dalamnya akan ikut terhapus.`)) return;
  router.delete(route('admin.data-models.destroy', id), { preserveScroll: true });
}
</script>

<template>
  <AppLayout>
    <template #header>
      <h1 class="text-lg font-semibold text-slate-900">Tabel Data</h1>
    </template>

    <div class="mx-auto max-w-3xl space-y-4">
      <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">Kelola tabel data custom untuk aplikasi Anda.</p>
        <Link :href="route('admin.data-models.create')">
          <Button size="sm">
            <Plus class="mr-1 h-4 w-4" />
            Buat Tabel Baru
          </Button>
        </Link>
      </div>

      <div v-if="dataModels.length === 0" class="rounded-lg border border-dashed border-slate-300 py-12 text-center text-sm text-slate-400">
        Belum ada tabel data. Klik "Buat Tabel Baru" untuk mulai.
      </div>

      <div v-else class="space-y-2">
        <div
          v-for="model in dataModels"
          :key="model.id"
          class="flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-4"
        >
          <div class="rounded-md bg-slate-100 p-2">
            <Table2 class="h-5 w-5 text-slate-500" />
          </div>

          <div class="flex-1">
            <p class="text-sm font-medium text-slate-900">{{ model.name }}</p>
            <p class="text-xs text-slate-400">
              {{ model.fields_count }} field · {{ model.records_count }} data
            </p>
          </div>

          <Link :href="route('admin.data-records.index', model.id)">
            <Button variant="outline" size="sm">
              Lihat Data
              <ArrowRight class="ml-1 h-3.5 w-3.5" />
            </Button>
          </Link>

          <button
            class="rounded-md p-2 text-slate-400 hover:bg-red-50 hover:text-red-500"
            @click="deleteModel(model.id, model.name)"
          >
            <Trash2 class="h-4 w-4" />
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>