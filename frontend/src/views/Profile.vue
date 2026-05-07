<template>
  <Layout title="Perfil">
    <div class="p-4">
      <h2 class="text-xl font-bold mb-4"> Meu Perfil </h2>
      <div class="p-3 flex flex-row">
        <div class="p-3 basis-1/2 shadow-xl">
          <div> Nome: {{ user.name }} </div>
          <div> E-mail: {{ user.email }} </div>
          <div> Função: {{ user.role }} </div>
        </div>
        <div class="basis-2/2"></div>
      </div>

      <div v-if="user">
        <h3 class="font-bold"> Editar dados </h3>
        <input v-model="user.name" class="border p-2 block mb-2" />
        <input v-model="user.email" class="border p-2 block mb-2" />
        <button @click="updateProfile" class="btn-primary"> Salvar </button>
      </div>
    </div>
  </Layout>
</template>

<script setup>
  import api from "../api/axios";
  import Layout from "../components/Layout.vue";
  import { ref, onMounted } from "vue";

  const user = ref({});

  async function loadProfile() {
    const { data } = await api.get("/profile.php");
    user.value = data;
  }

  async function updateProfile() {
    await api.post("/update_profile.php", user.value);
    alert("Perfil atualizado!");
  }

  onMounted(loadProfile);
</script>