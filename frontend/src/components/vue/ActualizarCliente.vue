<template>
  <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
    
    <div class="p-8 bg-slate-900 text-white">
       <div class="flex justify-end p-2">
  <a href="/clientes/listaClientes" class="text-3xl hover:text-red-400 transition-colors">
    &times;
  </a>
</div>
      <h1 class="text-3xl font-extrabold tracking-tight">Actualizar Cliente</h1>
      <p class="text-slate-400 mt-2 text-sm uppercase tracking-widest font-semibold" style="color: var(--text)"><b>Edición de ficha de cliente #{{ idCliente }}</b></p>
    </div>
  

    <form @submit.prevent="submit" class="p-8 space-y-10">
      
      <section>
        <div class="flex items-center gap-2 mb-6 pb-2">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider"  style="color: #d97706;">Información Personal</h2>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Nombre</label>
            <input v-model="form.nombre" type="text" class="w-full border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 transition-all" required />
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Apellido Paterno</label>
            <input v-model="form.ap_paterno" type="text" class="w-full border-slate-200 rounded-xl text-sm" required />
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Apellido Materno</label>
            <input v-model="form.ap_materno" type="text" class="w-full border-slate-200 rounded-xl text-sm" required />
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">RFC</label>
            <input v-model="form.rfc" type="text" maxlength="13" class="w-full border-slate-200 rounded-xl text-sm uppercase font-mono" required />
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Fecha de Nacimiento</label>
            <input v-model="form.fecha_nacimiento" type="date" class="w-full border-slate-200 rounded-xl text-sm" required />
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Sexo</label>
            <select v-model="form.sexo" class="w-full border-slate-200 rounded-xl text-sm" required>
              <option value="Masculino">Masculino</option>
              <option value="Femenino">Femenino</option>
            </select>
          </div>
        </div>
      </section>

      <section>
        <div class="flex items-center gap-2 mb-6 border-b border-slate-100 pb-2">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider"  style="color: #d97706;">Ubicación y Contacto</h2>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Correo Electrónico</label>
            <input v-model="form.correo" type="email" class="w-full border-slate-200 rounded-xl text-sm" required />
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Teléfono</label>
            <input v-model="form.telefono" type="text" maxlength="10" class="w-full border-slate-200 rounded-xl text-sm" required />
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="md:col-span-2 space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Calle</label>
            <input v-model="form.calle" type="text" class="w-full border-slate-200 rounded-xl text-sm" required />
          </div>
          <div class="flex gap-4">
            <div class="w-1/2 space-y-1">
              <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">N. Ext</label>
              <input v-model.number="form.num_ext" type="number" class="w-full border-slate-200 rounded-xl text-sm" />
            </div>
            <div class="w-1/2 space-y-1">
              <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">N. Int</label>
              <input v-model.number="form.num_int" type="number" class="w-full border-slate-200 rounded-xl text-sm" />
            </div>
          </div>
            <div class="space-y-1">
              <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Colonia</label>
              <input v-model="form.colonia" type="text" placeholder="Colonia" class="w-full border-slate-200 rounded-xl text-sm" required />
            </div>
            <div class="space-y-1">
              <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Municipio</label>
              <input v-model="form.municipio" type="text" placeholder="Municipio" class="w-full border-slate-200 rounded-xl text-sm" required />
            </div>
            <div class="space-y-1">
              <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Estado</label>
              <input v-model="form.estado" type="text" placeholder="Estado" class="w-full border-slate-200 rounded-xl text-sm" required />
            </div>
        </div>
      </section>

      <section>
        <div class="flex items-center gap-2 mb-6 pb-2">
         
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider"  style="color: #d97706;">Crédito y Perfil</h2>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Tipo de Cliente</label>
            <select v-model="form.tipo_cliente" class="w-full border-slate-200 rounded-xl text-sm" required>
              <option value="Fisica">Persona Física</option>
              <option value="Moral">Persona Moral</option>
            </select>
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Límite Crédito</label>
            <input v-model.number="form.limite_credito" type="number" step="0.01" class="w-full border-slate-200 rounded-xl text-sm font-bold text-blue-600" required />
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Saldo Pendiente</label>
            <input v-model.number="form.saldo_pendiente" type="number" step="0.01" class="w-full border-slate-200 rounded-xl text-sm font-bold text-red-600" required />
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-slate-400 uppercase ml-1">Status</label>
            <select v-model="form.status" class="w-full border-slate-200 rounded-xl text-sm"  required>
              <option value="Activo">Activo</option>
              <option value="Inactivo">Inactivo</option>
            </select>
          </div>
        </div>
      </section>

      <div class="space-y-2">
        <p v-if="success" class="bg-emerald-100 text-emerald-700 p-4 rounded-xl font-bold text-center border border-emerald-200 shadow-sm">{{ success }}</p>
        <div v-if="error" class="bg-red-100 text-red-700 p-4 rounded-xl font-bold text-center border border-red-200 shadow-sm">
          {{ error }}
        </div>
      </div>

      <div class="flex gap-4 pt-6">
        <button type="button" @click="window.history.back()" class="flex-1 bg-slate-100 text-slate-600 p-4 rounded-xl font-bold uppercase tracking-widest hover:bg-slate-200 transition-all"
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
    ">
          Cancelar
        </button>
        <button type="submit" class="flex-[2] bg-slate-900 text-white p-4 rounded-xl font-bold text-lg uppercase tracking-wider hover:bg-blue-600 shadow-lg disabled:bg-slate-400 transition-all"
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
    " :disabled="loading">
          {{ loading ? 'Actualizando...' : 'Guardar Cambios' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { apiFetch } from '../../utils/api';

const props = defineProps(['idCliente']);

const form = ref({
  nombre: '', ap_paterno: '', ap_materno: '',
  fecha_nacimiento: '', sexo: 'Masculino',
  correo: '', rfc: '', telefono: '',
  num_ext: null, num_int: null,
  calle: '', colonia: '', municipio: '', estado: '',
  status: 'Activo',
  limite_credito: 0, saldo_pendiente: 0, dias_credito: 0,
  tipo_cliente: 'Fisica'
});

const loading = ref(false);
const error = ref(null);
const success = ref(null);

const cargarDatos = async () => {
  if (!props.idCliente) return;
  loading.value = true;
  error.value = null;

  try {
    const res = await apiFetch(`/clientes/${props.idCliente}`);
    if (res.ok) {
      const data = await res.json();
      Object.assign(form.value, data);
    } else {
      error.value = "Cliente no encontrado.";
    }
  } catch (e) {
    error.value = "Error al conectar con la base de datos.";
  } finally {
    loading.value = false;
  }
};

onMounted(cargarDatos);
watch(() => props.idCliente, cargarDatos);

const submit = async () => {
  loading.value = true;
  error.value = null;
  success.value = null;

  try {
    const res = await apiFetch(`/clientes/${props.idCliente}`, {
      method: 'PUT',
      body: JSON.stringify(form.value),
    });

    const data = await res.json();

    if (res.ok) {
      success.value = '¡Cliente actualizado correctamente!';
      setTimeout(() => window.location.href = '/clientes/listaClientes', 1500);
    } else if (res.status === 422) {
      // Manejo de errores de validación de Laravel
      error.value = Object.values(data).flat().join(' | ');
    } else {
      error.value = data.message || 'Error al actualizar';
    }
  } catch (e) {
    error.value = 'Error de conexión con el servidor.';
  } finally {
    loading.value = false;
  }
};
</script>