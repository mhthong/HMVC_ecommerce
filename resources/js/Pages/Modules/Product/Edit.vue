<template>
    <Head title="Edit Product" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Edit Product
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <v-form @submit.prevent="EditPost">
                    <v-container>
                        <v-row>
                            <v-col cols="12" md="9">
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
                                        v-if="
                                            field.includes === 'text' &&
                                            (field.type === 'text' ||
                                                field.type === 'number')
                                        "
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

                                    <div v-if="field.includes === 'textarea'">
                                        <ckeditor
                                            v-model="field.value"
                                            :editor="editor"
                                            :config="editorConfig"
                                        />
                                    </div>

                                    <v-textarea
                                        v-model="field.value"
                                        :label="field.name"
                                        :placeholder="field.des"
                                        :type="field.type"
                                        :rules="getValidationRules(field)"
                                        :disabled="field.disable"
                                        v-if="
                                            field.includes === 'text' &&
                                            field.type === 'textarea'
                                        "
                                        md="6"
                                    ></v-textarea>

                                    <v-combobox
                                        v-model="field.value"
                                        :label="field.name"
                                        multiple
                                        chips
                                        clearable
                                        :rules="getValidationRules(field)"
                                        :disabled="field.disable"
                                        v-if="field.includes === 'multiltext'"
                                        placeholder="Enter multiple values, press enter to add."
                                    ></v-combobox>

                                    <div
                                        v-if="
                                            field.includes === 'image' &&
                                            field.name !== 'Foreign Image'
                                        "
                                        class="border border-secondary p-3 mb-2"
                                    >
                                        <label :for="field.key">{{
                                            field.name
                                        }}</label>
                                        <v-row>
                                            <v-col cols="12">
                                                <div
                                                    class="p-2 col-img mb-2"
                                                    :id="'holder' + field.key"
                                                >
                                                    <img
                                                        :src="field.value"
                                                        alt=""
                                                        style="height: 80px"
                                                    />
                                                </div>
                                            </v-col>
                                        </v-row>
                                        <input
                                            :id="field.key"
                                            class="form-control hidden"
                                            type="text"
                                            name="filepath"
                                        />
                                        <p class="primary">{{ field.des }}</p>
                                        <input
                                            type="button"
                                            :data-input="field.key"
                                            :id="'thumbnail' + field.key"
                                            :data-preview="'holder' + field.key"
                                            value="Upload"
                                            class="btn btn-primary px-4 py-2 text-white"
                                            style="
                                                border-radius: 10px;
                                                background: var(
                                                    --color-prettylights-syntax-constant
                                                );
                                            "
                                            @click="
                                                () =>
                                                    openFileManager(
                                                        'thumbnail' + field.key
                                                    )
                                            "
                                        />
                                    </div>
                                </div>
                            </v-col>

                            <v-col cols="12" md="3">
                                <v-select
                                    v-model="Form.fields.status.value"
                                    :label="Form.fields.status.name"
                                    :items="Form.fields.status.items"
                                    v-if="Form.fields.status.type === 'select'"
                                    :rules="
                                        getValidationRules(Form.fields.status)
                                    "
                                    :disabled="Form.fields.status.disable"
                                    md="6"
                                ></v-select>

                                <v-select
                                    v-model="Form.fields.discount_id.value"
                                    :label="Form.fields.discount_id.name"
                                    :items="Form.fields.discount_id.items"
                                    v-if="
                                        Form.fields.discount_id.type ===
                                        'select'
                                    "
                                    :rules="
                                        getValidationRules(
                                            Form.fields.discount_id
                                        )
                                    "
                                    :disabled="Form.fields.discount_id.disable"
                                    md="6"
                                ></v-select>

                                <v-checkbox
                                    v-model="Form.fields.is_featured.value"
                                    :label="Form.fields.is_featured.name"
                                    :true-value="1"
                                    :false-value="0"
                                    :rules="
                                        getValidationRules(
                                            Form.fields.is_featured
                                        )
                                    "
                                    :disabled="Form.fields.is_featured.disable"
                                ></v-checkbox>

                                <div
                                    v-if="
                                        Form.fields.ForeignImage.includes ===
                                        'image'
                                    "
                                    class="border border-secondary p-3 mb-2"
                                >
                                    <label
                                        :for="Form.fields.ForeignImage.key"
                                        >{{
                                            Form.fields.ForeignImage.name
                                        }}</label
                                    >
                                    <v-row>
                                        <v-col cols="12">
                                            <div
                                                class="p-2 col-img mb-2"
                                                :id="
                                                    'holder' +
                                                    Form.fields.ForeignImage.key
                                                "
                                            >
                                                <img
                                                    v-for="(
                                                        image, index
                                                    ) in Form.fields.ForeignImage.value?.split(
                                                        ','
                                                    )"
                                                    :key="index"
                                                    :src="image.trim()"
                                                    alt=""
                                                    style="height: 80px"
                                                />
                                            </div>
                                        </v-col>
                                    </v-row>
                                    <input
                                        :id="Form.fields.ForeignImage.key"
                                        class="form-control hidden"
                                        type="text"
                                        name="filepath"
                                    />
                                    <p class="primary">
                                        {{ Form.fields.ForeignImage.des }}
                                    </p>
                                    <input
                                        type="button"
                                        :data-input="
                                            Form.fields.ForeignImage.key
                                        "
                                        :id="
                                            'thumbnail' +
                                            Form.fields.ForeignImage.key
                                        "
                                        :data-preview="
                                            'holder' +
                                            Form.fields.ForeignImage.key
                                        "
                                        value="Upload"
                                        class="btn btn-primary px-4 py-2 text-white"
                                        style="
                                            border-radius: 10px;
                                            background: var(
                                                --color-prettylights-syntax-constant
                                            );
                                        "
                                        @click="
                                            () =>
                                                openFileManager(
                                                    'thumbnail' +
                                                        Form.fields.ForeignImage
                                                            .key
                                                )
                                        "
                                    />
                                </div>

                                <div
                                    class="border border-secondary p-3 mb-2 category"
                                    v-if="Form.fields.Category_id"
                                >
                                    <category-children
                                        :children="categoryData"
                                        :Check="data.categories"
                                        @checkBook="handleCheckBook"
                                    ></category-children>
                                </div>
                                <v-btn type="submit" color="primary"
                                    >Save</v-btn
                                >
                            </v-col>
                        </v-row>
                    </v-container>
                </v-form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import axios from "axios";
import { toast } from "vue3-toastify";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import diacritics from "diacritics";
import CategoryChildren from "@/Components/CategoryChildrenCheckBox.vue";
import { Ckeditor } from "@ckeditor/ckeditor5-vue";

import "ckeditor5/ckeditor5.css";

import { editor, editorConfig } from "@/Components/ckeditorConfig.js";

const props = defineProps({
    data: {
        type: Object, // Update to Object if `post` is an object
        required: true,
    },

    Discount: {
        type: Object, // Update to Object if `page` is an object
        required: true,
    },
    Category: {
        type: Object, // Update to Object if `page` is an object
        required: true,
    },
});

const categoryData = ref([]);

const Form = useForm({
    fields: {
        name: {
            name: "Name",
            key: "name",
            value: null,
            des: "Name of the Product",
            type: "text",
            includes: "text",
            disable: false,
            required: true,
        },
        slug: {
            name: "Slug",
            key: "slug",
            value: null,
            des: null,
            type: "text",
            includes: "slug",
            disable: false,
            required: true,
        },
        price: {
            name: "Price",
            key: "price",
            value: null,
            des: "Price",
            type: "number",
            includes: "text",
            disable: false,
            required: true,
        },
        priceoff: {
            name: "Off Sale",
            key: "priceoff",
            value: null,
            des: "Off Sale",
            type: "number",
            includes: "text",
            disable: false,
            required: true,
        },
        status: {
            name: "Status",
            key: "status",
            value: null,
            des: "Post status (e.g., published, draft)",
            type: "select",
            items: ["Published", "Draft"],
            disable: false,
            required: true,
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

        shortdescription: {
            name: "Short description",
            key: "shortdescription",
            value: null,
            des: "Short description",
            type: "textarea",
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

        discount_id: {
            name: "Discount",
            key: "Discount",
            value: null,
            des: "Discount",
            type: "select",
            items: ["None", ...props.Discount.map((Discount) => Discount.code)],
            disable: false,
            required: false,
        },

        image: {
            name: "Image",
            key: "image",
            value: null,
            des: "Image URL or path",
            type: "text",
            includes: "image",
            disable: false,
            required: true,
        },

        ForeignImage: {
            name: "Foreign Image",
            key: "Foreignimage",
            value: null,
            des: "Image URL or path",
            type: "text",
            includes: "image",
            disable: false,
            required: false,
        },

        Category_id: {
            name: "Category",
            key: "Category",
            value: [],
            des: "Category",
            type: "checkbox",
            disable: false,
            required: false,
        },

        content: {
            name: "Content",
            key: "content",
            value: "Content of the Post",
            des: "Content of the Post",
            type: "textarea",
            includes: "textarea",
            disable: false,
            required: true,
        },
    },
});

function openFileManager(imageInput) {
    const element = $(`#${imageInput}`);
    if (element.length === 0) {
        console.error(`Element with ID ${imageInput} does not exist`);
        return;
    }

    try {
        element.filemanager("image", {
            prefix: "/laravel-filemanager",
        });
    } catch (error) {
        console.error("Error initializing file manager:", error);
    }
}

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

// Hàm xử lý khi checkbox được chọn hoặc bỏ chọn
function handleCheckBook(child) {
    console.log("Selected category:", child);

    if (child.checked) {
        // Nếu checkbox được chọn (true), push child.id vào Category_id.value
        if (!Form.fields.Category_id.value.includes(child.id)) {
            Form.fields.Category_id.value.push(child.id);
        }
    } else {
        // Nếu checkbox bị bỏ chọn (false), remove child.id khỏi Form.fields.Category_id.value
        const index = Form.fields.Category_id.value.indexOf(child.id);
        if (index > -1) {
            Form.fields.Category_id.value.splice(index, 1);
        }
    }

    console.log(
        "Updated Form.fields.Category_id:",
        Form.fields.Category_id.value
    ); // Kiểm tra kết quả
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

function FormValidation(Formlist) {
    const fields = Formlist;
    const payload = Object.entries(fields).reduce((acc, [key, field]) => {
        acc[key] =
            field.includes === "image"
                ? document.getElementById(field.key)?.value || field.value
                : field.value;

        if (field.key === "Discount") {
            const data = props.Discount.find(
                (data) => data.code === Form.fields.discount_id.value
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

const EditPost = async () => {
    const payload = FormValidation(Form.fields);

    const filteredPayload = payload;

    console.log(filteredPayload);

    try {
        const response = await axios.put(
            route("product_manager.update", { product: props.data.id }),
            filteredPayload
        );
        toast.success(response.data.message);
    } catch (error) {
        toast.error(error.response.data.message);
    }
};

// Watch for changes in the name field to automatically update the slug
watch(
    () => Form.fields.name.value,
    (newValue) => {
        if (!isSlugEditable.value) {
            Form.fields.slug.value = newValue
                ? diacritics.remove(
                      newValue.trim().toLowerCase().replace(/\s+/g, "-")
                  )
                : null;
        }
    }
);
// onMounted lifecycle hook
onMounted(() => {
    const data = props.data;


    if (data) {
        Object.keys(Form.fields).forEach((key) => {
            // Special handling for 'target' key to ensure it's always an array
            if (key === "target" && !Array.isArray(data[key])) {
                // If the value is not an array, convert it to an array
                Form.fields[key].value = data[key] ? data[key].split(",") : [];
            } else {
                // For all other fields, set the value directly
                Form.fields[key].value = data[key];
            }

  
            if (key === "discount_id") {
                const discount = props.Discount.find(
                    (discount) => discount.id === Form.fields.discount_id.value
                );

                Form.fields.discount_id.value = discount
                    ? discount.code
                    : "None";
            }
            if (key === "Category_id") {
                Form.fields.Category_id.value = data["categories"].map(
                    (Category) => Category.id
                );
            }

            if (key === "ForeignImage") {
                Form.fields.ForeignImage.value = data["foreign_images"]
                    .map((image) => image.image) // Get the image URLs
                    .join(","); // Join them into a single string with commas
            }
        });

        // Add 'checked' property to categories in props.Category
        props.Category.forEach((category) => {
            // Find if the category exists in props.data.categories
            const matchedCategory = props.data.categories.find(
                (cat) => cat.id === category.id
            );
            // Set 'checked' to the category's id if matched, otherwise false
            category.checked = matchedCategory ? category.id : false;

            // Check nested children
            if (category.children && category.children.length > 0) {
                category.children.forEach((childCategory) => {
                    // Find if the child category exists in props.data.categories
                    const matchedChildCategory = props.data.categories.find(
                        (cat) => cat.id === childCategory.id
                    );
                    // Set 'checked' to the child category's id if matched, otherwise false
                    childCategory.checked = matchedChildCategory
                        ? childCategory.id
                        : false;
                });
            }
        });

        // Update the category data with the checked property
        categoryData.value = buildCategoryTree(props.Category);
    }


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

.category > ul > li {
    border-bottom: solid 1px #ccc;
}

.category {
    max-height: 350px;
    overflow: auto;
}
</style>
