<template>
  <br>
  <div class="flex bg-gray-100">
    <!-- Sidebar -->
    <!--<aside class="w-64 bg-blue-800 text-white flex flex-col">-->
    <aside
      :class="['bg-blue-800 text-white transition-all duration-300 flex flex-col']"
    >

      <!-- Header Sidebar -->
      <div class="p-4 flex justify-between items-center border-b border-blue-700">
        <span v-if="!collapsed" class="text-xl font-bold"> Painel Admin </span>
        <button @click="toggleSidebar" class="text-white hover:text-gray-200">
          <ChevronLeft v-if="!collapsed" :size="20" />
          <ChevronRight v-else :size="20" />
        </button>
      </div>

      <br>
      <!-- Links do menu -->
      <nav class="flex-1 p-3 space-y-2">
        <router-link
          v-for="link in menu"
          :key="link.to"
          :to="link.to"
          class="flex items-center gap-3 py-2 px-3 rounded hover:bg-blue-700 transition"
        >
          <component :is="link.icon" :size="20" />
          <span v-if="!collapsed">{{ link.label }}</span>
        </router-link>
      </nav>

      <br>
      <!-- Rodapé -->
      <div class="p-3 border-t border-blue-700">
        <!--<v-btn prepend-icon="$vuetify" @click="logout" color="primary" variant="tonal"> Sairdes </v-btn>-->

        <button
          @click="logout"
          class="w-full flex items-center gap-2 justify-center bg-red-600 hover:bg-red-700 py-2 rounded"
        >
          <LogOut :size="18" />
          <span v-if="!collapsed"> Sair </span>
        </button>
      </div>
      
      <!--Código antigo-->
      <!--<div class="p-4 text-xl font-bold border-b border-blue-700"> Painel Admin </div>
      <nav class="flex-1 p-4 space-y-2">
        <router-link to="/dashboard" class="block py-2 px-3 rounded hover:bg-blue-700">🏠 Dashboard</router-link>
        <router-link to="/users" class="block py-2 px-3 rounded hover:bg-blue-700">👥 Usuários</router-link>
        <router-link to="/logs" class="block py-2 px-3 rounded hover:bg-blue-700">🧾 Logs</router-link>
        <router-link to="/profile" class="block py-2 px-3 rounded hover:bg-blue-700">🙍 Perfil</router-link>
      </nav>-->
    </aside>

    <!-- Conteúdo principal -->
    <div class="flex-1 flex flex-col" overflow-hidden>
      <!-- Navbar -->
      <header class="bg-white shadow p-4 flex justify-between items-center">
        
        <!--Código antigo-->
        <h1 class="font-semibold text-lg">{{ title }}</h1>
        <span class="text-gray-600">{{ user?.name }}</span>
        
        <!-- Breadcrumbs -->
        <div class="text-sm text-gray-500 flex items-center gap-1">
          <template v-for="(crumb, index) in breadcrumbs" :key="crumb.path">
            <router-link
              v-if="index < breadcrumbs.length - 1"
              :to="crumb.path"
              class="hover:text-blue-600"
            >
              {{ crumb.name }}
            </router-link>
            <span v-else class="font-semibold text-gray-800">
              {{ crumb.name }}
            </span>
            <span v-if="index < breadcrumbs.length - 1">/</span>
          </template>
        </div>

        <br>
        <!-- Usuário -->
        <span class="text-gray-600 font-medium">
          {{ user?.name || "Usuário" }}
        </span>
      </header>

      <!-- Conteúdo da página -->
      <main class="flex-1 overflow-y-auto p-6">
        <slot/>
      </main>
    </div>

    <!--Código antigo
    <div class="p-4 border-t border-blue-7000">
      <button @click="logout" class="w-full bg-red-600 hover:bg-red-700 py-2 rounded"> Sair </button>
    </div>-->
    
  </div>
</template>

<script setup>
  import api from "../api/axios";
  import { useUserStore } from "../store/userStore";
  import { useRouter, useRoute } from "vue-router";
  import { computed, ref } from "vue";

  // Ícones Lucide
  import { Home, Users, FileText, User, LogOut, ChevronLeft, ChevronRight } from "lucide-vue-next";

  const props = defineProps({ title: String });
  const store = useUserStore();
  const router = useRouter();
  const route = useRoute();
  const user = computed(() => store.user);

  const collapsed = ref(false);
  function toggleSidebar() {
    collapsed.value = !collapsed.value;
  }

  // Itens do menu
  const menu = [
    { label: "Dashboard", to: "/dashboard", icon: Home },
    { label: "Usuários", to: "/users", icon: Users },
    { label: "Logs", to: "/logs", icon: FileText },
    { label: "Perfil", to: "/profile", icon: User },
  ];

  function logout() {
    api.get("/logout.php");
    store.logout();
    router.push("/");
  }
</script>
