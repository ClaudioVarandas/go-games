/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./resources/**/*.tsx",
    "./resources/**/*.ts",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        // Colors are defined via CSS variables in app.css
      },
    },
  },
  plugins: [],
}
