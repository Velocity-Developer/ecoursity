import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  plugins: [tailwindcss()],
  build: {
    outDir: 'assets',
    emptyOutDir: false, // Menjaga agar aset plugin lain tidak terhapus sengaja
    // Mengaktifkan watch mode melalui config (opsional, tetapi script package.json di atas sudah cukup)
    watch: {
      include: ['src/**/*', 'templates/**/*.php', '**/*.php'],
    },
    rollupOptions: {
      input: 'src/css/main.css',
      output: {
        // Mengunci nama file output agar selalu keluar di assets/css/main.css
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith('main.css')) {
            return 'css/main.css'
          }
          // Fallback untuk aset lain jika ada (gambar/font)
          return 'css/[name][extname]'
        },
      },
    },
  },
})
