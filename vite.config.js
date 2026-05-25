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
        ressourcenRoi: resolve(__dirname, 'site/ressourcen/ki-roi-berechnen.html'),
        ressourcenAgenticAi: resolve(__dirname, 'site/ressourcen/agentic-ai-governance.html'),
        ressourcenTrainingPflicht: resolve(__dirname, 'site/ressourcen/ki-training-pflicht-eu-ai-act.html'),
        ressourcenChefsache: resolve(__dirname, 'site/ressourcen/ki-chefsache-fuehrungskraefte.html'),
        ressourcenTopManagement: resolve(__dirname, 'site/ressourcen/ki-governance-topmanagement.html'),
        ressourcenLiteracy: resolve(__dirname, 'site/ressourcen/ki-literacy-mitarbeiter.html'),
        ressourcenSkillsGap: resolve(__dirname, 'site/ressourcen/ki-skills-gap-2026.html'),
        ressourcenAiFluency: resolve(__dirname, 'site/ressourcen/ai-fluency-kernkompetenz.html'),
        ressourcenPilot: resolve(__dirname, 'site/ressourcen/ki-pilot-skalierung.html'),
        ressourcenSurfaceLevel: resolve(__dirname, 'site/ressourcen/ki-adoption-surface-level.html'),
        ressourcenParadox: resolve(__dirname, 'site/ressourcen/ki-paradox-deutschland.html'),
        ressourcenFractionalCAIO: resolve(__dirname, 'site/ressourcen/fractional-chief-ai-officer.html'),
        ressourcenRoadmap: resolve(__dirname, 'site/ressourcen/ki-roadmap-erstellen.html'),
        ressourcenPolicy: resolve(__dirname, 'site/ressourcen/ki-policy-unternehmen.html'),
        ressourcenVertrieb: resolve(__dirname, 'site/ressourcen/ki-im-vertrieb-use-cases.html'),
        ressourcenRisiko: resolve(__dirname, 'site/ressourcen/ki-risikomanagement-eu-ai-act.html'),
        kiReadinessCheck: resolve(__dirname, 'site/ki-readiness-check.html'),
        kairon: resolve(__dirname, 'site/kairon.html'),
        kaidop: resolve(__dirname, 'site/kaidop.html'),
        kailead: resolve(__dirname, 'site/kailead.html')
      }
    }
  }
})
