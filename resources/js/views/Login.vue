<template>
  <div class="max-w-md mx-auto mt-20 p-6 bg-white rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-4">Sign in to Stry</h2>
    <form @submit.prevent="sendLink">
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
        <input
          v-model="email"
          type="email"
          required
          class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
          placeholder="you@example.com"
        />
      </div>
      <button
        type="submit"
        class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition"
        :disabled="loading"
      >
        {{ loading ? 'Sending...' : 'Send Magic Link' }}
      </button>
      <p v-if="message" class="mt-3 text-sm text-green-600">{{ message }}</p>
      <p v-if="error" class="mt-3 text-sm text-red-600">{{ error }}</p>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const email = ref('');
const loading = ref(false);
const message = ref('');
const error = ref('');

async function sendLink() {
  loading.value = true;
  error.value = '';
  message.value = '';
  try {
    await authStore.requestLoginLink(email.value);
    message.value = 'Check your email (or logs) for the login link.';
  } catch (err) {
    error.value = err.response?.data?.message || 'Something went wrong.';
  } finally {
    loading.value = false;
  }
}
</script>