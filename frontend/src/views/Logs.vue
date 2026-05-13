<style>
  /*@page {
    margin: 60px 30px 50px 30px;
  }*/

  header {
    /*position: fixed;
    top: -40px;*/
    left: 0;
    right: 0;
    height: 40px;
    text-align: center;
    font-weight: bold;
  }

  footer {
    position: fixed;
    bottom: -30px;
    left: 0;
    right: 0;
    text-align: right;
    font-size: 10px;
  }
</style>

<template>
  <Layout title="Logs">
      <div class="m-2 flex gap-2">
        <h3 class="text-lg font-bold"> Filtrar por </h3>

        <div class="row">
          <label for="action" class="form-control"> Ação </label>
          <input id="action" v-model="filter.action" placeholder="Ação" class="rounded-border" />

          <label for="initial_date" class="form-label"> Data inicial </label>
          <input id="initial_date" type="date" v-model="filter.from" class="rounded-border" />

          <label for="final_date" class="form-label"> Data final </label>
          <input id="final_date" type="date" v-model="filter.to" class="rounded-border" />
        </div>

        <button @click="loadLogs" class="btn-primary"> Filtrar </button>
        <button @click="downloadPDF" :disabled="loading" class="btn-orange">
          <span v-if="loading" class="w-4 h-4 btn-spinner"></span>
          {{ loading ? "Gerando..." : "Gerar PDF" }}
        </button>
      </div>

      <table class="table-auto">
        <thead>
          <tr>
            <th class="p-2 border"> ID </th>
            <th class="p-2 border"> Admin </th>
            <th class="p-2 border"> Ação </th>
            <th class="p-2 border"> Alvo </th>
            <th class="p-2 border"> Data </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="l in logs" :key="l.id">
            <td class="p-2 border text-center">{{ l.id }}</td>
            <td class="p-2 border">{{ l.admin_name }}</td>
            <td class="p-2 border">{{ l.action }}</td>
            <td class="p-2 border">{{ l.target_name }}</td>
            <td class="p-2 border">{{ formatDate(l.created_at) }}</td>
          </tr>
        </tbody>
      </table>

      <!-- Feedback se vazio -->
      <p v-if="logs.length === 0" class="mt-4 btn-gray">Nenhum log encontrado.</p>
  </Layout>
</template>

<script setup>
  import Layout from "../components/Layout.vue";
  import { ref, onMounted } from "vue";
  import api from "../api/axios";

  const logs = ref([]);
  const loading = ref(false);
  const filter = ref({ 
    action: "", 
    from: "", 
    to: ""
  });

  const page = ref(1);
  const limit = 10;

  //Carregar os logs
  async function loadLogs() {
    try {
      //Variável "query" recebe os filtros da pesquisa, que será incorporada à variável "data"
      const query = new URLSearchParams({...filter.value, page: page.value, limit}).toString();
      const { data } = await api.get(`/logs.php?${query}`);
      logs.value = data;
    } catch (error) {
      console.error("Erro ao visualizar logs:", error.response?.data || error.message);
      alert("Acesso negado. Somente administradores.");
    }
    
  }

  function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleString("pt-BR");
  }

  async function downloadPDF() {

    try {
      const loading = ref(false);
      const query = new URLSearchParams(filter.value).toString();

      const response = await api.get(`/logs_pdf.php?{$query}`, {
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
    } finally {
      loading.value = false;
    }
  }

  onMounted(loadLogs);
</script>