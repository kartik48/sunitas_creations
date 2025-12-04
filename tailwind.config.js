import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                serif: ['Georgia', 'serif'],
            },
            colors: {
                'terracotta': '#C85C3F',
                'warm-brown': '#8B4513',
                'cream': '#F5E6D3',
                'ochre': '#CC7722',
                'dark-earth': '#3E2723',
            },
        },
    },

    plugins: [forms],
};
