<template>
  <div class="p-4 max-w-lg mx-auto">
    <h2 class="text-2xl font-bold mb-4">Registrar Producto</h2>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block font-medium">Nombre</label>
        <input v-model="form.nombre" type="text" class="w-full border p-2" required />
      </div>
      <div>
        <label class="block font-medium">Marca</label>
        <input v-model="form.marca" type="text" class="w-full border p-2" required />
      </div>
      <div>
        <label class="block font-medium">Precio venta</label>
        <input v-model.number="form.precio_venta" type="number" step="0.01" class="w-full border p-2" required />
      </div>
      <div>
        <label class="block font-medium">Precio compra</label>
        <input v-model.number="form.precio_compra" type="number" step="0.01" class="w-full border p-2" required />
      </div>
      <div>
        <label class="block font-medium">Utilidad (%)</label>
        <input v-model.number="form.utilidad" type="number" step="0.01" class="w-full border p-2" required />
      </div>
      <div>
        <label class="block font-medium">Código de barras</label>
        <input v-model="form.codigo_barras" type="text" class="w-full border p-2" required />
      </div>
      <div>
        <label class="block font-medium">Status</label>
        <select v-model="form.status" class="w-full border p-2" required>
          <option value="Activo">Activo</option>
          <option value="Inactivo">Inactivo</option>
        </select>
      </div>
      <div>
        <label class="block font-medium">Unidad de medida</label>
        <input v-model="form.unidad_medida" type="text" class="w-full border p-2" required />
      </div>
      <div>
        <label class="block font-medium">Cantidad presentación</label>
        <input v-model.number="form.cantidad_presentacion" type="number" class="w-full border p-2" required />
      </div>
      <div>
        <label class="block font-medium">Color</label>
        <input v-model="form.color" type="text" class="w-full border p-2" />
      </div>
      <div>
        <label class="block font-medium">Categoría</label>
        <select v-model.number="form.id_categoria" class="w-full border p-2" required>
          <option disabled value="">Seleccione una categoría</option>
          <option v-for="cat in categories" :key="cat.id_categoria" :value="cat.id_categoria">
            {{ cat.nombre_categoria }}
          </option>
        </select>
      </div>
      <div>
        <label class="block font-medium">Cantidad inicial (opcional)</label>
        <input v-model.number="form.cantidad_inicial" type="number" class="w-full border p-2" min="0" />
      </div>

      <div class="flex justify-end">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded" :disabled="loading">
          {{ loading ? 'Guardando...' : 'Registrar' }}
        </button>
      </div>

      <div v-if="error" class="text-red-500 mt-2">{{ error }}</div>
      <div v-if="success" class="text-green-500 mt-2">{{ success }}</div>
    </form>
  </div>
</template>

<script setup>
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
    // ignore for now
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
      // reset form
      Object.assign(form.value, {
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
