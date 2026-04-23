<template>
  <div class="max-w-6xl mx-auto">
    <header class="mb-10">
      <h2 class="text-3xl font-bold tracking-tight" style="color: var(--text)">Resumen General</h2>
      <p class="text-muted mt-1 small">Análisis de rendimiento y alertas de inventario</p>
    </header>

    <section class="grid grid-cols-2 gap-6 mb-12">
      <div v-if="loadingGanancias" class="col-span-4 py-10 text-center text-muted italic">Cargando métricas...</div>
      
      <template v-else-if="ganancias && ganancias.resumen">
        <div class="card hover:shadow-md transition-shadow">
          <h3 class="text-muted text-[13px] font-bold uppercase tracking-widest" style="color: var(--text)">Operaciones</h3>
          <p class="text-3xl font-bold mt-2" style="color: var(--accent-nav);">{{ ganancias.resumen.total_operaciones }}</p>
        </div>
        
        <div class="card hover:shadow-md transition-shadow">
          <h3 class="text-muted text-[13px] font-bold uppercase tracking-widest" style="color: var(--text)">Ingresos Brutos</h3>
          <p class="text-3xl font-bold mt-2" style="color: var(--accent-nav);">${{ ganancias.resumen.ingresos_brutos }}</p>
        </div>

        <div class="card hover:shadow-md transition-shadow">
          <h3 class="text-muted text-[13px] font-bold uppercase tracking-widest" style="color: var(--text)">Ganancia Real</h3>
          <p class="text-3xl font-bold mt-2 text-emerald-600" style="color: var(--accent-nav);">${{ ganancias.resumen.ganancia_real }}</p>
        </div>

        <div class="card hover:shadow-md transition-shadow">
          <h3 class="text-muted text-[13px] font-bold uppercase tracking-widest" style="color: var(--text)">Margen</h3>
          <p class="text-3xl font-bold mt-2 text-blue-600" style="color: var(--accent-nav);">{{ ganancias.resumen.margen_promedio }}</p>
        </div>
      </template>
    </section>

    <section class="card overflow-hidden !p-0">
      <div class="p-6 border-b bg-slate-50/50" style="border-color: var(--border)">
        <h3 class="font-bold uppercase" style="color: var(--text)">Alertas de Stock Crítico</h3>
      </div>
      
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr>
              <th>ID</th>
              <th>PRODUCTO</th>
              <th>MARCA</th>
              <th class="text-right">ESTADO</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in stockCritico.data" :key="p.id_producto">
              <td class="font-mono text-xs text-muted">#00{{ p.id_producto }}</td>
              <td class="font-semibold">{{ p.nombre }}</td>
              <td>
                <span class="small px-2 py-0.5 rounded border text-muted " style="border-color: var(--accent-muted); background: var(--accent-muted)">
                  {{ p.marca }}
                </span>
              </td>
              <td class="text-right">
                <span :class="p.stock_actual <= 0 ? 'text-red-600' : 'text-orange-600'" 
                      class="small font-bold uppercase tracking-tighter">
                  {{ p.stock_actual <= 0 ? 'Sin Stock' : 'Bajo Stock' }} ({{ p.stock_actual }})
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { apiFetch } from '../../utils/api';

const ganancias = ref(null);
const stockCritico = ref({ data: [] });
const loadingGanancias = ref(false);
const loadingStock = ref(false);
const errorGanancias = ref(null);
const errorStock = ref(null);

const loadGanancias = async () => {
  loadingGanancias.value = true;
  try {
    const res = await apiFetch('/reportes/ganancias');
    if (res.ok) ganancias.value = await res.json();
  } catch (e) {
    errorGanancias.value = 'Error de conexión';
  } finally {
    loadingGanancias.value = false;
  }
};

const loadStock = async () => {
  loadingStock.value = true;
  try {
    const res = await apiFetch('/reportes/stock-critico');
    if (res.ok) stockCritico.value = await res.json();
  } catch (e) {
    errorStock.value = 'Error de conexión';
  } finally {
    loadingStock.value = false;
  }
};

onMounted(() => {
  loadGanancias();
  loadStock();
});
</script>