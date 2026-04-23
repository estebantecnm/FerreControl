<template>
  <div class="card w-full max-w-[500px] !p-8 shadow-xl border-none">
    <div class="text-center mb-8">
      <div class="bg-slate-900 w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center text-white text-2xl font-bold">
        FC
      </div>
      <h2 class="text-2xl font-bold" style="color: var(--text)">FerreControl</h2>
      <p class="text-muted small">Gestión de Inventarios</p>
    </div>

    <form @submit.prevent="handleLogin" class="flex flex-col space-y-3"">
      <div class="mb-4">
        <label class="block text-sm font-bold mb-1" style="color: var(--text)">ID de Usuario</label>
        <input 
  v-model="form.id_usuario" 
  type="number" 
  placeholder="Ingresa tu ID" 
  class="w-full h-10 px-3 border border-slate-300 rounded-lg text-sm box-border outline-none"
/>
      </div>

      <div class="mb-6">
        <label class="block text-sm font-bold mb-1" style="color: var(--text)">Contraseña</label>
        <input 
  v-model="form.contrasena" 
  type="password" 
  placeholder="••••••••" 
  class="w-full h-10 px-3 border border-slate-300 rounded-lg text-sm box-border outline-none"
/>
      </div>

      <button type="submit" class="btn btn-primary w-full py-3 font-bold transition-all hover:opacity-90"   style="
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
        Entrar al Sistema
      </button>

      <p class="mt-6 text-center text-[10px] text-muted uppercase tracking-widest">
        Acceso restringido a personal
      </p>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { auth } from '../../utils/auth';

const form = ref({ id_usuario: '', contrasena: '' });

const handleLogin = async () => {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(form.value)
    });

    const data = await response.json();
    
    if (response.ok) {
      auth.setToken(data.token);
      auth.setUser(data.usuario);
      window.location.href = '/dashboard'; 
    } else {
      alert(data.msj || "Credenciales incorrectas");
    }
  } catch (error) {
    console.error("Error en el login:", error);
    alert("No se pudo conectar con el servidor");
  }
};
</script>