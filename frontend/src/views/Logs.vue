<template>
  <Layout title="Logs">
    <div class="p-4">
      <!--<h2 class="text-xl font-bold mb-4"> Logs de Atividade </h2>-->
      <div class="mb-4 flex gap-2">
        <h3> Filtrar por </h3>

        <!--<div class="container">-->
          <!--<form>-->
            <div class="row text-center fw-bold mb-1">
              <div class="col-md-4">
                <label for="action" class="form-label"> Ação </label>
              </div>
              <div class="col-md-4">
                <label for="initial_date" class="form-label"> Data inicial </label>
              </div>
              <div class="col-md-4">
                <label for="final_date" class="form-label"> Data final </label>
              </div>
            </div>

            <div class="row g-3">
              <div class="col-md-4">
                <input type="text" class="form-control" id="action" placeholder="Ação" v-model="filter.action">
              </div>
              <div class="col-md-4">
                <input type="date" class="form-control" id="initial_date" placeholder="" v-model="filter.from">
              </div>
              <div class="col-md-4">
                <input type="date" class="form-control" id="final_date" placeholder="" v-model="filter.to">
              </div>
            </div>
          <!--</form>-->
        <!--</div>-->

        <!--<div class="mb-4">
          <input id="action" v-model="filter.action" placeholder="Ação" class="border p-2" />
          <input id="initial_date" type="date" v-model="filter.from" class="border p-2 rounded" />
          <input id="final_date" type="date" v-model="filter.to" class="border p-2 rounded" />
        </div>-->

        <button @click="loadLogs" class="bg-blue-500 text-white px-4 py-2 rounded"> Filtrar </button>
        <button @click="downloadPDF" class="bg-green-500 text-white px-4 py-2 rounded"> Gerar PDF </button>
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
            <td>{{ formatDate(l.created_at) }}</td>
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

  const page = ref(1);
  const limit = 10;

  async function loadLogs() {
    const query = new URLSearchParams(filter.value, page, limit).toString();
    const { data } = await api.get(`/logs.php?${query}`);
    //const { data } = await api.get(`/logs.php?action=${filter.value.action}`);
    logs.value = data;
  }

  function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleString("pt-BR");
  }

  async function downloadPDF() {
    try {
      const response = await api.get("/logs_pdf.php", {
        responseType: "blob", // recebe como arquivo binário
      });

      const blob = new Blob([response.data], { type: "application/pdf" });
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement("a");
      link.href = url;
      link.download = "relatorio_logs.pdf";
      link.click();
      window.URL.revokeObjectURL(url);
    } catch (error) {
      console.error("Erro ao gerar PDF:", error.response?.data || error.message);
      alert("Erro ao gerar o PDF de logs.");
    }

    /*const token = localStorage.getItem("token");
    const res = await fetch(`http://localhost/user-api/api/logs_pdf.php?action=${filter.value.action}`, {
      headers: { Authorization: `Bearer ${token}` },
    });
    const blob = await res.blob();
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "logs.pdf";
    a.click();
    URL.revokeObjectURL(url);*/
  }

  onMounted(loadLogs);
</script>