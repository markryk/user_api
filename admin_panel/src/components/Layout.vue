<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-blue-800 text-white flex flex-col">
      <div class="p-4 text-xl font-bold border-b border-blue-700">
        Painel Admin
      </div>
      <nav class="flex-1 p-4 space-y-2">
        <router-link to="/dashboard" class="block py-2 px-3 rounded hover:bg-blue-700">🏠 Dashboard</router-link>
        <router-link to="/users" class="block py-2 px-3 rounded hover:bg-blue-700">👥 Usuários</router-link>
        <router-link to="/logs" class="block py-2 px-3 rounded hover:bg-blue-700">🧾 Logs</router-link>
        <router-link to="/profile" class="block py-2 px-3 rounded hover:bg-blue-700">🙍 Perfil</router-link>
      </nav>
      <div class="p-4 border-t border-blue-700">
        <button @click="logout" class="w-full bg-red-600 hover:bg-red-700 py-2 rounded">
          Sair
        </button>
      </div>
    </aside>

    <!-- Conteúdo principal -->
    <div class="flex-1 flex flex-col">
      <!-- Navbar -->
      <header class="bg-white shadow p-4 flex justify-between items-center">
        <h1 class="font-semibold text-lg">{{ title }}</h1>
        <span class="text-gray-600">{{ user?.name }}</span>
      </header>

      <!-- Conteúdo da página -->
      <main class="flex-1 overflow-y-auto p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
  import { useUserStore } from "../store/userStore";
  import { useRouter } from "vue-router";
  import { computed } from "vue";

  const props = defineProps({ title: String });
  const store = useUserStore();
  const router = useRouter();
  const user = computed(() => store.user);

  function logout() {
    store.logout();
    router.push("/");
  }
</script>
