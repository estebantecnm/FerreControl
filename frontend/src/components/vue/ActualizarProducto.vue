<template>
  <div class="max-w-7xl mx-auto p-6 bg-white shadow-lg rounded-lg">
    <h1 class="text-3xl font-bold mb-2">Actualizar Producto</h1>
    <p class="text-slate-600 mb-8">Ingrese la información técnica y comercial del nuevo artículo</p>

    <form @submit.prevent="submit" class="space-y-12">
      <section>
        <h2 class="text-xl font-bold uppercase tracking-wide mb-6">DATOS GENERALES</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-bold mb-1">Nombre</label>
            <input v-model="form.nombre" type="text" class="w-full border p-2 rounded" placeholder="Ej. Martillo de bola" required />
          </div>
          <div>
            <label class="block text-sm font-bold mb-1">Código de barras</label>
            <input v-model="form.codigo_barras" type="text" class="w-full border p-2 rounded" placeholder="750..." />
          </div>
          <div>
            <label class="block text-sm font-bold mb-1">Marca</label>
            <input v-model="form.marca" type="text" class="w-full border p-2 rounded" placeholder="Truper" required />
          </div>
          <div>
            <label class="block text-sm font-bold mb-1">Categoría</label>
            <select v-model.number="form.id_categoria" class="w-full border p-2 rounded" required>
              <option :value="null" disabled>Seleccione una categoría</option>
              <option v-for="cat in categories" :key="cat.id_categoria" :value="cat.id_categoria">
                {{ cat.nombre_categoria }}
              </option>
            </select>
          </div>
        </div>
      </section>

      <section>
        <h2 class="text-xl font-bold uppercase tracking-wide mb-6">COSTOS E INVENTARIO</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-bold text-slate-500 uppercase mb-1">PRECIO COMPRA</label>
            <input v-model.number="form.precio_compra" type="number" step="0.01" class="w-full border p-2 rounded" required />
          </div>
          <div>
            <label class="block text-sm font-bold text-slate-500 uppercase mb-1">UTILIDAD (%)</label>
            <input v-model.number="form.utilidad" type="number" step="0.01" class="w-full border p-2 rounded" required />
          </div>
          <div>
            <label class="block text-sm font-bold text-slate-500 uppercase mb-1">PRECIO VENTA</label>
            <input v-model.number="form.precio_venta" type="number" step="0.01" class="w-full border p-2 rounded font-bold text-blue-700" required />
          </div>
          <div>
            <label class="block text-sm font-bold mb-1">Stock Inicial</label>
            <input v-model.number="form.cantidad_inicial" type="number" class="w-full border p-2 rounded bg-slate-50" />
          </div>
          <div>
            <label class="block text-sm font-bold mb-1">Unidad de medida</label>
            <input v-model="form.unidad_medida" type="text" class="w-full border p-2 rounded" placeholder="Pza, Kg..." />
          </div>
          <div>
            <label class="block text-sm font-bold mb-1">Status</label>
            <select v-model="form.status" class="w-full border p-2 rounded" required>
              <option value="Activo">Activo</option>
              <option value="Inactivo">Inactivo</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-bold mb-1">Cantidad presentación</label>
            <input v-model.number="form.cantidad_presentacion" type="number" class="w-full border p-2 rounded" />
          </div>
          <div>
            <label class="block text-sm font-bold mb-1">Color (Opcional)</label>
            <input v-model="form.color" type="text" class="w-full border p-2 rounded" placeholder="N/A" />
          </div>
        </div>
      </section>

      <p v-if="success" class="text-emerald-600 font-bold text-center">{{ success }}</p>
      <p v-if="error" class="text-red-500 font-bold text-center">{{ error }}</p>

      <div class="pt-8">
        <button type="submit" class="w-full bg-slate-900 text-white p-4 rounded font-bold text-lg uppercase tracking-wider hover:bg-slate-800 disabled:bg-slate-500" :disabled="loading">
          {{ loading ? 'Actualizando...' : 'Actualizar Producto' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { apiFetch } from '../../utils/api';

const props = defineProps(['idProducto']);

// Estructura de formulario idéntica a RegistrarProducto
const form = ref({
  nombre: '',
  codigo_barras: '',
  marca: '',
  id_categoria: null,
  precio_compra: 0,
  utilidad: 0,
  precio_venta: 0,
  cantidad_inicial: 0,
  unidad_medida: 'Pza',
  status: 'Activo',
  cantidad_presentacion: 1,
  color: ''
});

const categories = ref([]);
const loading = ref(false);
const error = ref(null);
const success = ref(null);

const cargarDatos = async () => {
  if (!props.idProducto) return;
  loading.value = true;
  error.value = null;
  success.value = null;

  try {
    // 1. Cargar Categorías
    const resCat = await apiFetch('/categorias');
    if (resCat.ok) categories.value = await resCat.json();

    // 2. Cargar Producto
    const resProd = await apiFetch(`/productos/${props.idProducto}`);
    if (resProd.ok) {
      const data = await resProd.json();
      // "Rellenamos" el formulario con los datos de la DB
      Object.assign(form.value, data);
    } else {
      error.value = "Producto no encontrado.";
    }
  } catch (e) {
    error.value = "Error al conectar con la base de datos.";
  } finally {
    loading.value = false;
  }
};

onMounted(cargarDatos);

// Vigilar si el ID cambia para recargar
watch(() => props.idProducto, cargarDatos);

const submit = async () => {
  loading.value = true;
  error.value = null;
  success.value = null;

  const datosAEnviar = { 
    ...form.value,
    id_usuario: 1 // Forzamos el admin por defecto
  };

  try {
    const res = await apiFetch(`/productos/${props.idProducto}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(datosAEnviar),
    });

    const data = await res.json();

    if (res.ok) {
      success.value = '¡Producto actualizado correctamente!';
      setTimeout(() => window.location.href = '/inventario/lista', 1500);
    } else {
      // Si el error es por el código de barras, ahora lo veremos claro
      error.value = data.codigo_barras ? data.codigo_barras[0] : (data.message || 'Error al actualizar');
    }
  } catch (e) {
    error.value = 'Error de conexión.';
  } finally {
    loading.value = false;
  }
};

</script>