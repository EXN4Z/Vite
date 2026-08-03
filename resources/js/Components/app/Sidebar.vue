<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import {
  LayoutDashboard,
  Settings,
  Users,
  ChevronsLeft,
  ChevronsRight,
} from 'lucide-vue-next';
import SidebarItem from '@/Components/app/SidebarItem.vue';
import { Button } from '@/Components/ui/button';

const collapsed = ref(false);
const page = usePage();

const menuItems = [
  { label: 'Dashboard', href: '/dashboard', icon: LayoutDashboard },
  { label: 'Users', href: '/users', icon: Users },
  { label: 'Settings', href: '/settings', icon: Settings },
];

function isActive(href) {
  return page.url.startsWith(href);
}

function toggleCollapse() {
  collapsed.value = !collapsed.value;
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

    <!-- Menu items -->
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
      <SidebarItem
        v-for="item in menuItems"
        :key="item.href"
        :href="item.href"
        :icon="item.icon"
        :label="item.label"
        :active="isActive(item.href)"
        :collapsed="collapsed"
      />
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