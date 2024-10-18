<template>
    <Head title="Edit Category" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Edit Category
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <v-container>
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-form @submit.prevent="EditPage">
                                <div
                                    v-for="(field, key) in Form.fields"
                                    :key="key"
                                >
                                    <v-text-field
                                        v-model="field.value"
                                        :label="field.name"
                                        :placeholder="field.des"
                                        :type="field.type"
                                        :rules="getValidationRules(field)"
                                        :disabled="field.disable"
                                        v-if="field.includes === 'text'"
                                        md="6"
                                    ></v-text-field>

                                    <div
                                        v-if="field.includes === 'slug'"
                                        style="display: flex"
                                    >
                                        <v-text-field
                                            v-model="field.value"
                                            :label="field.name"
                                            :placeholder="field.des"
                                            :type="field.type"
                                            :rules="getValidationRules(field)"
                                            :disabled="!isSlugEditable"
                                            md="6"
                                        ></v-text-field>
                                        <v-btn
                                            @click="toggleSlugEdit"
                                            small
                                            class="text-black"
                                        >
                                            {{
                                                isSlugEditable
                                                    ? "Lock "
                                                    : "Edit"
                                            }}
                                        </v-btn>
                                    </div>

                                    <v-checkbox
                                        v-if="field.type === 'checkbox'"
                                        v-model="field.value"
                                        :label="field.name"
                                        :true-value="1"
                                        :false-value="0"
                                        :rules="getValidationRules(field)"
                                        :disabled="field.disable"
                                    ></v-checkbox>

                                    <div v-if="field.includes === 'textarea'">
                                        <ckeditor
                                            v-model="field.value"
                                            :editor="editor"
                                            :config="editorConfig"
                                        />
                                    </div>

                                    <v-select
                                        v-model="field.value"
                                        :label="field.name"
                                        :items="field.items"
                                        v-if="field.type === 'select'"
                                        :rules="getValidationRules(field)"
                                        :disabled="field.disable"
                                        md="6"
                                    ></v-select>
                                </div>

                                <v-btn type="submit" color="primary">
                                    {{ Title }}
                                </v-btn>
                            </v-form>
                        </v-col>

                        <v-col
                            cols="12"
                            md="6"
                            class="border border-secondary p-3 mb-2"
                        >
                            <div style="max-height: 70vh; overflow: auto">
                                <div class="d-flex align-items-center">
                                    <h3>Category List</h3>
                                    <a
                                        href="#"
                                        @click="createCategory()"
                                        class="text-primary"
                                        >New category</a
                                    >
                                </div>
                                <category-children
                                    :children="categoryData"
                                    @editCategory="editCategory"
                                    @deleteCategory="deleteCategory"
                                ></category-children>
                            </div>
                        </v-col>
                    </v-row>

                    <v-dialog v-model="isActive" max-width="500">
                        <v-card title="Delete Category">
                            <v-card-actions>
                                <v-spacer></v-spacer>

                                <v-btn
                                    text="Yes"
                                    class="btn btn-primary bg-primary"
                                    @click="
                                        deleteItemConfirmed();
                                        isActive = false;
                                    "
                                ></v-btn>

                                <v-btn
                                    class="btn btn-primary bg-dark"
                                    text="No"
                                    @click="isActive = false"
                                ></v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-container>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch, reactive } from "vue";
import { useForm } from "@inertiajs/vue3";
import axios from "axios";
import { toast } from "vue3-toastify";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import CategoryChildren from "@/Components/CategoryChildren.vue";

import { Head } from "@inertiajs/vue3";
import { VueDraggable } from "vue-draggable-plus";
import { Ckeditor } from "@ckeditor/ckeditor5-vue";

import "ckeditor5/ckeditor5.css";

import { editor, editorConfig } from "@/Components/ckeditorConfig.js";

const props = defineProps({
    data: {
        type: Object, // Update to Object if `page` is an object
        required: true,
    },
});

const isActive = ref(false);

const categoryData = ref([]);
const Datas = ref([]);
const unfilltername = ref([]);

// Hàm chuyển đổi từ danh sách phẳng sang cấu trúc cây
function buildCategoryTree(categories) {
    let map = {};
    let tree = [];
    let unfillter = [];

    // Tạo một map từ id của các category
    categories.forEach((category) => {
        map[category.id] = category;
        // Khởi tạo thuộc tính children nếu chưa có
        if (!category.children) {
            category.children = [];
        }
    });

    // Lặp qua các category để sắp xếp các danh mục cha và con
    categories.forEach((category) => {
        if (category.parent_id !== null) {
            // Nếu category có parent_id, đẩy nó vào children của parent
            map[category.parent_id].children.push(category);
        } else {
            // Nếu không có parent_id (là root), đẩy vào tree
            tree.push(category);
        }
    });

    return tree;
}

const Form = reactive({
    fields: {
        name: {
            name: "Name",
            key: "name",
            value: null,
            des: "Name of the slider",
            type: "text",
            includes: "text",
            disable: false,
            required: true,
        },

        description: {
            name: "Description",
            key: "description",
            value: null,
            des: "Meta description",
            type: "textarea",
            includes: "text",
            disable: false,
            required: true,
        },
        parent_id: {
            name: "Parent",
            key: "parent_id",
            value: "None",
            des: "Page status (e.g., published, draft)",
            type: "select",
            items: computed(() => {
                // Filter out the names that are inside unfilltername.value
                return [
                    "None",
                    ...Datas.value
                        .filter(data => !unfilltername.value.includes(data.name)) // Exclude gathered names from unfilltername
                        .map((data) => data.name),
                ];
            }),
            disable: false,
            required: false,
        },
        is_featured: {
            name: "Featured",
            key: "is_featured",
            value: 0,
            des: "Featured",
            type: "checkbox",
            includes: "checkbox",
            disable: false,
            required: false,
        },
    },
});

const Title = ref("SAVE");

const id = ref(null);

function FormValidation(Formlist) {
    const fields = Formlist;
    const payload = Object.entries(fields).reduce((acc, [key, field]) => {
        acc[key] =
            field.includes === "image"
                ? document.getElementById(field.key)?.value || field.value
                : field.value;

        if (field.key === "parent_id") {
            const data = props.data.find(
                (data) => data.name === Form.fields.parent_id.value
            );
            if (data) {
                acc[key] = data.id;
            } else {
                acc[key] = null;
            }
        }
        return acc;
    }, {});

    for (const [key, field] of Object.entries(fields)) {
        for (const rule of getValidationRules(field)) {
            const error = rule(payload[key]);
            if (error !== true) {
                toast.error(`Validation failed for field ${key}: ${error}`);
                return;
            }
        }
    }

    return payload;
}

function getValidationRules(field) {
    const rules = [];

    if (field.required === true) {
        rules.push((v) => !!v || "Field is required");
    } else {
        return rules;
    }

    // Check if the field is of type 'email'
    if (field.type === "email") {
        rules.push((v) => !!v || "Email is required");
        rules.push((v) => /.+@.+\..+/.test(v) || "Email must be valid");
    }
    // Check if the field is of type 'text' and not 'image'
    else if (field.type === "text" && field.includes !== "image") {
        rules.push((v) => !!v || "Field is required");
    }

    // Additional rule for 'slug' fields
    if (field.includes === "slug") {
        rules.push((v) => {
            const noDiacritics = diacritics.remove(v); // Remove diacritics
            return noDiacritics === v || "Slug should not contain diacritics";
        });
    }

    return rules;
}

const isSlugEditable = ref(false);

const toggleSlugEdit = () => {
    isSlugEditable.value = !isSlugEditable.value;
};

function findCategoryById(categories, id) {
    for (const category of categories) {
        if (category.id === id) {
            // Add parent name
            unfilltername.value.push(category.name);

            // Recursively gather all child names
            if (category.children && category.children.length > 0) {
                gatherChildNames(category.children);
            }
            return; // Stop the search once the category is found
        }
    }
}

// Helper function to gather child names recursively
function gatherChildNames(children) {
    for (const child of children) {
        // Add child name
        unfilltername.value.push(child.name);

        // If the child has children, recursively gather their names too
        if (child.children && child.children.length > 0) {
            gatherChildNames(child.children);
        }
    }
}

// Usage inside editCategory function
function editCategory(child) {
    Title.value = "UPDATE";
    id.value = child.id;
    unfilltername.value = [];

    // Map the child data to the form fields
    Object.keys(Form.fields).forEach((key) => {
        if (Form.fields[key]) {
            if (key == "parent_id") {
                const parent = Datas.value.find(
                    (data) => data.id === child[key]
                );
                Form.fields[key].value = parent ? parent.name : "None";
            } else {
                Form.fields[key].value = child[key];
            }
        }
    });

    // Find the category by id and gather the names
    findCategoryById(Datas.value, id.value);

    console.log("Category ID:", id.value);
    console.log("Gathered Names:", unfilltername.value);
}

function deleteCategory(child) {
    id.value = child.id;
    isActive.value = true;
    console.log(id.value);
}

function createCategory() {
    Title.value = "Save";

    // Map the child data to the form fields
    ClearnForm();
}

function ClearnForm() {
    // Map the child data to the form fields
    Object.keys(Form.fields).forEach((key) => {
        if (Form.fields[key]) {
            if (key == "is_featured") {
                Form.fields[key].value = 0;
            } else {
                Form.fields[key].value = null;
            }
        }
    });
}

const EditPage = async () => {
    const payload = FormValidation(Form.fields);

    const filteredPayload = payload;

    console.log(filteredPayload);

    if (Title.value == "SAVE") {
        try {
            const response = await axios.post(
                route("category_items_manager.store"),
                filteredPayload
            );

            toast.success(response.data.message);
            categoryData.value = buildCategoryTree(response.data.data);
            Datas.value = response.data.data;
            ClearnForm();
        } catch (error) {
            if (error.response) {
                toast.error(error.response.data.message);
            }
        }
    } else {
        try {
            console.log(id, filteredPayload);

            const response = await axios.put(
                route("category_items_manager.update", {
                    category: id.value,
                }),
                filteredPayload
            );
            toast.success(response.data.message);
            categoryData.value = buildCategoryTree(response.data.data);
            Datas.value = response.data.data;
            ClearnForm();
        } catch (error) {
            if (error.response) {
                toast.error(error.response.data.message);
            }
        }
    }
};

const deleteItemConfirmed = async () => {
    try {
        const response = await axios.delete(
            route("category_items_manager.destroy", {
                category: id.value,
            })
        );

        toast.success(response.data.message);
        categoryData.value = buildCategoryTree(response.data.data);
        Datas.value = response.data.data;
        ClearnForm();
    } catch (error) {
        if (error.response) {
            toast.error(error.response.data.message);
        }
    }
};

// Watch for changes in the name field to automatically update the slug
/* watch(
            () => Form.fields.position.value,
            (newValue, oldValue) => {
                if (oldValue !== newValue && oldValue !== null) {
                    Form.fields.relationship.value = null;
                }
                if (newValue === "Page") {
                    // Giả sử bạn có props.Page là một mảng các object trang
                    Form.fields.relationship.items = props.Page.map(
                        (page) => page.name
                    );
                } else {
                    // Giả sử bạn có props.Page là một mảng các object trang
                    Form.fields.relationship.items = props.Post.map(
                        (Post) => Post.name
                    );
                }
            }
        ); */

// onMounted lifecycle hook
onMounted(() => {
    const data = props.data;

    if (data) {
        Object.keys(Form.fields).forEach((key) => {
            if (data[key] !== undefined) {
                Form.fields[key].value = data[key];
            }
        });
    }

    Datas.value = props.data;

    categoryData.value = buildCategoryTree(props.data);

    console.log(categoryData);
});
</script>

<style>
input:where([type="button"]) {
    color: rgb(51, 112, 255);
}

.v-btn--variant-elevated,
.v-btn--variant-flat {
    color: rgb(255 255 255 / 87%);
}

.position {
    position: relative;
}
.position-current,
.position-new {
    position: absolute;
    top: 0;
    left: 0;
}

.position-new {
    z-index: 2;
}
.fade-move,
.fade-enter-active,
.fade-leave-active {
    transition: all 0.5s cubic-bezier(0.55, 0, 0.1, 1);
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: scaleY(0.01) translate(30px, 0);
}

.fade-leave-active {
    position: absolute;
}
.sort-target {
    padding: 0 1rem;
}

ul {
    list-style: none;
    padding-left: 20px;
}

li {
    margin-bottom: 10px;
}

.d-flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>
