<template>
    <ul>
        <li
            v-for="child in children"
            :key="child.id"
            class="border border-secondary p-1 pe-0 mt-2"
        >
            <div class="d-flex align-items-center">
                <span>{{ child.name }}</span>
                <div>
                    <v-icon
                        icon="mdi:mdi-pencil-box-outline"
                        @click="editCategory(child)"
                    />
                    <v-icon
                        icon="mdi:mdi-delete"
                        @click="deleteCategory(child)"
                    />
                </div>
            </div>
            <!-- Hiển thị children nếu tồn tại -->
            <ul v-if="child.children && child.children.length > 0">
                <category-children :children="child.children" @editCategory="editCategory" @deleteCategory="deleteCategory"></category-children>
            </ul>
        </li>
    </ul>
</template>

<script setup>
import { ref, onMounted, watch, reactive } from "vue";
import CategoryChildren from "@/Components/CategoryChildren.vue";

const props = defineProps({
    children: {
        type: Object, // Update to Object if `page` is an object
        required: true,
    },
});

const emit = defineEmits(["editCategory", "deleteCategory"]);

function editCategory(child) {
    // Emit the selected category to the parent
    emit("editCategory", child);
}

function deleteCategory(child) {
    // Emit delete action for this category
    emit("deleteCategory", child);
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
</style>
