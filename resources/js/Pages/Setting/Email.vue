<template>
    <Head title="Email settings" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Email settings
            </h2>
            <p class="text-white">
                Template file structure using HTML and system variables system.
            </p>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <SettingsForm :settings="settings" />
            </div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <h2
                class="font-semibold text-xl"
            >
                Email Test 
            </h2>

                <v-form @submit.prevent="sendEmail">
                    <v-container>
                        <v-row>
                            <v-col cols="12" md="9">
                                <v-text-field
                                    v-model="emailForm.email"
                                    label="Email"
                                    :rules="emailRules"
                                    required
                                    type="email"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-btn type="submit" color="primary"
                                    >Send Email</v-btn
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
import { ref, onMounted, onUnmounted, watch, computed, nextTick } from "vue";

import axios from "axios";
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import SettingsForm from "@/Components/SettingsForm.vue";

import { toast } from "vue3-toastify"; // Import các phương thức từ vue3-toastify

const props = defineProps({
    dataSettings: {
        type: Array,
        required: true,
    },
});

// Define form fields and settings
const settings = useForm({
    fields: {
        email_driver: {
            name: "Email Driver",
            key: "email_driver",
            value: null,
            des: "The email driver to use, e.g., SMTP, Mailgun.",
            type: "text",
            includes: "text",
        },

        email_hostName: {
            name: "Server",
            key: "email_hostName",
            value: null,
            des: "The email server hostname, e.g., smtp.example.com.",
            type: "text",
            includes: "text",
        },
        email_encryption: {
            name: "Encoding",
            key: "email_encryption",
            value: null,
            des: "The encryption method, e.g., SSL, TLS.",
            type: "text",
            includes: "text",
        },
        email_port: {
            name: "Port",
            key: "email_port",
            value: null,
            des: "The port number for the email server, e.g., 587.",
            type: "number",
            includes: "text",
        },
        email_userName: {
            name: "Username",
            key: "email_userName",
            value: null,
            des: "The username for authenticating with the email server.",
            type: "text",
            includes: "text",
        },

        email_password: {
            name: "Password",
            key: "email_password",
            value: null,
            des: "The password for authenticating with the email server.",
            type: "text",
            includes: "text",
        },
        email_senderName: {
            name: "Sender Name",
            key: "email_senderName",
            value: null,
            des: "The name that will appear as the sender in emails.",
            type: "text",
            includes: "text",
        },
        email_senderEmail: {
            name: "Sender Email",
            key: "email_senderEmail",
            value: null,
            des: "The email address that will appear as the sender in emails.",
            type: "email",
            includes: "text",
        },
    },
});

// Validation rules for email field
const emailRules = [
    (v) => !!v || "Email is required",
    (v) => /.+@.+\..+/.test(v) || "E-mail must be valid",
];

const emailForm = useForm({
    email: null,
});

const sendEmail = async () => {
    console.log(emailForm);

    // Step 3: Submit the validated settings using Axios
    await axios
        .post(route("email.test"), emailForm)
        .then((response) => {
            toast.success(response.data.message);
        })
        .catch((error) => {
            toast.error(error.response.data.message);
        });
};

// onMounted lifecycle hook
onMounted(() => {
    // Iterate through dataSettings and update the form fields
    props.dataSettings.forEach((setting) => {
        const key = setting.key;
        const value = setting.value;

        if (settings.fields[key]) {
            settings.fields[key].value = value;
        }
    });
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
</style>
