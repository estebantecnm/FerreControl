<template>
<div class="mt-8 bg-white rounded-xl shadow-sm overflow-hidden">
   <div class="p-8 bg-slate-50/30 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Inventario de Productos</h2>
        <p class="text-slate-500 mt-1">
          {{ isAdmin ? 'Panel de control total de existencias y costos' : 'Consulta de precios y stock disponible' }}
        </p>
      </div>
      
      <div class="flex items-center gap-3">

        <span class="text-xs font-medium text-slate-400 bg-slate-100 px-2 py-1 rounded" style="color: var(--text)">
         <b> {{ filteredProducts.length }} Productos </b>
        </span>
      </div>
    </div>

    <div class="px-8 pb-4">
      <div class="relative">

        <input
          v-model="search"
          placeholder=" 🔍 Buscar por nombre, marca o código de barras..."
          class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
          @input="currentPage = 1"
        />
      </div>
    </div>

    <div v-if="loading" class="p-20 text-center">
      <div class="animate-spin inline-block w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full mb-4"></div>
      <p class="text-slate-500 italic">Cargando catálogo de la ferretería...</p>
    </div>

    <div v-else-if="error" class="p-10 text-center text-red-500 font-medium">
      ⚠️ {{ error }}
    </div>

    <div v-else class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-slate-100 border-b border-slate-200">
            <th class="px-4 py-4 text-left text-slate-500 font-bold uppercase tracking-wider">ID</th>
            <th class="px-4 py-4 text-left text-slate-500 font-bold uppercase tracking-wider">Producto</th>
            <th class="px-4 py-4 text-left text-slate-500 font-bold uppercase tracking-wider">Marca</th>
            <th v-if="isAdmin" class="px-4 py-4 text-left text-slate-500 font-bold uppercase tracking-wider">Cód. Barras</th>
            <th class="px-4 py-4 text-right text-slate-500 font-bold uppercase tracking-wider">P. Venta</th>
            <th v-if="isAdmin" class="px-4 py-4 text-right text-orange-600 font-bold uppercase tracking-wider">P. Compra</th>
            <th class="px-4 py-4 text-center text-slate-500 font-bold uppercase tracking-wider">Stock</th>
            <th v-if="isAdmin" class="px-4 py-4 text-center text-slate-500 font-bold uppercase tracking-wider">Gestión</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="prod in paginatedProducts" :key="prod.id_producto" class="hover:bg-blue-50/30 transition-colors">
            <td class="px-4 py-4 font-mono text-xs text-slate-400">#{{ prod.id_producto }}</td>
            <td class="px-4 py-4">
              <div class="font-bold text-slate-700">{{ prod.nombre }}</div>
              <div v-if="isAdmin" class="text-[10px] text-slate-400 italic">Cat ID: {{ prod.id_categoria }}</div>
            </td>
            <td class="px-4 py-4 text-slate-500 uppercase text-xs">{{ prod.marca }}</td>
            <td v-if="isAdmin" class="px-4 py-4 font-mono text-xs text-blue-600">{{ prod.codigo_barras }}</td>
            <td class="px-4 py-4 font-bold text-right text-slate-900">${{ Number(prod.precio_venta).toFixed(2) }}</td>
            <td v-if="isAdmin" class="px-4 py-4 text-right text-orange-600 font-medium">${{ Number(prod.precio_compra).toFixed(2) }}</td>
            
            <td class="px-4 py-4">
              <div class="flex flex-col items-center">
                <span :class="getStockClass(prod.stock)" class="px-2 py-0.5 rounded text-[10px] font-black uppercase">
                   {{ getStockLabel(prod.stock) }}
                </span>
                <span class="text-slate-400 font-mono text-[10px] mt-1">{{ prod.stock }} {{ prod.unidad_medida }}</span>
              </div>
            </td>

            <td v-if="isAdmin" class="px-4 py-4 text-center">
              <a :href="`/inventario/actualizar/${prod.id_producto}`" 
                 class="inline-block bg-slate-800 text-white text-[10px] px-3 py-2 rounded font-bold uppercase hover:bg-blue-600 transition-all shadow-sm">
                + Stock
              </a>
            </td>
          </tr>
        </tbody>
      </table>

<div class="p-4 bg-slate-50 flex flex-col md:flex-row justify-between items-center gap-4">        
  <div class="text-xs text-slate-500">
          Mostrando {{ paginatedProducts.length }} de {{ filteredProducts.length }} productos
        </div>
        
        <div class="flex items-center gap-2">
          <button 
            @click="currentPage--" 
            :disabled="currentPage === 1"
            class="px-3 py-1 border rounded bg-white text-sm disabled:opacity-50 hover:bg-slate-100 transition-colors"
          >
            Anterior
          </button>
          
          <div class="flex gap-1">
            <button 
              v-for="page in totalPages" 
              :key="page"
              @click="currentPage = page"
              :class="currentPage === page ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100'"
              class="w-8 h-8 border rounded text-xs font-bold transition-colors"
            >
              {{ page }}
            </button>
          </div>

          <button 
            @click="currentPage++" 
            :disabled="currentPage === totalPages"
            class="px-3 py-1 border rounded bg-white text-sm disabled:opacity-50 hover:bg-slate-100 transition-colors"
          >
            Siguiente
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { apiFetch } from '../../utils/api';
import { auth } from '../../utils/auth';

// Estado
const products = ref([]);
const loading = ref(false);
const error = ref(null);
const isAdmin = ref(false);

// Filtros y Paginación
const search = ref('');
const currentPage = ref(1);
const itemsPerPage = 10;

// Lógica de Búsqueda
const filteredProducts = computed(() => {
  if (!search.value.trim()) return products.value;
  
  const term = search.value.toLowerCase();
  return products.value.filter(p => 
    p.nombre.toLowerCase().includes(term) ||
    p.marca.toLowerCase().includes(term) ||
    (p.codigo_barras && p.codigo_barras.toString().includes(term))
  );
});

// Lógica de Paginación
const totalPages = computed(() => Math.ceil(filteredProducts.value.length / itemsPerPage));

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredProducts.value.slice(start, end);
});

// Métodos
const load = async () => {
  loading.value = true;
  error.value = null;
  try {
    const res = await apiFetch('/productos');
    if (res.ok) {
      products.value = await res.json();
    } else {
      error.value = 'No se pudo cargar el inventario';
    }
  } catch (err) {
    error.value = 'Error de conexión con el servidor';
  } finally {
    loading.value = false;
  }
};

const getStockClass = (val) => {
  if (val <= 0) return 'bg-red-100 text-red-700 ring-1 ring-red-200';
  if (val <= 5) return 'bg-orange-100 text-orange-700 ring-1 ring-orange-200';
  return 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200';
};

const getStockLabel = (val) => {
  if (val <= 0) return 'Agotado';
  if (val <= 5) return 'Crítico';
  return 'En Stock';
};

onMounted(() => {
  const user = auth.getUser();
  if (user && Number(user.rol) === 1) isAdmin.value = true;
  load();
});
</script>