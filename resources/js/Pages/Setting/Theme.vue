<template>
    <Head title="Theme" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Theme
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <v-row>
                    <v-col cols="12" md="3">
                            <ul>
                            <li
                                v-for="t in theme"
                                :key="t.name"
                                @click="selectTheme(t)"
                                :class="{ tab_active: t.active }"
                                class="p-3 mb-3 tab"
                            >
                                <v-icon>{{ t.icon }}</v-icon>
                                <button>
                                    {{ t.name }}
                                </button>
                            </li>
                        </ul>

                    </v-col>

                    <v-col cols="12" md="9">
                        <SettingsForm :settings="settings" />
                    </v-col>
                </v-row>
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


const settings = useForm({
    fields: {
        page_name: {
            name: "Page Name",
            key: "page_name",
            value: null, // Replace with actual value if available
            des: "The name of the page.",
            type: "text",
            includes: "text",
        },
        seo_title: {
            name: "SEO Title",
            key: "seo_title",
            value: null, // Replace with actual value if available
            des: "The SEO title for the page.",
            type: "text",
            includes: "text",
        },
        seo_description: {
            name: "SEO Description",
            key: "seo_description",
            value: null, // Replace with actual value if available
            des: "The SEO description for the page.",
            type: "textarea",
            includes: "text",
        },
        seo_image: {
            name: "Shared Image",
            key: "seo_image",
            value: "", // Replace with actual image URL if available
            des: "The image used for sharing on social media.",
            type: "text",
            includes: "image",
        },
        footer_color: {
            name: "Footer Background Color",
            key: "footer_color",
            value: "#000000", // Replace with actual value if available
            des: "The background color for the footer.",
            type: "text",
            includes: "text",
        },
        copyright: {
            name: "Copyright",
            key: "copyright",
            value: null, // Replace with actual value if available
            des: "The copyright text for the footer.",
            type: "text",
            includes: "text",
        },
        hotline: {
            name: "Hotline",
            key: "hotline",
            value: null, // Replace with actual value if available
            des: "The primary hotline number.",
            type: "tel",
            includes: "text",
        },
        hotline_2: {
            name: "Hotline 2",
            key: "hotline_2",
            value: null, // Replace with actual value if available
            des: "The secondary hotline number.",
            type: "tel",
            includes: "text",
        },
        name_company: {
            name: "Business Name",
            key: "name_company",
            value: null, // Replace with actual value if available
            des: "The name of the business.",
            type: "text",
            includes: "text",
        },
        address_company: {
            name: "Address",
            key: "address_company",
            value: null, // Replace with actual value if available
            des: "The business address.",
            type: "text",
            includes: "text",
        },
        email_company: {
            name: "Email",
            key: "email_company",
            value: null, // Replace with actual value if available
            des: "The business email address.",
            type: "email",
            includes: "text",
        },
        google_map_embed: {
            name: "Google Map Embed",
            key: "google_map_embed",
            value: null, // Replace with actual value if available
            des: "The Google Map iframe for the location.",
            type: "textarea",
            includes: "textarea",
        },
    },
});


const settingsGeneral = useForm({
    fields: {
        page_name: {
            name: "Page Name",
            key: "page_name",
            value: null, // Replace with actual value if available
            des: "The name of the page.",
            type: "text",
            includes: "text",
        },
        seo_title: {
            name: "SEO Title",
            key: "seo_title",
            value: null, // Replace with actual value if available
            des: "The SEO title for the page.",
            type: "text",
            includes: "text",
        },
        seo_description: {
            name: "SEO Description",
            key: "seo_description",
            value: null, // Replace with actual value if available
            des: "The SEO description for the page.",
            type: "textarea",
            includes: "text",
        },
        seo_image: {
            name: "Shared Image",
            key: "seo_image",
            value: "", // Replace with actual image URL if available
            des: "The image used for sharing on social media.",
            type: "text",
            includes: "image",
        },
        footer_color: {
            name: "Footer Background Color",
            key: "footer_color",
            value: "#000000", // Replace with actual value if available
            des: "The background color for the footer.",
            type: "text",
            includes: "text",
        },
        copyright: {
            name: "Copyright",
            key: "copyright",
            value: null, // Replace with actual value if available
            des: "The copyright text for the footer.",
            type: "text",
            includes: "text",
        },
        hotline: {
            name: "Hotline",
            key: "hotline",
            value: null, // Replace with actual value if available
            des: "The primary hotline number.",
            type: "tel",
            includes: "text",
        },
        hotline_2: {
            name: "Hotline 2",
            key: "hotline_2",
            value: null, // Replace with actual value if available
            des: "The secondary hotline number.",
            type: "tel",
            includes: "text",
        },
        name_company: {
            name: "Business Name",
            key: "name_company",
            value: null, // Replace with actual value if available
            des: "The name of the business.",
            type: "text",
            includes: "text",
        },
        address_company: {
            name: "Address",
            key: "address_company",
            value: null, // Replace with actual value if available
            des: "The business address.",
            type: "text",
            includes: "text",
        },
        email_company: {
            name: "Email",
            key: "email_company",
            value: null, // Replace with actual value if available
            des: "The business email address.",
            type: "email",
            includes: "text",
        },
        google_map_embed: {
            name: "Google Map Embed",
            key: "google_map_embed",
            value: null, // Replace with actual value if available
            des: "The Google Map iframe for the location.",
            type: "textarea",
            includes: "textarea",
        },
    },
});

const settingsImages = useForm({
    fields: {
        Favicon: {
            name: "Favicon",
            key: "Favicon",
            value: null, // Replace with actual value if available
            des: "The favicon for the website.",
            type: "text",
            includes: "image",
        },
        Logo: {
            name: "Logo",
            key: "Logo",
            value: null, // Replace with actual value if available
            des: "The main logo for the website.",
            type: "text",
            includes: "image",
        },
        logo_mobile: {
            name: "Mobile Logo",
            key: "logo_mobile",
            value: null, // Replace with actual value if available
            des: "The logo for the mobile version of the website.",
            type: "text",
            includes: "image",
        },
        banner_image: {
            name: "Banner Image",
            key: "banner_image",
            value: null, // Replace with actual value if available
            des: "The main banner image for the website.",
            type: "text",
            includes: "image",
        },
        banner_image_mobile: {
            name: "Mobile Banner Image",
            key: "banner_image_mobile",
            value: null, // Replace with actual value if available
            des: "The banner image for the mobile version of the website.",
            type: "text",
            includes: "image",
        },
    },
});

const settingsFacebook = useForm({
    fields: {
        facebook_chat_enabled: {
            name: "Enable Facebook Chat",
            key: "facebook_chat_enabled",
            value: null, // Replace with actual value if available
            des: "Enable or disable Facebook chat on the website.",
            type: "select",
            items: ['No', 'Yes']
        },
        facebook_page_id: {
            name: "Facebook Page ID",
            key: "facebook_page_id",
            value: null, // Replace with actual value if available
            des: "The Facebook page ID for your fan page.",
            type: "text",
            includes: "text",
        },
        facebook_comment_enabled_in_post: {
            name: "Enable Facebook Comments in Posts",
            key: "facebook_comment_enabled_in_post",
            value: null, // Replace with actual value if available
            des: "Enable or disable Facebook comments in post detail pages.",
            type: "select",
            items: ['No', 'Yes']
        },
        facebook_app_id: {
            name: "Facebook App ID",
            key: "facebook_app_id",
            value: null, // Replace with actual value if available
            des: "The Facebook App ID used for integration.",
            type: "text",
            includes: "text",
        },
        facebook_admins: {
            name: "Facebook Admins",
            key: "facebook_admins",
            value: null, // Replace with actual value if available
            des: "List of Facebook Admin IDs for the website.",
            type: "repeater",
            includes: "text",
            repeater: {
                name: "Facebook Admin ID",
                type: "text",
            },
        },
    },
});

const settingsSocial = useForm({
    fields: {
        Facebook: {
            name: "Facebook",
            key: "Facebook",
            value: null,
            des: "Facebook page URL.",
            type: "text",
            includes: "text",
            placeholder: "https://facebook.com",
        },
        Twitter: {
            name: "Twitter",
            key: "Twitter",
            value: null,
            des: "Twitter profile URL.",
            type: "text",
            includes: "text",
            placeholder: "https://twitter.com",
        },
        Linkedin: {
            name: "Linkedin",
            key: "Linkedin",
            value: null,
            des: "LinkedIn profile URL.",
            type: "text",
            includes: "text",
            placeholder: "https://linkedin.com",
        },
        Zalo: {
            name: "Zalo",
            key: "Zalo",
            value: null,
            des: "Zalo profile URL.",
            type: "text",
            includes: "text",
            placeholder: "https://zalo",
        },
        Youtube: {
            name: "Youtube",
            key: "Youtube",
            value: null,
            des: "YouTube channel URL.",
            type: "text",
            includes: "text",
            placeholder: "https://youtube.com",
        },
        Instagram: {
            name: "Instagram",
            key: "Instagram",
            value: null,
            des: "Instagram profile URL.",
            type: "text",
            includes: "text",
            placeholder: "https://instagram.com",
        },
        Pinterest: {
            name: "Pinterest",
            key: "Pinterest",
            value: null,
            des: "Pinterest profile URL.",
            type: "text",
            includes: "text",
            placeholder: "https://pinterest.com",
        },
    },
});

const theme = ref([
    {
        name: "General Information",
        active: true,
        icon: "mdi-home-city",
        FormData: settingsGeneral,
    },
    {
        name: "Image Information",
        active: false,
        icon: "mdi-image",
        FormData: settingsImages,
    },
    {
        name: "Social",
        active: false,
        icon: "mdi-share-all",
        FormData: settingsSocial,
    },
    {
        name: "Facebook Integration",
        active: false,
        icon: "mdi-facebook",
        FormData: settingsFacebook,
    },
]);

// Handle theme selection
function selectTheme(selectedTheme) {
    // Reset all themes
    theme.value.forEach((themeItem) => {
        themeItem.active = false;
    });

    // Assign the fields of the selected theme to settings.fields
    selectedTheme.active = true;
    settings.fields = selectedTheme.FormData.fields;
}

// onMounted lifecycle hook
onMounted(() => {

    // Iterate through dataSettings and update the form fields
    props.dataSettings.forEach((setting) => {

        const key = setting.key;
        const value = setting.value;

        if (settings.fields[key]) {
            settings.fields[key].value = value;
        }

        if (settingsGeneral.fields[key]) {
            settingsGeneral.fields[key].value = value;
        }

        if (settingsImages.fields[key]) {
            settingsImages.fields[key].value = value;
        }

        if (settingsSocial.fields[key]) {
            settingsSocial.fields[key].value = value;
        }

        if (settingsFacebook.fields[key]) {
            settingsFacebook.fields[key].value = value;
        }

    });

    console.log( props.dataSettings);
    
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

.tab {
    background: white;
    border-radius: 2rem;
    display: flex;
    gap: 1rem;
    align-items: center;
    transition: background-color 0.3s ease; /* Smooth transition */
}

.tab_active {
    background: rgb(24, 220, 255);
}

</style>
