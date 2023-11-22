/** @type {import('tailwindcss').Config} */
module.exports = {
  corePlugins: {
      preflight: false,
  },
  content: [
      "./assets/**/*.js",
      "./templates/**/*.html.twig",
      "./node_modules/flowbite/**/*.js"
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('flowbite/plugin')({
        charts: true,
    }),
  ],
}

