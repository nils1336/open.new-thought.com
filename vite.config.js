import { defineConfig } from 'vite'
import { resolve } from 'path'

export default defineConfig({
  root: 'site',
  build: {
    outDir: '../dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'site/index.html'),
        assessment: resolve(__dirname, 'site/lead-assessment.html')
      }
    }
  }
})
