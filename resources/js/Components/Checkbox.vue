<template>
    <div class="flex items-center">
      <input
        :id="name"
        type="checkbox"
        :checked="modelValue"
        @change="$emit('update:modelValue', $event.target.checked)"
        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
      />
      <label
        v-if="label"
        :for="name"
        class="ml-2 block text-sm text-gray-900 dark:text-gray-300"
      >
        {{ label }}
      </label>
    </div>
  </template>
  
  <script>
  export default {
    name: 'Checkbox',
    props: {
      modelValue: Boolean,
      label: {
        type: String,
        default: '',
      },
      name: {
        type: String,
        required: true,
      },
    },
  };
  </script>
  