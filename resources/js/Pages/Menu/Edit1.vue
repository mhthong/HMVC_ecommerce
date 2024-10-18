<template>
    <div class="flex justify-between">
      <nested-draggable v-model="list" class="w-full"></nested-draggable>
    </div>
  </template>
  
  <script setup >
  import { ref } from 'vue';
  import NestedDraggable from '@/Components/NestedDirective.vue';


  const list = ref([
    {
      name: 'item 1',
      children: [
        {
          name: 'item 2',
          children: []
        }
      ]
    },
    {
      name: 'item 3',
      children: [
        {
          name: 'item 4',
          children: []
        }
      ]
    },
    {
      name: 'item 5',
      children: []
    }
  ])
  </script>
  