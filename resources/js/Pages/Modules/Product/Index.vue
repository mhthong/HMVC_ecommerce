<template>
    <Head title="Product Manager" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Product Manager
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div>
                    <!-- Bảng dữ liệu -->
                    <v-container>
                        <v-row>
                            <v-col cols="2" md="2">
                                <v-select
                                    v-model="selectedStatus"
                                    :items="statusOptions"
                                    label="Status"
                                    clearable
                                />
                            </v-col>
                            <v-col cols="2" md="2">
                                <v-select
                                    v-model="selectedDiscount"
                                    :items="discountOptions"
                                    label="Discount"
                                    clearable
                                />
                            </v-col>
                            <v-col cols="2" md="2">
                                <v-select
                                    v-model="selectedCategory"
                                    :items="categoryOptions"
                                    label="Category"
                                    clearable
                                />
                            </v-col>
                            <v-col cols="2" md="2">
                                <v-select
                                    v-model="isFeaturedFilter"
                                    :items="FeaturedFilterOptions"
                                    label="Featured"
                                    clearable
                                />
                            </v-col>
                            <v-col cols="2" md="2">
                                <v-btn class="bg-primary">
                                    <Link :href="route('product_manager.create')">
                                        Add New
                                    </Link>
                                </v-btn>
                            </v-col>
                        </v-row>
                        <DataTable
                            :headers="headers"
                            :items="filteredData"
                            :routes="routes"
                            @edit="confirmEdit"
                        />
                    </v-container>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { defineComponent, ref, computed, watch, defineEmits } from "vue"; // Import defineEmits
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import { Link } from "@inertiajs/vue3";

// Props
const props = defineProps({
    datas: Array, // Dữ liệu sản phẩm
    Discount: Object, // Dữ liệu giảm giá
    Category: Object, // Dữ liệu danh mục
});

const data = ref([...props.datas]);

// Trạng thái bộ lọc
const selectedStatus = ref(null);
const selectedDiscount = ref(null);
const selectedCategory = ref(null);
const isFeaturedFilter = ref(false);

// Tùy chọn cho bộ lọc
const statusOptions = ["Published", "Pending"]; // Các trạng thái cho sản phẩm

// Lấy danh sách các tùy chọn discount từ Discount props
const discountOptions = computed(() => {
    return [
        ...new Set(
            Object.values(props.Discount)
                .map((item) => item.code)
                .filter(Boolean)
        ),
    ];
});

const FeaturedFilterOptions = ref(["Featured", "None Featured"]);

// Function to handle updated data from child component
function updateDatasFromChild(newDatas) {
    console.log(newDatas);
    
    data.value = newDatas;
}

// Lấy danh sách các tùy chọn category từ Category props
const categoryOptions = computed(() => {
    return [
        ...new Set(
            Object.values(props.Category)
                .map((category) => category.name)
                .filter(Boolean)
        ),
    ];
});


// Lọc dữ liệu dựa trên các tiêu chí lọc
const filteredData = computed(() => {
console.log(data);

    return data.value.filter((item) => {
        // Lọc theo status
        const matchesStatus = selectedStatus.value
            ? item.status === selectedStatus.value
            : true;

        // Lọc theo discount
        const matchesDiscount = selectedDiscount.value
            ? item.discount?.code === selectedDiscount.value
            : true;

        // Lọc theo categories
        const matchesCategory = selectedCategory.value
            ? item.categories.some((cat) => cat.name === selectedCategory.value)
            : true;

        // Lọc theo is_featured

        const matchesFeatured =
            isFeaturedFilter.value == "Featured"
                ? item.is_featured === 1
                : isFeaturedFilter.value == "None Featured"
                ? item.is_featured === 0
                : true;

        // Trả về sản phẩm nếu tất cả các tiêu chí đều phù hợp
        return (
            matchesStatus &&
            matchesDiscount &&
            matchesCategory &&
            matchesFeatured
        );
    });
});

// Headers cho DataTable
const headers = [
    { title: "ID", key: "id" },
    { title: "Name", key: "name" },
    { title: "Price", key: "price" },
    { title: "Status", key: "status" },
    { title: "Featured", key: "is_featured" },
    {
        title: "Categories",
        key: "categories",
        formatter: (item) => item.categories.map((cat) => cat.name).join(", "),
    },
    {
        title: "Discount",
        key: "discount.code",
        formatter: (item) => (item.discount ? item.discount.code : "None"),
    },
    { title: "Action", key: "created_at", sortable: false }, // Thêm cột Action
];

// Routes cho các hành động của DataTable (edit, delete)
const routes = {
    edit: "/dashboard/product-manager/update/:id",
    destroy: "/dashboard/product-manager/delete/:id",
    bulkDestroy: "/dashboard/product-manager/bulk-delete",
};

// Các phương thức cần thiết
const confirmEdit = (item) => {
    console.log("Editing item:", item);
};
</script>

<style scoped>
/* Thêm CSS cho thành phần nếu cần */
</style>
