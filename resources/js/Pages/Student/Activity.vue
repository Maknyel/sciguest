<template>
  <StudentLayout :user="user">
    <div class="activity-page">
      <div class="back-wrap">
        <button class="back-btn" @click="$inertia.visit(`/student/module/${activity.module_id}`)">← Back</button>
      </div>

      <h1 class="page-title">{{ activity.name }}</h1>
      <p class="module-label">Module {{ activity.module?.order }} – {{ activity.module?.name }}</p>

      <!-- Tabs -->
      <div class="tabs">
        <button v-for="tab in tabs" :key="tab" :class="['tab', activeTab === tab ? 'active' : '']" @click="activeTab = tab">
          {{ tab }}
        </button>
      </div>

      <!-- Safety Reminders -->
      <div v-if="activeTab === 'Safety'" class="tab-content">
        <div class="card">
          <h3>⚠️ Safety Reminders</h3>
          <p class="whitespace">{{ activity.safety_reminders || 'Always follow proper laboratory safety procedures.' }}</p>
        </div>
      </div>

      <!-- Procedure -->
      <div v-if="activeTab === 'Procedure'" class="tab-content">
        <div class="card">
          <h3>📋 Laboratory Procedure</h3>
          <p class="whitespace">{{ activity.procedure || 'Follow the step-by-step instructions carefully.' }}</p>
        </div>
        <button v-if="!record.activity_completed" class="btn-complete" @click="markComplete">
          ✓ Mark Activity as Complete
        </button>
        <div v-else class="completed-badge">✅ Activity Completed!</div>
      </div>

      <!-- Quiz -->
      <div v-if="activeTab === 'Quiz'" class="tab-content">
        <div v-if="!quizSubmitted && !record.quiz_completed">
          <div v-for="(q, i) in activity.quiz_questions" :key="q.id" class="question-card">
            <p class="question-text">{{ i + 1 }}. {{ q.question }}</p>
            <div class="options">
              <label v-for="opt in q.options" :key="opt" class="option-label">
                <input type="radio" :name="`q_${q.id}`" :value="opt" v-model="answers[q.id]" />
                {{ opt }}
              </label>
            </div>
          </div>
          <button class="btn-submit" @click="submitQuiz" :disabled="!allAnswered">Submit Quiz</button>
        </div>
        <div v-else-if="quizResult || record.quiz_completed" class="quiz-result">
          <div class="result-icon">🏆</div>
          <h3>Quiz Completed!</h3>
          <p class="score">Score: {{ quizResult?.score ?? record.quiz_score }} / {{ quizResult?.max_score ?? record.quiz_max_score }}</p>
        </div>
        <div v-else class="card">
          <p>Complete the procedure first, then take the quiz.</p>
        </div>
      </div>
    </div>
  </StudentLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import StudentLayout from '@/Layouts/StudentLayout.vue';

const props = defineProps({ activity: Object, record: Object, user: Object });

const tabs = ['Safety', 'Procedure', 'Quiz'];
const activeTab = ref('Safety');
const answers = ref({});
const quizSubmitted = ref(false);
const quizResult = ref(null);

const allAnswered = computed(() =>
  props.activity.quiz_questions?.every(q => answers.value[q.id])
);

async function markComplete() {
  await axios.post(`/student/activity/${props.activity.id}/complete`);
  router.reload({ only: ['record'] });
}

async function submitQuiz() {
  const res = await axios.post(`/student/activity/${props.activity.id}/quiz`, { answers: answers.value });
  quizResult.value = res.data;
  quizSubmitted.value = true;
}
</script>

<style scoped>
.activity-page { max-width: 800px; margin: 0 auto; padding-bottom: 40px; }
.back-wrap { margin-bottom: 16px; }
.back-btn { background: rgba(255,220,100,0.9); color: #1a0a00; border: none; padding: 6px 16px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 700; }
.page-title { font-size: 24px; font-weight: 700; color: #00e5ff; margin-bottom: 4px; }
.module-label { color: #8090b0; font-size: 13px; margin-bottom: 20px; }
.tabs { display: flex; gap: 4px; margin-bottom: 20px; }
.tab { padding: 8px 20px; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; background: rgba(255,255,255,0.05); color: #a0b0d0; cursor: pointer; font-size: 13px; transition: all 0.2s; }
.tab.active, .tab:hover { background: rgba(0,229,255,0.15); color: #00e5ff; border-color: #00e5ff; }
.tab-content { }
.card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 24px; margin-bottom: 16px; }
.card h3 { color: #e0e6ff; font-size: 16px; margin-bottom: 12px; }
.whitespace { color: #a0b0d0; font-size: 14px; line-height: 1.7; white-space: pre-wrap; }
.btn-complete { background: linear-gradient(90deg,#00e5ff,#00ff88); border: none; border-radius: 8px; padding: 12px 24px; color: #0a0f1e; font-weight: 700; cursor: pointer; font-size: 14px; margin-top: 8px; }
.completed-badge { background: rgba(0,255,136,0.1); border: 1px solid rgba(0,255,136,0.3); border-radius: 8px; padding: 12px 20px; color: #00ff88; font-weight: 600; margin-top: 8px; }
.question-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 20px; margin-bottom: 16px; }
.question-text { color: #e0e6ff; font-size: 15px; margin-bottom: 12px; font-weight: 600; }
.options { display: flex; flex-direction: column; gap: 8px; }
.option-label { display: flex; align-items: center; gap: 8px; color: #a0b0d0; font-size: 14px; cursor: pointer; }
.option-label input { accent-color: #00e5ff; }
.btn-submit { background: linear-gradient(90deg,#6c63ff,#00e5ff); border: none; border-radius: 8px; padding: 12px 28px; color: white; font-weight: 700; cursor: pointer; font-size: 14px; margin-top: 8px; }
.btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }
.quiz-result { text-align: center; padding: 40px; background: rgba(255,255,255,0.04); border-radius: 12px; border: 1px solid rgba(255,255,255,0.08); }
.result-icon { font-size: 48px; margin-bottom: 12px; }
.quiz-result h3 { color: #00e5ff; font-size: 22px; margin-bottom: 8px; }
.score { color: #00ff88; font-size: 20px; font-weight: 700; }
</style>
