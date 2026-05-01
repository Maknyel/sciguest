<template>
  <TeacherLayout :user="user">
    <div class="page">
      <h1 class="page-title">Teacher Management</h1>

      <div v-if="$page.props.flash?.success" class="alert success">{{ $page.props.flash.success }}</div>
      <div v-if="$page.props.errors?.error" class="alert error">{{ $page.props.errors.error }}</div>

      <!-- Add New Teacher -->
      <div class="card mb-6">
        <div class="card-header" @click="showAddForm = !showAddForm">
          <h3 class="card-title">➕ Add New Teacher</h3>
          <span class="toggle-icon">{{ showAddForm ? '▲' : '▼' }}</span>
        </div>
        <div v-if="showAddForm" class="card-body">
          <div class="form-grid">
            <div class="field">
              <label>Full Name</label>
              <input v-model="addForm.full_name" type="text" placeholder="Full Name" />
              <span v-if="$page.props.errors?.full_name" class="err">{{ $page.props.errors.full_name }}</span>
            </div>
            <div class="field">
              <label>Username</label>
              <input v-model="addForm.username" type="text" placeholder="Username" />
              <span v-if="$page.props.errors?.username" class="err">{{ $page.props.errors.username }}</span>
            </div>
            <div class="field">
              <label>Password</label>
              <input v-model="addForm.password" :type="showAddPass ? 'text' : 'password'" placeholder="Password" />
              <span v-if="$page.props.errors?.password" class="err">{{ $page.props.errors.password }}</span>
            </div>
            <div class="field">
              <label>Confirm Password</label>
              <input v-model="addForm.password_confirmation" :type="showAddPass ? 'text' : 'password'" placeholder="Confirm Password" />
            </div>
          </div>
          <label class="checkbox-label">
            <input type="checkbox" v-model="showAddPass" /> Show passwords
          </label>
          <button class="btn-save" @click="submitAdd" :disabled="addForm.processing">
            {{ addForm.processing ? 'Creating…' : '✅ Create Teacher' }}
          </button>
        </div>
      </div>

      <!-- Teachers Table -->
      <div class="card">
        <h3 class="card-title">👩‍🏫 All Teachers</h3>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>Joined</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="teacher in teachers" :key="teacher.id">
                <td>{{ teacher.id }}</td>
                <td>
                  <template v-if="editingId === teacher.id">
                    <input class="inline-input" v-model="editForm.full_name" />
                  </template>
                  <template v-else>{{ teacher.full_name }}</template>
                </td>
                <td>
                  <template v-if="editingId === teacher.id">
                    <input class="inline-input" v-model="editForm.username" />
                  </template>
                  <template v-else>{{ teacher.username }}</template>
                </td>
                <td>{{ formatDate(teacher.created_at) }}</td>
                <td class="actions">
                  <!-- Editing row -->
                  <template v-if="editingId === teacher.id">
                    <div class="field mini">
                      <input class="inline-input" v-model="editForm.password" :type="showEditPass ? 'text' : 'password'" placeholder="New password (optional)" />
                    </div>
                    <div class="field mini">
                      <input class="inline-input" v-model="editForm.password_confirmation" :type="showEditPass ? 'text' : 'password'" placeholder="Confirm" />
                    </div>
                    <label class="checkbox-label mini">
                      <input type="checkbox" v-model="showEditPass" /> Show
                    </label>
                    <span v-if="editErrors.username" class="err">{{ editErrors.username }}</span>
                    <span v-if="editErrors.password" class="err">{{ editErrors.password }}</span>
                    <div class="btn-row">
                      <button class="btn-sm save" @click="submitEdit(teacher.id)" :disabled="editSubmitting">
                        {{ editSubmitting ? '…' : '💾 Save' }}
                      </button>
                      <button class="btn-sm cancel" @click="cancelEdit">Cancel</button>
                    </div>
                  </template>
                  <!-- View row -->
                  <template v-else>
                    <button
                      class="btn-sm edit"
                      @click="startEdit(teacher)"
                      :disabled="teacher.id === 1 && user.id !== 1"
                      :title="teacher.id === 1 && user.id !== 1 ? 'Cannot edit primary teacher' : ''"
                    >✏️ Edit</button>
                    <button
                      class="btn-sm del"
                      @click="deleteTeacher(teacher)"
                      :disabled="teacher.id === 1 || teacher.id === user.id"
                      :title="teacher.id === 1 ? 'Cannot delete primary teacher' : teacher.id === user.id ? 'Cannot delete yourself' : ''"
                    >🗑️ Delete</button>
                  </template>
                </td>
              </tr>
              <tr v-if="!teachers.length">
                <td colspan="5" class="empty">No teachers found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </TeacherLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

const props = defineProps({ user: Object, teachers: Array });

const showAddForm  = ref(false);
const showAddPass  = ref(false);
const showEditPass = ref(false);
const editingId    = ref(null);
const editSubmitting = ref(false);
const editErrors   = reactive({});

const addForm = useForm({
  full_name: '',
  username: '',
  password: '',
  password_confirmation: '',
});

const editForm = reactive({
  full_name: '',
  username: '',
  password: '',
  password_confirmation: '',
});

function submitAdd() {
  addForm.post('/teacher/teacher-management', {
    onSuccess: () => {
      addForm.reset();
      showAddForm.value = false;
    },
  });
}

function startEdit(teacher) {
  editingId.value = teacher.id;
  editForm.full_name = teacher.full_name;
  editForm.username = teacher.username;
  editForm.password = '';
  editForm.password_confirmation = '';
  Object.keys(editErrors).forEach(k => delete editErrors[k]);
}

function cancelEdit() {
  editingId.value = null;
}

function submitEdit(id) {
  editSubmitting.value = true;
  Object.keys(editErrors).forEach(k => delete editErrors[k]);

  router.put(`/teacher/teacher-management/${id}`, editForm, {
    onSuccess: () => {
      editingId.value = null;
    },
    onError: (errors) => {
      Object.assign(editErrors, errors);
    },
    onFinish: () => {
      editSubmitting.value = false;
    },
  });
}

function deleteTeacher(teacher) {
  if (!confirm(`Delete teacher "${teacher.full_name}"? This cannot be undone.`)) return;
  router.delete(`/teacher/teacher-management/${teacher.id}`);
}

function formatDate(dateStr) {
  if (!dateStr) return '—';
  return new Date(dateStr).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}
</script>

<style scoped>
.page { max-width: 1000px; margin: 0 auto; }
.page-title { font-size: 26px; font-weight: 700; color: #00e5ff; text-align: center; margin-bottom: 28px; }
.mb-6 { margin-bottom: 24px; }

.alert { padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
.alert.success { background: rgba(0,255,136,0.1); border: 1px solid rgba(0,255,136,0.25); color: #00ff88; }
.alert.error   { background: rgba(255,80,80,0.1);  border: 1px solid rgba(255,80,80,0.25);  color: #ff8080; }

.card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 24px; }
.card-header { display: flex; align-items: center; justify-content: space-between; cursor: pointer; }
.card-title { font-size: 16px; color: #e0e6ff; font-weight: 600; margin: 0; }
.toggle-icon { color: #00e5ff; font-size: 12px; }
.card-body { margin-top: 20px; }

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 12px; }
@media (max-width: 600px) { .form-grid { grid-template-columns: 1fr; } }

.field { display: flex; flex-direction: column; gap: 5px; }
.field label { font-size: 12px; color: #8090b0; }
.field input, .inline-input {
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 8px;
  padding: 9px 12px;
  color: #e0e6ff;
  font-size: 14px;
  outline: none;
  width: 100%;
  box-sizing: border-box;
  transition: border-color 0.2s;
}
.field input:focus, .inline-input:focus { border-color: #00e5ff; }
.field.mini { margin-bottom: 6px; }
.inline-input { padding: 6px 10px; font-size: 13px; }

.checkbox-label { display: flex; align-items: center; gap: 6px; color: #8090b0; font-size: 13px; cursor: pointer; margin-bottom: 14px; }
.checkbox-label.mini { margin-bottom: 8px; font-size: 12px; }
.checkbox-label input { accent-color: #00e5ff; }

.err { color: #ff8080; font-size: 12px; }

.btn-save {
  background: linear-gradient(90deg, #00e5ff, #00ff88);
  border: none; border-radius: 8px; padding: 10px 22px;
  color: #0a0f1e; font-weight: 700; font-size: 14px; cursor: pointer;
}
.btn-save:disabled { opacity: 0.6; cursor: not-allowed; }

.table-wrap { overflow-x: auto; margin-top: 16px; }
table { width: 100%; border-collapse: collapse; font-size: 13px; }
thead tr { border-bottom: 1px solid rgba(255,255,255,0.1); }
th { color: #8090b0; font-weight: 600; padding: 10px 12px; text-align: left; }
td { padding: 12px 12px; color: #e0e6ff; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: top; }
.empty { text-align: center; color: #8090b0; }

.actions { display: flex; flex-direction: column; gap: 6px; min-width: 200px; }
.btn-row { display: flex; gap: 8px; margin-top: 4px; }
.btn-sm {
  padding: 5px 12px; border: none; border-radius: 6px;
  font-size: 12px; font-weight: 600; cursor: pointer;
}
.btn-sm.edit   { background: rgba(0,229,255,0.15); color: #00e5ff; }
.btn-sm.del    { background: rgba(255,107,107,0.15); color: #ff6b6b; }
.btn-sm.save   { background: rgba(0,255,136,0.15); color: #00ff88; }
.btn-sm.cancel { background: rgba(255,255,255,0.08); color: #a0b0d0; }
.btn-sm:disabled { opacity: 0.4; cursor: not-allowed; }
.btn-sm:hover:not(:disabled) { filter: brightness(1.2); }
</style>
