<template>
  <div class="flex items-center justify-between p-3 border-b hover:bg-gray-50">
    <router-link
      :to="{ name: 'reader', params: { chapterSlug: chapter.slug } }"
      class="flex-1"
    >
      <span class="font-medium">{{ chapter.title }}</span>
      <span class="ml-2 text-sm text-gray-500">
        {{ chapter.is_unlocked ? '🔓' : '🔒' }}
      </span>
    </router-link>
    <span class="text-sm text-gray-400">{{ formattedDate }}</span>
  </div>
</template>

<script setup>
const props = defineProps({
  chapter: Object,
  storySlug: String,
});

const formattedDate = computed(() => {
  if (props.chapter.published_at) {
    return new Date(props.chapter.published_at).toLocaleDateString();
  }
  return 'Draft';
});
</script>