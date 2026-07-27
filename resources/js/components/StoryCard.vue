<template>
  <router-link :to="{ name: 'story', params: { slug: story.slug } }" class="block">
    <div class="border rounded-lg overflow-hidden shadow hover:shadow-md transition">
      <img v-if="story.cover_image" :src="story.cover_image" alt="Cover" class="w-full h-48 object-cover" />
      <div class="p-4">
        <h3 class="font-bold text-lg truncate">{{ story.title }}</h3>
        <p class="text-sm text-gray-600">by {{ story.author.name }}</p>
        <div class="mt-2 flex justify-between text-sm text-gray-500">
          <span>{{ story.genre }}</span>
          <span>{{ story.chapters_count }} chapters</span>
        </div>
      </div>
    </div>
  </router-link>
</template>

<script setup>
defineProps({ story: Object });
</script>