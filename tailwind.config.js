import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                verde: {
                    50: '#f0fbf0',
                    100: '#d9f2d4',
                    200: '#b5e5ad',
                    300: '#c7de8d',
                    400: '#6fc460',
                    500: '#54b848',
                    600: '#00ae41',
                    700: '#008a34',
                    800: '#006d29',
                    900: '#00521f',
                },
            },
        },
    },

    plugins: [forms],
};
