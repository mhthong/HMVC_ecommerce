<template>
    <div>
      <QRCodeVue :value="qrValue" :size="120" />
    </div>
  </template>
  
  <script setup>
  import { computed } from "vue";
  import QRCodeVue from "qrcode.vue"; // Import the QR code component
  
  // Props
  const props = defineProps({
    warrantyCode: String, // Assume you're passing warrantyCode as a prop
  });
  
  // Use Laravel's route helper to generate the URL
  const qrValue = computed(() => {
    return route('warranty.index', { warranty_code: props.warrantyCode });
  });
  </script>
  