<template>
  <TeacherLayout :user="user">
    <div class="am-page">
      <h1 class="page-title">Activity Management</h1>

      <div v-for="module in modules" :key="module.id" class="module-section">
        <h2 class="module-title">🔥 Module {{ module.order }}</h2>

        <div v-for="activity in module.activities" :key="activity.id" class="activity-card">
          <div class="activity-header">
            <div class="activity-info">
              <p class="activity-name">{{ activity.icon }} {{ activity.name }}</p>
              <span :class="['lock-badge', activity.is_locked ? 'locked' : 'unlocked']">
                {{ activity.is_locked ? '🔒 Locked' : '🔓 Unlocked' }}
              </span>
              <span class="deadline-info">
                {{ activity.deadline_enabled && activity.deadline_at ? '🕐 ' + formatDate(activity.deadline_at) : '🚫 No deadline set' }}
              </span>
            </div>
            <label class="toggle">
              <input type="checkbox" :checked="!activity.is_locked" @change="toggleLock(activity)" />
              <span class="slider" />
            </label>
          </div>

          <div class="deadline-section">
            <label class="checkbox-label">
              <input type="checkbox" v-model="activity.deadline_enabled" @change="saveActivity(activity)" />
              Enable Deadline
            </label>
            <div v-if="activity.deadline_enabled" class="deadline-picker">
              <input type="datetime-local" v-model="activity.deadline_at_input" class="date-input" />
            </div>
            <div class="action-row">
              <button class="btn-select-dt" @click="openDatePicker(activity)">Select Date &amp; Time</button>
              <button class="btn-save" @click="saveActivity(activity)">💾 Save</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </TeacherLayout>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

const props = defineProps({ user: Object, modules: Array });

function formatDate(d) {
  if (!d) return '';
  return new Date(d).toLocaleString();
}

function openDatePicker(activity) {
  const input = document.createElement('input');
  input.type = 'datetime-local';
  input.onchange = (e) => {
    activity.deadline_at = e.target.value;
    activity.deadline_at_input = e.target.value;
  };
  input.click();
}

async function toggleLock(activity) {
  activity.is_locked = !activity.is_locked;
  await saveActivity(activity);
}

async function saveActivity(activity) {
  await axios.put(`/teacher/activity-management/${activity.id}`, {
    is_locked: activity.is_locked,
    deadline_enabled: activity.deadline_enabled,
    deadline_at: activity.deadline_at_input || activity.deadline_at,
  });
}
</script>

<style scoped>
.am-page { }
.page-title { font-size: 26px; font-weight: 700; color: #00e5ff; text-align: center; margin-bottom: 28px; }
.module-section { margin-bottom: 32px; }
.module-title { font-size: 16px; color: #ffaa00; margin-bottom: 14px; }
.activity-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 20px 24px; margin-bottom: 16px; }
.activity-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
.activity-name { color: #00e5ff; font-size: 15px; font-weight: 600; margin-bottom: 6px; }
.lock-badge { display: inline-block; font-size: 11px; padding: 3px 10px; border-radius: 12px; margin-right: 8px; }
.lock-badge.locked { background: rgba(255,80,80,0.15); color: #ff6b6b; }
.lock-badge.unlocked { background: rgba(0,255,136,0.15); color: #00ff88; }
.deadline-info { font-size: 11px; color: #6070a0; }
.toggle { position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; }
.toggle input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; inset: 0; background: rgba(255,255,255,0.1); border-radius: 24px; transition: 0.3s; cursor: pointer; }
.slider:before { content: ''; position: absolute; width: 18px; height: 18px; left: 3px; top: 3px; background: #6070a0; border-radius: 50%; transition: 0.3s; }
.toggle input:checked + .slider { background: rgba(0,229,255,0.3); }
.toggle input:checked + .slider:before { transform: translateX(20px); background: #00e5ff; }
.deadline-section { }
.checkbox-label { display: flex; align-items: center; gap: 8px; color: #8090b0; font-size: 13px; cursor: pointer; margin-bottom: 12px; }
.checkbox-label input { accent-color: #00e5ff; }
.date-input {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 8px;
  padding: 8px 12px;
  color: #e0e6ff;
  font-size: 13px;
  outline: none;
  margin-bottom: 12px;
  width: 100%;
  box-sizing: border-box;
}
.action-row { display: flex; gap: 12px; }
.btn-select-dt { flex: 1; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 10px; color: #e0e6ff; cursor: pointer; font-size: 13px; }
.btn-save { flex: 1; background: linear-gradient(90deg,#7c3aed,#00e5ff); border: none; border-radius: 8px; padding: 10px; color: white; font-weight: 600; cursor: pointer; font-size: 13px; }
</style>
