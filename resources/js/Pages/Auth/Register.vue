<template>
  <div class="auth-bg">
    <StarParticles />
    <div class="auth-card">
      <h2 class="title">Create Account</h2>
      <p class="subtitle">Join the Physics Virtual Lab</p>

      <form @submit.prevent="submit">
        <div class="field-group">
          <label>Full Name</label>
          <input v-model="form.full_name" type="text" placeholder="" />
          <span v-if="form.errors.full_name" class="err">{{ form.errors.full_name }}</span>
        </div>

        <div class="row-2">
          <div class="field-group">
            <label>Teacher</label>
            <select v-model="form.teacher_id">
              <option value="">Select</option>
              <option v-for="t in teachers" :key="t.id" :value="t.id">{{ t.full_name }}</option>
            </select>
          </div>
          <div class="field-group">
            <label>Section</label>
            <select v-model="form.section_id">
              <option value="">Select</option>
              <option v-for="s in sections" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
            <span v-if="form.errors.section_id" class="err">{{ form.errors.section_id }}</span>
          </div>
        </div>

        <div class="field-group">
          <label>Username</label>
          <input v-model="form.username" type="text" />
          <span v-if="form.errors.username" class="err">{{ form.errors.username }}</span>
        </div>

        <div class="field-group">
          <label>Password</label>
          <input v-model="form.password" :type="showPass ? 'text' : 'password'" />
          <span v-if="form.errors.password" class="err">{{ form.errors.password }}</span>
        </div>

        <div class="password-hints">
          <span :class="['hint', form.password.length >= 8 ? 'ok' : 'no']">✗ At least 8 characters</span>
          <span :class="['hint', /[A-Z]/.test(form.password) ? 'ok' : 'no']">✗ Uppercase</span>
          <span :class="['hint', /[0-9]/.test(form.password) ? 'ok' : 'no']">✗ Number</span>
          <span :class="['hint', /[a-z]/.test(form.password) ? 'ok' : 'no']">✗ Lowercase</span>
        </div>

        <div class="field-group">
          <label>Confirm Password</label>
          <input v-model="form.password_confirmation" :type="showPass ? 'text' : 'password'" />
        </div>

        <label class="checkbox-label">
          <input type="checkbox" v-model="showPass" /> Show Password
        </label>

        <button class="btn-register" type="submit" :disabled="form.processing">Register</button>
      </form>

      <p class="login-link">Already have an account? <Link href="/" class="link">Login</Link></p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import StarParticles from '@/Components/StarParticles.vue';

const props = defineProps({ sections: Array, teachers: Array });
const showPass = ref(false);
const form = useForm({
  full_name: '',
  username: '',
  password: '',
  password_confirmation: '',
  section_id: '',
  teacher_id: '',
});

function submit() {
  form.post('/register');
}
</script>

<style scoped>
.auth-bg {
  min-height: 100vh;
  background: linear-gradient(135deg, #0a0f1e 0%, #0d1f3c 50%, #1a0a2e 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Segoe UI', sans-serif;
  position: relative;
  padding: 20px;
}
.auth-card {
  background: rgba(15, 25, 50, 0.85);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 16px;
  padding: 36px 32px;
  width: 420px;
  max-width: 95vw;
  z-index: 1;
}
.title { color: #e0e6ff; font-size: 22px; font-weight: 700; text-align: center; margin-bottom: 4px; }
.subtitle { color: #8090b0; font-size: 13px; text-align: center; margin-bottom: 20px; }
.field-group { margin-bottom: 14px; }
.field-group label { display: block; color: #a0b0d0; font-size: 13px; margin-bottom: 6px; }
.field-group input, .field-group select {
  width: 100%;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 8px;
  padding: 10px 12px;
  color: #e0e6ff;
  font-size: 14px;
  outline: none;
  box-sizing: border-box;
  transition: border-color 0.2s;
}
.field-group input:focus, .field-group select:focus { border-color: #00e5ff; }
.field-group select option { background: #1a2540; }
.row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.password-hints { display: grid; grid-template-columns: 1fr 1fr; gap: 4px; margin-bottom: 14px; }
.hint { font-size: 12px; }
.hint.ok { color: #00ff88; }
.hint.no { color: #ff6b6b; }
.checkbox-label { display: flex; align-items: center; gap: 6px; color: #8090b0; font-size: 13px; cursor: pointer; margin-bottom: 16px; }
.checkbox-label input { accent-color: #00e5ff; }
.btn-register {
  width: 100%;
  padding: 12px;
  background: linear-gradient(90deg, #00e5ff, #00ff88);
  border: none;
  border-radius: 8px;
  color: #0a0f1e;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
}
.btn-register:disabled { opacity: 0.6; cursor: not-allowed; }
.err { color: #ff8080; font-size: 12px; margin-top: 4px; display: block; }
.login-link { text-align: center; color: #8090b0; font-size: 13px; margin-top: 14px; }
.link { color: #00e5ff; text-decoration: none; font-weight: 600; }
</style>
