<template>
  <StudentLayout :user="user">
    <div class="module-page">
      <div class="back-wrap">
        <Link href="/student/dashboard" class="back-btn">← Back</Link>
      </div>

      <h1 class="page-title">{{ module.icon }} Module {{ module.order }} – {{ module.name }}</h1>

      <div class="activities-grid">
        <div v-for="activity in activities" :key="activity.id"
          class="activity-card"
          :class="{ locked: activity.is_locked }"
          @click="!activity.is_locked && goToActivity(activity.id)">

          <div class="card-bg-wrap">
            <div class="card-bg" :style="activity.image_url ? { backgroundImage: `url(${activity.image_url})`, backgroundSize: 'cover', backgroundPosition: 'center' } : { background: cardGradient(activity.order) }" />
            <div class="card-overlay" />
          </div>

          <div v-if="activity.is_locked" class="lock-badge">🔒 Locked</div>

          <div class="card-body">
            <span class="activity-badge">Activity {{ activity.order }}</span>
            <h3 class="activity-name">{{ activity.name }}</h3>
            <div class="status-row" v-if="!activity.is_locked">
              <span :class="['status', activity.activity_completed ? 'done' : 'pending']">
                Activity: {{ activity.activity_completed ? 'Complete' : 'Incomplete' }}
              </span>
              <span :class="['status', activity.quiz_completed ? 'done' : 'pending']">
                Quiz: {{ activity.quiz_completed ? activity.quiz_score + '/' + activity.quiz_max_score : 'Incomplete' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';

const props = defineProps({ module: Object, activities: Array, user: Object });

function goToActivity(id) {
  router.visit(`/student/activity/${id}`);
}

function cardGradient(order) {
  const g = [
    'linear-gradient(135deg,#1a2a4c,#2d4a8f)',
    'linear-gradient(135deg,#2a1a4c,#6a2d8f)',
    'linear-gradient(135deg,#4c2a1a,#8f5a2d)',
    'linear-gradient(135deg,#1a4c2a,#2d8f5a)',
    'linear-gradient(135deg,#4c1a2a,#8f2d5a)',
    'linear-gradient(135deg,#1a4c4c,#2d8f8f)',
    'linear-gradient(135deg,#3a3a1a,#7a7a2d)',
    'linear-gradient(135deg,#1a3a3a,#2d7a7a)',
  ];
  return g[(order - 1) % g.length];
}
</script>

<style scoped>
.module-page { padding-bottom: 40px; }
.back-wrap { margin-bottom: 16px; }
.back-btn {
  display: inline-block;
  background: rgba(255,220,100,0.9);
  color: #1a0a00;
  font-size: 13px;
  font-weight: 700;
  padding: 6px 16px;
  border-radius: 8px;
  text-decoration: none;
}
.page-title { text-align: center; font-size: 26px; font-weight: 700; color: #00e5ff; margin-bottom: 32px; }
.activities-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 20px;
}
.activity-card {
  border-radius: 14px;
  overflow: hidden;
  position: relative;
  height: 220px;
  display: flex;
  flex-direction: column;
  border: 1px solid rgba(255,255,255,0.1);
  cursor: pointer;
  transition: transform 0.3s, box-shadow 0.3s;
}
.activity-card:not(.locked):hover { transform: translateY(-5px); box-shadow: 0 12px 32px rgba(0,0,0,0.5); }
.activity-card.locked { cursor: default; opacity: 0.75; }
.card-bg-wrap { position: absolute; inset: 0; }
.card-bg { position: absolute; inset: 0; }
.card-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.75)); }
.lock-badge {
  position: absolute;
  top: 10px;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(50,50,80,0.9);
  color: #c0c0d0;
  font-size: 12px;
  padding: 4px 12px;
  border-radius: 20px;
  z-index: 1;
}
.card-body { position: absolute; bottom: 0; left: 0; right: 0; padding: 14px; z-index: 1; }
.activity-badge {
  display: inline-block;
  background: rgba(200,200,255,0.15);
  color: #c0d0ff;
  font-size: 10px;
  padding: 2px 10px;
  border-radius: 12px;
  margin-bottom: 6px;
}
.activity-name { font-size: 13px; font-weight: 700; color: #fff; text-align: center; margin-bottom: 8px; }
.status-row { display: flex; flex-direction: column; gap: 2px; }
.status { font-size: 11px; }
.status.done { color: #00ff88; }
.status.pending { color: #ff9944; }
</style>
