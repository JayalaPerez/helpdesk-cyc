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
      colors: {
        brand: {
          DEFAULT: '#0B4D8F',   // primario
          50:  '#eef6ff',
          100: '#d9ecff',
          200: '#b3d6ff',
          300: '#84baff',
          400: '#5498f2',
          500: '#2f7bdd',
          600: '#0B4D8F',      // usa este como principal
          700: '#083e72',
          800: '#062f56',
          900: '#041f39',
        },
        accent: '#F39C12',      // acento
        neutral: '#0f172a',     // texto fuerte
      },
      fontFamily: {
        sans: ['Inter', ...defaultTheme.fontFamily.sans],
      },
      borderRadius: {
        xl: '1rem',
        '2xl': '1.25rem',
      },
      boxShadow: {
        soft: '0 10px 25px rgba(0,0,0,0.06)',
      },
    },
  },

  plugins: [forms],
};
