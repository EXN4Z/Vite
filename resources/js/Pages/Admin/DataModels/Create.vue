<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Plus, Trash2, GripVertical } from 'lucide-vue-next';

const props = defineProps({
  fieldTypes: { type: Array, required: true },
});

const typeLabels = {
  text: 'Teks Pendek',
  textarea: 'Teks Panjang',
  number: 'Angka',
  date: 'Tanggal',
  boolean: 'Ya/Tidak',
  select: 'Pilihan (Dropdown)',
};

const form = useForm({
  name: '',
  description: '',
  fields: [
    { name: '', type: 'text', is_required: false, options: null },
  ],
});

function addField() {
  form.fields.push({ name: '', type: 'text', is_required: false, options: null });
}

function removeField(index) {
  if (form.fields.length === 1) return; // minimal 1 field
  form.fields.splice(index, 1);
}

function submit() {
  form.post(route('admin.data-models.store'));
}
</script>

<template>
  <AppLayout>
    <template #header>
      <h1 class="text-lg font-semibold text-slate-900">Buat Tabel Baru</h1>
    </template>

    <div class="mx-auto max-w-2xl space-y-6">
      <!-- Info dasar tabel -->
      <div class="rounded-lg border border-slate-200 bg-white p-4 space-y-3">
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Nama Tabel</label>
          <Input v-model="form.name" placeholder="Contoh: Laporan Penjualan" />
          <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
        </div>
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Deskripsi (opsional)</label>
          <Input v-model="form.description" placeholder="Catatan singkat tentang tabel ini" />
        </div>
      </div>

      <!-- Field builder -->
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <h2 class="text-sm font-semibold text-slate-700">Field / Kolom</h2>
          <Button type="button" variant="outline" size="sm" @click="addField">
            <Plus class="mr-1 h-3.5 w-3.5" />
            Tambah Field
          </Button>
        </div>

        <div
          v-for="(field, index) in form.fields"
          :key="index"
          class="flex items-start gap-2 rounded-lg border border-slate-200 bg-white p-3"
        >
          <GripVertical class="mt-2.5 h-4 w-4 shrink-0 text-slate-300" />

          <div class="flex-1 space-y-2">
            <Input v-model="field.name" placeholder="Nama field, contoh: Judul Laporan" />

            <div class="flex items-center gap-3">
              <select
                v-model="field.type"
                class="rounded-md border border-slate-300 px-2 py-1.5 text-sm"
              >
                <option v-for="type in fieldTypes" :key="type" :value="type">
                  {{ typeLabels[type] || type }}
                </option>
              </select>

              <label class="flex items-center gap-1.5 text-xs text-slate-500">
                <input type="checkbox" v-model="field.is_required" />
                Wajib diisi
              </label>
            </div>

            <p v-if="form.errors[`fields.${index}.name`]" class="text-xs text-red-500">
              {{ form.errors[`fields.${index}.name`] }}
            </p>
          </div>

          <button
            type="button"
            class="mt-1 rounded-md p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-500 disabled:opacity-30"
            :disabled="form.fields.length === 1"
            @click="removeField(index)"
          >
            <Trash2 class="h-4 w-4" />
          </button>
        </div>
      </div>

      <div class="flex gap-2">
        <Button :disabled="form.processing" @click="submit">Simpan Tabel</Button>
      </div>
    </div>
  </AppLayout>
</template>