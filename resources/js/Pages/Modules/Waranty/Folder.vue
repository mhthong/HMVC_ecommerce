<template>
    <Head title="Warranty Folder Manager" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Warranty Folder Manager
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div>
                    <!-- Add New Discount Button -->
                    <v-container>
                        <v-btn class="bg-primary me-2" @click="printQRs">
                            Print
                        </v-btn>
                        <v-btn class="bg-primary" @click="openClear">
                            Clear Qr Code
                        </v-btn>
                    </v-container>

                    <!-- Discount Form Dialog -->
                    <v-container>
                        <v-dialog v-model="isActive" max-width="500" persistent>
                            <v-card :title="Title">
                                <v-form @submit.prevent="saveDiscount">
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

                    <!-- Data -->
                    <v-container>
                        <v-row class="warranty" v-if="datas && datas.length > 0">
                            <v-col
                                v-for="warranty in datas"
                                :key="warranty.id"
                                class="col-warranty"
                            >
                                <QRCodeGenerator
                                    :warrantyCode="warranty.warranty_code"
                                />
                            </v-col>
                        </v-row>

                        <v-row class="warranty" v-if="datas && datas.length < 1">
                            <v-col>
                                <i>None Data QrCode </i>
                            </v-col>
                        </v-row>
                    </v-container>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref ,defineProps} from "vue";
import { useForm } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import axios from "axios";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import QRCodeGenerator from "@/Components/QRCodeGenerator.vue";
import { toast } from "vue3-toastify";
import { useRoute } from "vue-router";

// Props
const props = defineProps({
    datas: Array,
    product_id_clear: [Number, String], // Accept both Number and String
});


// State
const isActive = ref(false);
const Title = ref("Create Discount");
const selectedItem = ref(null);
const localDatas = ref([...props.datas]);
const tableKey = ref(0);

// Open the clear QR code dialog
const openClear = () => {
    Title.value = "Clear Qr Code";
    isActive.value = true;
};

// Close the dialog
const closeDialog = () => {
    isActive.value = false;
};

// Save the discount
const saveDiscount = async () => {
    try {
        const response = await axios.put(
            route("waranty_manager.clear", {
                product_id: Number(props.product_id_clear),
            }) // Ensure product_id is a number
        );

        // Hiển thị thông báo thành công
        toast.success(response.data.message, {
            onClose: () => {
                window.location.reload(); // This will reload the page when the toast closes
            },
        });
    } catch (error) {
        console.error(error);
        toast.error(
            error.response?.data?.message ||
                "An error occurred while clearing the warranty."
        );
    }
    isActive.value = false; // Close the dialog
};

// Print QR Codes
const printQRs = async () => {
    // Get the existing QR code container
    const qrContainer = document.querySelector(".warranty");

    if (!qrContainer || !qrContainer.children.length) {
        console.warn("No QR codes available to print.");
        return; // Exit if there are no QR codes
    }

    // Create a new window for printing
    const printWindow = window.open(
        "",
        "Print QR Codes",
        "width=800,height=600"
    );

    // Create a new container to hold the images
    const printContainer = document.createElement("div");

    // Get all canvas elements in the container
    const canvases = qrContainer.querySelectorAll("canvas");

    // Convert each canvas to an image and append it to the print container
    const imagePromises = Array.from(canvases).map((canvas) => {
        return new Promise((resolve) => {
            const img = document.createElement("img");
            img.src = canvas.toDataURL(); // Convert canvas to data URL
            img.alt = "QR Code"; // Set alt text for the image
            img.style.margin = "20px"; // Add margin for spacing

            // Ensure the image is fully loaded before resolving the promise
            img.onload = () => resolve(img);

            // In case the image fails to load, resolve regardless
            img.onerror = () => resolve(img);

            // Append the image to the print container
            printContainer.appendChild(img);
        });
    });

    // Wait for all images to be fully loaded
    await Promise.all(imagePromises);

    // Add styles for the print window
    const styles = `
        <style>
            body { font-family: Arial, sans-serif; }
            h4 { margin: 0; }
            div { margin: 20px; }
        </style>
    `;

    // Write the styled print container to the print window
    printWindow.document.write(styles + printContainer.outerHTML);
    printWindow.document.close();

    // Trigger the print command after the document is fully ready
    printWindow.focus(); // Ensure the print window is focused
    printWindow.print();
};
</script>

<style scoped>
.bg-primary {
    background-color: #1976d2;
    color: white;
}

.warranty {
    max-height: 60vh;
    overflow: auto;
    border: solid 1px #adadad;
}
.col-warranty {
    flex-grow: 0;
}
</style>
