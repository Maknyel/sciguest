<template>
  <div class="stars-container">
    <div v-for="star in stars" :key="star.id" class="star"
      :style="{ left: star.x + '%', top: star.y + '%', width: star.size + 'px', height: star.size + 'px', animationDelay: star.delay + 's', animationDuration: star.duration + 's' }" />
    <svg class="lines-svg" :viewBox="`0 0 ${w} ${h}`" preserveAspectRatio="none">
      <line v-for="(line, i) in lines" :key="i"
        :x1="line.x1" :y1="line.y1" :x2="line.x2" :y2="line.y2"
        stroke="rgba(255,255,255,0.08)" stroke-width="1" />
    </svg>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const stars = ref([]);
const lines = ref([]);
const w = ref(1200);
const h = ref(800);

onMounted(() => {
  const count = 60;
  const pts = [];
  for (let i = 0; i < count; i++) {
    const x = Math.random() * 100;
    const y = Math.random() * 100;
    pts.push({ x, y });
    stars.value.push({
      id: i,
      x,
      y,
      size: Math.random() * 3 + 1,
      delay: Math.random() * 4,
      duration: Math.random() * 3 + 3,
    });
  }
  for (let i = 0; i < pts.length; i++) {
    for (let j = i + 1; j < pts.length; j++) {
      const dx = pts[i].x - pts[j].x;
      const dy = pts[i].y - pts[j].y;
      const dist = Math.sqrt(dx * dx + dy * dy);
      if (dist < 15) {
        lines.value.push({
          x1: (pts[i].x / 100) * w.value,
          y1: (pts[i].y / 100) * h.value,
          x2: (pts[j].x / 100) * w.value,
          y2: (pts[j].y / 100) * h.value,
        });
      }
    }
  }
});
</script>

<style scoped>
.stars-container {
  position: fixed;
  inset: 0;
  pointer-events: none;
  z-index: 0;
  overflow: hidden;
}
.star {
  position: absolute;
  border-radius: 50%;
  background: white;
  opacity: 0.7;
  animation: twinkle linear infinite;
}
@keyframes twinkle {
  0%, 100% { opacity: 0.7; transform: scale(1); }
  50% { opacity: 0.2; transform: scale(0.6); }
}
.lines-svg {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
}
</style>
