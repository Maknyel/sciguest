<template>
  <StudentLayout :user="user">
    <div class="notif-page">
      <div class="header-row">
        <h1 class="page-title">Notifications</h1>
        <button v-if="notifications.length" class="btn-delete-all" @click="deleteAll">Delete All</button>
      </div>

      <div v-if="notifications.length === 0" class="empty">No notifications</div>

      <div v-for="n in notifications" :key="n.id" class="notif-card">
        <div class="notif-content">
          <p class="notif-text">{{ n.message }}</p>
          <p class="notif-time">{{ formatDate(n.created_at) }}</p>
        </div>
        <button class="btn-delete" @click="deleteOne(n.id)">Delete</button>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';

const props = defineProps({ notifications: Array, user: Object });

function formatDate(d) {
  return new Date(d).toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function deleteOne(id) {
  router.delete(`/student/notifications/${id}`);
}

function deleteAll() {
  router.delete('/student/notifications');
}
</script>

<style scoped>
.notif-page { max-width: 760px; margin: 0 auto; }
.header-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
.page-title { font-size: 26px; font-weight: 700; color: #00e5ff; }
.btn-delete-all { background: #e53935; border: none; border-radius: 8px; color: white; padding: 8px 18px; font-size: 13px; font-weight: 600; cursor: pointer; }
.empty { color: #6070a0; font-size: 14px; text-align: center; padding: 40px; }
.notif-card {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 12px;
  padding: 18px 20px;
  margin-bottom: 12px;
}
.notif-text { color: #e0e6ff; font-size: 14px; line-height: 1.5; margin-bottom: 6px; }
.notif-time { color: #6070a0; font-size: 12px; }
.btn-delete { background: #e53935; border: none; border-radius: 6px; color: white; padding: 6px 14px; font-size: 12px; cursor: pointer; white-space: nowrap; flex-shrink: 0; }
</style>
