<template>
  <TeacherLayout :user="user">
    <div class="notif-page">
      <h1 class="page-title">Notifications</h1>

      <div class="card">
        <h3>Completed Students</h3>
        <div v-if="completedStudents.length === 0" class="empty">No completed students</div>
        <table v-else class="table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Section</th>
              <th>Activities Completed</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="s in completedStudents" :key="s.id">
              <td>👤 {{ s.full_name }}</td>
              <td>{{ s.section?.name }}</td>
              <td>{{ s.student_activities?.length }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </TeacherLayout>
</template>

<script setup>
import TeacherLayout from '@/Layouts/TeacherLayout.vue';
const props = defineProps({ user: Object, completedStudents: Array });
</script>

<style scoped>
.notif-page { }
.page-title { font-size: 26px; font-weight: 700; color: #00e5ff; margin-bottom: 24px; text-align: center; }
.card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 24px; }
.card h3 { color: #e0e6ff; font-size: 16px; margin-bottom: 16px; }
.empty { color: #6070a0; font-size: 14px; }
.table { width: 100%; border-collapse: collapse; }
.table th { color: #6070a0; font-size: 12px; text-align: left; padding: 8px 12px; border-bottom: 1px solid rgba(255,255,255,0.06); }
.table td { color: #c0d0e0; font-size: 13px; padding: 10px 12px; border-bottom: 1px solid rgba(255,255,255,0.04); }
</style>
