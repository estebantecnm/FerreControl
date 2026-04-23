<script setup>
import { ref, onMounted } from 'vue';

const saleId = ref('');
const lastTotal = ref(0);
const irANuevaVenta = () => {
    // Limpia cualquier estado previo si fuera necesario
    window.location.href = '/ventas/pos'; 
};

onMounted(() => {
    // Lee los datos de la URL cuando la página carga
    const params = new URLSearchParams(window.location.search);
    saleId.value = params.get('id') || 'N/A';
    lastTotal.value = parseFloat(params.get('total') || '0');
});

const handlePrint = async () => {
    const id = saleId.value;
    if (!id || id === 'N/A') return;
    
    try {
        const token = localStorage.getItem('token');
        const res = await fetch(`http://127.0.0.1:8000/api/ventas/${id}/ticket`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });

        if (!res.ok) throw new Error('Error al conectar con el servidor');
        
        const html = await res.text();
        const ticketWindow = window.open('', '_blank');
        
        if (ticketWindow) {
            ticketWindow.document.write(html);
            ticketWindow.document.close();
            setTimeout(() => ticketWindow.print(), 500);
        }
    } catch (e) {
        alert('No se pudo imprimir: ' + e.message);
    }
};
</script>

<template>
    <div class="card w-full max-w-[500px] mx-auto !p-8 shadow-xl border-none">
  <div class="max-w-sm w-full mx-auto shadow-2xl rounded-2xl overflow-hidden bg-white">
    
    <div class="bg-emerald-600 text-white p-6 text-center">
      <h3 class="text-xl font-bold" style="color: chocolate;"><i>¡Venta exitosa!</i></h3>
      <p class="text-emerald-50 text-sm"><b>Total: ${{ lastTotal.toFixed(2) }}</b></p>
    </div>

    <div class="p-6 space-y-5">
      <p class="text-center text-slate-600 font-medium" style="color: chocolate;">¿Qué deseas hacer ahora?</p>
      <div class="grid grid-cols-1 gap-3">
        <br>
        <button @click="handlePrint" class="btn btn-primary w-full py-3 font-bold transition-all hover:opacity-90"   style="
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
          🖨️ Imprimir Ticket
        </button>
        <br>
        <a href="/ventas/pos" class="text-center block bg-white }hover:bg-slate-50 text-slate-700 py-4 rounded-xl font-semibold text-lg transition-all active:scale-95">
          ✅ Nueva Venta
        </a>
      </div>
      <br>
      <p class="text-center text-[10px] text-slate-400 uppercase tracking-widest mt-2">
        ID Venta: #{{ saleId }}
      </p>
    </div>
    </div>
  </div>
</template>