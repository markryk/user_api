<template>
  <Layout title="Dashboard">
    <div>
      <h1 class="text-2xl font-bold mb-4"> Painel Administrativo </h1>
      <!--<p>Olá, {{ store.user?.name || "usuário" }}!</p>-->
      
      <h2 class="text-xl font-semibold mb-2">Resumo do Sistema</h2>
      <p>Conteúdo da dashboard aqui...</p>
    </div>
  </Layout>
</template>

<script setup>
  import Layout from "../components/Layout.vue";
</script>

<!--<script setup>
  //import { useUserStore } from "../store/userStore";
  //import { useRouter } from "vue-router";
  import Layout from "../components/Layout.vue";

  const store = useUserStore();
  const router = useRouter();

  function logout() {
    store.logout();
    router.push("/");
  }
</script>-->