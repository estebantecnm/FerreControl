<script setup>
import { ref, onMounted } from 'vue';
import { apiFetch } from '../../utils/api';
import { auth } from '../../utils/auth';

const clientes = ref([]);
const loading = ref(false);
const error = ref(null);
const isAdmin = ref(false);

const loadClientes = async () => {
    loading.value = true;
    try {
        const res = await apiFetch('/clientes');
        if (res.ok) {
            clientes.value = await res.json();
        } else {
            error.value = "No se pudo cargar la lista de clientes.";
        }
    } catch (err) {
        error.value = "Error de conexión con el servidor.";
    } finally {
        loading.value = false;
    }
};

const eliminarCliente = async (id, nombre) => {
    if (!confirm(`¿Estás seguro de eliminar al cliente "${nombre}"? Esta acción no se puede deshacer.`)) return;

    try {
        const res = await apiFetch(`/clientes/${id}`, { method: 'DELETE' });
        if (res.ok) {
            // Filtramos el array local para quitar al cliente eliminado sin recargar la página
            clientes.value = clientes.value.filter(c => c.id_cliente !== id);
            alert("Cliente eliminado correctamente.");
        } else {
            alert("Error al intentar eliminar el cliente.");
        }
    } catch (err) {
        alert("Error de red al intentar eliminar.");
    }
};

onMounted(() => {
    const user = auth.getUser();
    if (user && Number(user.rol) === 1) isAdmin.value = true;
    loadClientes();
});
</script>

<template>
    <div class="mt-8 bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200">
        <div class="p-8 bg-slate-50/50 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Directorio de Clientes</h2>
                <p class="text-slate-500 mt-1">Administración de contactos y facturación</p>
            </div>
            <a href="/clientes/registrarCliente" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-md">
                + Nuevo Cliente
            </a>
        </div>

        <div v-if="loading" class="p-10 text-center text-gray-400 animate-pulse">Cargando clientes...</div>
        <div v-else-if="error" class="p-10 text-center text-red-500">{{ error }}</div>

        <div v-else class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-slate-100">
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">Nombre Completo</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">RFC</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">Contacto</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase">Estatus</th>
                        <th v-if="isAdmin" class="px-6 py-4 text-center text-xs font-bold text-slate-400 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-for="c in clientes" :key="c.id_cliente" class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-mono text-xs text-slate-400">#00{{ c.id_cliente }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-700">{{ c.nombre }} {{ c.ap_paterno }}</div>
                            <div class="text-xs text-slate-400">{{ c.correo || 'Sin correo electrónico' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            <span class="block font-medium">{{ c.rfc || 'N/A' }}</span>
                            <span class="text-[10px] text-slate-400 uppercase">{{ c.curp || '' }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ c.telefono || 'Sin teléfono' }}
                        </td>
                        <td class="px-6 py-4">
                            <span :class="c.status === 'Activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'" 
                                  class="px-2 py-1 rounded-md text-[10px] font-black uppercase">
                                {{ c.status }}
                            </span>
                        </td>
                        <td v-if="isAdmin" class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <a :href="`/clientes/actualizar/${c.id_cliente}`" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Editar">
                                   Actualizar 
                                   <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 55 55" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                <button @click="eliminarCliente(c.id_cliente, c.nombre)" 
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Eliminar">
                                        Eliminar
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 55 55" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>