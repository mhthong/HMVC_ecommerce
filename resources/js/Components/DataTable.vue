<template>
    <v-container>
        <v-card>
            <v-card-title>
                <v-text-field
                    v-model="searchQuery"
                    label="Search"
                    md="4"
                    max-width="400px"
                ></v-text-field>
            </v-card-title>

            <v-data-table
                :key="tableKey"
                :headers="headers"
                :items="filteredData"
                :items-per-page="itemsPerPage"
                :search="searchQuery"
                class="elevation-1"
            >
                <template v-slot:item.created_at="{ item }">
                    <a :href="getEditUrl(item.id)">
                        <v-icon color="blue">mdi-pencil</v-icon>
                    </a>

                    <v-icon @click="confirmDelete(item)" color="red">mdi-delete</v-icon>
                </template>
            </v-data-table>
        </v-card>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="deleteDialog" max-width="500">
            <v-card>
                <v-card-title class="headline">Confirm Deletion</v-card-title>
                <v-card-text>Are you sure you want to delete this item?</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="closeDeleteDialog">Cancel</v-btn>
                    <v-btn color="red darken-1" text @click="deleteItemConfirmed">Delete</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
import { defineComponent, ref, computed } from 'vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';

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
        const searchQuery = ref('');
        const currentPage = ref(1);
        const itemsPerPage = ref(10);
        const tableKey = ref(0); // To force the table to re-render

        const headers = props.headers;

        // Create a reactive version of items
        const items = ref(props.items);

        const filteredData = computed(() => {
            if (!searchQuery.value) {
                return items.value;
            }
            return items.value.filter(item =>
                Object.values(item).some(value =>
                    String(value)
                        .toLowerCase()
                        .includes(searchQuery.value.toLowerCase())
                )
            );
        });

        const getEditUrl = (id) => {
            if (!props.routes || !props.routes.edit) {
                console.error('Routes or edit URL is not defined');
                return '#';
            }
            return props.routes.edit.replace(':id', id);
        };

        const deleteDialog = ref(false);
        const itemToDelete = ref(null);

        const confirmDelete = (item) => {
            itemToDelete.value = item;
            deleteDialog.value = true;
        };

        const closeDeleteDialog = () => {
            deleteDialog.value = false;
            itemToDelete.value = null;
        };

        const deleteItemConfirmed = async () => {
            if (!itemToDelete.value) return;

            const deleteUrl = props.routes.destroy.replace(':id', itemToDelete.value.id);

            try {
                const response = await axios.delete(deleteUrl);
                console.log('Delete response:', response);

                toast.success(response.data.message);

                // Update the items array with the new data from the server
                items.value = response.data.datas;

                // Increment the key to force table update
                tableKey.value += 1;

                closeDeleteDialog();
            } catch (error) {
                console.error(
                    'Error details:',
                    error.response ? error.response.data : error.message
                );
                toast.error(error.response.data.message);
            }
        };

        return {
            searchQuery,
            currentPage,
            itemsPerPage,
            headers,
            filteredData,
            deleteDialog,
            confirmDelete,
            closeDeleteDialog,
            deleteItemConfirmed,
            getEditUrl,
            tableKey, // Add tableKey to return
        };
    },
});
</script>

<style>
/* Your styles here */
</style>
