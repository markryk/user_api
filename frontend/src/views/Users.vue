<template>
  <Layout title="Gerenciamento de Usuários">

    <!-- Recarregar tela de usuários -->
    <button @click="loadUsers" class="m-2 btn-primary"> Recarregar </button>

    <!-- Adicionar usuário -->
    <button @click="$router.push('/users/create')" class="m-2 btn-primary"> Criar usuário </button>

    <CreateUser v-if="showCreate" @created="handleCreated" @cancel="showCreate = false"/>

    <br><br>

    <table class="table-auto">
      <thead>
        <tr>
          <th class="p-2 border"> ID </th>
          <th class="p-2 border"> Nome </th>
          <th class="p-2 border"> Email </th>
          <th class="p-2 border"> Função </th>
          <th class="p-2 border"> Criado em </th>
          <th class="p-2 border"> Ações </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="u in users" :key="u.id">
          <td class="p-2 border text-center">{{ u.id }}</td>
          <td class="p-2 border">{{ u.name }}</td>
          <td class="p-2 border">{{ u.email }}</td>
          <td class="p-2 border">{{ u.role }}</td>
          <td class="p-2 border">{{ formatDate(u.created_at) }}</td>
          <td>
            <button @click="del(u.id)" class="m-1 btn-warning"> Editar </button>
            <button @click="promote(u.id)" class="m-1 btn-orange" v-if="u.role !== 'admin'"> Promover à admin </button>
            <button @click="del(u.id)" class="m-1 btn-danger"> Excluir </button>
          </td>
        </tr>
      </tbody>
      
    </table>
  </Layout>
</template>

<script setup>
  import { ref, onMounted } from "vue";
  import api from "../api/axios";
  import Layout from "../components/Layout.vue";
  import CreateUser from "../views/CreateUser.vue";

  const users = ref([]);
  const showCreate = ref(false);

  // Função para carregar usuários do backend
  async function loadUsers() {
    try {
      const { data } = await api.get("/users.php");
      users.value = data;
    } catch (error) {
      console.error("Erro completo:", error.response?.data || error.message);
      alert("Não foi possível carregar os usuários. Verifique o token JWT :(");
    }
  }

  function handleCreated() {
    showCreate.value = false;
    loadUsers();
  }

  /*async function createUser() {
    const { data } = await api.post("/register.php");
  }*/

  // Formata data para exibição
  function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString("pt-BR");
  }

  async function promote(id) {
    await api.post("/promote_user.php", { id });
    alert("Usuário promovido e notificado por email!");
    loadUsers();
  }

  async function del(id) {
    await api.post("/delete_user.php", { id });
    alert("Usuário deletado!");
    loadUsers();
  }

  // Carrega ao abrir a página
  onMounted(loadUsers);
</script>