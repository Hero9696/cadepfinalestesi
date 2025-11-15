
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { defineProps } from 'vue'

const props = defineProps({
  fileContent: {
    type: String,
    required: true,
  },
  filePath: {
    type: String,
    required: true,
  },
  errors: Object,
})

const form = useForm({
  content: props.fileContent,
})

const submit = () => {
  form.put(route('chatbot.workflow.update'), {
    preserveScroll: true,
    onSuccess: () => {
      // Maybe show a toast notification
    },
    onError: () => {
      // Maybe show a toast notification for error
    },
  })
}
</script>

<template>
  <AppLayout title="Editor de Workflow del Chatbot">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Editor de Workflow del Chatbot
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
          <div class="p-6">
            <!-- Warning Message -->
            <div class="mb-4 rounded-lg border-l-4 border-yellow-400 bg-yellow-50 p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 3.001-1.742 3.001H4.42c-1.53 0-2.493-1.667-1.743-3.001l5.58-9.92zM10 13a1 1 0 110-2 1 1 0 010 2zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm text-yellow-700">
                    <b>Advertencia:</b> Estás editando un archivo crítico del sistema del chatbot (<code>{{ filePath }}</code>).
                    <br>
                    Cualquier error de sintaxis puede causar que el chatbot deje de funcionar. Procede con extrema precaución.
                    <br>
                    <b>Después de guardar, debes reiniciar el chatbot manualmente para que los cambios se apliquen.</b>
                  </p>
                </div>
              </div>
            </div>

            <!-- Flash Messages -->
            <div v-if="$page.props.flash.success" class="mb-4 rounded-md bg-green-50 p-4">
                <p class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</p>
            </div>
            <div v-if="$page.props.flash.error" class="mb-4 rounded-md bg-red-50 p-4">
                <p class="text-sm font-medium text-red-800">{{ $page.props.flash.error }}</p>
            </div>

            <form @submit.prevent="submit">
              <div>
                <label for="workflow-content" class="block text-sm font-medium text-gray-700">Contenido de <code>app.js</code></label>
                <textarea
                  id="workflow-content"
                  v-model="form.content"
                  rows="30"
                  class="mt-1 block w-full rounded-md border-gray-300 font-mono text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                ></textarea>
                <div v-if="form.errors.content" class="mt-2 text-sm text-red-600">{{ form.errors.content }}</div>
              </div>

              <div class="mt-6 flex items-center justify-end">
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                >
                  <span v-if="form.processing">Guardando...</span>
                  <span v-else>Guardar Cambios</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
