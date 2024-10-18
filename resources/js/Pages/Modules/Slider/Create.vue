<template>
    <Head title="Create Slider" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Create Slider
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
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import diacritics from "diacritics";
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

// Watch for changes in the name field to automatically update the slug
/* watch(() => Form.fields.position.value,
    (newValue,oldValue) => {
        if(oldValue !== newValue) {
            Form.fields.relationship.value = null;
        }
        if (newValue === "Page") {
            // Giả sử bạn có props.Page là một mảng các object trang
            Form.fields.relationship.items = props.Page.map((page) => (page.name));
        } else {
            // Giả sử bạn có props.Page là một mảng các object trang
            Form.fields.relationship.items = props.Post.map((Post) => (Post.name));
        }

    }
); */

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
                toast.error(`Validation failed for field ${key}: ${error}`);
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
            route("slider_manager.store"),
            filteredPayload
        );
        toast.success(response.data.message, {
                        onClose: () => {
                            window.location.reload(); // This will reload the page when the toast closes
                        },
                    });
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
