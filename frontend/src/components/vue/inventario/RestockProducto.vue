<template>
  <div class="flex justify-end">
  <a href="/inventario/lista" 
     class="text-3xl hover:text-red-400 transition-colors">
    &times;
  </a>
</div>
  <div class="max-w-2xl mx-auto p-8 bg-white shadow-xl rounded-2xl ">
    <div class="flex items-center gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Entrada de Stock 📦</h1>
        <p class="text-slate-500 text-sm">Aumentar existencias en inventario</p>
      </div>
    </div>

    <div v-if="loading && !product" class="animate-pulse space-y-4">
      <div class="h-10 bg-slate-100 rounded w-3/4"></div>
      <div class="h-20 bg-slate-100 rounded"></div>
    </div>

    <div v-else-if="product">
      <div class="mb-8 p-4 bg-slate-50 rounded-xl">
        <h2 class="text-lg font-bold text-slate-700">{{ product.nombre }}</h2>
        <div class="flex justify-between mt-2">
          <span class="text-slate-500 text-sm">Stock Actual:</span>
          <span class="font-mono font-bold text-slate-900 text-lg">{{ product.stock }} {{ product.unidad_medida }}</span>
        </div>
      </div>

      <form @submit.prevent="submitRestock" class="space-y-6">
        <div>
          <label class="block text-xs font-black text-slate-400 uppercase mb-2 tracking-widest" style="color: #d97706;"><b>Cantidad a añadir</b></label>
          <input 
            v-model.number="amountToAdd" 
            type="number" 
            min="1"
            class="w-full text-3xl font-mono text-center p-4 bg-white border-2 border-slate-200 rounded-2xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all"
            placeholder="0"
            required
          />
        </div>

        <div class="bg-emerald-50 p-4 rounded-xl flex justify-between items-center">
          <span class="text-emerald-700 font-medium uppercase" style="color: green;"><b>Stock Final tras operación:</b></span>
          <span class="text-2xl font-black text-emerald-600 font-mono" style="color: green;"><b>
            {{ product.stock + (amountToAdd || 0) }}
            </b>
          </span>
        </div>

        <div class="pt-4 flex gap-3">
          <a href="/inventario/lista" class="w-1/3 text-center py-4 text-slate-400 font-bold hover:text-slate-600 transition-colors" style="color: red;">
            CANCELAR
          </a>
          <button 
            type="submit" 
            :disabled="processing || amountToAdd <= 0"
            class="w-2/3 bg-slate-900 text-white p-4 rounded-xl font-black text-lg uppercase shadow-lg hover:bg-black disabled:bg-slate-300 transition-all"
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
    "
          >
            {{ processing ? 'PROCESANDO...' : 'CONFIRMAR ENTRADA' }}
          </button>
        </div>
      </form>

      <p v-if="statusMsg" :class="isError ? 'text-red-500' : 'text-emerald-600'" class="mt-4 text-center font-bold">
        {{ statusMsg }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { apiFetch } from '../../../utils/api';

const props = defineProps(['idProducto']);
const product = ref(null);
const amountToAdd = ref(1);
const loading = ref(true);
const processing = ref(false);
const statusMsg = ref('');
const isError = ref(false);

const cargarProducto = async () => {
  try {
    const res = await apiFetch(`/productos/${props.idProducto}`);
    if (res.ok) {
      product.value = await res.json();
    } else {
      statusMsg.value = "Producto no encontrado.";
      isError.value = true;
    }
  } catch (e) {
    statusMsg.value = "Error de conexión.";
    isError.value = true;
  } finally {
    loading.value = false;
  }
};

onMounted(cargarProducto);

const submitRestock = async () => {
  processing.value = true;
  statusMsg.value = '';
  
  try {
    const payload = {
      tipo_movimiento: 'Entrada',
      cantidad: amountToAdd.value,
      stock_anterior: product.value.stock,
      stock_nuevo: product.value.stock + amountToAdd.value,
      id_producto: props.idProducto,
      id_usuario: 1 // <--- VERIFICA QUE ESTE ID EXISTA EN LA TABLA USUARIOS
    };

    console.log("Enviando Payload:", payload); // DEBUG 1

    const res = await apiFetch('/movimientos-stock', {
      method: 'POST',
      body: JSON.stringify(payload),
    });

    console.log("Status de la respuesta:", res.status); // DEBUG 2

    // Leemos el cuerpo de la respuesta pase lo que pase
    const responseData = await res.json();
    console.log("Cuerpo de la respuesta del servidor:", responseData); // DEBUG 3

    if (res.ok) {
      statusMsg.value = "✅ Movimiento registrado con éxito.";
      // setTimeout(() => window.location.href = '/inventario/lista', 1500);
    } else {
      isError.value = true;
      statusMsg.value = "Error del servidor: " + (responseData.message || "Ver consola");
    }
  } catch (e) {
    isError.value = true;
    statusMsg.value = "Error de conexión: " + e.message;
    console.error(e);
  } finally {
    processing.value = false;
  }
};
</script>