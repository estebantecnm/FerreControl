// @ts-check
import { defineConfig } from 'astro/config';
import vue from '@astrojs/vue';
import tailwindcss from '@tailwindcss/vite';

// https://astro.build/config
export default defineConfig({
  output: 'server', // <--- ESTO FALTA. Permite rutas dinámicas como [id].astro
  integrations: [vue()],
  vite: {
    plugins: [tailwindcss()]
  }
});