<template>
  <div class="p-4">
    <h2 class="text-2xl font-bold mb-4">Punto de Venta</h2>

    <div class="mb-4">
      <input
        v-model="search"
        placeholder="Buscar producto..."
        class="w-full border p-2"
      />
    </div>

    <div v-if="loadingProducts" class="text-center py-4">Cargando productos...</div>
    <div v-else-if="errorProducts" class="text-red-500">{{ errorProducts }}</div>
    <ul v-else class="space-y-2 max-h-40 overflow-auto">
      <li
        v-for="p in filteredProducts"
        :key="p.id_producto"
        class="flex justify-between items-center border p-2"
      >
        <div>
          <strong>{{ p.nombre }}</strong> - stock: {{ p.stock }}
        </div>
        <button
          @click="addToCart(p)"
          class="bg-green-500 text-white px-3 py-1 rounded"
        >
          Añadir
        </button>
      </li>
    </ul>

    <div v-if="altMessage" class="mt-2 text-yellow-700">{{ altMessage }}</div>

    <div class="mt-6">
      <h3 class="text-xl font-semibold">Carrito</h3>
      <table class="min-w-full bg-white border">
        <thead>
          <tr>
            <th class="px-2 py-1 border">Producto</th>
            <th class="px-2 py-1 border">Cantidad</th>
            <th class="px-2 py-1 border">Precio u.</th>
            <th class="px-2 py-1 border">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in cart" :key="item.product.id_producto">
            <td class="px-2 py-1 border">{{ item.product.nombre }}</td>
            <td class="px-2 py-1 border">{{ item.quantity }}</td>
            <td class="px-2 py-1 border">{{ item.product.precio_venta }}</td>
            <td class="px-2 py-1 border">{{ (item.quantity * item.product.precio_venta).toFixed(2) }}</td>
          </tr>
        </tbody>
      </table>
      <div class="mt-2 text-right font-bold">Total: {{ cartTotal.toFixed(2) }}</div>
      <div class="flex justify-end mt-4">
        <button
          @click="processSale"
          class="bg-blue-600 text-white px-4 py-2 rounded"
          :disabled="cart.length === 0 || processingSale"
        >
          {{ processingSale ? 'Procesando...' : 'Finalizar Venta' }}
        </button>
      </div>
      <div v-if="saleMessage" :class="saleError ? 'text-red-500' : 'text-green-500'" class="mt-2">
        {{ saleMessage }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { apiFetch } from '../../utils/api';
import { auth } from '../../utils/auth';

const products = ref([]);
const loadingProducts = ref(false);
const errorProducts = ref(null);

const search = ref('');
const altMessage = ref('');

const cart = ref([]);
const processingSale = ref(false);
const saleMessage = ref('');
const saleError = ref(false);

const cartTotal = computed(() => cart.value.reduce((sum, i) => sum + i.quantity * i.product.precio_venta, 0));

const filteredProducts = computed(() => {
  const term = search.value.trim().toLowerCase();
  if (!term) return products.value;
  return products.value.filter(p => p.nombre.toLowerCase().includes(term));
});

const loadProducts = async () => {
  loadingProducts.value = true;
  try {
    const res = await apiFetch('/productos');
    if (res.ok) {
      products.value = await res.json();
    } else {
      errorProducts.value = 'Error al cargar los productos';
    }
  } catch (e) {
    errorProducts.value = 'No se pudo conectar';
  } finally {
    loadingProducts.value = false;
  }
};

onMounted(loadProducts);

function addToCart(product) {
  altMessage.value = '';
  if (product.stock <= 0) {
    altMessage.value = 'Producto sin stock, busca un producto alternativo.';
    return;
  }
  const existing = cart.value.find(i => i.product.id_producto === product.id_producto);
  if (existing) {
    if (existing.quantity < product.stock) {
      existing.quantity += 1;
    } else {
      altMessage.value = 'No hay suficiente stock para aumentar la cantidad';
    }
  } else {
    cart.value.push({ product, quantity: 1 });
  }
}

async function processSale() {
  saleMessage.value = '';
  saleError.value = false;
  processingSale.value = true;
  try {
    const user = auth.getUser();
    const id_usuario = user?.id_usuario || null;
    // create pedido cliente
    const pedidoPayload = {
      total: cartTotal.value,
      impuesto: 0,
      tipo_pedido: 'Mostrador',
      id_usuario,
      id_cliente: 1, // placeholder; maybe choose from UI
      productos: cart.value.map(i => ({
        id_producto: i.product.id_producto,
        cantidad: i.quantity,
        precio_unitario: i.product.precio_venta,
      })),
    };
    const r1 = await apiFetch('/pedidos-cliente', {
      method: 'POST',
      body: JSON.stringify(pedidoPayload),
    });
    if (!r1.ok) throw new Error('No se pudo crear el pedido');
    const dataPedido = await r1.json();
    const id_pedido_cliente = dataPedido.pedido?.id_pedido_cliente || dataPedido.pedido?.id; // adjust

    // realizar venta inmediata
    const ventaPayload = {
      id_pedido_cliente,
      id_usuario,
      metodo_pago: 'Efectivo',
      pago_cliente: cartTotal.value,
    };
    const r2 = await apiFetch('/ventas', {
      method: 'POST',
      body: JSON.stringify(ventaPayload),
    });
    if (!r2.ok) {
      const msg = await r2.text();
      throw new Error(msg || 'Error al procesar la venta');
    }
    saleMessage.value = 'Venta realizada con éxito';
    cart.value = [];
  } catch (e) {
    saleError.value = true;
    saleMessage.value = e.message;
  } finally {
    processingSale.value = false;
  }
}
</script>
