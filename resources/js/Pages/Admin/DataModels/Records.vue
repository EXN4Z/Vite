<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Plus, Trash2, X } from 'lucide-vue-next';

const props = defineProps({
  dataModel: { type: Object, required: true },
  records: { type: Array, required: true },
});

const showAddForm = ref(false);

// Bangun default values kosong sesuai field yang ada
function buildEmptyValues() {
  const values = {};
  props.dataModel.fields.forEach((f) => {
    values[f.key] = f.type === 'boolean' ? false : '';
  });
  return values;
}

const form = useForm({
  values: buildEmptyValues(),
});

function submit() {
  form.post(route('admin.data-records.store', props.dataModel.id), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      form.values = buildEmptyValues();
      showAddForm.value = false;
    },
  });
}

function deleteRecord(id) {
  if (!confirm('Yakin mau hapus data ini?')) return;
  router.delete(route('admin.data-records.destroy', [props.dataModel.id, id]), {
    preserveScroll: true,
  });
}
</script>

<template>
  <AppLayout>
    <template #header>
      <h1 class="text-lg font-semibold text-slate-900">{{ dataModel.name }}</h1>
    </template>

    <div class="mx-auto max-w-4xl space-y-4">
      <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">{{ dataModel.description || 'Tidak ada deskripsi.' }}</p>
        <Button size="sm" @click="showAddForm = !showAddForm">
          <Plus v-if="!showAddForm" class="mr-1 h-4 w-4" />
          <X v-else class="mr-1 h-4 w-4" />
          {{ showAddForm ? 'Batal' : 'Tambah Data' }}
        </Button>
      </div>

      <!-- Form tambah data -->
      <div v-if="showAddForm" class="rounded-lg border border-slate-200 bg-white p-4 space-y-3">
        <div v-for="field in dataModel.fields" :key="field.id">
          <label class="mb-1 block text-xs font-medium text-slate-600">
            {{ field.name }}
            <span v-if="field.is_required" class="text-red-400">*</span>
          </label>

          <Input
            v-if="['text', 'number', 'date'].includes(field.type)"
            v-model="form.values[field.key]"
            :type="field.type === 'number' ? 'number' : field.type === 'date' ? 'date' : 'text'"
          />
          <textarea
            v-else-if="field.type === 'textarea'"
            v-model="form.values[field.key]"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
            rows="3"
          />
          <label v-else-if="field.type === 'boolean'" class="flex items-center gap-2 text-sm">
            <input type="checkbox" v-model="form.values[field.key]" />
            Ya
          </label>
          <select
            v-else-if="field.type === 'select'"
            v-model="form.values[field.key]"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm"
          >
            <option value="">— Pilih —</option>
            <option v-for="opt in field.options || []" :key="opt" :value="opt">{{ opt }}</option>
          </select>

          <p v-if="form.errors[`values.${field.key}`]" class="mt-1 text-xs text-red-500">
            {{ form.errors[`values.${field.key}`] }}
          </p>
        </div>

        <Button :disabled="form.processing" @click="submit">Simpan Data</Button>
      </div>

      <!-- Tabel data -->
      <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-slate-200 bg-slate-50">
              <th
                v-for="field in dataModel.fields"
                :key="field.id"
                class="px-4 py-2 text-left text-xs font-medium text-slate-500"
              >
                {{ field.name }}
              </th>
              <th class="w-10"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="records.length === 0">
              <td :colspan="dataModel.fields.length + 1" class="px-4 py-8 text-center text-sm text-slate-400">
                Belum ada data.
              </td>
            </tr>
            <tr v-for="record in records" :key="record.id" class="border-b border-slate-100 last:border-0">
              <td v-for="field in dataModel.fields" :key="field.id" class="px-4 py-2 text-slate-700">
                <span v-if="field.type === 'boolean'">
                  {{ record.values[field.key] ? 'Ya' : 'Tidak' }}
                </span>
                <span v-else>{{ record.values[field.key] ?? '—' }}</span>
              </td>
              <td class="px-2 py-2">
                <button
                  class="rounded-md p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-500"
                  @click="deleteRecord(record.id)"
                >
                  <Trash2 class="h-3.5 w-3.5" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>