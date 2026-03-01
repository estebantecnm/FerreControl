<template>
  <form @submit.prevent="handleLogin" class="p-4 border rounded">
    <input v-model="form.id_usuario" type="number" placeholder="ID de Usuario" class="block w-full mb-2 p-2 border" />
    <input v-model="form.contrasena" type="password" placeholder="Contraseña" class="block w-full mb-2 p-2 border" />
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Entrar</button>
  </form>
</template>

<script setup>
import { ref } from 'vue';
// the helper in utils already handles storage
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
      window.location.href = '/dashboard'; // Redirige al éxito
    } else {
      alert(data.msj);
    }
  } catch (error) {
    console.error("Error en el login:", error);
  }
};
</script>