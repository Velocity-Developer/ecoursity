import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  plugins: [tailwindcss()],
  server: {
    watch: {
      // Memastikan Vite mendeteksi perubahan saat Anda mengedit file PHP
      ignored: ['!**/templates/**/*.php', '!**/*.php'],
    },
  },
  build: {
    outDir: 'assets',
    emptyOutDir: false,
    rollupOptions: {
      input: 'src/css/main.css',
      output: {
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith('main.css')) {
            return 'css/main.css'
          }
          // Fallback untuk aset lain (font, gambar, dll)
          return '[ext]/[name]-[hash][extname]'
        },
      },
    },
  },
})