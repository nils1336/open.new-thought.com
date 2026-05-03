import { defineConfig } from 'vite'

export default defineConfig({
  root: 'site',
  build: {
    outDir: '../dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: 'site/index.html',
        assessment: 'site/lead-assessment.html'
      }
    }
  }
})
