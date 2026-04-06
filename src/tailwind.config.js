import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    safelist: [
        // Backgrounds
        "bg-beeyellow",
        // Level badge gradients (returned dynamically from User::levelBadgeColor())
        "from-zinc-500", "to-zinc-400",
        "from-green-500", "to-emerald-400",
        "from-blue-500", "to-cyan-400",
        "from-violet-500", "to-purple-600",
        "from-yellow-400", "to-amber-500",
        "from-red-500", "to-orange-400",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                "beeyellow": "#FFD91A",
            },
        },
    },

    plugins: [forms],
};
