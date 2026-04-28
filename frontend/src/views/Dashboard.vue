<template>
  <Layout title="Dashboard">
    <div class="bg-white p-6 rounded shadow">
      <p> Olá, {{ store?.user?.name || "usuário" }}! </p> <!--Não tá puxando o nome do usuário, resolver-->
      <!--<h1 class="text-2xl font-bold mb-4"> Painel Administrativo </h1>
      <h2 class="text-xl font-semibold mb-2">Resumo do Sistema</h2>-->
      <p>Conteúdo da dashboard aqui...</p>
    </div>
  </Layout>
</template>

<script setup>
  import Layout from "../components/Layout.vue";
  import { useUserStore } from "../store/userStore";

  const store = useUserStore();
  const user = store.user;
</script>