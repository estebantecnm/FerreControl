<script setup>
import { ref } from 'vue';
import { apiFetch } from '../../utils/api';

const mensaje = ref({ texto: '', tipo: '' });
const errores = ref({}); // Para mostrar errores específicos de validación de Laravel
const cargando = ref(false);

const cliente = ref({
    nombre: '', 
    ap_paterno: '', 
    ap_materno: '', // Requerido por tu API
    fecha_nacimiento: '', // Requerido por tu API
    sexo: 'Masculino',
    correo: '', // Requerido por tu API
    rfc: '', // Requerido por tu API
    telefono: '', 
    num_ext: null, // Cambiado a null para evitar strings vacíos
    num_int: null, 
    calle: '', 
    colonia: '', 
    municipio: '', 
    estado: '',
    status: 'Activo',
    limite_credito: 0, 
    saldo_pendiente: 0, 
    dias_credito: 0,
    tipo_cliente: 'Fisica' // Solo Fisica o Moral según tu API
});

const guardarCliente = async () => {
    cargando.value = true;
    mensaje.value = { texto: '', tipo: '' };
    errores.value = {};

    try {
        const res = await apiFetch('/clientes', {
            method: 'POST',
            body: JSON.stringify(cliente.value)
        });

        const data = await res.json();

        if (res.ok) {
            mensaje.value = { texto: 'Cliente registrado con éxito.', tipo: 'success' };
            setTimeout(() => window.location.href = '/clientes/listaClientes', 1500);
        } else if (res.status === 422) {
            // Capturamos los errores del Validator de Laravel
            errores.value = data;
            mensaje.value = { texto: 'Revisa los campos obligatorios.', tipo: 'error' };
        } else {
            mensaje.value = { texto: data.message || 'Error inesperado.', tipo: 'error' };
        }
    } catch (e) {
        mensaje.value = { texto: 'Error de conexión con el servidor.', tipo: 'error' };
    } finally {
        cargando.value = false;
    }
};
</script>

<template>
    <form @submit.prevent="guardarCliente" class="space-y-6 pb-20">
        <div v-if="mensaje.texto" :class="mensaje.tipo === 'success' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'" class="p-4 rounded-lg font-bold border">
            {{ mensaje.texto }}
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h2 class="text-xs font-black text-blue-600 uppercase mb-4 tracking-widest">1. Información Obligatoria</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Nombre *</label>
                    <input v-model="cliente.nombre" type="text" class="w-full border-slate-200 rounded-lg text-sm" required>
                    <p v-if="errores.nombre" class="text-red-500 text-[10px] mt-1">{{ errores.nombre[0] }}</p>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Apellido Paterno *</label>
                    <input v-model="cliente.ap_paterno" type="text" class="w-full border-slate-200 rounded-lg text-sm" required>
                    <p v-if="errores.ap_paterno" class="text-red-500 text-[10px] mt-1">{{ errores.ap_paterno[0] }}</p>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Apellido Materno *</label>
                    <input v-model="cliente.ap_materno" type="text" class="w-full border-slate-200 rounded-lg text-sm" required>
                    <p v-if="errores.ap_materno" class="text-red-500 text-[10px] mt-1">{{ errores.ap_materno[0] }}</p>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">RFC (13 caracteres) *</label>
                    <input v-model="cliente.rfc" type="text" maxlength="13" class="w-full border-slate-200 rounded-lg text-sm uppercase" required>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Fecha Nacimiento *</label>
                    <input v-model="cliente.fecha_nacimiento" type="date" class="w-full border-slate-200 rounded-lg text-sm" required>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Sexo *</label>
                    <select v-model="cliente.sexo" class="w-full border-slate-200 rounded-lg text-sm" required>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h2 class="text-xs font-black text-blue-600 uppercase mb-4 tracking-widest">2. Contacto y Domicilio</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Correo *</label>
                    <input v-model="cliente.correo" type="email" class="w-full border-slate-200 rounded-lg text-sm" required>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Teléfono (10 dígitos) *</label>
                    <input v-model="cliente.telefono" type="text" maxlength="10" class="w-full border-slate-200 rounded-lg text-sm" required>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Calle *</label>
                    <input v-model="cliente.calle" type="text" class="w-full border-slate-200 rounded-lg text-sm" required>
                </div>
                <div class="flex gap-2">
                    <div class="w-1/2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Ext</label>
                        <input v-model.number="cliente.num_ext" type="number" class="w-full border-slate-200 rounded-lg text-sm">
                    </div>
                    <div class="w-1/2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Int</label>
                        <input v-model.number="cliente.num_int" type="number" class="w-full border-slate-200 rounded-lg text-sm">
                    </div>
                </div>
                <input v-model="cliente.colonia" type="text" placeholder="Colonia *" class="w-full border-slate-200 rounded-lg text-sm" required>
                <input v-model="cliente.municipio" type="text" placeholder="Municipio *" class="w-full border-slate-200 rounded-lg text-sm" required>
                <input v-model="cliente.estado" type="text" placeholder="Estado *" class="w-full border-slate-200 rounded-lg text-sm" required>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
            <h2 class="text-xs font-black text-blue-600 uppercase mb-4 tracking-widest">3. Configuración de Crédito</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Tipo *</label>
                    <select v-model="cliente.tipo_cliente" class="w-full border-slate-200 rounded-lg text-sm" required>
                        <option value="Fisica">Persona Física</option>
                        <option value="Moral">Persona Moral</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Límite Crédito *</label>
                    <input v-model.number="cliente.limite_credito" type="number" step="0.01" class="w-full border-slate-200 rounded-lg text-sm" required>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Días Crédito *</label>
                    <input v-model.number="cliente.dias_credito" type="number" class="w-full border-slate-200 rounded-lg text-sm" required>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Status *</label>
                    <select v-model="cliente.status" class="w-full border-slate-200 rounded-lg text-sm" required>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <button type="submit" :disabled="cargando" class="bg-slate-900 text-white px-12 py-3 rounded-xl font-bold hover:bg-blue-600 transition-all disabled:opacity-50 shadow-lg">
                {{ cargando ? 'Procesando...' : 'Guardar Cliente' }}
            </button>
        </div>
    </form>
</template>