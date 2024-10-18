<template>
    <Head title="Warranty Manager" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Warranty Manager
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div>
                    <!-- Add New Discount Button -->
                    <v-container>
                        <v-btn class="bg-primary" @click="openDiscountForm">
                            Add New
                        </v-btn>
                    </v-container>

                    <!-- Discount Form Dialog -->
                    <v-container>
                        <v-dialog v-model="isActive" max-width="500" persistent>
                            <v-card :title="Title">
                                <v-form @submit.prevent="saveDiscount">
                                    <v-card-text>
                                        <div
                                            v-for="(
                                                field, key
                                            ) in Formlist.fields"
                                            :key="key"
                                            class="p-4"
                                        >
                                            <!-- Text Field -->
                                            <v-text-field
                                                v-model="field.value"
                                                :label="field.name"
                                                :placeholder="field.des"
                                                :type="field.type"
                                                :rules="
                                                    getValidationRules(field)
                                                "
                                                :disabled="field.disable"
                                                v-if="field.includes === 'text'"
                                            ></v-text-field>

                                            <!-- Select Field -->
                                            <v-select
                                                v-model="field.value"
                                                :label="field.name"
                                                :items="field.items"
                                                v-if="field.type === 'select'"
                                                :rules="
                                                    getValidationRules(field)
                                                "
                                                :disabled="field.disable"
                                            ></v-select>
                                        </div>
                                    </v-card-text>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn type="submit" color="primary"
                                            >Save</v-btn
                                        >
                                        <v-btn @click="closeDialog"
                                            >Close</v-btn
                                        >
                                    </v-card-actions>
                                </v-form>
                            </v-card>
                        </v-dialog>
                    </v-container>

                    <!-- Data Table -->
                    <DataTable
                        :headers="headers"
                        :items="localDatas"
                        :routes="routes"
                        @edit="confirmEdit"
                        :key="tableKey"

                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import axios from "axios";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import { toast } from "vue3-toastify";
import { nextTick } from 'vue';

// Props
const props = defineProps({
    datas: Array,
});

// State
const isActive = ref(false);
const Title = ref("Create Discount");
const selectedItem = ref(null);
const localDatas = ref([...props.datas]); // Bản sao của datas từ props
const tableKey = ref(0);

// Form Configuration
const Formlist = useForm({
    fields: {
        product_id: {
            name: "Product",
            key: "product",
            value: "",
            des: "Discount unit (money, %)",
            type: "select",
            items:  [...props.datas.map((item) => (item.name))],
            disable: false,
            required: true,
        },
        quantity: {
            name: "Quantity",
            key: "quantity",
            value: "",
            des: "Quantity value",
            type: "number",
            includes: "text",
            disable: false,
            required: true,
        },
 
    },
});

// DataTable Headers
const headers = [
    { title: "ID", key: "id" },
    { title: "Name", key: "name" },
    { title: "Action", key: "created_at", sortable: false },
];

// Routes for DataTable actions
const routes = {
    folder:"/dashboard/waranty-manager/:id",
};

// Open the discount form modal
const openDiscountForm = () => {
    Title.value = "Create Discount";
    clearFormFields();
    isActive.value = true;
};

// Close the dialog
const closeDialog = () => {
    isActive.value = false;
};

// Clear form fields
const clearFormFields = () => {
    Object.keys(Formlist.fields).forEach((key) => {
        Formlist.fields[key].value = "";
    });
};

// Populate the form fields for editing
const confirmEdit = (item) => {
    Title.value = "Edit Discount";
    selectedItem.value = item;
    populateFormFields(item);
    isActive.value = true;
};

// Populate form fields with the selected item data
const populateFormFields = (item) => {
    Object.keys(Formlist.fields).forEach((key) => {
        if (item[key] !== undefined) {
            Formlist.fields[key].value = item[key];
        }
    });
};

function FormValidation(Formlist) {
    const fields = Formlist;
    const payload = Object.entries(fields).reduce((acc, [key, field]) => {
        acc[key] =
            field.includes === "image"
                ? document.getElementById(field.key)?.value || field.value
                : field.value;

                console.log(field.key,field.key === "product" ,field);
                
                if (field.key === "product" ) {
                const data = props.datas.find(
                    (data) => data.name === field.value
                );
                if (data) {
                    acc[key] = data.id;
                } else {
                    acc[key] = null;
                }
            }

        return acc;
    }, {});

    // Validation for all fields
    for (const [key, field] of Object.entries(fields)) {
        for (const rule of getValidationRules(field)) {
            const error = rule(payload[key]);
            if (error !== true) {
                toast.error(`Validation failed for field ${key}: ${error}`);
                return;
            }
        }

        // Additional validation: Check if unit is "%" and value <= 100
        if (
            key === "unit" &&
            payload[key] === "%" &&
            fields.value.value > 100
        ) {
            toast.error("Validation failed: Value cannot exceed 100%.");
            return;
        }
    }

    return payload;
}

// Save the discount
const saveDiscount = async () => {
    const payload = FormValidation(Formlist.fields);

    if (!payload) {
        return;
    }

    console.log(payload);
    

    if (Title.value === "Create Discount") {
        try {
            const response = await axios.post(
                route("waranty_manager.store"),
                payload
            );
            if (response) {

                toast.success(response.data.message);
        }
        } catch (error) {
            console.error(error);
            toast.error("Error while creating.");
        }
    } else {
        try {
            const response = await axios.put(
                route("discounts_manager.update", {
                    discount: selectedItem.value.id,
                }),
                payload
            );
            if (response) {

                localDatas.value = [...response.data.datas]; // Cập nhật dữ liệu
            tableKey.value += 1; // Tăng giá trị khóa để buộc bảng làm mới
            toast.success(response.data.message);
            }
        } catch (error) {
            console.error(error);
            toast.error("Error while updating discount.");
        }
    }

    isActive.value = false;
};

// Validation rules
const getValidationRules = (field) => {
    const rules = [];
    if (field.required) {
        rules.push((v) => !!v || "This field is required.");
    }
    return rules;
};
</script>

<style scoped>
.bg-primary {
    background-color: #1976d2;
    color: white;
}
</style>
