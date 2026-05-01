<template>
  <TeacherLayout :user="user">
    <div class="announce-page">
      <h1 class="page-title">Announcements</h1>

      <!-- Create -->
      <div class="card">
        <h3>📢 Create Announcement</h3>
        <form @submit.prevent="post">
          <textarea v-model="form.message" class="textarea" placeholder="Write announcement..." rows="4" />
          <select v-model="form.section_id" class="select-input">
            <option value="">All Sections</option>
            <option v-for="s in sections" :key="s.id" :value="s.id">{{ s.name }}</option>
          </select>
          <button class="btn-post" type="submit" :disabled="form.processing">🚀 Post</button>
        </form>
      </div>

      <!-- List -->
      <div class="card" style="margin-top:20px;">
        <div class="list-header">
          <h3>📋 Announcements</h3>
          <button v-if="announcements.length" class="btn-delete-all" @click="deleteAll">🗑 Delete All</button>
        </div>

        <div v-if="announcements.length === 0" class="empty">No announcements yet</div>

        <div v-for="a in announcements" :key="a.id" class="announce-item">
          <div class="announce-text">
            <p>{{ a.message }}</p>
            <span class="announce-meta">{{ a.section ? a.section.name : 'All Sections' }}</span>
          </div>
          <button class="btn-delete-sm" @click="deleteOne(a.id)">🗑</button>
        </div>
      </div>
    </div>
  </TeacherLayout>
</template>

<script setup>
import { useForm, router } from '@inertiajs/vue3';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

const props = defineProps({ user: Object, sections: Array, announcements: Array });

const form = useForm({ message: '', section_id: '' });

function post() {
  form.post('/teacher/announcements', {
    onSuccess: () => form.reset(),
  });
}

function deleteOne(id) {
  router.delete(`/teacher/announcements/${id}`);
}

function deleteAll() {
  if (confirm('Delete all announcements?')) {
    router.delete('/teacher/announcements');
  }
}
</script>

<style scoped>
.announce-page { max-width: 800px; margin: 0 auto; }
.page-title { font-size: 26px; font-weight: 700; color: #00e5ff; text-align: center; margin-bottom: 24px; }
.card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 24px; }
.card h3 { color: #e0e6ff; font-size: 16px; margin-bottom: 16px; }
.textarea {
  width: 100%;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 8px;
  padding: 12px;
  color: #e0e6ff;
  font-size: 14px;
  resize: vertical;
  outline: none;
  box-sizing: border-box;
  margin-bottom: 12px;
  font-family: inherit;
}
.textarea:focus { border-color: #00e5ff; }
.select-input {
  width: 100%;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 8px;
  padding: 10px 12px;
  color: #e0e6ff;
  font-size: 14px;
  outline: none;
  margin-bottom: 14px;
  box-sizing: border-box;
}
.select-input option { background: #1a2540; }
.btn-post { width: 100%; background: linear-gradient(90deg,#00e5ff,#00ff88); border: none; border-radius: 8px; padding: 12px; color: #0a0f1e; font-weight: 700; font-size: 15px; cursor: pointer; }
.list-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.btn-delete-all { background: #e53935; border: none; border-radius: 8px; color: white; padding: 8px 16px; font-size: 13px; cursor: pointer; }
.empty { color: #6070a0; font-size: 14px; }
.announce-item {
  display: flex;
  gap: 12px;
  align-items: flex-start;
  padding: 14px 0;
  border-left: 3px solid rgba(0,229,255,0.3);
  padding-left: 14px;
  margin-bottom: 8px;
}
.announce-text p { color: #e0e6ff; font-size: 14px; line-height: 1.5; margin-bottom: 4px; }
.announce-meta { color: #6070a0; font-size: 12px; }
.btn-delete-sm { background: #e53935; border: none; border-radius: 6px; color: white; padding: 6px 10px; font-size: 13px; cursor: pointer; flex-shrink: 0; }
</style>
