<template>
  <div class="space-y-12">
    <div v-for="categoria in categorias" :key="categoria.id" class="bg-white p-6 rounded-lg shadow-md">
      
      <div class="flex items-center justify-between mb-4 pb-4">
        <div class="flex flex-col w-full max-w-sm">
          <label class="text-xs font-bold text-slate-500 uppercase mb-1" style="color: var(--text)"><b>Nombre de la Categoría</b></label>
          <div class="flex gap-2">
            <input 
              v-model="categoria.nombre_categoria" 
              type="text" 
              class="border rounded px-3 py-1 w-full focus:ring-2 focus:ring-blue-500 outline-none"
            />
            <button 
              @click="guardarCambios(categoria)"
              class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700 transition"
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
              Actualizar
            </button>
          </div>
        </div>
        
        <span class="text-slate-400 text-sm italic">ID: #{{ categoria.id_categoria }}</span>

      </div>

      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 text-slate-700">
          <tr>
            <th class="p-2 border">ID Producto</th>
            <th class="p-2 border">Nombre del Producto</th>
            <th class="p-2 border">Estado</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="producto in categoria.productos" :key="producto.id">
           <td class="p-2 border text-slate-500">#{{ producto.id_producto || producto.id }}</td>
            <td class="p-2 border font-medium">{{ producto.nombre }}</td>
            
            <td class="p-2 border">
              <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Vinculado</span>
            </td>
          </tr>
          <tr v-if="categoria.productos && categoria.productos.length === 0">
            <td colspan="3" class="p-4 text-center text-slate-400">No hay productos en esta categoría</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const categorias = ref([]);

async function fetchCategorias() {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/categorias', {
      headers: { 
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Accept': 'application/json' // <--- ESTO EVITA EL ERROR 500 DE REDIRECCIÓN
      }
    });
    
    if (!response.ok) throw new Error('Error en la API');
    
    const data = await response.json();
    categorias.value = data; 
  } catch (error) {
    console.error('Error al cargar:', error);
  }
}

async function guardarCambios(categoria) {
  // 1. Depuración: Mira en la consola qué trae el objeto
  console.log("Categoría completa:", categoria);
  console.log("ID que se está enviando:", categoria.id_categoria); // Ajusta esto según el nombre real

  // 2. Asegúrate de usar el ID correcto
  const idParaActualizar = categoria.id_categoria || categoria.id; 

  try {
    const response = await fetch(`http://127.0.0.1:8000/api/categorias/${idParaActualizar}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        nombre_categoria: categoria.nombre_categoria
      })
    });

    const data = await response.json();
    if (response.ok) {
      alert('¡Categoría actualizada!');
    } else {
      console.error('Error del servidor:', data);
      alert('Error: ' + (data.message || 'No se pudo actualizar'));
    }
  } catch (error) {
    console.error('Error de conexión:', error);
  }
}
onMounted(fetchCategorias);
</script>