<template>
  <TeacherLayout :user="user">
    <div class="teacher-dashboard">
      <h1 class="page-title">Teacher Dashboard</h1>

      <div class="card">
        <h2 class="card-title">📊 Section Progress (Modules)</h2>
        <div class="chart-meta">
          <span class="top">🏆 Top: {{ topSection }}</span>
          <span class="low">⚠ Lowest: {{ lowestSection }}</span>
        </div>

        <!-- Legend -->
        <div class="legend">
          <span v-for="(m, i) in modules" :key="m.id" class="legend-item">
            <span class="legend-dot" :style="{ background: moduleColors[i] }" />
            Module {{ m.order }}
          </span>
        </div>

        <!-- Bar Chart -->
        <div class="chart-area">
          <div class="y-axis">
            <span v-for="p in [100,90,80,70,60,50,40,30,20,10,0]" :key="p">{{ p }}%</span>
          </div>
          <div class="bars-area">
            <div v-for="section in sections" :key="section.id" class="section-group">
              <div class="bars">
                <div v-for="(m, mi) in modules" :key="m.id"
                  class="bar"
                  :style="{
                    height: (sectionProgress[section.id]?.[m.id] || 0) + '%',
                    background: moduleColors[mi],
                    minHeight: '4px',
                  }"
                  :title="`${section.name} - Module ${m.order}: ${sectionProgress[section.id]?.[m.id] || 0}%`"
                />
              </div>
              <p class="section-label">{{ section.name }}</p>
            </div>
          </div>
        </div>

        <!-- Filter buttons -->
        <div class="filter-row">
          <button v-for="pct in filterOptions" :key="pct"
            :class="['filter-btn', activeFilter === pct ? 'active' : '']"
            @click="activeFilter = pct">
            {{ pct === 'all' ? 'All' : pct + '%' }}
          </button>
        </div>
      </div>
    </div>
  </TeacherLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

const props = defineProps({
  user: Object,
  sections: Array,
  modules: Array,
  sectionProgress: Object,
  completedStudents: Array,
});

const moduleColors = ['#00e5ff', '#00ff88', '#ffaa00', '#ff6b6b'];
const activeFilter = ref('all');
const filterOptions = ['all', 0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100];

const topSection = computed(() => {
  let best = null, bestPct = -1;
  for (const s of props.sections) {
    const vals = Object.values(props.sectionProgress[s.id] || {});
    const avg = vals.length ? vals.reduce((a, b) => a + b, 0) / vals.length : 0;
    if (avg > bestPct) { bestPct = avg; best = s.name; }
  }
  return best || '—';
});

const lowestSection = computed(() => {
  let worst = null, worstPct = 101;
  for (const s of props.sections) {
    const vals = Object.values(props.sectionProgress[s.id] || {});
    const avg = vals.length ? vals.reduce((a, b) => a + b, 0) / vals.length : 0;
    if (avg < worstPct) { worstPct = avg; worst = s.name; }
  }
  return worst || '—';
});
</script>

<style scoped>
.teacher-dashboard { }
.page-title { font-size: 26px; font-weight: 700; color: #00e5ff; text-align: center; margin-bottom: 24px; }
.card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 28px; }
.card-title { font-size: 18px; color: #e0e6ff; margin-bottom: 10px; }
.chart-meta { display: flex; justify-content: space-between; margin-bottom: 12px; }
.top { color: #ffaa00; font-size: 13px; }
.low { color: #ff6b6b; font-size: 13px; }
.legend { display: flex; gap: 16px; margin-bottom: 16px; flex-wrap: wrap; }
.legend-item { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #a0b0d0; }
.legend-dot { width: 12px; height: 12px; border-radius: 3px; }
.chart-area { display: flex; gap: 0; height: 300px; }
.y-axis { display: flex; flex-direction: column; justify-content: space-between; align-items: flex-end; padding-right: 10px; font-size: 11px; color: #6070a0; width: 40px; flex-shrink: 0; }
.bars-area { flex: 1; display: flex; align-items: flex-end; justify-content: space-around; border-left: 1px solid rgba(255,255,255,0.08); border-bottom: 1px solid rgba(255,255,255,0.08); padding: 0 10px; height: 100%; }
.section-group { display: flex; flex-direction: column; align-items: center; gap: 6px; flex: 1; height: 100%; justify-content: flex-end; }
.bars { display: flex; gap: 4px; align-items: flex-end; height: 90%; }
.bar { width: 18px; border-radius: 4px 4px 0 0; transition: height 0.5s ease; }
.section-label { font-size: 11px; color: #6070a0; text-align: center; }
.filter-row { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 20px; }
.filter-btn { padding: 6px 14px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #8090b0; font-size: 12px; cursor: pointer; transition: all 0.2s; }
.filter-btn.active, .filter-btn:hover { background: rgba(0,229,255,0.15); color: #00e5ff; border-color: #00e5ff; border-radius: 50%; }
</style>
