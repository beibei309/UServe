import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import daisyui from 'daisyui';

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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                upsi: {
                    red: '#C41E3A',      // More vibrant UPSI red for action & attention
                    blue: '#367588',     // Professional UPSI blue for trust & navigation
                    gold: '#F59E0B',     // Brighter UPSI gold for highlights & verification
                    light: '#FFFFFF',    // Pure white for clean backgrounds
                    'light-gray': '#F9FAFB', // Very light gray for subtle backgrounds
                    dark: '#111827',     // Dark for primary text
                    'text-primary': '#1F2937', // Primary text color for maximum readability
                },
                            'custom-teal': '#367588', // <- add your color here

            },
        },
    },

    plugins: [forms, daisyui],

    daisyui: {
        themes: [
            {
                upsi: {
                    primary: '#C41E3A',    // Vibrant UPSI Red for CTAs and action buttons
                    secondary: '#F59E0B',  // Bright UPSI Gold for badges & secondary actions
                    accent: '#367588',     // Professional UPSI Blue for links and accents
                    neutral: '#1F2937',    // Dark gray for neutral elements
                    'base-100': '#FFFFFF', // Pure white for main backgrounds
                    'base-200': '#F9FAFB', // Very light gray for subtle backgrounds
                    'base-300': '#F3F4F6', // Light gray for borders and dividers
                    info: '#367588',       // UPSI Blue for informational elements
                    success: '#10B981',    // Green for success states
                    warning: '#F59E0B',    // UPSI Gold for warnings
                    error: '#C41E3A',      // UPSI Red for errors
                },
            },
            "dark",
        ],
    },
}
