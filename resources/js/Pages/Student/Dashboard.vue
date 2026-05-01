<template>
  <StudentLayout :user="user">
    <div class="dashboard">
      <h1 class="page-title">🔬 Physics Experiments</h1>

      <div class="modules-grid">
        <div v-for="module in modules" :key="module.id" class="module-card" @click="goToModule(module.id)">
          <div class="card-bg" :style="module.image_url ? { backgroundImage: `url(${module.image_url})`, backgroundSize: 'cover', backgroundPosition: 'center' } : { background: moduleGradient(module.order) }">
            <div class="card-overlay" />
          </div>
          <div class="card-badge">Module {{ module.order }}</div>
          <div class="card-body">
            <h3 class="card-title">{{ module.icon }} {{ module.name }}</h3>
            <p class="card-desc">{{ module.description }}</p>
            <div class="progress-bar-wrap">
              <div class="progress-bar" :style="{ width: (progress[module.id] || 0) + '%' }" />
            </div>
            <p class="progress-text">{{ progress[module.id] || 0 }}%</p>
          </div>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';

const props = defineProps({
  modules: Array,
  progress: Object,
  user: Object,
});

function goToModule(id) {
  router.visit(`/student/module/${id}`);
}

function moduleGradient(order) {
  const gradients = [
    'linear-gradient(135deg, #1a3a5c 0%, #2d6a8f 50%, #1a4a7a 100%)',
    'linear-gradient(135deg, #2d1a5c 0%, #6a2d8f 50%, #4a1a7a 100%)',
    'linear-gradient(135deg, #5c2d1a 0%, #8f5a2d 50%, #7a3d1a 100%)',
    'linear-gradient(135deg, #1a5c2d 0%, #2d8f5a 50%, #1a7a3d 100%)',
  ];
  return gradients[(order - 1) % gradients.length];
}
</script>

<style scoped>
.dashboard { padding-bottom: 40px; }
.page-title {
  text-align: center;
  font-size: 28px;
  font-weight: 700;
  color: #00e5ff;
  margin-bottom: 36px;
}
.modules-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 24px;
  max-width: 1100px;
  margin: 0 auto;
}
.module-card {
  border-radius: 16px;
  overflow: hidden;
  cursor: pointer;
  position: relative;
  height: 280px;
  display: flex;
  flex-direction: column;
  border: 1px solid rgba(255,255,255,0.1);
  transition: transform 0.3s, box-shadow 0.3s;
}
.module-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 40px rgba(0,0,0,0.5);
}
.card-bg {
  position: absolute;
  inset: 0;
}
.card-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.7) 100%);
}
.card-badge {
  position: absolute;
  top: 12px;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(255, 220, 100, 0.9);
  color: #1a0a00;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 20px;
  z-index: 1;
}
.card-body {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 16px;
  z-index: 1;
}
.card-title {
  font-size: 15px;
  font-weight: 700;
  color: #ffffff;
  margin-bottom: 6px;
  text-align: center;
}
.card-desc {
  font-size: 12px;
  color: #c0d0e0;
  text-align: center;
  margin-bottom: 10px;
  line-height: 1.4;
}
.progress-bar-wrap {
  height: 4px;
  background: rgba(255,255,255,0.2);
  border-radius: 2px;
  margin-bottom: 6px;
}
.progress-bar {
  height: 100%;
  background: linear-gradient(90deg, #00e5ff, #00ff88);
  border-radius: 2px;
  transition: width 0.5s ease;
}
.progress-text {
  text-align: center;
  font-size: 12px;
  color: #a0b0d0;
}
</style>
