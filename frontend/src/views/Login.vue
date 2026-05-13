<template>
  <div class="m-2 min-h-screen flex items-center justify-center bg-linear-to-br from-blue-500 to-indigo-600">
    
    <div class="m-2 bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
      
      <!-- Título -->
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-6"> Sistema PHP + Vue </h2>

      <!-- Inputs -->
      <div class="flex flex-col gap-4">
        <input v-model="email" placeholder="Email" class="login-input"/>

        <input v-model="password" type="password" placeholder="Senha" class="login-input"/>

        <!-- Botão -->
        <button @click="login" class="btn-primary"> Entrar </button>

        <!-- Erro -->
        <p v-if="error" class="msg-error text-center"> {{ error }} </p>
      </div>

      <!-- Rodapé -->
      <p class="text-center mt-6">
        <span @click="$router.push('/reset')" class="text-blue-600 cursor-pointer hover:text-blue-900">
          Esqueci a senha
        </span>
      </p>
    </div>

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