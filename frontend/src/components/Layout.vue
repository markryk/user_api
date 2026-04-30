<template>
  <div class="flex bg-gray-100">
    <!-- Sidebar -->
    <aside :class="['bg-blue-800 text-white transition-all duration-300 flex flex-col']">

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
          <span v-if="!collapsed"> {{ link.label }} </span>
        </router-link>
      </nav>

      <br>
      <!-- Rodapé -->
      <div class="p-3 border-t border-blue-700">

        <button @click="logout" class="btn-sair">
          <LogOut :size="18" />
          <span v-if="!collapsed"> Sair </span>
        </button>
        
      </div>
    </aside>

    <!-- Conteúdo principal -->
    <div class="flex-1 flex flex-col" overflow-hidden>
      <!-- Navbar -->
      <header class="bg-white shadow p-4 flex justify-between items-center">
        
        <!-- Título e Usuário -->
        <h1 class="font-semibold text-lg"> {{ title }} </h1>
        <span class="text-gray-600 font-medium"> {{ user?.name || "Usuário" }} </span>
      </header>

      <!-- Conteúdo da página -->
      <main class="flex-1 overflow-y-auto p-6">
        <slot/>
      </main>
    </div>
  </div>
</template>

<script setup>
  import api from "../api/axios";
  import { useUserStore } from "../store/userStore";
  import { useRouter } from "vue-router";
  import { computed, ref, onMounted } from "vue";

  onMounted(() => {
    store.fetchProfile();
  });

  // Ícones Lucide
  import { Home, Users, FileText, User, LogOut, ChevronLeft, ChevronRight } from "lucide-vue-next";

  const props = defineProps({ title: String });
  const store = useUserStore();
  const router = useRouter();
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