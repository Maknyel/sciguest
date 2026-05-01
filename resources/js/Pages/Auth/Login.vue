<template>
  <div class="auth-bg">
    <StarParticles />
    <div class="auth-card">
      <h2 class="school-name">City of Bacoor National High School-Camella Springville</h2>
      <p class="subtitle">Login to continue</p>

      <form @submit.prevent="submit">
        <div v-if="form.errors.username" class="error-msg">{{ form.errors.username }}</div>

        <div class="field">
          <span class="field-icon">👤</span>
          <input v-model="form.username" type="text" placeholder="Username" autocomplete="username" />
        </div>

        <div class="field">
          <span class="field-icon">🔒</span>
          <input v-model="form.password" :type="showPassword ? 'text' : 'password'" placeholder="Password" autocomplete="current-password" />
        </div>

        <div class="options-row">
          <label class="checkbox-label">
            <input type="checkbox" v-model="form.remember" /> Remember me
          </label>
          <label class="checkbox-label">
            <input type="checkbox" v-model="showPassword" /> Show password
          </label>
        </div>

        <button class="btn-login" type="submit" :disabled="form.processing">Login</button>

        <Link href="/register" class="forgot-link" style="display:block;text-align:center;margin-top:8px;">Forgot Password?</Link>
      </form>

      <p class="register-link">
        Don't have an account?
        <Link href="/register" class="link">Create account</Link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import StarParticles from '@/Components/StarParticles.vue';

const showPassword = ref(false);
const form = useForm({ username: '', password: '', remember: false });

function submit() {
  form.post('/login');
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
}
.auth-card {
  background: rgba(15, 25, 50, 0.85);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 16px;
  padding: 40px 36px;
  width: 360px;
  max-width: 95vw;
  z-index: 1;
  box-shadow: 0 20px 60px rgba(0,0,0,0.5);
}
.school-name { color: #e0e6ff; font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 4px; line-height: 1.4; }
.subtitle { color: #8090b0; font-size: 13px; text-align: center; margin-bottom: 24px; }
.field {
  display: flex;
  align-items: center;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 8px;
  padding: 0 12px;
  margin-bottom: 14px;
  transition: border-color 0.2s;
}
.field:focus-within { border-color: #00e5ff; }
.field-icon { margin-right: 8px; font-size: 16px; }
.field input {
  flex: 1;
  background: none;
  border: none;
  outline: none;
  color: #e0e6ff;
  padding: 12px 0;
  font-size: 14px;
}
.field input::placeholder { color: #6070a0; }
.options-row { display: flex; justify-content: space-between; margin-bottom: 18px; }
.checkbox-label { display: flex; align-items: center; gap: 6px; color: #8090b0; font-size: 13px; cursor: pointer; }
.checkbox-label input { accent-color: #00e5ff; }
.btn-login {
  width: 100%;
  padding: 13px;
  background: linear-gradient(90deg, #00e5ff, #00ff88);
  border: none;
  border-radius: 8px;
  color: #0a0f1e;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  transition: opacity 0.2s;
}
.btn-login:hover { opacity: 0.9; }
.btn-login:disabled { opacity: 0.6; cursor: not-allowed; }
.forgot-link { color: #00e5ff; font-size: 13px; text-decoration: none; }
.forgot-link:hover { text-decoration: underline; }
.register-link { text-align: center; color: #8090b0; font-size: 13px; margin-top: 16px; }
.link { color: #00e5ff; text-decoration: none; font-weight: 600; }
.link:hover { text-decoration: underline; }
.error-msg { background: rgba(255,80,80,0.15); border: 1px solid rgba(255,80,80,0.3); border-radius: 8px; padding: 10px 14px; color: #ff8080; font-size: 13px; margin-bottom: 12px; }
</style>
