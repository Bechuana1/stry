<template>
  <div v-if="story">
    <div class="mb-6">
      <h1 class="text-4xl font-bold">{{ story.title }}</h1>
      <p class="text-gray-600">by {{ story.author.name }}</p>
      <div class="mt-2 flex gap-2">
        <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded">{{ story.genre }}</span>
        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded">{{ story.status }}</span>
      </div>
      <p class="mt-4 text-gray-700">{{ story.synopsis }}</p>
    </div>
    <div class="border-t pt-4">
      <h2 class="text-2xl font-semibold mb-4">Table of Contents</h2>
      <div v-if="story.chapters && story.chapters.length" class="space-y-2">
        <ChapterListItem
          v-for="chapter in story.chapters"
          :key="chapter.id"
          :chapter="chapter"
          :story-slug="story.slug"
        />
      </div>
      <p v-else class="text-gray-500">No chapters published yet.</p>
    </div>
  </div>
  <div v-else class="text-center py-10">Loading...</div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useStoryStore } from '../stores/story';
import ChapterListItem from '../components/ChapterListItem.vue';

const route = useRoute();
const storyStore = useStoryStore();
const story = ref(null);

onMounted(async () => {
  story.value = await storyStore.fetchStory(route.params.slug);
});
</script>