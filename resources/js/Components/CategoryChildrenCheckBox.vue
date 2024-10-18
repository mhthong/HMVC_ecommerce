<template>
    <ul>
        <li
            v-for="child in children"
            :key="child.id"
            class=""
        >
            <v-checkbox 
                :label="child.name"  
                :value="child.id"
                v-model="child.checked"  
                @change="toggleCheck(child)">
            </v-checkbox>

            <!-- Display children if they exist -->
            <ul v-if="child.children && child.children.length > 0">
                <category-children 
                    :children="child.children" 
                    :Check="Check"
                    @checkBook="toggleCheck"
                ></category-children>
            </ul>
        </li>
    </ul>
</template>

<script setup>
import { defineProps, defineEmits } from "vue";
import CategoryChildren from "@/Components/CategoryChildrenCheckBox.vue";

const props = defineProps({
    children: {
        type: Array, // Should be Array, not Object since `v-for` is used
        required: true,
    },
    Check: {
        type: Object, 
        required: false,
    },
});

const emit = defineEmits(['checkBook']);

// Function to handle checkbox change
function toggleCheck(child) {
    emit('checkBook', child);
}
</script>

<style scoped>
.d-flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.v-btn {
    margin-left: 10px;
}

li > ul {
    padding-left: 1rem;
}

ul > li {
    border-left: solid 1px #ccc;
}
</style>
