<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/" class="text-xl font-bold text-indigo-600">
              Stry
            </router-link>
          </div>
          <div class="flex items-center space-x-4">
            <template v-if="authStore.isAuthenticated">
              <span class="text-sm text-gray-600">
                💎 {{ authStore.gemBalance }} Gems
                <span v-if="authStore.isPremium" class="ml-2 text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded">
                  Premium
                </span>
              </span>
              <span class="text-sm text-gray-700">{{ authStore.user?.name }}</span>
              <button @click="handleLogout" class="text-sm text-red-600 hover:text-red-800">
                Logout
              </button>
            </template>
            <template v-else>
              <router-link to="/login" class="text-sm text-indigo-600 hover:text-indigo-800">
                Sign In
              </router-link>
            </template>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from './stores/auth';

const authStore = useAuthStore();
const router = useRouter();

onMounted(async () => {
  // Fetch user on app load
  if (!authStore.isAuthenticated) {
    await authStore.fetchUser();
  }
});

async function handleLogout() {
  await authStore.logout();
  router.push('/');
}
</script>