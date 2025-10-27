<template>
  <Layout title="Perfil">
    <div class="p-4">
      <h2 class="text-xl font-bold mb-4">Meu Perfil</h2>
      <div v-if="user">
        <input v-model="user.name" class="border p-2 block mb-2" />
        <input v-model="user.email" class="border p-2 block mb-2" />
        <button @click="updateProfile" class="bg-blue-500 text-white px-4 py-2 rounded">Salvar</button>
      </div>
    </div>
  </Layout>
</template>

<script setup>
  import Layout from "../components/Layout.vue";
  import { ref, onMounted } from "vue";
  import api from "../api/axios";
  import { useUserStore } from "../store/userStore";
  const userStore = useUserStore();
  const user = ref({});

  async function loadProfile() {
    const { data } = await api.get("/profile.php");
    user.value = data;
  }

  async function updateProfile() {
    await api.post("/update_user.php", user.value);
    alert("Perfil atualizado!");
  }

  onMounted(loadProfile);
</script>