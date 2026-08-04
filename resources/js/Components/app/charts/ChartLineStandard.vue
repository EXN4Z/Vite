<script setup>
import { VisAxis, VisLine, VisXYContainer } from '@unovis/vue';

const chartData = [
  { month: 'Jan', desktop: 186, mobile: 80 },
  { month: 'Feb', desktop: 305, mobile: 200 },
  { month: 'Mar', desktop: 237, mobile: 120 },
  { month: 'Apr', desktop: 73, mobile: 190 },
  { month: 'Mei', desktop: 209, mobile: 130 },
  { month: 'Jun', desktop: 214, mobile: 140 },
];

const colors = ['#0f172a', '#94a3b8'];
</script>

<template>
  <div class="h-64 w-full">
    <VisXYContainer :data="chartData">
      <VisLine :x="(d, i) => i" :y="(d) => d.desktop" :color="colors[0]" />
      <VisLine :x="(d, i) => i" :y="(d) => d.mobile" :color="colors[1]" />
      <VisAxis
        type="x"
        :tick-format="(i) => chartData[i]?.month ?? ''"
        :tick-line="false"
        :domain-line="false"
        :grid-line="false"
      />
    </VisXYContainer>
  </div>
</template>