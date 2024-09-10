<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        default: 'Default Title',
    },
    description: {
        type: String,
        default: 'Default Description',
    },
    keywords: {
        type: String,
        default: 'default, keywords',
    },
    ogTitle: {
        type: String,
        default: 'Default OG Title',
    },
    ogDescription: {
        type: String,
        default: 'Default OG Description',
    },
    ogImage: {
        type: String,
        default: '/path/to/default-og-image.jpg',
    },
    ogUrl: {
        type: String,
        default: window.location.href,
    },
    ogSiteName: {
        type: String,
        default: 'Your Site Name',
    },
    ogLocale: {
        type: String,
        default: 'en_US',
    },
    twitterTitle: {
        type: String,
        default: 'Default Twitter Title',
    },
    twitterDescription: {
        type: String,
        default: 'Default Twitter Description',
    },
    twitterImage: {
        type: String,
        default: '/path/to/default-twitter-image.jpg',
    },
    canonicalUrl: {
        type: String,
        default: window.location.href,
    },
    robotsContent: {
        type: String,
        default: 'index, follow',
    },
    author: {
        type: String,
        default: 'Author Name',
    },
    viewport: {
        type: String,
        default: 'width=device-width, initial-scale=1.0',
    },
    charset: {
        type: String,
        default: 'UTF-8',
    },
    language: {
        type: String,
        default: 'en-US',
    },
});
</script>

<template>
    <Head :title="title">
        <!-- Charset -->
        <meta charset="charset">
        
        <!-- Viewport -->
        <meta name="viewport" :content="viewport">

        <!-- SEO Meta Tags -->
        <meta name="description" :content="description">
        <meta name="keywords" :content="keywords">
        <meta name="robots" :content="robotsContent">
        <meta name="author" :content="author">
        <meta name="language" :content="language">

        <!-- Canonical URL -->
        <link rel="canonical" :href="canonicalUrl">

        <!-- Open Graph Meta Tags -->
        <meta property="og:title" :content="ogTitle">
        <meta property="og:description" :content="ogDescription">
        <meta property="og:image" :content="ogImage">
        <meta property="og:url" :content="ogUrl">
        <meta property="og:type" content="website">
        <meta property="og:site_name" :content="ogSiteName">
        <meta property="og:locale" :content="ogLocale">

        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" :content="twitterTitle">
        <meta name="twitter:description" :content="twitterDescription">
        <meta name="twitter:image" :content="twitterImage">
    </Head>
</template>
