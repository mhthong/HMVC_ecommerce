<template>
    <Head title="General" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                General
            </h2>
        </template>

        <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <SettingsForm
          :settings="settings"
        />
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


const props = defineProps({
    dataSettings: {
        type: Array,
        required: true,
    },
});


// Define form fields and settings
const settings = useForm({
    fields: {
        admin_logo: {
            name: "Logo admin",
            key: "admin_logo",
            value: null,
            des: "URL of the admin logo.",
            type: "text",
            includes: "image",
        },
        admin_favicon: {
            name: "Favicon",
            key: "admin_favicon",
            value: null,
            des: "URL of the favicon image.",
            type: "text",
            includes: "image",
        },
        admin_login_screen_backgrounds: {
            name: "Login Screen Background",
            key: "admin_login_screen_backgrounds",
            value: null,
            des: "URL of the login screen background image.",
            type: "text",
            includes: "image",
        },
        admin_email: {
            name: "Email admin",
            key: "admin_email",
            value: null,
            des: "The email address for the admin.",
            type: "email",
            includes: "text",
        },

        admin_title: {
            name: "Admin Title",
            key: "admin_title",
            value: null,
            des: "Title for the admin panel.",
            type: "text",
            includes: "text",
        },
        google_analytics: {
            name: "Google Analytics",
            key: "google_analytics",
            value: null,
            des: "Google Analytics tracking code.",
            type: "text",
            includes: "text",
        },
        analytics_view_id: {
            name: "Analytics View ID",
            key: "analytics_view_id",
            value: null,
            des: "Google Analytics View ID.",
            type: "text",
            includes: "text",
        },
        analytics_service_account_credentials: {
            name: "Analytics Service Account Credentials",
            key: "analytics_service_account_credentials",
            value: null,
            des: "Credentials for Google Analytics service account.",
            type: "text",
            includes: "text",
        },
        newsletter_mailchimp_api_key: {
            name: "Mailchimp API Key",
            key: "newsletter_mailchimp_api_key",
            value: null,
            des: "API key for Mailchimp.",
            type: "text",
            includes: "text",
        },
        newsletter_mailchimp_list_id: {
            name: "Mailchimp List ID",
            key: "newsletter_mailchimp_list_id",
            value: null,
            des: "List ID for Mailchimp.",
            type: "text",
            includes: "text",
        },
        newsletter_sendgrid_api_key: {
            name: "SendGrid API Key",
            key: "newsletter_sendgrid_api_key",
            value: null,
            des: "API key for SendGrid.",
            type: "text",
            includes: "text",
        },
        newsletter_sendgrid_list_id: {
            name: "SendGrid List ID",
            key: "newsletter_sendgrid_list_id",
            value: null,
            des: "List ID for SendGrid.",
            type: "text",
            includes: "text",
        },

    },
});

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

.position{
position: relative;
}
.position-current , .position-new {
    position: absolute;
    top: 0;
    left: 0;
}

.position-new {
    z-index: 2;
}

</style>
