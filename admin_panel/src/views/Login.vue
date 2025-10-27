<template>
  <div class="flex flex-col items-center justify-center h-screen">
    <h2 class="text-xl mb-4">Login</h2>
    <input v-model="email" placeholder="Email" class="border p-2 mb-2 w-64" />
    <input v-model="password" type="password" placeholder="Senha" class="border p-2 mb-2 w-64" />
    <button @click="login" class="bg-blue-500 text-white px-4 py-2 rounded"> Entrar </button>
    <p v-if="error" class="text-red-500 mt-2">{{ error }}</p>
  </div>
</template>

<script setup>
  //import api from "../api/axios"; // certifique-se que este import existe
  //import { nextTick } from "vue";

  import { ref } from "vue";
  import { useRouter } from "vue-router";
  import { useUserStore } from "../store/userStore";

  const email = ref("");
  const password = ref("");
  const error = ref("");
  const router = useRouter();
  const userStore = useUserStore();

  async function login() {
    try {
      const success = await userStore.login(email.value, password.value);

      //Solução sem utilizar userStore
      //const { data } = await api.post("/login.php", { email: email.value, password:password.value });

      //Salva o access_token retornado pelo backend (solução sem utilizar userStore)
      //localStorage.setItem("token", data.access_token);

      //await nextTick();

      //console.log("Login response:", data); // veja se há "jwt" no retorno
      
      //Redireciona após login
      if (success) {
        console.log("Redirecionando para /dashboard...");
        router.push("/dashboard");
      }
      
    } catch (e) {
      error.value = "Credenciais inválidas.";
    }
  }
</script>
