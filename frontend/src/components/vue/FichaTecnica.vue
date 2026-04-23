<template>
  <div class="max-w-4xl mx-auto p-8 mt-10">
    <div v-if="producto" class="bg-slate-800 text-white p-6 rounded-t-3xl flex justify-between items-center">
      <div>
        <h3 class="font-black uppercase text-sm tracking-widest">Ficha Técnica</h3>
        <p class="text-lg font-semibold">{{ producto.nombre }}</p>
      </div>
      <a href="/productos/listaProductos" class="text-3xl hover:text-red-400 transition-colors">&times;</a>
    </div>

    <div class="bg-white p-6 rounded-b-3xl shadow-sm">
      
<div class="bg-slate-50 rounded-2xl p-5 mb-8">
            <div class="text-xs font-black text-slate-400 uppercase mb-4">Nuevo Atributo</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input v-model="newSpec.nombre_atributo" placeholder="Nombre (ej: Material)" maxlength="30" class="p-4 text-sm border border-slate-300 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500" />
          <input v-model="newSpec.valor" placeholder="Valor (ej: Acero)" maxlength="30" class="p-4 text-sm border border-slate-300 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500" />
          <button @click="saveSpec" :disabled="processing" class="md:col-span-2 bg-slate-800 hover:bg-black text-white py-4 rounded-2xl font-bold text-sm transition-all" 
          style="
        background-color: #f59e0b;
        color: rgb(0, 0, 0);
        padding: 5px 15px;
        border: none;
        border-radius: 20px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.25s ease;
    ">
            {{ processing ? 'Guardando...' : '➕ Guardar Especificación' }}
          </button>
        </div>
      </div>

      <div v-if="loadingSpecs" class="text-center py-12">Cargando...</div>
      
      <div v-else class="overflow-x-auto rounded-2xl">
        <table class="w-full text-left">
          <thead class="bg-slate-100">
            <tr>
              <th class="px-6 py-4 text-xs font-black uppercase text-slate-500">Atributo</th>
              <th class="px-6 py-4 text-xs font-black uppercase text-slate-500">Valor</th>
              <th class="px-6 py-4"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="s in specs" :key="s.id_especificacion" class="hover:bg-slate-50">
              <td class="px-6 py-5 uppercase text-xs font-black text-slate-400">{{ s.nombre_atributo }}</td>
              <td class="px-6 py-5 font-semibold text-slate-700">{{ s.valor }}</td>
              <td class="px-6 py-5 text-right">
                <button @click="deleteSpec(s.id_especificacion)" class="text-red-400 hover:text-red-600"
                style="
        background-color: #f59e0b;
        color: rgb(0, 0, 0);
        padding: 5px 15px;
        border: none;
        border-radius: 20px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.25s ease;
    ">Eliminar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { apiFetch } from '../../utils/api';

const props = defineProps({ idProducto: [String, Number] });

// Estados
const producto = ref(null);
const specs = ref([]);
const newSpec = ref({ nombre_atributo: '', valor: '' });
const loadingSpecs = ref(false);
const processing = ref(false);

// Cargar datos al iniciar
onMounted(async () => {
  // Cargar info del producto
  const resProd = await apiFetch(`/productos/${props.idProducto}`);
  if (resProd.ok) producto.value = await resProd.json();
  
  // Cargar especificaciones
  await fetchSpecs();
});

const fetchSpecs = async () => {
  loadingSpecs.value = true;
  const res = await apiFetch(`/especificaciones?id_producto=${props.idProducto}`);
  if (res.ok) {
    const data = await res.json();
    specs.value = Array.isArray(data) ? data : (data.data || []);
  }
  loadingSpecs.value = false;
};

const saveSpec = async () => {
  processing.value = true;
  await apiFetch('/especificaciones', {
    method: 'POST',
    body: JSON.stringify({ ...newSpec.value, id_producto: props.idProducto })
  });
  newSpec.value = { nombre_atributo: '', valor: '' };
  await fetchSpecs();
  processing.value = false;
};

const deleteSpec = async (id) => {
  if (confirm('¿Eliminar?')) {
    await apiFetch(`/especificaciones/${id}`, { method: 'DELETE' });
    await fetchSpecs();
  }
};
</script>
