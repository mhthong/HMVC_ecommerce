<template>
    <Head title="Edit Slider" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Edit Slider
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <v-form @submit.prevent="EditPage">
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

                                    <div v-if="field.includes === 'textarea'">
                                        <ckeditor
                                            v-model="field.value"
                                            :editor="editor"
                                            :config="editorConfig"
                                        />
                                    </div>

                                    <div
                                        v-if="field.includes === 'image'"
                                        class="border border-secondary p-3 mb-2"
                                    >
                                        <label :for="field.key">{{
                                            field.name
                                        }}</label>
                                        <v-row>
                                            <v-col cols="6">
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

                                <v-btn type="submit" color="primary"
                                    >Save</v-btn
                                >
                            </v-col>
                        </v-row>
                    </v-container>
                </v-form>

                <v-container>
                    <v-btn
                        color="surface-variant"
                        text="Add"
                        variant="flat"
                        class="mb-2"
                        @click="add"
                    ></v-btn>

                    <!--                    <button @click="handleAdd">Add</button> -->
                    <v-dialog v-model="isActive" max-width="500">
                        <v-card title="Add Slider Items">
                            <div
                                v-for="(field, key) in Formlist.fields"
                                :key="key"
                                class="p-4"
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
                                        {{ isSlugEditable ? "Lock " : "Edit" }}
                                    </v-btn>
                                </div>

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

                                <div
                                    v-if="field.includes === 'image'"
                                    class="border border-secondary p-3 mb-2"
                                >
                                    <label :for="field.key">{{
                                        field.name
                                    }}</label>
                                    <v-row>
                                        <v-col cols="6">
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
                            <v-card-actions>
                                <v-spacer></v-spacer>

                                <v-btn
                                    text="Add"
                                    @click="
                                        handleAdd();
                                        isActive = false;
                                    "
                                ></v-btn>

                                <v-btn
                                    text="Close"
                                    @click="isActive = false"
                                ></v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <div class="">
                        <VueDraggable
                            v-model="list"
                            class="flex flex-col gap-2 bg-gray-500/5 rounded"
                            target=".sort-target"
                            :scroll="true"
                        >
                            <TransitionGroup
                                type="transition"
                                tag="ul"
                                name="fade"
                                class="sort-target"
                            >
                                <li
                                    v-for="(item, index) in list"
                                    :key="item"
                                    class="h-50px bg-gray-500/5 rounded flex items-center justify-between px-2"
                                >
                                    <v-row class="items-center justify-between">
                                        <v-col cols="2" md="1" class="">
                                            <v-icon
                                                icon="mdi:mdi-list-box-outline"
                                            />
                                        </v-col>
                                        <v-col cols="3" md="3">
                                            <div>
                                                {{ item.title }}
                                            </div>

                                        </v-col>
                                        <v-col cols="3" md="4">
                                            <div>
                                                {{ item.description }}
                                            </div>

                                        </v-col>
                                        <v-col cols="2" md="3">
                                            <div>
                                                <img :src="item.image" alt="" srcset="" style="height: 50px;margin: 1rem;border-radius: 50%;">
                                            </div>

                                        </v-col>

                                        <v-col cols="2" md="1">
                                            <v-icon
                                                icon="mdi:mdi-pencil-box-outline"
                                                @click="edit(item)"
                                            />

                                            <v-icon
                                                icon="mdi:mdi-minus-box"
                                                @click="remove(index)"
                                            />
                                        </v-col>
                                    </v-row>
                                </li>
                            </TransitionGroup>
                        </VueDraggable>
                        <v-btn
                            class="mt-2"
                            type="submit"
                            color="primary"
                            @click="saveSliderItems"
                            >Save Slider Items</v-btn
                        >
                    </div>
                </v-container>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import diacritics from "diacritics";
import axios from "axios";
import { toast } from "vue3-toastify";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
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
    sliderItems: {
        type: Object, // Update to Object if `page` is an object
        required: true,
    },
});


const isActive = ref(false);

const Form = useForm({
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
        key: {
            name: "Key",
            key: "key",
            value: null,
            des: null,
            type: "text",
            includes: "text",
            disable: false,
            required: true,
        },
        status: {
            name: "Status",
            key: "status",
            value: null,
            des: "Page status (e.g., published, draft)",
            type: "select",
            items: ["Published", "Draft"],
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
        status: {
            name: "Status",
            key: "status",
            value: null,
            des: "Page status (e.g., published, draft)",
            type: "select",
            items: ["Published", "Draft"],
            disable: false,
            required: true,
        },
    },
});

const Formlist = useForm({
    fields: {
        title: {
            name: "Title",
            key: "title",
            value: null,
            des: "Title",
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
        link: {
            name: "Url",
            key: "Url",
            value: null,
            des: "Url of the slider",
            type: "text",
            includes: "text",
            disable: false,
            required: true,
        },
        image: {
            name: "Image",
            key: "image",
            value: null,
            des: "Image URL or path",
            type: "text",
            includes: "image",
            disable: false,
            required: false,
        },
    },
});

const list = ref([
]);
const modal = ref(["editModal"]);
const idEdit = ref(null);
function FormValidation(Formlist) {
    const fields = Formlist;
    const payload = Object.entries(fields).reduce((acc, [key, field]) => {
        acc[key] =
            field.includes === "image"
                ? document.getElementById(field.key)?.value || field.value
                : field.value;
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

function handleAdd() {
    const payload = FormValidation(Formlist.fields);

    // Tìm id lớn nhất hiện có trong list và cộng thêm 1
    const maxId = list.value.length > 0 ? Math.max(...list.value.map(item => item.id)) : 0;
    const newId = maxId + 1;

    let newTitle = payload.title;
    let duplicateCount = 0;

    // Lặp để tìm tiêu đề không trùng lặp
    while (list.value.some((item) => item.title === newTitle && item.id !== idEdit.value)) {
        duplicateCount++;
        newTitle = `${payload.title} (${duplicateCount})`;
    }

    // Kiểm tra xem có đang ở chế độ chỉnh sửa hay không
    if (modal.value === "editModal") {
        // Tìm mục trong list có id trùng với idEdit.value
        const itemIndex = list.value.findIndex((item) => item.id === idEdit.value);
        if (itemIndex !== -1) {
            // Cập nhật mục tương ứng trong list
            list.value[itemIndex] = {
                ...list.value[itemIndex],
                link: payload.link,
                image: payload.image,
                description: payload.description,
                title: newTitle,  // Tiêu đề sau khi kiểm tra trùng lặp
            };
        }
    } else {
        // Thêm phần tử mới nếu không ở chế độ chỉnh sửa
        list.value.push({
            link: payload.link,
            image: payload.image,
            description: payload.description,
            title: newTitle,  // Sử dụng tiêu đề không trùng lặp
            id: newId,  // Tạo id mới là id lớn nhất + 1
        });
    }

    console.log(list);

    // Đóng modal sau khi xử lý
    isActive.value = false;
}



async function saveSliderItems() {
    console.log("List being sent:", list.value);
    console.log("Slider ID:", props.data.id);

    try {
        const response = await axios.post(
            "/dashboard/slider-items-manager/createorupdate",
            {
                slider_items: list.value, // Gửi danh sách list
                slider_id: props.data.id, // Gửi slider_id từ props
            }
        );
        toast.success(response.data.message);

    } catch (error) {
        toast.success("Error saving slider items");
    }
}

function remove(index) {
    list.value.splice(index, 1);
}
function edit(index) {
    modal.value = "editModal";
    isActive.value = true;
    idEdit.value = index.id;

    // Duyệt qua từng key trong index
    Object.keys(index).forEach((key) => {
        // Kiểm tra nếu key tồn tại trong Formlist.fields trước khi gán
        if (Formlist.fields.hasOwnProperty(key) && index[key] !== undefined && key !== 'id') {
            Formlist.fields[key].value = index[key];  // Gán giá trị nếu tồn tại
        }
    });

}


function add() {
    modal.value = "addModal";
    isActive.value = true;

}

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

const EditPage = async () => {
    const payload = FormValidation(Form.fields);

    const filteredPayload = Object.fromEntries(
        Object.entries(payload).filter(([_, value]) => value != null)
    );

    console.log(filteredPayload, props.data.id);

    try {
        const response = await axios.put(
            route("slider_manager.update", { slider: props.data.id }),
            filteredPayload
        );
        toast.success(response.data.message);
    } catch (error) {
        toast.error(error.response.data.message);
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

    list.value = props.sliderItems;
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
</style>
