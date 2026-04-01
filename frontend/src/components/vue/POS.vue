<template>
  <div class="p-4">
    <h2 class="text-2xl font-bold mb-4">Punto de Venta</h2>

    <!-- Selección de Cliente -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6">
      <label class="block text-[10px] font-black uppercase text-slate-400 mb-2">Asignar Cliente</label>
      <select v-model="selectedClientId" 
              class="w-full border-2 border-slate-100 rounded-lg p-2 text-sm focus:border-blue-500 outline-none">
        <option :value="1">🛒 Público en General</option>
        <option v-for="c in clients" :key="c.id_cliente" :value="c.id_cliente">
          👤 {{ c.nombre }} {{ c.ap_paterno }}
        </option>
      </select>
    </div>

    <!-- Buscador -->
    <div class="mb-4">
      <input
        v-model="search"
        placeholder="Buscar producto por nombre..."
        class="w-full border-2 border-slate-200 rounded-lg p-3 text-sm focus:border-blue-500 outline-none"
        @input="altMessage = ''"
      />
    </div>

    <!-- Lista de productos -->
    <div v-if="loadingProducts" class="text-center py-8 text-slate-500">
      Cargando productos...
    </div>
    <div v-else-if="errorProducts" class="text-red-500 text-center py-4">
      {{ errorProducts }}
    </div>
    <div v-else-if="!search.trim()" class="text-slate-400 text-center py-8 border border-dashed rounded-xl">
      Escribe en el buscador para ver los productos
    </div>
    <ul v-else-if="filteredProducts.length === 0" class="text-slate-400 text-center py-8">
      No se encontraron productos con ese nombre
    </ul>
    <ul v-else class="space-y-3 max-h-96 overflow-auto pr-2">
      <li
        v-for="p in filteredProducts"
        :key="p.id_producto"
        class="flex items-center justify-between border border-slate-200 rounded-xl p-4 bg-white hover:shadow-sm transition-shadow"
      >
        <div class="flex-1">
          <strong class="text-base">{{ p.nombre }}</strong>
          <div class="text-sm text-slate-500">
            Stock: <span :class="p.stock < 5 ? 'text-red-600 font-medium' : 'text-emerald-600'">{{ p.stock }}</span>
          </div>
        </div>

        <!-- Cantidad + Botón Añadir -->
        <div class="flex items-center gap-3">
          <input
            v-model.number="quantityInputs[p.id_producto]"
            type="number"
            min="1"
            :max="p.stock"
            class="w-16 text-center py-2 border border-slate-300 rounded-lg outline-none text-sm"
            @keypress.enter="addToCart(p)"
          />
          <button
            @click="addToCart(p)"
            class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-medium transition-colors"
            :disabled="quantityInputs[p.id_producto] < 1 || p.stock <= 0"
          >
            Añadir
          </button>
        </div>
      </li>
    </ul>

    <div v-if="altMessage" class="mt-3 text-yellow-700 bg-yellow-50 p-3 rounded-lg text-sm">
      {{ altMessage }}
    </div>

    <!-- Carrito -->
    <div class="mt-8">
      <h3 class="text-xl font-semibold mb-3">Carrito</h3>
      
      <table class="min-w-full bg-white border border-slate-200 rounded-xl overflow-hidden">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-3 text-left border-b">Producto</th>
            <th class="px-4 py-3 text-center border-b">Cantidad</th>
            <th class="px-4 py-3 text-right border-b">Precio u.</th>
            <th class="px-4 py-3 text-right border-b">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in cart" :key="item.product.id_producto" class="border-b last:border-b-0">
            <td class="px-4 py-3">{{ item.product.nombre }}</td>
            <td class="px-4 py-3 text-center">{{ item.quantity }}</td>
            <td class="px-4 py-3 text-right">${{ item.product.precio_venta }}</td>
            <td class="px-4 py-3 text-right font-medium">
              ${{ (item.quantity * item.product.precio_venta).toFixed(2) }}
            </td>
          </tr>
        </tbody>
      </table>

      <div class="mt-4 text-right text-xl font-bold">
        Total: ${{ cartTotal.toFixed(2) }}
      </div>

      <!-- Monto y botón Finalizar Venta -->
      <div class="mt-6 flex flex-col lg:flex-row gap-6 justify-end">
        <div class="p-5 bg-gray-50 rounded-xl border w-full max-w-sm">
          <div class="flex justify-between items-center mb-3">
            <label class="font-bold text-gray-700">Monto Recibido:</label>
            <input 
              v-model.number="amountPaid" 
              type="number" 
              step="0.01"
              class="border border-slate-300 rounded-lg p-3 w-32 text-right font-mono text-lg focus:border-blue-500"
              placeholder="0.00"
            />
          </div>
          
          <div v-if="amountPaid > 0" class="flex justify-between items-center text-lg">
            <span class="font-semibold">Cambio:</span>
            <span :class="amountPaid < cartTotal ? 'text-red-600' : 'text-green-600'" class="font-bold font-mono">
              ${{ (amountPaid - cartTotal).toFixed(2) }}
            </span>
          </div>
        </div>

        <button
          @click="processSale"
          class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-semibold text-lg transition-colors self-end"
          :disabled="cart.length === 0 || processingSale || amountPaid < cartTotal"
        >
          {{ processingSale ? 'Procesando...' : 'Finalizar Venta' }}
        </button>
      </div>
    </div>

    <!-- ==================== MODAL ÉXITO + DOBLE ACCIÓN (IMPRIMIR O NO) ==================== -->
    <div v-if="showPrintModal" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center z-[9999] p-4">
      <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden">
        
        <!-- Header -->
        <div class="bg-emerald-600 text-white p-6 text-center">
          <div class="mx-auto w-16 h-16 bg-white text-emerald-600 rounded-2xl flex items-center justify-center text-4xl mb-4 shadow-inner">✅</div>
          <h3 class="text-2xl font-bold">¡Venta realizada con éxito!</h3>
          <p class="text-emerald-100 mt-1">Total: <span class="font-mono font-semibold">${{ lastTotal.toFixed(2) }}</span></p>
        </div>

        <!-- Body -->
        <div class="p-8 space-y-6">
          <p class="text-center text-slate-600 text-lg">
            ¿Qué deseas hacer ahora?
          </p>

          <div class="grid grid-cols-1 gap-4">
            <!-- Botón Imprimir Ticket -->
            <button
              @click="printTicket"
              class="flex items-center justify-center gap-3 bg-slate-800 hover:bg-black text-white py-5 rounded-2xl font-semibold text-xl transition-all active:scale-95"
            >
              🖨️ Imprimir Ticket
            </button>

            <!-- Botón Finalizar sin imprimir -->
            <button
              @click="finishWithoutPrint"
              class="flex items-center justify-center gap-3 border-2 border-slate-300 hover:border-slate-400 text-slate-700 py-5 rounded-2xl font-semibold text-xl transition-all active:scale-95"
            >
              ✅ Finalizar sin Ticket
            </button>
          </div>

          <p class="text-center text-[10px] text-slate-400">
            ID Venta: #{{ lastSaleId || '—' }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { apiFetch } from '../../utils/api';
import { auth } from '../../utils/auth';

const showPrintModal = ref(false);
const lastSaleId = ref(null);
const lastTotal = ref(0);           // ← Para mostrar el total en el modal

// ==================== REFS ====================
const clients = ref([]);
const loadingClients = ref(false);
const errorClients = ref(null);
const selectedClientId = ref(1);

const products = ref([]);
const loadingProducts = ref(false);
const errorProducts = ref(null);

const search = ref('');
const altMessage = ref('');
const cart = ref([]);
const amountPaid = ref(0);
const processingSale = ref(false);
const saleMessage = ref('');   // ya no se usa mucho, pero lo dejamos por si acaso
const saleError = ref(false);

// Cantidad por producto en la lista
const quantityInputs = ref({});

// ==================== COMPUTED ====================
const cartTotal = computed(() => {
  return cart.value.reduce((total, item) => {
    return total + (item.quantity * (item.product.precio_venta || 0));
  }, 0);
});

const filteredProducts = computed(() => {
  if (!search.value?.trim()) return [];
  
  const term = search.value.toLowerCase().trim();
  return products.value.filter(p => {
    const tieneStock = p.stock > 0;
    const coincideBusqueda = 
      p.nombre?.toLowerCase().includes(term) || 
      p.marca?.toLowerCase().includes(term);
    return tieneStock && coincideBusqueda;
  });
});

// ==================== CARGAR DATOS ====================
const loadClients = async () => {
  loadingClients.value = true;
  try {
    const res = await apiFetch('/clientes');
    if (res.ok) clients.value = await res.json();
  } catch (e) {
    errorClients.value = 'No se pudo conectar';
  } finally {
    loadingClients.value = false;
  }
};

const loadProducts = async () => {
  loadingProducts.value = true;
  try {
    const res = await apiFetch('/productos');
    if (res.ok) products.value = await res.json();
  } catch (e) {
    errorProducts.value = 'No se pudo conectar';
  } finally {
    loadingProducts.value = false;
  }
};

onMounted(() => {
  loadClients();
  loadProducts();
});

// ==================== FUNCIONES ====================
function addToCart(product) {
  altMessage.value = '';
  const qty = quantityInputs.value[product.id_producto] || 1;
  
  if (qty < 1 || product.stock <= 0) return;
  if (qty > product.stock) {
    altMessage.value = `Solo hay ${product.stock} unidades disponibles`;
    return;
  }

  const existing = cart.value.find(i => i.product.id_producto === product.id_producto);
  if (existing) {
    if (existing.quantity + qty > product.stock) {
      altMessage.value = 'No hay suficiente stock';
      return;
    }
    existing.quantity += qty;
  } else {
    cart.value.push({ product, quantity: qty });
  }

  quantityInputs.value[product.id_producto] = 1;
}

// Función para imprimir
const printTicket = async () => {
  if (!lastSaleId.value) {
    alert('No se encontró ID de venta');
    finishWithoutPrint();
    return;
  }

  try {
    // Usa apiFetch que ya tiene la URL base correcta y el token
    const res = await apiFetch(`/ventas/${lastSaleId.value}/ticket`);
    if (!res.ok) throw new Error('Error al obtener el ticket');
    const html = await res.text();

    // Abre una nueva ventana y escribe el HTML
    const ticketWindow = window.open();
    ticketWindow.document.write(html);
    ticketWindow.document.close();
    ticketWindow.print(); // Opcional: dispara el diálogo de impresión automáticamente
  } catch (e) {
    alert('Error al imprimir: ' + e.message);
  }

  finishWithoutPrint();
};

// Solo limpia y cierra modal
const finishWithoutPrint = () => {
  showPrintModal.value = false;
  lastSaleId.value = null;
  lastTotal.value = 0;
  
  // Limpiamos todo para la siguiente venta
  cart.value = [];
  amountPaid.value = 0;
  search.value = '';
  saleMessage.value = '';
  saleError.value = false;
};

async function processSale() {
  if (amountPaid.value < cartTotal.value) {
    saleError.value = true;
    saleMessage.value = 'El monto recibido es menor al total de la venta.';
    return;
  }

  saleMessage.value = '';
  saleError.value = false;
  processingSale.value = true;

  try {
    const user = auth.getUser();
    const id_usuario = user?.id_usuario || 1;

    const pedidoPayload = {
      total: cartTotal.value,
      impuesto: 0,
      tipo_pedido: 'Mostrador',
      id_usuario,
      id_cliente: selectedClientId.value,
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
    const id_pedido_cliente = dataPedido.pedido?.id_pedido_cliente || dataPedido.pedido?.id;

    const ventaPayload = {
      id_pedido_cliente,
      id_usuario,
      metodo_pago: 'Efectivo',
      pago_cliente: amountPaid.value,
    };

    const r2 = await apiFetch('/ventas', {
      method: 'POST',
      body: JSON.stringify(ventaPayload),
    });

    if (!r2.ok) {
      const msg = await r2.text();
      throw new Error(msg || 'Error al procesar la venta');
    }

    // ==================== ÉXITO ====================
    const dataVenta = await r2.json();                    // ← capturamos respuesta
    lastSaleId.value = dataVenta.venta?.id_venta || null;
    lastTotal.value = cartTotal.value;

    // Mostramos el modal de doble acción
    showPrintModal.value = true;

  } catch (e) {
    saleError.value = true;
    saleMessage.value = e.message;
  } finally {
    processingSale.value = false;
  }
}
</script>