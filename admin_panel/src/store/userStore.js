import { defineStore } from "pinia";
import api from "../api/axios";

export const useUserStore = defineStore("user", {
  state: () => ({
    user: null,
    //token: localStorage.getItem("token") || null,
  }),
  actions: {
    async login(email, password) {
      try {
        const { data } = await api.post("/login.php", { email, password });

        // Salva o token JWT e refresh token
        localStorage.setItem("token", data.access_token);
        localStorage.setItem("refresh_token", data.refresh_token);

        // Se o backend enviar dados do usuário, salve também
        this.user = data.user ?? null;

        console.log("Token salvo com sucesso:", data.access_token);
        return true; //indica que o login deu certo
      } catch (error) {
        console.error("Erro no login:", error);
        throw error; //faz o componente saber que deu erro
      }
    },

    logout() {
      this.user = null;
      this.token = null;
      localStorage.removeItem("token");
    },

    async fetchProfile() {
      const { data } = await api.get("/profile.php");
      this.user = data;
    },

  },
});