import { createRouter, createWebHistory } from "vue-router";
import Login from "../views/Login.vue";
import Dashboard from "../views/Dashboard.vue";
import Users from "../views/Users.vue";
import Logs from "../views/Logs.vue";
import Profile from "../views/Profile.vue";
import ResetPassword from "../views/ResetPassword.vue";
import CreateUser from "../views/CreateUser.vue";

const routes = [
  { path: "/", redirect: "/login" },
  { path: "/login", component: Login },
  { path: "/dashboard", component: Dashboard },
  { path: "/users", name: Users, component: Users },
  { path: "/users/create", name: "CreateUser", component: CreateUser },
  { path: "/logs", component: Logs },
  { path: "/profile", component: Profile },
  { path: "/reset/:token?", component: ResetPassword },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;

/*
import { useUserStore } from "../store/userStore";

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Proteção de rotas com JWT
router.beforeEach((to, from, next) => {
  const store = useUserStore();

  if (to.name !== "Login" && !store.token) {
    // Se não estiver logado, vai para a tela de login
    return next({ name: "Login" });
  }

  if (to.name === "Login" && store.token) {
    // Se já estiver logado e tentar acessar /, vai direto pro dashboard
    return next({ name: "Dashboard" });
  }

  if (to.path !== "/" && !store.token) return next("/");
  next();
});
*/