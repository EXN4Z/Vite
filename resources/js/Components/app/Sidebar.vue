<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import * as Icons from 'lucide-vue-next';
import {
  ChevronsLeft,
  ChevronsRight,
  ChevronDown,
  ChevronRight,
  Circle,
} from 'lucide-vue-next';
import SidebarItem from '@/Components/app/SidebarItem.vue';
import { Button } from '@/Components/ui/button';

const collapsed = ref(false);
const openParents = ref({});
const page = usePage();

// Menu diambil dari shared prop Inertia (di-set di HandleInertiaRequests middleware)
const menuItems = computed(() => page.props.sidebarMenus || []);

// Convert nama icon (string, misal "Table2") jadi component icon asli.
// Fallback ke icon "Circle" kalau nama tidak ditemukan / kosong.
function resolveIcon(iconName) {
  return Icons[iconName] || Circle;
}

function isActive(href) {
  return href && page.url.startsWith(href);
}

function toggleCollapse() {
  collapsed.value = !collapsed.value;
}

function toggleParent(id) {
  openParents.value[id] = !openParents.value[id];
}
</script>

<template>
  <aside
    class="flex h-screen flex-col border-r border-slate-200 bg-white transition-all duration-200"
    :class="collapsed ? 'w-[72px]' : 'w-64'"
  >
    <!-- Logo / App name -->
    <div class="flex h-16 items-center border-b border-slate-200 px-4">
      <span v-if="!collapsed" class="text-lg font-semibold text-slate-900">
        Marimas One
      </span>
      <span v-else class="mx-auto text-lg font-semibold text-slate-900">M</span>
    </div>

    <!-- Menu items (dinamis dari database) -->
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
      <template v-for="item in menuItems" :key="item.id">
        <!-- Menu tanpa children -->
        <SidebarItem
          v-if="!item.children || item.children.length === 0"
          :href="item.route || '#'"
          :icon="resolveIcon(item.icon)"
          :label="item.label"
          :active="isActive(item.route)"
          :collapsed="collapsed"
        />

        <div v-else>
          <button
            class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900"
            :class="collapsed ? 'justify-center' : ''"
            @click="toggleParent(item.id)"
          >
            <component :is="resolveIcon(item.icon)" class="h-5 w-5 shrink-0" />
            <span v-if="!collapsed" class="flex-1 truncate text-left">{{ item.label }}</span>
            <component
              v-if="!collapsed"
              :is="openParents[item.id] ? ChevronDown : ChevronRight"
              class="h-4 w-4 shrink-0"
            />
          </button>

          <div v-if="openParents[item.id] && !collapsed" class="ml-4 mt-1 space-y-1 border-l border-slate-100 pl-3">
            <SidebarItem
              v-for="child in item.children"
              :key="child.id"
              :href="child.route || '#'"
              :icon="resolveIcon(child.icon)"
              :label="child.label"
              :active="isActive(child.route)"
              :collapsed="false"
            />
          </div>
        </div>
      </template>

      <p v-if="menuItems.length === 0 && !collapsed" class="px-3 py-2 text-xs text-slate-400">
        Belum ada menu.
      </p>
    </nav>

    <!-- Collapse toggle -->
    <div class="border-t border-slate-200 p-3">
      <Button
        variant="ghost"
        size="sm"
        class="w-full justify-center"
        @click="toggleCollapse"
      >
        <ChevronsLeft v-if="!collapsed" class="h-4 w-4" />
        <ChevronsRight v-else class="h-4 w-4" />
      </Button>
    </div>
  </aside>
</template>