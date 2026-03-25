<template>
  <div class="max-w-4xl mx-auto p-4 md:p-8">
    <div class="card !p-0 shadow-sm overflow-hidden">
      
      <div class="p-6 border-b bg-gray-50/50" style="border-color: var(--border)">
        <h2 class="text-xl font-bold" style="color: var(--text)">Registrar Producto</h2>
        <p class="text-muted small">Ingrese la información técnica y comercial del nuevo artículo</p>
      </div>

      <form @submit.prevent="submit" class="p-6 md:p-8 space-y-8">
        
        <section>
          <h3 class="text-xs font-black uppercase tracking-wider text-blue-600 mb-4">Datos Generales</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-bold mb-1">Nombre</label>
              <input v-model="form.nombre" type="text" class="w-full" placeholder="Ej. Martillo de bola" required />
            </div>
            <div>
              <label class="block text-sm font-bold mb-1">Código de barras</label>
              <input v-model="form.codigo_barras" type="text" class="w-full font-mono" placeholder="750..." required />
            </div>
            <div>
              <label class="block text-sm font-bold mb-1">Marca</label>
              <input v-model="form.marca" type="text" class="w-full" placeholder="Truper" required />
            </div>
            <div>
              <label class="block text-sm font-bold mb-1">Categoría</label>
              <select v-model.number="form.id_categoria" class="w-full" required>
                <option disabled value="null">Seleccione una categoría</option>
                <option v-for="cat in categories" :key="cat.id_categoria" :value="cat.id_categoria">
                  {{ cat.nombre_categoria }}
                </option>
              </select>
            </div>
          </div>
        </section>

        <section>
          <h3 class="text-xs font-black uppercase tracking-wider text-emerald-600 mb-4">Costos e Inventario</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-slate-50 p-4 rounded-lg border border-dashed" style="border-color: var(--border)">
            <div>
              <label class="block text-xs font-bold text-muted uppercase mb-1">Precio Compra</label>
              <input v-model.number="form.precio_compra" type="number" step="0.01" class="w-full bg-white" required />
            </div>
            <div>
              <label class="block text-xs font-bold text-muted uppercase mb-1">Utilidad (%)</label>
              <input v-model.number="form.utilidad" type="number" step="0.01" class="w-full bg-white text-blue-600 font-bold" required />
            </div>
            <div>
              <label class="block text-xs font-bold text-muted uppercase mb-1">Precio Venta</label>
              <input v-model.number="form.precio_venta" type="number" step="0.01" class="w-full bg-white font-bold" required />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div>
              <label class="block text-sm font-bold mb-1">Stock Inicial</label>
              <input v-model.number="form.cantidad_inicial" type="number" class="w-full" min="0" />
            </div>
            <div>
              <label class="block text-sm font-bold mb-1">Unidad de medida</label>
              <input v-model="form.unidad_medida" type="text" class="w-full" placeholder="Pza, Kg..." required />
            </div>
            <div>
              <label class="block text-sm font-bold mb-1">Status</label>
              <select v-model="form.status" class="w-full" required>
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
              </select>
            </div>
          </div>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
          <div>
            <label class="block text-sm font-bold mb-1">Cantidad presentación</label>
            <input v-model.number="form.cantidad_presentacion" type="number" class="w-full" required />
          </div>
          <div>
            <label class="block text-sm font-bold mb-1">Color (Opcional)</label>
            <input v-model="form.color" type="text" class="w-full" placeholder="N/A" />
          </div>
        </section>

        <div class="flex flex-col md:flex-row items-center justify-between pt-6 border-t gap-4" style="border-color: var(--border)">
          <div class="flex-1">
            <div v-if="error" class="text-red-500 text-sm font-bold bg-red-50 p-2 rounded border border-red-100 italic">{{ error }}</div>
            <div v-if="success" class="text-green-600 text-sm font-bold bg-green-50 p-2 rounded border border-green-100">{{ success }}</div>
          </div>
          <div class="flex gap-4 w-full md:w-auto">
            <button type="submit" class="btn btn-primary px-10 py-2.5 font-bold shadow-sm w-full md:w-auto" :disabled="loading">
              {{ loading ? 'Guardando...' : 'Registrar Producto' }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
/* EL SCRIPT SE MANTIENE EXACTAMENTE IGUAL AL ORIGINAL */
import { ref, onMounted } from 'vue';
import { apiFetch } from '../../utils/api';
import { auth } from '../../utils/auth';

const form = ref({
  nombre: '',
  marca: '',
  precio_venta: 0,
  precio_compra: 0,
  utilidad: 0,
  codigo_barras: '',
  status: 'Activo',
  unidad_medida: '',
  cantidad_presentacion: 1,
  color: '',
  id_categoria: null,
  cantidad_inicial: 0,
  id_usuario: null,
});

const categories = ref([]);
const loading = ref(false);
const error = ref(null);
const success = ref(null);

const loadCategories = async () => {
  try {
    const res = await apiFetch('/categorias');
    if (res.ok) {
      categories.value = await res.json();
    }
  } catch (e) {
    console.error(e);
  }
};

onMounted(() => {
  const user = auth.getUser();
  if (user) form.value.id_usuario = user.id_usuario;
  loadCategories();
});

const submit = async () => {
  loading.value = true;
  error.value = null;
  success.value = null;

  try {
    const payload = { ...form.value };
    const res = await apiFetch('/productos', {
      method: 'POST',
      body: JSON.stringify(payload),
    });
    if (res.ok) {
      success.value = 'Producto registrado correctamente';
      Object.assign(form.value, {
        nombre: '', marca: '', precio_venta: 0, precio_compra: 0,
        utilidad: 0, codigo_barras: '', status: 'Activo',
        unidad_medida: '', cantidad_presentacion: 1, color: '',
        id_categoria: null, cantidad_inicial: 0,
      });
    } else if (res.status === 422) {
      const data = await res.json();
      error.value = Object.values(data).flat().join(', ');
    } else if (res.status === 403) {
      error.value = 'Acceso denegado';
    } else {
      error.value = 'Error al guardar';
    }
  } catch (e) {
    error.value = 'No se pudo conectar con el servidor';
  } finally {
    loading.value = false;
  }
};
</script>