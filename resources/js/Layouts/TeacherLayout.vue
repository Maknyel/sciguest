<template>
  <div class="layout">
    <StarParticles />
    <aside :class="['sidebar', { collapsed: sidebarCollapsed }]">
      <div class="sidebar-header">
        <button class="menu-btn" @click="sidebarCollapsed = !sidebarCollapsed">☰</button>
        <span v-if="!sidebarCollapsed" class="logo">SCIQUEST</span>
      </div>
      <nav class="sidebar-nav">
        <Link href="/teacher/dashboard" :class="['nav-item', isActive('/teacher/dashboard') ? 'active' : '']">
          <span class="nav-icon">📋</span>
          <span v-if="!sidebarCollapsed">Dashboard</span>
        </Link>
        <Link href="/teacher/notifications" :class="['nav-item', isActive('/teacher/notifications') ? 'active' : '']">
          <span class="nav-icon">🔔</span>
          <span v-if="!sidebarCollapsed">Notifications</span>
        </Link>
        <Link href="/teacher/announcements" :class="['nav-item', isActive('/teacher/announcements') ? 'active' : '']">
          <span class="nav-icon">📢</span>
          <span v-if="!sidebarCollapsed">Announcements</span>
        </Link>
        <Link href="/teacher/activity-management" :class="['nav-item', isActive('/teacher/activity-management') ? 'active' : '']">
          <span class="nav-icon">✏️</span>
          <span v-if="!sidebarCollapsed">Activity Management</span>
        </Link>
        <Link href="/teacher/account-management" :class="['nav-item', isActive('/teacher/account-management') ? 'active' : '']">
          <span class="nav-icon">👥</span>
          <span v-if="!sidebarCollapsed">Account Management</span>
        </Link>
        <Link href="/teacher/teacher-management" :class="['nav-item', isActive('/teacher/teacher-management') ? 'active' : '']">
          <span class="nav-icon">👩‍🏫</span>
          <span v-if="!sidebarCollapsed">Teacher Management</span>
        </Link>
        <Link href="/teacher/profile" :class="['nav-item', isActive('/teacher/profile') ? 'active' : '']">
          <span class="nav-icon">⚙️</span>
          <span v-if="!sidebarCollapsed">My Profile</span>
        </Link>
      </nav>
    </aside>

    <div class="main-wrapper">
      <header class="topbar">
        <div class="topbar-right">
          <div class="user-menu" @click="userMenuOpen = !userMenuOpen">
            <span>👤</span>
            <span class="user-name">{{ user?.full_name }}</span>
            <span>▾</span>
            <div v-if="userMenuOpen" class="dropdown">
              <Link href="/logout" method="post" as="button" class="dropdown-item logout">Logout</Link>
            </div>
          </div>
        </div>
      </header>
      <main class="content">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import StarParticles from '@/Components/StarParticles.vue';

const props = defineProps({ user: Object });
const sidebarCollapsed = ref(false);
const userMenuOpen = ref(false);

function isActive(path) {
  return usePage().url.startsWith(path);
}
</script>

<style scoped>
.layout { display: flex; min-height: 100vh; background: #0a0f1e; color: #e0e6ff; font-family: 'Segoe UI', sans-serif; position: relative; }
.sidebar { width: 200px; min-height: 100vh; background: rgba(10,20,50,0.9); backdrop-filter: blur(10px); border-right: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; z-index: 10; transition: width 0.3s; flex-shrink: 0; }
.sidebar.collapsed { width: 60px; }
.sidebar-header { display: flex; align-items: center; gap: 10px; padding: 20px 15px; border-bottom: 1px solid rgba(255,255,255,0.05); }
.menu-btn { background: none; border: none; color: #00e5ff; font-size: 20px; cursor: pointer; }
.logo { font-size: 18px; font-weight: 700; color: #00e5ff; letter-spacing: 2px; }
.sidebar-nav { display: flex; flex-direction: column; padding: 10px 0; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 12px 15px; color: #a0b0d0; text-decoration: none; transition: all 0.2s; border-left: 3px solid transparent; font-size: 13px; }
.nav-item:hover, .nav-item.active { background: rgba(0,229,255,0.1); color: #00e5ff; border-left-color: #00e5ff; }
.nav-icon { font-size: 16px; min-width: 20px; }
.main-wrapper { flex: 1; display: flex; flex-direction: column; overflow: hidden; position: relative; z-index: 1; }
.topbar { height: 60px; background: rgba(10,20,50,0.7); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: flex-end; padding: 0 25px; }
.user-menu { display: flex; align-items: center; gap: 8px; cursor: pointer; position: relative; padding: 6px 12px; border-radius: 8px; background: rgba(255,255,255,0.05); }
.user-menu:hover { background: rgba(255,255,255,0.1); }
.user-name { font-size: 14px; }
.dropdown { position: absolute; top: 110%; right: 0; background: #1a2540; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; min-width: 140px; z-index: 100; box-shadow: 0 8px 32px rgba(0,0,0,0.5); }
.dropdown-item { display: block; width: 100%; padding: 10px 16px; background: none; border: none; color: #e0e6ff; text-align: left; cursor: pointer; font-size: 14px; text-decoration: none; }
.dropdown-item:hover { background: rgba(255,255,255,0.05); }
.dropdown-item.logout { color: #ff6b6b; }
.content { flex: 1; padding: 30px; overflow-y: auto; }
</style>
