/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_LARAVEL_API?: string
  readonly VITE_SOCKET_URL?: string
  readonly VITE_COURSE_ID?: string
}

interface ImportMeta {
  readonly env: ImportMetaEnv
}

declare module '*.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<object, object, unknown>
  export default component
}
