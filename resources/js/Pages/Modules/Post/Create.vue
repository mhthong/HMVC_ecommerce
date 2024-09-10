<template>
    <Head title="Create Page" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Create Page
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <v-form @submit.prevent="CreatePage">
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

                                    <div v-if="field.includes === 'slug'" style="display: flex;">
                                        <v-text-field
                                            v-model="field.value"
                                            :label="field.name"
                                            :placeholder="field.des"
                                            :type="field.type"
                                            :rules="getValidationRules(field)"
                                            :disabled="!isSlugEditable"
                                            md="6"
                                        ></v-text-field>
                                        <v-btn @click="toggleSlugEdit" small class="text-black">
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
                                            v-model="field.value"
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
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import axios from "axios";
import { toast } from "vue3-toastify";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

import { Ckeditor } from "@ckeditor/ckeditor5-vue";

import "ckeditor5/ckeditor5.css";

import { editor, editorConfig } from "@/Components/ckeditorConfig.js";

const Form = useForm({
    fields: {
        name: {
            name: "Name",
            key: "name",
            value: null,
            des: "Name of the page",
            type: "text",
            includes: "text",
            disable: false,
        },
        slug: {
            name: "Slug",
            key: "slug",
            value: null,
            des: null,
            type: "text",
            includes: "slug",
            disable: false,
        },

        status: {
            name: "Status",
            key: "status",
            value: null,
            des: "Page status (e.g., published, draft)",
            type: "select",
            items: ["Published", "Draft"],
            disable: false,
        },

        template: {
            name: "Template",
            key: "template",
            value: "Module/Page/Template",
            des: "Template for the page",
            type: "text",
            includes: "text",
            disable: false,
        },
        description: {
            name: "Description",
            key: "description",
            value: null,
            des: "Meta description",
            type: "textarea",
            includes: "text",
            disable: false,
        },
        content: {
            name: "Content",
            key: "content",
            value: "Content of the page",
            des: "Content of the page",
            type: "textarea",
            includes: "textarea",
            disable: false,
        },
        image: {
            name: "Image",
            key: "image",
            value: null,
            des: "Image URL or path",
            type: "text",
            includes: "image",
            disable: false,
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

function getValidationRules(field) {
    const rules = [];
    if (field.type === "email") {
        rules.push((v) => !!v || "Email is required");
        rules.push((v) => /.+@.+\..+/.test(v) || "Email must be valid");
    } else if (field.type === "text" && field.includes !== "image") {
        rules.push((v) => !!v || "Field is required");
    }
    return rules;
}

const isSlugEditable = ref(false);

const toggleSlugEdit = () => {
    isSlugEditable.value = !isSlugEditable.value;
};

// Watch for changes in the name field to automatically update the slug
watch(
    () => Form.fields.name.value,
    (newValue) => {
        if (!isSlugEditable.value) {
            Form.fields.slug.value = newValue
                ? newValue.trim().toLowerCase().replace(/\s+/g, "-")
                : null;
        }
    }
);

const CreatePage = async () => {
    const fields = Form.fields;
    const payload = Object.entries(fields).reduce((acc, [key, field]) => {
        acc[key] =
            field.includes === "image"
                ? document.getElementById(field.key)?.value || ""
                : field.value;
        return acc;
    }, {});

    for (const [key, field] of Object.entries(fields)) {
        for (const rule of getValidationRules(field)) {
            const error = rule(payload[key]);
            if (error !== true) {
                console.error(`Validation failed for field ${key}: ${error}`);
                return;
            }
        }
    }

    const filteredPayload = Object.fromEntries(
        Object.entries(payload).filter(([_, value]) => value != null)
    );

    console.log(filteredPayload);

    try {
        const response = await axios.post(
            route("page_manager.store"),
            filteredPayload
        );
        toast.success(response.data.message);
    } catch (error) {
        toast.error(error.response.data.message);
    }
};
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
</style>
