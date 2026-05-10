import { defineConfig } from 'vite'
import { resolve } from 'path'
import { fileURLToPath } from 'url'

const __dirname = fileURLToPath(new URL('.', import.meta.url))

export default defineConfig({
  root: resolve(__dirname, 'site'),
  build: {
    outDir: resolve(__dirname, 'dist'),
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'site/index.html'),
        programme: resolve(__dirname, 'site/programme.html'),
        academy: resolve(__dirname, 'site/academy.html'),
        assessment: resolve(__dirname, 'site/lead-assessment.html')
      }
    }
  }
})
