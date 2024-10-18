<template>
    <Head title="Block Manager" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Block Manager
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div>
                    <v-container>
                        <v-btn class="bg-primary">
                        <Link :href="route('block_manager.create')">
                            Add New
                        </Link>
                    </v-btn>
                    </v-container>
                    <DataTable
                        :headers="headers"
                        :items="datas"
                        :routes="routes"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import DataTable from "@/Components/DataTable.vue";
import { Link } from "@inertiajs/vue3";
export default {
    components: {
        AuthenticatedLayout,
        DataTable,
        Head,
        Link,
    },
    props: {
        datas: Array, // Ensure you declare pages as a prop to receive it from the controller
    },
    data() {
        return {
            headers: [
                { title: "ID", key: "id" },
                { title: "Name", key: "name" },
                { title: "Alias", key: "alias" },
                { title: "Status", key: "status" },
                { title: "Description", key: "description" },
                { title: "Action", key: "created_at", sortable: false }, // Thêm cột Action
            ], // Initialize with an empty array or appropriate default value

            routes: {
                    edit: '/dashboard/block-manager/update/:id',
                    destroy: '/dashboard/block-manager/delete/:id'
                },
        };
    },
    methods: {
        // Add methods as needed
    },
    mounted() {
        // Fetch data here if needed, e.g. from an API
        console.log(this.datas);
    },
};
</script>

<style scoped>
/* Add your styles here */
</style>
