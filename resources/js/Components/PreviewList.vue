<template>
    <div>
      <h2>Preview List</h2>
      <pre>{{ list }}</pre>
    </div>
  </template>
  
  <script setup>
  import { defineProps } from 'vue'
  
  const props = defineProps({
    list: {
      type: Array,
      required: true
    }
  })
  </script>
  
  <style scoped>
  /* Add any necessary styles here */
  </style>
  