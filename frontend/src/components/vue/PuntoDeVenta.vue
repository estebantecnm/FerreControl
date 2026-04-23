<template>
  <div v-if="!showSuccessPage">
    </div>

  <VentaExitosa 
    v-else 
    :sale-id="lastSaleId" 
    @print="printTicket" 
    @close="resetForm" 
  />
</template>

<script setup>
import VentaExitosa from './VentaExitosa.vue'; // Ajusta la ruta

const showSuccessPage = ref(false);

// En processSale, cuando la venta es exitosa:
// lastSaleId.value = dataVenta.venta?.id_venta;
// showSuccessPage.value = true; // Cambiamos la vista en lugar de abrir modal

const resetForm = () => {
  showSuccessPage.value = false;
  // ... resto de tu lógica de limpieza ...
};
</script>