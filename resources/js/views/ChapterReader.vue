<template>
  <div class="max-w-3xl mx-auto">
    <div v-if="loading" class="text-center py-20">Loading chapter...</div>
    <div v-else-if="error" class="text-center py-20 text-red-600">{{ error }}</div>
    <div v-else-if="chapter">
      <!-- Navigation -->
      <div class="flex justify-between items-center mb-6">
        <button @click="goToStory" class="text-indigo-600 hover:underline">← Back to Story</button>
        <div class="flex gap-2">
          <button
            v-if="chapter.previous_chapter_id"
            @click="loadChapter(chapter.previous_chapter_id)"
            class="px-4 py-2 border rounded hover:bg-gray-50"
          >
            Prev
          </button>
          <button
            v-if="chapter.next_chapter_id"
            @click="loadChapter(chapter.next_chapter_id)"
            class="px-4 py-2 border rounded hover:bg-gray-50"
          >
            Next
          </button>
        </div>
      </div>

      <!-- Title -->
      <h1 class="text-3xl font-bold mb-2">{{ chapter.title }}</h1>
      <p class="text-gray-500 text-sm mb-6">
        Chapter {{ chapter.chapter_number }} • {{ chapter.word_count }} words • 
        {{ Math.ceil(chapter.estimated_read_seconds / 60) }} min read
      </p>

      <!-- Content or Lock Screen -->
      <div v-if="chapter.is_unlocked">
        <div v-html="chapter.content_html" class="prose prose-lg max-w-none"></div>
        <!-- Reactions and comments will go here later -->
      </div>
      <div v-else>
        <div class="bg-gray-100 rounded-lg p-8 text-center my-8">
          <p class="text-lg font-semibold">🔒 This chapter is locked</p>
          <p class="text-gray-600 mt-2">
            Unlock it with 2 Gems or wait until {{ formatDate(chapter.locked_until) }}
          </p>
          <button
            @click="handleUnlock"
            class="mt-4 bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700"
            :disabled="unlocking"
          >
            {{ unlocking ? 'Unlocking...' : 'Unlock with 2 Gems' }}
          </button>
          <p v-if="gemBalance < 2" class="text-red-500 text-sm mt-2">
            You need 2 Gems. You have {{ gemBalance }}.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useReaderStore } from '../stores/reader';
import { useAuthStore } from '../stores/auth';

const route = useRoute();
const router = useRouter();
const readerStore = useReaderStore();
const authStore = useAuthStore();

const chapter = ref(null);
const loading = ref(false);
const error = ref(null);
const unlocking = ref(false);

const gemBalance = computed(() => authStore.gemBalance);

async function loadChapter(slug) {
  loading.value = true;
  error.value = null;
  try {
    const data = await readerStore.fetchChapter(slug);
    chapter.value = data;
    // If the chapter is unlocked, we might want to update the URL
    if (data.slug && route.params.chapterSlug !== data.slug) {
      router.replace({ name: 'reader', params: { chapterSlug: data.slug } });
    }
  } catch (err) {
    error.value = 'Failed to load chapter.';
  } finally {
    loading.value = false;
  }
}

async function handleUnlock() {
  if (unlocking.value) return;
  if (gemBalance < 2) {
    alert('You need more Gems. Please purchase Gems.');
    return;
  }
  unlocking.value = true;
  try {
    await readerStore.unlockChapter(chapter.value.id);
    // Refresh chapter data after unlock
    await loadChapter(chapter.value.slug);
    // Refresh auth store to update gem balance
    await authStore.fetchUser();
  } catch (err) {
    alert('Failed to unlock chapter.');
  } finally {
    unlocking.value = false;
  }
}

function goToStory() {
  if (chapter.value?.story_slug) {
    router.push({ name: 'story', params: { slug: chapter.value.story_slug } });
  } else {
    router.push({ name: 'home' });
  }
}

function formatDate(date) {
  return new Date(date).toLocaleDateString();
}

// Watch for route param changes (when user navigates to a different chapter)
watch(() => route.params.chapterSlug, (newSlug) => {
  if (newSlug) loadChapter(newSlug);
}, { immediate: true });
</script>