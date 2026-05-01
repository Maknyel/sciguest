<template>
  <TeacherLayout :user="user">
    <div class="am-page">
      <h1 class="page-title">Activity Management</h1>

      <!-- Add Module Form -->
      <div class="card add-module-card">
        <button class="toggle-btn" @click="showModuleForm = !showModuleForm">
          <span>➕ Add New Module</span>
          <span>{{ showModuleForm ? '▲' : '▼' }}</span>
        </button>
        <div v-if="showModuleForm" class="form-body">
          <div class="form-row">
            <div class="field">
              <label>Icon (emoji)</label>
              <input v-model="moduleForm.icon" type="text" placeholder="⚙" maxlength="4" />
            </div>
            <div class="field flex-3">
              <label>Module Name *</label>
              <input v-model="moduleForm.name" type="text" placeholder="e.g. Force and Motion" />
            </div>
          </div>
          <div class="field">
            <label>Description</label>
            <textarea v-model="moduleForm.description" placeholder="Short description..." rows="2" />
          </div>
          <div class="field">
            <label>Cover Image</label>
            <label class="img-upload-box">
              <div v-if="modulePreview" class="img-preview" :style="{ backgroundImage: `url(${modulePreview})` }" />
              <div v-else class="img-placeholder">🖼 Click to upload image</div>
              <input type="file" accept="image/*" class="hidden-file" @change="onModuleImage" />
            </label>
          </div>
          <div class="form-actions">
            <button class="btn-cancel" @click="showModuleForm = false; resetModuleForm()">Cancel</button>
            <button class="btn-save" @click="saveModule" :disabled="!moduleForm.name || moduleForm.processing">
              {{ moduleForm.processing ? 'Creating…' : '✓ Create Module' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Modules List -->
      <div v-for="module in modules" :key="module.id" class="module-section">
        <div class="module-header">
          <div class="module-title-wrap">
            <div v-if="module.image_url" class="module-thumb" :style="{ backgroundImage: `url(${module.image_url})` }" />
            <h2 class="module-title">{{ module.icon }} Module {{ module.order }} — {{ module.name }}</h2>
          </div>
          <div class="module-actions">
            <label class="btn-upload" :title="'Upload module image'">
              🖼 Image
              <input type="file" accept="image/*" class="hidden-file" @change="uploadModuleImage(module, $event)" />
            </label>
            <button class="btn-add-activity" @click="toggleAddActivity(module.id)">➕ Add Activity</button>
            <button class="btn-delete-mod" @click="deleteModule(module.id)">🗑 Delete Module</button>
          </div>
        </div>

        <!-- Add Activity Form (per module) -->
        <div v-if="addActivityFor === module.id" class="card activity-form-card">
          <h3>New Activity in Module {{ module.order }}</h3>
          <div class="field">
            <label>Activity Name *</label>
            <input v-model="activityForm.name" type="text" placeholder="e.g. ROLL, ROLL AND AWAY!" />
          </div>
          <div class="field">
            <label>Cover Image</label>
            <label class="img-upload-box">
              <div v-if="activityPreview" class="img-preview" :style="{ backgroundImage: `url(${activityPreview})` }" />
              <div v-else class="img-placeholder">🖼 Click to upload image</div>
              <input type="file" accept="image/*" class="hidden-file" @change="onActivityImage" />
            </label>
          </div>
          <div class="field">
            <label>Description</label>
            <textarea v-model="activityForm.description" placeholder="Brief description..." rows="2" />
          </div>
          <div class="field">
            <label>Procedure</label>
            <textarea v-model="activityForm.procedure" placeholder="Step-by-step procedure..." rows="4" />
          </div>
          <div class="field">
            <label>Safety Reminders</label>
            <textarea v-model="activityForm.safety_reminders" placeholder="Safety reminders..." rows="3" />
          </div>
          <div class="form-actions">
            <button class="btn-cancel" @click="addActivityFor = null; resetActivityForm()">Cancel</button>
            <button class="btn-save" @click="saveActivity(module.id)" :disabled="!activityForm.name || activityForm.processing">
              {{ activityForm.processing ? 'Creating…' : '✓ Create Activity' }}
            </button>
          </div>
        </div>

        <!-- Activities -->
        <div v-for="activity in module.activities" :key="activity.id" class="activity-card">
          <div class="activity-top">
            <div class="activity-info">
              <div class="activity-name-row">
                <div v-if="activity.image_url" class="activity-thumb" :style="{ backgroundImage: `url(${activity.image_url})` }" />
                <p class="activity-name">{{ activity.name }}</p>
              </div>
              <div class="badges">
                <span :class="['badge', activity.is_locked ? 'locked' : 'unlocked']">
                  {{ activity.is_locked ? '🔒 Locked' : '🔓 Unlocked' }}
                </span>
                <span class="badge deadline-badge">
                  {{ activity.deadline_enabled && activity.deadline_at ? '🕐 ' + formatDate(activity.deadline_at) : '🚫 No deadline' }}
                </span>
              </div>
            </div>
            <div class="activity-controls">
              <label class="btn-icon-sm upload-img" title="Upload activity image">
                🖼
                <input type="file" accept="image/*" class="hidden-file" @change="uploadActivityImage(activity, $event)" />
              </label>
              <label class="toggle">
                <input type="checkbox" :checked="!activity.is_locked" @change="toggleLock(activity)" />
                <span class="slider" />
              </label>
              <button class="btn-icon-sm" @click="toggleQuizSection(activity.id)" title="Manage Quiz">📝</button>
              <button class="btn-icon-sm danger" @click="deleteActivity(activity.id)" title="Delete Activity">🗑</button>
            </div>
          </div>

          <!-- Deadline -->
          <div class="deadline-section">
            <label class="checkbox-label">
              <input type="checkbox" v-model="activity.deadline_enabled" @change="saveActivity_(activity)" />
              Enable Deadline
            </label>
            <div v-if="activity.deadline_enabled" class="deadline-row">
              <input type="datetime-local" v-model="activity.deadline_at_input" class="date-input" />
              <button class="btn-save-sm" @click="saveActivity_(activity)">💾 Save Deadline</button>
            </div>
          </div>

          <!-- Quiz Questions Section -->
          <div v-if="quizOpenFor === activity.id" class="quiz-section">
            <div class="quiz-header">
              <span>📝 Quiz Questions ({{ activity.quiz_questions?.length || 0 }})</span>
              <button class="btn-add-q" @click="toggleAddQuestion(activity.id)">➕ Add Question</button>
            </div>

            <!-- Existing Questions -->
            <div v-for="(q, qi) in activity.quiz_questions" :key="q.id" class="question-row">
              <div class="q-text">{{ qi + 1 }}. {{ q.question }}</div>
              <div class="q-options">
                <span v-for="opt in q.options" :key="opt"
                  :class="['opt', opt === q.correct_answer ? 'correct' : '']">
                  {{ opt }}
                </span>
              </div>
              <button class="btn-del-q" @click="deleteQuestion(q.id)">✕</button>
            </div>
            <p v-if="!activity.quiz_questions?.length" class="empty-q">No questions yet.</p>

            <!-- Add Question Form -->
            <div v-if="addQuestionFor === activity.id" class="add-question-form">
              <div class="field">
                <label>Question *</label>
                <textarea v-model="questionForm.question" placeholder="Enter question..." rows="2" />
              </div>
              <div class="field">
                <label>Options (one per line, min 2) *</label>
                <textarea v-model="questionForm.optionsText"
                  placeholder="Option A&#10;Option B&#10;Option C&#10;Option D" rows="4" />
              </div>
              <div class="field">
                <label>Correct Answer * (must match one option exactly)</label>
                <input v-model="questionForm.correct_answer" type="text" placeholder="Option A" />
              </div>
              <div class="form-actions">
                <button class="btn-cancel" @click="addQuestionFor = null; resetQuestionForm()">Cancel</button>
                <button class="btn-save" @click="saveQuestion(activity.id)"
                  :disabled="!questionForm.question || !questionForm.optionsText || !questionForm.correct_answer">
                  ✓ Add Question
                </button>
              </div>
            </div>
          </div>
        </div>

        <p v-if="!module.activities?.length" class="no-activities">No activities yet. Click "Add Activity" to create one.</p>
      </div>

      <p v-if="!modules.length" class="empty-modules">No modules yet. Create one above.</p>
    </div>
  </TeacherLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

const props = defineProps({ user: Object, modules: Array });

// UI state
const showModuleForm = ref(false);
const addActivityFor = ref(null);
const quizOpenFor    = ref(null);
const addQuestionFor = ref(null);

// Module form (useForm supports File objects)
const moduleForm = useForm({ name: '', icon: '', description: '', image: null });
const modulePreview = ref(null);

function onModuleImage(e) {
  const file = e.target.files[0];
  if (!file) return;
  moduleForm.image = file;
  modulePreview.value = URL.createObjectURL(file);
}
function resetModuleForm() {
  moduleForm.reset();
  modulePreview.value = null;
}

// Activity form
const activityForm = useForm({ name: '', description: '', procedure: '', safety_reminders: '', image: null });
const activityPreview = ref(null);

function onActivityImage(e) {
  const file = e.target.files[0];
  if (!file) return;
  activityForm.image = file;
  activityPreview.value = URL.createObjectURL(file);
}
function resetActivityForm() {
  activityForm.reset();
  activityPreview.value = null;
}

const questionForm = reactive({ question: '', optionsText: '', correct_answer: '' });
function resetQuestionForm() { Object.assign(questionForm, { question: '', optionsText: '', correct_answer: '' }); }

function toggleAddActivity(moduleId) {
  addActivityFor.value = addActivityFor.value === moduleId ? null : moduleId;
  resetActivityForm();
}
function toggleQuizSection(activityId) {
  quizOpenFor.value = quizOpenFor.value === activityId ? null : activityId;
}
function toggleAddQuestion(activityId) {
  addQuestionFor.value = addQuestionFor.value === activityId ? null : activityId;
  resetQuestionForm();
}

// Module CRUD
function saveModule() {
  moduleForm.post('/teacher/activity-management/modules', {
    forceFormData: true,
    onSuccess: () => { showModuleForm.value = false; resetModuleForm(); },
  });
}
function deleteModule(id) {
  if (confirm('Delete this module and ALL its activities?')) {
    router.delete(`/teacher/activity-management/modules/${id}`);
  }
}

// Activity CRUD
function saveActivity(moduleId) {
  activityForm.post(`/teacher/activity-management/modules/${moduleId}/activities`, {
    forceFormData: true,
    onSuccess: () => { addActivityFor.value = null; resetActivityForm(); },
  });
}
function deleteActivity(id) {
  if (confirm('Delete this activity and its quiz questions?')) {
    router.delete(`/teacher/activity-management/activities/${id}`);
  }
}

// Lock toggle + deadline save
async function toggleLock(activity) {
  activity.is_locked = !activity.is_locked;
  await axios.put(`/teacher/activity-management/activity/${activity.id}`, {
    is_locked: activity.is_locked,
    deadline_enabled: activity.deadline_enabled,
    deadline_at: activity.deadline_at_input || activity.deadline_at,
  });
}
async function saveActivity_(activity) {
  await axios.put(`/teacher/activity-management/activity/${activity.id}`, {
    is_locked: activity.is_locked,
    deadline_enabled: activity.deadline_enabled,
    deadline_at: activity.deadline_at_input || activity.deadline_at,
  });
}

// Quiz Questions CRUD
function saveQuestion(activityId) {
  const options = questionForm.optionsText.split('\n').map(s => s.trim()).filter(Boolean);
  router.post(`/teacher/activity-management/activities/${activityId}/questions`, {
    question: questionForm.question,
    options,
    correct_answer: questionForm.correct_answer,
  }, {
    onSuccess: () => { addQuestionFor.value = null; resetQuestionForm(); },
  });
}
function deleteQuestion(id) {
  if (confirm('Delete this question?')) {
    router.delete(`/teacher/activity-management/questions/${id}`);
  }
}

function formatDate(d) {
  if (!d) return '';
  return new Date(d).toLocaleString();
}

async function uploadModuleImage(module, event) {
  const file = event.target.files[0];
  if (!file) return;
  const fd = new FormData();
  fd.append('image', file);
  try {
    const { data } = await axios.post(`/teacher/activity-management/modules/${module.id}/image`, fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    module.image_url = data.url;
  } catch {
    alert('Image upload failed. Max 4MB, images only.');
  }
  event.target.value = '';
}

async function uploadActivityImage(activity, event) {
  const file = event.target.files[0];
  if (!file) return;
  const fd = new FormData();
  fd.append('image', file);
  try {
    const { data } = await axios.post(`/teacher/activity-management/activities/${activity.id}/image`, fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    activity.image_url = data.url;
  } catch {
    alert('Image upload failed. Max 4MB, images only.');
  }
  event.target.value = '';
}
</script>

<style scoped>
.am-page { max-width: 900px; margin: 0 auto; padding-bottom: 60px; }
.page-title { font-size: 26px; font-weight: 700; color: #00e5ff; text-align: center; margin-bottom: 24px; }

/* Add Module Card */
.add-module-card { margin-bottom: 28px; }
.toggle-btn {
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: linear-gradient(90deg, rgba(0,229,255,0.12), rgba(0,255,136,0.08));
  border: 1px solid rgba(0,229,255,0.25);
  border-radius: 10px;
  padding: 12px 18px;
  color: #00e5ff;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
}
.toggle-btn:hover { background: rgba(0,229,255,0.18); }

/* Cards */
.card {
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 14px;
  padding: 20px 24px;
  margin-bottom: 16px;
}
.form-body { padding-top: 16px; }
.activity-form-card { background: rgba(0,229,255,0.04); border-color: rgba(0,229,255,0.15); margin-bottom: 12px; }
.activity-form-card h3 { color: #00e5ff; font-size: 14px; margin-bottom: 14px; }

/* Form fields */
.field { margin-bottom: 12px; }
.field label { display: block; font-size: 12px; color: #8090b0; margin-bottom: 5px; }
.field input, .field textarea, .field select {
  width: 100%;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 8px;
  padding: 9px 12px;
  color: #e0e6ff;
  font-size: 13px;
  outline: none;
  box-sizing: border-box;
  font-family: inherit;
  resize: vertical;
}
.field input:focus, .field textarea:focus { border-color: #00e5ff; }
.form-row { display: flex; gap: 10px; }
.flex-3 { flex: 3; }
.form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px; }
.btn-cancel { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 8px 18px; color: #8090b0; cursor: pointer; font-size: 13px; }
.btn-save { background: linear-gradient(90deg,#00e5ff,#00ff88); border: none; border-radius: 8px; padding: 8px 20px; color: #0a0f1e; font-weight: 700; cursor: pointer; font-size: 13px; }
.btn-save:disabled { opacity: 0.5; cursor: not-allowed; }

.img-upload-box {
  display: block;
  cursor: pointer;
  border: 2px dashed rgba(0,229,255,0.25);
  border-radius: 10px;
  overflow: hidden;
  height: 140px;
  transition: border-color 0.2s;
}
.img-upload-box:hover { border-color: rgba(0,229,255,0.6); }
.img-preview { width: 100%; height: 100%; background-size: cover; background-position: center; }
.img-placeholder { height: 100%; display: flex; align-items: center; justify-content: center; color: #6070a0; font-size: 14px; }

/* Module section */
.module-section { margin-bottom: 36px; }
.module-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 8px; }
.module-title-wrap { display: flex; align-items: center; gap: 10px; }
.module-thumb { width: 48px; height: 48px; border-radius: 8px; background-size: cover; background-position: center; border: 1px solid rgba(255,255,255,0.12); flex-shrink: 0; }
.module-title { font-size: 16px; color: #ffaa00; font-weight: 700; }
.module-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.btn-upload { display: flex; align-items: center; gap: 4px; background: rgba(148,77,255,0.12); border: 1px solid rgba(148,77,255,0.3); border-radius: 8px; padding: 6px 12px; color: #bb88ff; cursor: pointer; font-size: 12px; }
.btn-upload:hover { background: rgba(148,77,255,0.2); }
.hidden-file { display: none; }
.btn-add-activity { background: rgba(0,229,255,0.12); border: 1px solid rgba(0,229,255,0.25); border-radius: 8px; padding: 6px 14px; color: #00e5ff; cursor: pointer; font-size: 12px; }
.btn-add-activity:hover { background: rgba(0,229,255,0.2); }
.btn-delete-mod { background: rgba(229,57,53,0.1); border: 1px solid rgba(229,57,53,0.25); border-radius: 8px; padding: 6px 14px; color: #ff6b6b; cursor: pointer; font-size: 12px; }
.btn-delete-mod:hover { background: rgba(229,57,53,0.2); }

/* Activity card */
.activity-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 16px 20px; margin-bottom: 12px; }
.activity-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
.activity-name-row { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
.activity-thumb { width: 40px; height: 40px; border-radius: 6px; background-size: cover; background-position: center; border: 1px solid rgba(255,255,255,0.1); flex-shrink: 0; }
.activity-name { color: #00e5ff; font-size: 14px; font-weight: 600; margin: 0; }
.upload-img { background: rgba(148,77,255,0.08); border-color: rgba(148,77,255,0.2); color: #bb88ff; }
.badges { display: flex; gap: 8px; flex-wrap: wrap; }
.badge { font-size: 11px; padding: 3px 10px; border-radius: 12px; }
.badge.locked { background: rgba(255,80,80,0.15); color: #ff6b6b; }
.badge.unlocked { background: rgba(0,255,136,0.15); color: #00ff88; }
.badge.deadline-badge { background: rgba(255,255,255,0.06); color: #8090b0; }
.activity-controls { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
.btn-icon-sm { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 6px 10px; cursor: pointer; font-size: 14px; }
.btn-icon-sm:hover { background: rgba(255,255,255,0.12); }
.btn-icon-sm.danger:hover { background: rgba(229,57,53,0.2); border-color: rgba(229,57,53,0.3); }

/* Toggle switch */
.toggle { position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; }
.toggle input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; inset: 0; background: rgba(255,255,255,0.1); border-radius: 24px; transition: .3s; cursor: pointer; }
.slider:before { content: ''; position: absolute; width: 18px; height: 18px; left: 3px; top: 3px; background: #6070a0; border-radius: 50%; transition: .3s; }
.toggle input:checked + .slider { background: rgba(0,229,255,0.3); }
.toggle input:checked + .slider:before { transform: translateX(20px); background: #00e5ff; }

/* Deadline */
.deadline-section { border-top: 1px solid rgba(255,255,255,0.05); padding-top: 12px; }
.checkbox-label { display: flex; align-items: center; gap: 8px; color: #8090b0; font-size: 12px; cursor: pointer; margin-bottom: 8px; }
.checkbox-label input { accent-color: #00e5ff; }
.deadline-row { display: flex; gap: 10px; }
.date-input { flex: 1; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 8px 12px; color: #e0e6ff; font-size: 12px; outline: none; }
.btn-save-sm { background: linear-gradient(90deg,#7c3aed,#00e5ff); border: none; border-radius: 8px; padding: 8px 16px; color: white; font-size: 12px; font-weight: 600; cursor: pointer; white-space: nowrap; }

/* Quiz section */
.quiz-section { border-top: 1px solid rgba(255,255,255,0.05); padding-top: 14px; margin-top: 12px; }
.quiz-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; font-size: 13px; color: #a0b0d0; }
.btn-add-q { background: rgba(0,229,255,0.1); border: 1px solid rgba(0,229,255,0.2); border-radius: 6px; padding: 4px 12px; color: #00e5ff; font-size: 12px; cursor: pointer; }
.question-row { display: flex; align-items: flex-start; gap: 10px; background: rgba(255,255,255,0.03); border-radius: 8px; padding: 10px 12px; margin-bottom: 8px; }
.q-text { color: #c0d0e0; font-size: 13px; flex: 1; line-height: 1.4; }
.q-options { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
.opt { font-size: 11px; padding: 2px 8px; border-radius: 10px; background: rgba(255,255,255,0.06); color: #8090b0; }
.opt.correct { background: rgba(0,255,136,0.15); color: #00ff88; }
.btn-del-q { background: none; border: none; color: #ff6b6b; cursor: pointer; font-size: 14px; flex-shrink: 0; padding: 2px 4px; }
.add-question-form { background: rgba(0,229,255,0.03); border: 1px solid rgba(0,229,255,0.12); border-radius: 10px; padding: 16px; margin-top: 10px; }
.empty-q { color: #6070a0; font-size: 12px; font-style: italic; }
.no-activities { color: #6070a0; font-size: 13px; font-style: italic; padding: 8px 0; }
.empty-modules { text-align: center; color: #6070a0; font-size: 14px; padding: 40px; }
</style>
