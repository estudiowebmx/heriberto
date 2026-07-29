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
                guinda: {
                    50: '#fdf2f3',
                    100: '#fce7e9',
                    200: '#f9d0d6',
                    300: '#f2a5b0',
                    400: '#e87080',
                    500: '#d64053',
                    600: '#A91C2C',
                    700: '#8B1A24',
                    800: '#73161E',
                    900: '#5C1118',
                },
            },
        },
    },

    plugins: [forms],
};
