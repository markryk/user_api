import { defineStore } from "pinia";
import api from "../api/axios";

export const useUserStore = defineStore("user", {
  state: () => ({
    //token: localStorage.getItem("token") || null,
    user: null
  }),
  actions: {
    setUser(userData) {
      this.user = userData;
    },    

    async login(email, password) {
      try {
        const { data } = await api.post("/login.php", { email, password });

        // supondo que o backend retorna: { access_token: "..." }
        this.token = data.access_token;

        // Salva o token JWT e refresh token
        localStorage.setItem("token", data.access_token);
        localStorage.setItem("refresh_token", data.refresh_token);

        // Se o backend enviar dados do usuário, salve também
        this.user = data.user ?? null;

        //Para se certificar que o token foi salvo, descomentar
        //console.log("Token salvo com sucesso:", data.access_token);
        return true; //indica que o login deu certo

      } catch (error) {
        console.error("Erro no login:", error);
        throw error; //faz o componente saber que deu erro
      }
    },

    logout() {
      api.get("/logout.php");
      
      this.user = null;
      //this.token = null;
      //localStorage.removeItem("token");

      alert("Logout realizado com sucesso!");
    },

    //Função que retorna o nome do usuário logado
    async fetchProfile() {
      const { data } = await api.get("/profile.php");
      this.user = data;
    },

  },
});