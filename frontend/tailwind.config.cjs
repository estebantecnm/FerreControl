/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './src/**/*.{astro,html,js,jsx,md,mdx,svelte,ts,tsx,vue}',
  ],
  theme: {
    extend: {
      colors: {
        // Colores extraídos de tu referencia
        'ferre-bg': '#0c0f14',      // Fondo profundo
        'ferre-surface': '#12161f', // Capas intermedias
        'ferre-card': '#181d28',    // Tarjetas y tablas
        'ferre-border': '#1e2736',  // Bordes sutiles
        'ferre-accent': '#f59e0b',  // Naranja principal
        'ferre-accent-dark': '#d97706',
        'ferre-text': '#cbd5e1',    // Texto legible en modo oscuro
        'ferre-muted': '#4b5a6e',   // Texto secundario
      },
      fontFamily: {
        // Fuentes presentes en tu diseño
        sans: ['"IBM Plex Sans"', 'sans-serif'],
        bebas: ['"Bebas Neue"', 'cursive'],
        mono: ['"IBM Plex Mono"', 'monospace'],
      },
    },
  },
  plugins: [],
}