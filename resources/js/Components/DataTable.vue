<template>
    <v-container class="table">
        <v-card>
            <v-card-title>
                <v-row>
                    <v-col>
                        <v-text-field
                            v-model="searchQuery"
                            label="Search"
                            md="4"
                            max-width="400px"
                        ></v-text-field>
                    </v-col>
                </v-row>
            </v-card-title>

            <v-data-table
                :key="tableKey"
                :headers="headers"
                :items="filteredData"
                :items-per-page="itemsPerPage"
                :search="searchQuery"
                class="elevation-1"
            >
                <!-- Checkbox in the ID column -->
                <template #item.id="{ item }" width="75px">
                    <v-checkbox
                        v-if="routes.bulkDestroy"
                        v-model="selectedItems"
                        :value="item.id"
                        :label="`${item.id}`"
                    ></v-checkbox>
                    <div v-if="!routes.bulkDestroy">{{ item.id }}</div>
                </template>

                <template #item.is_featured="{ item }">
                    {{ item.is_featured == 1 ? true : false }}
                </template>

                <template #item.categories="{ item }">
                    {{ item.categories.map((cat) => cat.name).join(", ") }}
                </template>

                <!-- Edit and delete icons -->
                <template #item.created_at="{ item }">
                    <a
                        :href="getEditUrl(item.id)"
                        v-if="routes.edit !== 'modal' && routes.edit"
                    >
                        <v-icon color="blue">mdi-pencil</v-icon>
                    </a>
                    <a
                        @click="$emit('edit', item)"
                        v-if="routes.edit === 'modal' && routes.edit"
                    >
                        <v-icon color="blue ">mdi-pencil</v-icon>
                    </a>

                    <v-icon @click="confirmDelete(item)" color="red"    v-if="routes.delete || routes.destroy"
                        >mdi-delete</v-icon
                    >

                    <a :href="getfolderUrl(item.id)"
                        color="gray"  v-if="routes.folder"
                    >
                        <v-icon color="blue">mdi-folder</v-icon>
                    </a>

                    
                </template>

                <!-- Other columns... -->
            </v-data-table>
        </v-card>

        <!-- Button to delete selected items -->
        <v-btn
            class="mt-2"
            v-if="routes.bulkDestroy"
            color="red"
            @click="confirmDeleteSelected"
            :disabled="selectedItems.length === 0"
        >
            Delete Selected
        </v-btn>
        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="deleteDialog" max-width="500">
            <v-card>
                <v-card-title class="headline">Confirm Deletion</v-card-title>
                <v-card-text>
                    <div v-if="selectedItems.length > 0">
                        Are you sure you want to delete the selected items?
                    </div>
                    <div v-else>Are you sure you want to delete this item?</div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="closeDeleteDialog"
                        >Cancel</v-btn
                    >
                    <v-btn
                        color="red darken-1"
                        text
                        @click="deleteItemConfirmed"
                        >Delete</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
import { defineComponent, ref, computed, watch, defineEmits } from "vue";
import axios from "axios";
import { toast } from "vue3-toastify";

export default defineComponent({
    props: {
        headers: {
            type: Array,
            required: true,
        },
        items: {
            type: Array,
            required: true,
        },
        routes: {
            type: Object,
            required: true,
        },
    },

    setup(props) {
        const searchQuery = ref("");
        const currentPage = ref(1);
        const itemsPerPage = ref(10);
        const tableKey = ref(0); // Để làm mới bảng khi cần
        const items = ref([...props.items]); // Local copy of items
        const selectedItems = ref([]); // Store selected item IDs
        const deleteDialog = ref(false);
        const itemToDelete = ref(null); // Single item to delete or null for multiple

        // Watch for changes in props.items and update local items
        watch(
            () => props.items,
            (newItems) => {
                items.value = [...newItems];
            }
        );

        const filteredData = computed(() => {
            if (!searchQuery.value) {
                return items.value;
            }
            return items.value.filter((item) =>
                Object.values(item).some((value) =>
                    String(value)
                        .toLowerCase()
                        .includes(searchQuery.value.toLowerCase())
                )
            );
        });

        const getEditUrl = (id) => {
            if (!props.routes || !props.routes.edit) {
                console.error("Routes or edit URL is not defined");
                return "#";
            }
            return props.routes.edit.replace(":id", id);
        };

        const getfolderUrl = (id) => {
            if (!props.routes || !props.routes.folder) {
                console.error("Routes or edit URL is not defined");
                return "#";
            }
            return props.routes.folder.replace(":id", id);
        };

        const confirmDelete = (item) => {
            itemToDelete.value = item;
            deleteDialog.value = true;
        };

        const closeDeleteDialog = () => {
            deleteDialog.value = false;
            itemToDelete.value = null;
        };

        // Confirm deletion for selected items
        const confirmDeleteSelected = () => {
            itemToDelete.value = null; // Reset single item
            deleteDialog.value = true;
        };

        // Delete item(s) based on selected checkboxes or single item
        const deleteItemConfirmed = async () => {
            try {
                if (itemToDelete.value) {
                    // Single item deletion
                    const deleteUrl = props.routes.destroy.replace(
                        ":id",
                        itemToDelete.value.id
                    );
                    const response = await axios.delete(deleteUrl);
                    toast.success(response.data.message, {
                        onClose: () => {
                            window.location.reload(); // This will reload the page when the toast closes
                        },
                    });
                    items.value = response.data.datas; // Update local items
                } else if (selectedItems.value.length > 0) {
                    // Delete selected items
                    const deleteUrl = props.routes.bulkDestroy; // Assuming bulk delete API
                    const response = await axios.post(deleteUrl, {
                        ids: selectedItems.value,
                    });
                    toast.success(response.data.message, {
                        onClose: () => {
                            window.location.reload(); // This will reload the page when the toast closes
                        },
                    });
                    items.value = response.data.datas; // Update local items
                    selectedItems.value = []; // Clear selection
                }

                tableKey.value += 1; // Refresh the table

                closeDeleteDialog();
            } catch (error) {
                console.error("Error:", error);
                toast.error("Delete failed");
            }
        };

        return {
            searchQuery,
            currentPage,
            itemsPerPage,
            filteredData,
            deleteDialog,
            confirmDelete,
            selectedItems,
            closeDeleteDialog,
            confirmDeleteSelected,
            deleteItemConfirmed,
            getEditUrl,
            tableKey,
            getfolderUrl,
        };
    },
});
</script>
<style>
.table .v-input__details {
    display: none;
}
</style>
