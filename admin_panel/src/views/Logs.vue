<template>
  <Layout title="Logs">
    <div class="p-4">
      <h2 class="text-xl font-bold mb-4"> Logs de Atividade </h2>
      <div class="mb-4 flex gap-2">
        <input v-model="filter.action" placeholder="Ação" class="border p-2" />
        <button @click="loadLogs" class="bg-blue-500 text-white px-4 py-2 rounded"> Filtrar </button>
        <button @click="downloadPdf" class="bg-green-500 text-white px-4 py-2 rounded"> Gerar PDF </button>
      </div>

      <table class="w-full border">
        <thead class="bg-gray-100">
          <tr>
            <th>ID</th><th>Admin</th><th>Ação</th><th>Alvo</th><th>Data</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="l in logs" :key="l.id">
            <td>{{ l.id }}</td>
            <td>{{ l.admin_name }}</td>
            <td>{{ l.action }}</td>
            <td>{{ l.target_name }}</td>
            <td>{{ l.created_at }}</td>
          </tr>
        </tbody>
      </table>

      <!-- Feedback se vazio -->
      <p v-if="logs.length === 0" class="mt-4 text-gray-500">Nenhum log encontrado.</p>

    </div>
  </Layout>
</template>

<script setup>
  import Layout from "../components/Layout.vue";
  import { ref, onMounted } from "vue";
  import api from "../api/axios";

  const logs = ref([]);
  const filter = ref({ action: "" });

  async function loadLogs() {
    const query = new URLSearchParams(filter.value).toString();
    const { data } = await api.get(`/logs.php?${query}`);
    //const { data } = await api.get(`/logs.php?action=${filter.value.action}`);
    logs.value = data;
  }

  function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleString("pt-BR");
  }

  async function downloadPdf() {
    const token = localStorage.getItem("token");
    const res = await fetch(`http://localhost/user-api/api/logs_pdf.php?action=${filter.value.action}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    const blob = await res.blob();
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "logs.pdf";
    a.click();
    URL.revokeObjectURL(url);
  }

  onMounted(loadLogs);
</script>