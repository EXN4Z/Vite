<script setup>
import { VisDonut, VisSingleContainer } from '@unovis/vue';

const chartData = [
  { browser: 'Chrome', visitors: 275, color: '#0f172a' },
  { browser: 'Safari', visitors: 200, color: '#334155' },
  { browser: 'Firefox', visitors: 187, color: '#64748b' },
  { browser: 'Edge', visitors: 173, color: '#94a3b8' },
  { browser: 'Lainnya', visitors: 90, color: '#cbd5e1' },
];
</script>

<template>
  <div class="h-64 w-full">
    <VisSingleContainer :data="chartData">
      <VisDonut
        :value="(d) => d.visitors"
        :color="(d) => d.color"
        :corner-radius="4"
        :pad-angle="0.02"
      />
    </VisSingleContainer>
  </div>
</template>