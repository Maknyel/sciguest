<template>
  <TeacherLayout :user="user">
    <div class="profile-page">
      <h1 class="page-title">My Profile</h1>

      <div class="cards-grid">

        <!-- Profile Info -->
        <div class="card">
          <h3 class="card-title">👤 Account Information</h3>

          <div v-if="successInfo" class="alert success">{{ successInfo }}</div>
          <div v-if="errorInfo" class="alert error">{{ errorInfo }}</div>

          <div class="field">
            <label>Full Name</label>
            <input v-model="infoForm.full_name" type="text" />
            <span v-if="infoErrors.full_name" class="err">{{ infoErrors.full_name }}</span>
          </div>
          <div class="field">
            <label>Username</label>
            <input v-model="infoForm.username" type="text" />
            <span v-if="infoErrors.username" class="err">{{ infoErrors.username }}</span>
          </div>
          <div class="field readonly">
            <label>Role</label>
            <input :value="user.role.charAt(0).toUpperCase() + user.role.slice(1)" readonly />
          </div>
          <div class="field readonly">
            <label>Account ID</label>
            <input :value="'#' + user.id" readonly />
          </div>

          <button class="btn-save" @click="saveInfo" :disabled="savingInfo">
            {{ savingInfo ? 'Saving…' : '💾 Save Changes' }}
          </button>
        </div>

        <!-- Change Password -->
        <div class="card">
          <h3 class="card-title">🔐 Change Password</h3>

          <div v-if="successPass" class="alert success">{{ successPass }}</div>
          <div v-if="errorPass" class="alert error">{{ errorPass }}</div>

          <div class="field">
            <label>Current Password</label>
            <input v-model="passForm.current_password" :type="showPass ? 'text' : 'password'" />
            <span v-if="passErrors.current_password" class="err">{{ passErrors.current_password }}</span>
          </div>
          <div class="field">
            <label>New Password</label>
            <input v-model="passForm.password" :type="showPass ? 'text' : 'password'" />
            <span v-if="passErrors.password" class="err">{{ passErrors.password }}</span>
          </div>
          <div class="field">
            <label>Confirm New Password</label>
            <input v-model="passForm.password_confirmation" :type="showPass ? 'text' : 'password'" />
          </div>

          <label class="checkbox-label">
            <input type="checkbox" v-model="showPass" /> Show passwords
          </label>

          <div class="password-hints">
            <span :class="['hint', passForm.password.length >= 8 ? 'ok' : 'no']">
              {{ passForm.password.length >= 8 ? '✓' : '✗' }} At least 8 characters
            </span>
            <span :class="['hint', /[A-Z]/.test(passForm.password) ? 'ok' : 'no']">
              {{ /[A-Z]/.test(passForm.password) ? '✓' : '✗' }} Uppercase letter
            </span>
            <span :class="['hint', /[0-9]/.test(passForm.password) ? 'ok' : 'no']">
              {{ /[0-9]/.test(passForm.password) ? '✓' : '✗' }} Number
            </span>
            <span :class="['hint', passForm.password && passForm.password === passForm.password_confirmation ? 'ok' : 'no']">
              {{ passForm.password && passForm.password === passForm.password_confirmation ? '✓' : '✗' }} Passwords match
            </span>
          </div>

          <button class="btn-save" @click="savePassword" :disabled="savingPass">
            {{ savingPass ? 'Updating…' : '🔒 Update Password' }}
          </button>
        </div>

      </div>
    </div>
  </TeacherLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import axios from 'axios';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

const props = defineProps({ user: Object });

const showPass   = ref(false);
const savingInfo = ref(false);
const savingPass = ref(false);

const successInfo = ref('');
const errorInfo   = ref('');
const infoErrors  = reactive({});

const successPass = ref('');
const errorPass   = ref('');
const passErrors  = reactive({});

const infoForm = reactive({
  full_name: props.user.full_name,
  username:  props.user.username,
});

const passForm = reactive({
  current_password:      '',
  password:              '',
  password_confirmation: '',
});

async function saveInfo() {
  savingInfo.value = true;
  successInfo.value = '';
  errorInfo.value = '';
  Object.keys(infoErrors).forEach(k => delete infoErrors[k]);

  try {
    await axios.put('/teacher/profile', infoForm);
    successInfo.value = 'Profile updated successfully.';
  } catch (e) {
    if (e.response?.status === 422) {
      Object.assign(infoErrors, e.response.data.errors || {});
    } else {
      errorInfo.value = 'Something went wrong.';
    }
  } finally {
    savingInfo.value = false;
  }
}

async function savePassword() {
  savingPass.value = true;
  successPass.value = '';
  errorPass.value = '';
  Object.keys(passErrors).forEach(k => delete passErrors[k]);

  try {
    await axios.put('/teacher/profile/password', passForm);
    successPass.value = 'Password updated successfully.';
    passForm.current_password = '';
    passForm.password = '';
    passForm.password_confirmation = '';
  } catch (e) {
    if (e.response?.status === 422) {
      Object.assign(passErrors, e.response.data.errors || {});
    } else {
      errorPass.value = 'Something went wrong.';
    }
  } finally {
    savingPass.value = false;
  }
}
</script>

<style scoped>
.profile-page { max-width: 900px; margin: 0 auto; }
.page-title { font-size: 26px; font-weight: 700; color: #00e5ff; text-align: center; margin-bottom: 28px; }
.cards-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
@media (max-width: 700px) { .cards-grid { grid-template-columns: 1fr; } }

.card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 28px; }
.card-title { font-size: 16px; color: #e0e6ff; margin-bottom: 20px; font-weight: 600; }

.field { margin-bottom: 14px; }
.field label { display: block; font-size: 12px; color: #8090b0; margin-bottom: 5px; }
.field input {
  width: 100%;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 8px;
  padding: 10px 12px;
  color: #e0e6ff;
  font-size: 14px;
  outline: none;
  box-sizing: border-box;
  transition: border-color 0.2s;
}
.field input:focus { border-color: #00e5ff; }
.field.readonly input { color: #6070a0; cursor: default; }
.err { color: #ff8080; font-size: 12px; margin-top: 4px; display: block; }

.checkbox-label { display: flex; align-items: center; gap: 6px; color: #8090b0; font-size: 13px; cursor: pointer; margin-bottom: 14px; }
.checkbox-label input { accent-color: #00e5ff; }

.password-hints { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-bottom: 20px; }
.hint { font-size: 12px; }
.hint.ok { color: #00ff88; }
.hint.no { color: #ff6b6b; }

.btn-save {
  width: 100%;
  background: linear-gradient(90deg, #00e5ff, #00ff88);
  border: none;
  border-radius: 8px;
  padding: 11px;
  color: #0a0f1e;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  margin-top: 4px;
}
.btn-save:disabled { opacity: 0.6; cursor: not-allowed; }

.alert { padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 14px; }
.alert.success { background: rgba(0,255,136,0.1); border: 1px solid rgba(0,255,136,0.25); color: #00ff88; }
.alert.error   { background: rgba(255,80,80,0.1);  border: 1px solid rgba(255,80,80,0.25);  color: #ff8080; }
</style>
