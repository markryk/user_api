<template>
  <Layout title="Gerenciamento de Usuários">
    <h2 class="text-xl font-bold mb-4">Gerenciamento de Usuários</h2>
    <table class="w-full border">

      <!-- Botão de recarregar -->
      <button @click="loadUsers" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">
        Recarregar
      </button>
      
      <thead class="bg-gray-100">
        <tr>
          <!--<th>ID</th><th>Nome</th><th>Email</th><th>Role</th><th>Ações</th>-->
          <th class="p-2 border">ID</th>
          <th class="p-2 border">Nome</th>
          <th class="p-2 border">Email</th>
          <th class="p-2 border">Função</th>
          <th class="p-2 border">Criado em</th>
          <th class="p-2 border">Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="u in users" :key="u.id">
          <!--<td>{{ u.id }}</td>
          <td>{{ u.name }}</td>
          <td>{{ u.email }}</td>
          <td>{{ u.role }}</td>-->
          <td class="p-2 border text-center">{{ user.id }}</td>
          <td class="p-2 border">{{ user.name }}</td>
          <td class="p-2 border">{{ user.email }}</td>
          <td class="p-2 border">{{ user.role }}</td>
          <td class="p-2 border">{{ formatDate(user.created_at) }}</td>
          <td>
            <button @click="promote(u.id)" class="bg-green-500 text-white px-2 py-1 rounded" v-if="u.role !== 'admin'">
              Promover
            </button>
            <button @click="del(u.id)" class="bg-red-500 text-white px-2 py-1 rounded ml-2">
              Excluir
            </button>
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

  const users = ref([]);

  // Função para carregar usuários do backend
  async function loadUsers() {
    try {
      const { data } = await api.get("/users.php");
      console.log("Usuários carregados:", data);
      users.value = data;
    } catch (error) {
      //console.error("Erro ao carregar usuários:", error);
      console.error("Erro completo:", error.response?.data || error.message);
      alert("Não foi possível carregar os usuários. Verifique o token JWT :(");
    }
  }

  // Formata data para exibição
  function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString("pt-BR");
  }

  // Carrega ao abrir a página
  onMounted(loadUsers);
</script>

<!--<template>
  <div class="p-4">
    
    <table class="w-full border">
      
    </table>
  </div>
</template>

<script setup>
  import { ref, onMounted } from "vue";
  import api from "../api/axios";

  const users = ref([]);

  async function loadUsers() {
  const { data } = await api.get("/users.php");
  users.value = data;
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

  onMounted(loadUsers);
</script>-->
