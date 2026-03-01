<template>
  <div class="p-4">
    <h2 class="text-2xl font-bold mb-4">Inventario de Productos</h2>

    <div v-if="loading" class="text-center py-10">Cargando...</div>
    <div v-else-if="error" class="text-red-500">{{ error }}</div>

    <div v-else>
      <table class="min-w-full bg-white border">
        <thead>
          <tr>
            <th class="px-4 py-2 border">ID</th>
            <th class="px-4 py-2 border">Nombre</th>
            <th class="px-4 py-2 border">Marca</th>
            <th class="px-4 py-2 border">Precio</th>
            <th class="px-4 py-2 border">Stock</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="prod in products" :key="prod.id_producto">
            <td class="px-4 py-2 border">{{ prod.id_producto }}</td>
            <td class="px-4 py-2 border">{{ prod.nombre }}</td>
            <td class="px-4 py-2 border">{{ prod.marca }}</td>
            <td class="px-4 py-2 border">{{ prod.precio_venta }}</td>
            <td class="px-4 py-2 border">{{ prod.stock }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { apiFetch } from '../../utils/api';

const products = ref([]);
const loading = ref(false);
const error = ref(null);

const load = async () => {
  loading.value = true;
  error.value = null;
  try {
    const res = await apiFetch('/productos');
    if (!res.ok) {
      if (res.status === 403) {
        error.value = 'Acceso denegado';
      } else {
        error.value = 'Error al cargar productos';
      }
      return;
    }
    products.value = await res.json();
  } catch (err) {
    error.value = 'No se pudo conectar al servidor';
  } finally {
    loading.value = false;
  }
};

onMounted(load);
</script>
