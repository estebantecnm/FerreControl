<template>
  <div class="p-4">
    <h2 class="text-2xl font-bold mb-4">Reportes</h2>

    <section class="mb-8">
      <h3 class="text-xl font-semibold mb-2">Ganancias</h3>
      <div v-if="loadingGanancias" class="py-2">Cargando...</div>
      <div v-else-if="errorGanancias" class="text-red-500">{{ errorGanancias }}</div>
      <div v-else-if="ganancias && ganancias.resumen">
        <ul class="space-y-1">
          <li>Total operaciones: {{ ganancias.resumen.total_operaciones }}</li>
          <li>Ingresos brutos: {{ ganancias.resumen.ingresos_brutos }}</li>
          <li>Costo inventario: {{ ganancias.resumen.costo_inventario }}</li>
          <li>Ganancia real: {{ ganancias.resumen.ganancia_real }}</li>
          <li>Margen promedio: {{ ganancias.resumen.margen_promedio }}</li>
        </ul>
      </div>
      <div v-else class="text-gray-500">Sin información disponible</div>
    </section>

    <section>
      <h3 class="text-xl font-semibold mb-2">Stock Crítico</h3>
      <div v-if="loadingStock" class="py-2">Cargando...</div>
      <div v-else-if="errorStock" class="text-red-500">{{ errorStock }}</div>
      <div v-else>
        <table class="min-w-full bg-white border">
          <thead>
            <tr>
              <th class="px-2 py-1 border">ID</th>
              <th class="px-2 py-1 border">Nombre</th>
              <th class="px-2 py-1 border">Marca</th>
              <th class="px-2 py-1 border">Stock actual</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in stockCritico.data" :key="p.id_producto">
              <td class="px-2 py-1 border">{{ p.id_producto }}</td>
              <td class="px-2 py-1 border">{{ p.nombre }}</td>
              <td class="px-2 py-1 border">{{ p.marca }}</td>
              <td class="px-2 py-1 border">{{ p.stock_actual }}</td>
            </tr>
          </tbody>
        </table>
        <div v-if="stockCritico.data.length === 0" class="mt-2">
          {{ stockCritico.msj || 'No hay productos críticos' }}
        </div>
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
  errorGanancias.value = null;
  try {
    const res = await apiFetch('/reportes/ganancias');
    if (res.ok) {
      ganancias.value = await res.json();
    } else {
      errorGanancias.value = 'Error al obtener reporte de ganancias';
    }
  } catch (e) {
    errorGanancias.value = 'No se pudo conectar';
  } finally {
    loadingGanancias.value = false;
  }
};

const loadStock = async () => {
  loadingStock.value = true;
  errorStock.value = null;
  try {
    const res = await apiFetch('/reportes/stock-critico');
    if (res.ok) {
      stockCritico.value = await res.json();
    } else {
      errorStock.value = 'Error al obtener stock crítico';
    }
  } catch (e) {
    errorStock.value = 'No se pudo conectar';
  } finally {
    loadingStock.value = false;
  }
};

onMounted(() => {
  loadGanancias();
  loadStock();
});
</script>
