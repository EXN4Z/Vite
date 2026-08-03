<script setup>
import Sidebar from '@/Components/app/Sidebar.vue';
</script>

<template>
  <div class="flex h-screen bg-slate-50">
    <Sidebar />

    <div class="flex flex-1 flex-col overflow-hidden">
      <!-- Topbar -->
      <header class="flex h-16 items-center border-b border-slate-200 bg-white px-6">
        <slot name="header">
          <h1 class="text-lg font-semibold text-slate-900">Dashboard</h1>
        </slot>
      </header>

      <!-- Page content -->
      <main class="flex-1 overflow-y-auto p-6">
        <slot />
      </main>
    </div>
  </div>
</template>