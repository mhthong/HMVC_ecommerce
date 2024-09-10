<template>
    <v-form @submit.prevent="submitSettings">
        <v-container>
            <v-row>
                <v-col cols="12" md="9">
                    <div v-for="(field, key) in settings.fields" :key="key">
                        <v-text-field
                            v-model="field.value"
                            :label="field.name"
                            :placeholder="field.des"
                            :type="field.type"
                            :rules="getValidationRules(field)"
                            v-if="field.includes == 'text'"
                            md="6"
                        ></v-text-field>

                        <v-textarea
                            v-if="field.includes == 'textarea'"
                            v-model="field.value"
                            :label="field.name"
                            :placeholder="field.des"
                            md="6"
                        >
                        </v-textarea>

                        <v-select
                            v-model="field.value"
                            :label="field.name"
                            :items="field.items"
                            v-if="field.type === 'select'"
                            :rules="getValidationRules(field)"
                            md="6"
                        ></v-select>

                        <!-- Hiển thị nút chọn hình ảnh nếu là trường hình ảnh -->
                        <div
                            v-if="field.includes === 'image'"
                            class="border border-secondary p-3 mb-2"
                        >
                            <label :for="field.key">{{ field.name }}</label>
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
                                        openFileManager('thumbnail' + field.key)
                                "
                            />
                        </div>
                    </div>
                </v-col>

                <v-col cols="12" md="3">
                    <v-btn type="submit" color="primary"> Save Settings </v-btn>
                </v-col>
            </v-row>
        </v-container>
    </v-form>
</template>

<script setup>
import { defineProps } from "vue";
import axios from "axios";
import { toast } from "vue3-toastify"; // Import các phương thức từ vue3-toastify

const props = defineProps({
    settings: {
        type: Object,
        required: true,
    },
});

// Open file manager

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

    /*     if (field.includes === "image") {
        // Ensure image URL is not empty
        rules.push((v) => !!v || "Image URL is required");
    } else { */
    // General validation for text fields
    if (field.type === "email") {
        rules.push((v) => !!v || "Email is required");
        rules.push((v) => /.+@.+\..+/.test(v) || "Email must be valid");
    } else if (field.type === "text") {
    }
    /*       */ // Add more rules based on field type as needed
    /*     } */

    return rules;
}

// Function to submit settings

const submitSettings = () => {
    // Step 1: Update the payload with the latest values from input elements

    const fields = props.settings.fields;

    // Prepare the payload with all field values
    const payload = Object.values(fields).reduce((acc, field) => {
        if (field.includes === "image") {
            // Update with the value from the input element
            const inputElement = document.getElementById(field.key);
            acc[field.key] = inputElement ? inputElement.value : "";
        } else {
            // Use the value from the Vue model
            acc[field.key] = field.value;
        }
        return acc;
    }, {});

    // Step 2: Validate all fields
    for (const key in fields) {
        const field = fields[key];
        const validationRules = getValidationRules(field);

        // Apply validation rules
        for (const rule of validationRules) {
            const error = rule(payload[field.key]);
            if (error !== true) {
                console.error(`Validation failed for field ${key}: ${error}`);
                return; // Stop submission if validation fails
            }
        }
    }

    // Filtering out null or undefined values from the payload object
    const filteredPayload = Object.fromEntries(
        Object.entries(payload).filter(([key, value]) => value != null)
    );

    // Step 3: Submit the validated settings using Axios
    axios
        .post(route("general.store"), filteredPayload)
        .then((response) => {
            toast.success(response.data.message);

            console.log(response.data.currentData);

            // Iterate through dataSettings and update the form fields
            response.data.currentData.forEach((setting) => {
                const key = setting.key;
                const value = setting.value;

                if (props.settings.fields[key]) {
                    props.settings.fields[key].value = value;
                }
            });
        })
        .catch((error) => {
            toast.error(error.response.data.message);
        });
};
</script>

<style>
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

.v-text-field .v-field--active input {
    opacity: 1;
    box-shadow: none !important;
}
</style>
