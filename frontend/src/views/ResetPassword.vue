<template>
  <div class="p-4 max-w-md mx-auto">
    <h2 class="text-xl font-bold mb-4">Redefinir Senha</h2>
    <input v-model="email" placeholder="Seu email" class="border p-2 w-full mb-2" v-if="!token" />
    <button v-if="!token" @click="requestReset" class="bg-blue-500 text-white px-4 py-2 rounded w-full">Enviar link</button>

    <div v-if="token">
      <input v-model="newPassword" type="password" placeholder="Nova senha" class="border p-2 w-full mb-2" />
      <button @click="resetPassword" class="bg-green-500 text-white px-4 py-2 rounded w-full">Atualizar senha</button>
    </div>
  </div>
</template>

<script setup>
  import { ref } from "vue";
  import { useRoute } from "vue-router";
  import api from "../api/axios";

  const route = useRoute();
  const token = route.params.token;
  const email = ref("");
  const newPassword = ref("");

  async function requestReset() {
    await api.post("/request_password_reset.php", { email: email.value });
    alert("Se o email existir, você receberá um link para redefinir a senha.");
  }

  async function resetPassword() {
    await api.post("/reset_password.php", { token, new_password: newPassword.value });
    alert("Senha redefinida com sucesso!");
  }
</script>