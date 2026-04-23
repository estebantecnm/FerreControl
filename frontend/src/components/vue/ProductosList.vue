<template>
  <div class="space-y-6">
    <!-- Buscador -->
<div class="bg-white p-4 rounded-2xl shadow-sm">      <div class="relative">
        <input 
          v-model="search" 
          type="text" 
          placeholder=" 🔍 Buscar por nombre, marca o modelo..." 
          class="w-full pl-12 pr-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all"
        />
      </div>
    </div>

    <!-- Tabla principal de productos -->
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-left">
        <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] uppercase font-black tracking-widest">
          <tr>
            <th class="p-5">Producto</th>
            <th class="p-5">Precio Venta</th>
            <th class="p-5">Stock Actual</th>
            <th class="p-5"><center>Acciones</center></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="p in filteredProducts" :key="p.id_producto" class="hover:bg-blue-50/30 transition-colors group">
            <td class="p-5">
              <div class="font-bold text-slate-800">{{ p.nombre }}</div>
              <div class="text-[10px] text-slate-400 uppercase tracking-tighter">ID: {{ p.id_producto }}</div>
            </td>
            <td class="p-5 font-bold text-emerald-600">${{ p.precio_venta }}</td>
            <td class="p-5">
              <span :class="p.stock < 10 ? 'text-red-500' : 'text-slate-600'" class="font-mono font-bold">
                {{ p.stock }}
              </span>
            </td>
           <td class="p-5 text-right space-x-2">
            <center>
 <a 
  :href="`/productos/especificaciones/${p.id_producto}`" 
  class="btn-icono bg-slate-100 text-slate-600 hover:bg-slate-200" 
  title="Especificaciones"
>
  ⚙️
</a>

  <a :href="`/productos/actualizar/${p.id_producto}`" class="btn-icono bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white">
    ✏️
  </a>

  <button @click="confirmDelete(p.id_producto)" class="btn-icono bg-red-50 text-red-400 hover:bg-red-500 hover:text-white">
    🗑️
  </button>
  </center>
</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- ==================== MODAL ESPECIFICACIONES (CORREGIDO) ==================== -->
    <div v-if="showSpecs" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-3xl w-full max-w-4xl max-h-[92vh] shadow-2xl overflow-hidden flex flex-col">


        <!-- Body con scroll -->
        <div class="flex-1 p-6 space-y-8 overflow-y-auto">

          <!-- 1. Nuevo Atributo -->
          <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5">
            <div class="text-xs font-black text-slate-400 uppercase mb-4">Nuevo Atributo</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <input 
                v-model="newSpec.nombre_atributo" 
                placeholder="Nombre del atributo (ej: Material)" 
                maxlength="30"
                class="p-4 text-sm border border-slate-300 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500"
              />
              <input 
                v-model="newSpec.valor" 
                placeholder="Valor (ej: Acero Inoxidable)" 
                maxlength="30"
                class="p-4 text-sm border border-slate-300 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500"
              />
              <button 
                @click="saveSpec" 
                :disabled="!newSpec.nombre_atributo?.trim() || !newSpec.valor?.trim() || processing"
                class="md:col-span-2 bg-slate-800 hover:bg-black text-white py-4 rounded-2xl font-bold text-sm transition-all disabled:opacity-40"
              >
                {{ processing ? 'Guardando...' : '➕ Guardar Especificación' }}
              </button>
            </div>
          </div>

          <!-- 2. Tabla de Especificaciones -->
          <div>
            <div class="flex justify-between items-center mb-4">
              <h4 class="text-xs font-black text-slate-400 uppercase">Especificaciones Técnicas</h4>
              <span class="text-xs bg-slate-100 px-3 py-1 rounded-full text-slate-500">{{ specs.length }} atributos</span>
            </div>

            <div v-if="loadingSpecs" class="text-center py-12 bg-slate-50 rounded-2xl">
              Cargando especificaciones...
            </div>

            <!-- Tabla con scroll horizontal y vertical -->
            <div v-else class="overflow-x-auto border border-slate-200 rounded-2xl">
              <table class="w-full min-w-[600px] text-left">
                <thead class="bg-slate-100 sticky top-0">
                  <tr>
                    <th class="px-6 py-4 text-xs font-black uppercase text-slate-500">Atributo</th>
                    <th class="px-6 py-4 text-xs font-black uppercase text-slate-500">Valor</th>
                    <th class="w-28 px-6 py-4"></th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="s in specs" :key="s.id_especificacion" class="hover:bg-slate-50">
                    <td class="px-6 py-5">
                      <span class="text-xs font-black uppercase text-slate-400">{{ s.nombre_atributo }}</span>
                    </td>
                    <td class="px-6 py-5 font-semibold text-slate-700">{{ s.valor }}</td>
                    <td class="px-6 py-5 text-right">
                      <button 
                        @click="deleteSpec(s.id_especificacion)"
                        class="text-red-400 hover:text-red-600 hover:bg-red-50 px-5 py-2 rounded-xl text-sm font-medium transition-all"
                      >
                        Eliminar
                      </button>
                    </td>
                  </tr>

                  <tr v-if="specs.length === 0">
                    <td colspan="3" class="px-6 py-16 text-center text-slate-400 italic">
                      Este producto aún no tiene especificaciones técnicas.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-if="errorSpecs" class="mt-4 text-red-500 text-center text-sm">
              {{ errorSpecs }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { apiFetch } from '../../utils/api';

const products = ref([]);
const search = ref('');
const showSpecs = ref(false);
const activeProduct = ref(null);
const specs = ref([]);
const loadingSpecs = ref(false);
const processing = ref(false);
const errorSpecs = ref('');

const newSpec = ref({
  nombre_atributo: '',
  valor: ''
});

const loadProducts = async () => {
  const res = await apiFetch('/productos');
  if (res.ok) products.value = await res.json();
};

onMounted(loadProducts);

const filteredProducts = computed(() => {
  const term = search.value.toLowerCase().trim();
  if (!term) return products.value;
  return products.value.filter(p => 
    (p.nombre || '').toLowerCase().includes(term) ||
    (p.marca || '').toLowerCase().includes(term) ||
    (p.modelo || '').toLowerCase().includes(term)
  );
});

const openSpecs = async (product) => {
  activeProduct.value = product;
  showSpecs.value = true;
  errorSpecs.value = '';
  newSpec.value = { nombre_atributo: '', valor: '' };
  await fetchSpecs();
};

const fetchSpecs = async () => {
  if (!activeProduct.value) return;
  loadingSpecs.value = true;
  specs.value = [];
  errorSpecs.value = '';

  try {
    const res = await apiFetch(`/especificaciones?id_producto=${activeProduct.value.id_producto}`);
    if (res.ok) {
      const data = await res.json();
      specs.value = Array.isArray(data) ? data : data.data || data.especificaciones || [];
    } else {
      errorSpecs.value = 'No se pudieron cargar las especificaciones';
    }
  } catch (e) {
    errorSpecs.value = 'Error de conexión';
  } finally {
    loadingSpecs.value = false;
  }
};

const saveSpec = async () => {
  if (!newSpec.value.nombre_atributo?.trim() || !newSpec.value.valor?.trim()) return;

  processing.value = true;
  errorSpecs.value = '';

  try {
    const payload = {
      nombre_atributo: newSpec.value.nombre_atributo.trim(),
      valor: newSpec.value.valor.trim(),
      id_producto: activeProduct.value.id_producto
    };

    const res = await apiFetch('/especificaciones', {
      method: 'POST',
      body: JSON.stringify(payload)
    });

    if (res.ok) {
      newSpec.value = { nombre_atributo: '', valor: '' };
      await fetchSpecs();
    } else {
      const err = await res.json().catch(() => ({}));
      errorSpecs.value = err.message || 'No se pudo guardar';
    }
  } catch (err) {
    errorSpecs.value = 'Error de conexión';
  } finally {
    processing.value = false;
  }
};

const deleteSpec = async (id) => {
  if (!confirm('¿Eliminar esta especificación?')) return;
  const res = await apiFetch(`/especificaciones/${id}`, { method: 'DELETE' });
  if (res.ok) await fetchSpecs();
};

const closeSpecs = () => {
  showSpecs.value = false;
  activeProduct.value = null;
  specs.value = [];
  errorSpecs.value = '';
};

const confirmDelete = async (id) => {
  if (confirm('¿Estás seguro de eliminar este producto?')) {
    const res = await apiFetch(`/productos/${id}`, { method: 'DELETE' });
    if (res.ok) loadProducts();
  }
};
</script>