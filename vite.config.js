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
        assessment: resolve(__dirname, 'site/lead-assessment.html'),
        kaironExecutive: resolve(__dirname, 'site/kairon-executive.html'),
        brochure: resolve(__dirname, 'site/brochure.html'),
        ressourcen: resolve(__dirname, 'site/ressourcen.html'),
        ressourcenWarum: resolve(__dirname, 'site/ressourcen/warum-ki-projekte-scheitern.html'),
        ressourcenWas: resolve(__dirname, 'site/ressourcen/was-ist-ki-governance.html'),
        ressourcenStrategie: resolve(__dirname, 'site/ressourcen/ki-strategie-vs-ki-governance.html'),
        ressourcenWiderstaende: resolve(__dirname, 'site/ressourcen/ki-widerstaende-mitarbeitende.html'),
        ressourcenLeitfaden: resolve(__dirname, 'site/ressourcen/ki-adoption-mittelstand-leitfaden.html'),
        ressourcenOrganisation: resolve(__dirname, 'site/ressourcen/ki-organisation-rollen-struktur.html'),
        ressourcenOrchestrator: resolve(__dirname, 'site/ressourcen/was-ist-ein-ki-orchestrator.html'),
        ressourcenRoi: resolve(__dirname, 'site/ressourcen/ki-roi-berechnen.html')
      }
    }
  }
})
