<template>
  <TeacherLayout :user="user">
    <div class="am-page">
      <h1 class="page-title">Account Management</h1>

      <!-- Filters -->
      <div class="filter-bar">
        <input v-model="search" type="text" class="search-input" placeholder="Search student..." @keyup.enter="applyFilter" />
        <select v-model="sectionFilter" class="select-input">
          <option value="">All Sections</option>
          <option v-for="s in sections" :key="s.id" :value="s.id">{{ s.name }}</option>
        </select>
        <button class="btn-apply" @click="applyFilter">Apply</button>
      </div>

      <button class="btn-delete-all" @click="deleteAll">🗑 Delete All Students</button>

      <!-- Student List -->
      <div class="card" style="margin-top:16px;">
        <h3>📋 Student List</h3>
        <table class="table">
          <thead>
            <tr><th>Name</th><th>Section</th><th>Action</th></tr>
          </thead>
          <tbody>
            <tr v-if="students.length === 0">
              <td colspan="3" class="empty">No students found</td>
            </tr>
            <tr v-for="s in students" :key="s.id">
              <td>👤 {{ s.full_name }}</td>
              <td>{{ s.section?.name }}</td>
              <td><button class="btn-delete" @click="deleteStudent(s.id)">Delete</button></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pending Approvals -->
      <div class="card" style="margin-top:20px;">
        <h3>⏳ Pending Approvals</h3>
        <table class="table">
          <thead>
            <tr><th>Name</th><th>Section</th><th>Action</th></tr>
          </thead>
          <tbody>
            <tr v-if="pending.length === 0">
              <td colspan="3" class="empty">No pending approvals</td>
            </tr>
            <tr v-for="s in pending" :key="s.id">
              <td>👤 {{ s.full_name }}</td>
              <td>{{ s.section?.name }}</td>
              <td class="action-btns">
                <button class="btn-approve" @click="approve(s.id)">Approve</button>
                <button class="btn-decline" @click="decline(s.id)">Decline</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </TeacherLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

const props = defineProps({ user: Object, students: Array, pending: Array, sections: Array, filters: Object });

const search = ref(props.filters?.search || '');
const sectionFilter = ref(props.filters?.section_id || '');

function applyFilter() {
  router.get('/teacher/account-management', { search: search.value, section_id: sectionFilter.value }, { preserveState: true });
}

function approve(id) {
  router.post(`/teacher/account-management/${id}/approve`);
}

function decline(id) {
  if (confirm('Decline this student?')) router.delete(`/teacher/account-management/${id}/decline`);
}

function deleteStudent(id) {
  if (confirm('Delete this student?')) router.delete(`/teacher/account-management/${id}`);
}

function deleteAll() {
  if (confirm('Delete ALL students? This cannot be undone.')) {
    router.delete('/teacher/account-management', { data: { section_id: sectionFilter.value } });
  }
}
</script>

<style scoped>
.am-page { }
.page-title { font-size: 26px; font-weight: 700; color: #00e5ff; text-align: center; margin-bottom: 24px; }
.filter-bar { display: flex; gap: 10px; margin-bottom: 12px; }
.search-input, .select-input {
  flex: 1;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 8px;
  padding: 10px 12px;
  color: #e0e6ff;
  font-size: 13px;
  outline: none;
}
.search-input::placeholder { color: #6070a0; }
.select-input option { background: #1a2540; }
.select-input { flex: 0 0 200px; }
.btn-apply { background: linear-gradient(90deg,#00e5ff,#00ff88); border: none; border-radius: 8px; padding: 10px 20px; color: #0a0f1e; font-weight: 700; cursor: pointer; }
.btn-delete-all { width: 100%; background: #e53935; border: none; border-radius: 8px; padding: 12px; color: white; font-weight: 700; font-size: 14px; cursor: pointer; }
.card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 24px; }
.card h3 { color: #e0e6ff; font-size: 16px; margin-bottom: 16px; }
.table { width: 100%; border-collapse: collapse; }
.table th { color: #6070a0; font-size: 12px; text-align: center; padding: 8px 12px; border-bottom: 1px solid rgba(255,255,255,0.06); }
.table td { color: #c0d0e0; font-size: 13px; padding: 10px 12px; border-bottom: 1px solid rgba(255,255,255,0.04); text-align: center; }
.empty { color: #6070a0; font-style: italic; }
.btn-delete { background: #e53935; border: none; border-radius: 6px; color: white; padding: 6px 14px; font-size: 12px; cursor: pointer; }
.btn-approve { background: #43a047; border: none; border-radius: 6px; color: white; padding: 6px 14px; font-size: 12px; cursor: pointer; margin-right: 6px; }
.btn-decline { background: #e53935; border: none; border-radius: 6px; color: white; padding: 6px 14px; font-size: 12px; cursor: pointer; }
.action-btns { display: flex; justify-content: center; gap: 6px; }
</style>
