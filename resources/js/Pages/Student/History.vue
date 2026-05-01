<template>
  <StudentLayout :user="user">
    <div class="history-page">
      <h1 class="page-title">History</h1>

      <div class="card">
        <h3>My Activity</h3>
        <div v-if="history.length === 0" class="empty">No history</div>
        <table v-else class="history-table">
          <thead>
            <tr>
              <th>Activity</th>
              <th>Module</th>
              <th>Score</th>
              <th>Completed</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="h in history" :key="h.id">
              <td>{{ h.activity?.icon }} {{ h.activity?.name }}</td>
              <td>{{ h.activity?.module?.name }}</td>
              <td>{{ h.quiz_score }}/{{ h.quiz_max_score }}</td>
              <td>{{ formatDate(h.completed_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import StudentLayout from '@/Layouts/StudentLayout.vue';

const props = defineProps({ history: Array, user: Object });

function formatDate(d) {
  if (!d) return '—';
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}
</script>

<style scoped>
.history-page { max-width: 900px; margin: 0 auto; }
.page-title { font-size: 26px; font-weight: 700; color: #00e5ff; margin-bottom: 24px; }
.card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 24px; }
.card h3 { color: #e0e6ff; font-size: 17px; margin-bottom: 16px; }
.empty { color: #6070a0; font-size: 14px; }
.history-table { width: 100%; border-collapse: collapse; }
.history-table th { color: #6070a0; font-size: 13px; text-align: left; padding: 8px 12px; border-bottom: 1px solid rgba(255,255,255,0.06); }
.history-table td { color: #c0d0e0; font-size: 13px; padding: 10px 12px; border-bottom: 1px solid rgba(255,255,255,0.04); }
.history-table tr:last-child td { border-bottom: none; }
</style>
