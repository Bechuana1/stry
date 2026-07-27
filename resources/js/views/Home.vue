<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">Stories</h1>
      <div class="flex gap-2">
        <select v-model="genreFilter" @change="loadStories" class="border rounded px-3 py-1">
          <option value="">All Genres</option>
          <option v-for="g in genres" :key="g" :value="g">{{ g }}</option>
        </select>
        <select v-model="sortBy" @change="loadStories" class="border rounded px-3 py-1">
          <option value="latest">Latest</option>
          <option value="popular">Popular</option>
          <option value="completed">Completed</option>
        </select>
      </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <StoryCard
        v-for="story in stories"
        :key="story.id"
        :story="story"
      />
    </div>
    <div v-if="loading" class="text-center py-10">Loading...</div>
    <div v-if="!loading && stories.length === 0" class="text-center py-10 text-gray-500">
      No stories found.
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useStoryStore } from '../stores/story';
import StoryCard from '../components/StoryCard.vue';

const storyStore = useStoryStore();
const stories = ref([]);
const genres = ref([]);
const genreFilter = ref('');
const sortBy = ref('latest');
const loading = ref(false);

async function loadStories() {
  loading.value = true;
  const params = {};
  if (genreFilter.value) params.genre = genreFilter.value;
  if (sortBy.value) params.sort = sortBy.value;
  const data = await storyStore.fetchStories(params);
  stories.value = data.data;
  loading.value = false;
}

async function loadGenres() {
  genres.value = await storyStore.fetchGenres();
}

onMounted(() => {
  loadGenres();
  loadStories();
});
</script>