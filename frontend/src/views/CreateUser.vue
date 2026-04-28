<template>
    <Layout title="Criar Usuário">
        <div class="p-4 border rounded m-2 max-w-md">
            <h2 class="text-lg font-bold mb-3"> Novo Usuário </h2>

            <input v-model="form.name" @input="validateField('name')" type="text" placeholder="Nome" class="input m-1 p-2 border rounded w-full"/>
            <span class="msg-error">{{ errors.name }}</span>

            <input v-model="form.email" @input="validateField('email')" type="email" placeholder="Email" class="input m-1 p-2 border rounded w-full"/>
            <span class="msg-error">{{ errors.email }}</span>

            <input v-model="form.password" @input="validateField('password')" type="password" placeholder="Senha" class="input m-1 p-2 border rounded w-full"/>
            <span class="msg-error">{{ errors.password }}</span>

            <select v-model="form.role" @change="validateField('role')" class="input m-1 p-2 border rounded w-full">
                <option value="user"> Usuário </option>
                <option value="admin"> Admin </option>
            </select>
            <span class="msg-error">{{ errors.role }}</span>

            <div class="mt-3">
                <button @click="submitUser" :disabled="loading" class="btn-primary m-1 flex">
                <!--O comando 'flex' permite que apareça o spinner ao lado do botão, mas faz o outro botão ficar abaixo-->
                    <!-- Spinner -->
                    <span v-if="loading" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    {{ loading ? "Salvando..." : "Salvar" }}
                </button>

                <button @click="cancel" :disabled="loading" class="btn-danger m-1"> Cancelar </button>
            </div>
        </div>
    </Layout>
</template>

<script setup>
    import { ref } from "vue";
    import { useRouter } from "vue-router";
    import api from "../api/axios";
    import Layout from "../components/Layout.vue";

    const router = useRouter();
    const loading = ref(false);
    const form = ref({name: "", email: "", password: "", role: ""});
    const errors = ref({name: "", email: "", password: "", role: ""});

    //Regex simples de email
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    //Validação de dados (nome, email e senha)
    function validateField(field) {
        switch (field) {
            case "name":
                errors.value.name = form.value.name.trim() ? "" : "Nome é obrigatório";
            break;

            case "email":
                if (!form.value.email.trim()) {
                    errors.value.email = "Email é obrigatório";
                } else if (!isValidEmail(form.value.email)) {
                    errors.value.email = "Email inválido";
                } else {
                    errors.value.email = "";
                }
            break;

            case "password":
                if (!form.value.password) {
                    errors.value.password = "Senha é obrigatória";
                } else if (form.value.password.length < 6) {
                    errors.value.password = "Mínimo 6 caracteres";
                } else {
                    errors.value.password = "";
                }
            break;

            case "role":
                errors.value.role = form.value.role ? "" : "Selecione uma função";
            break;
        }
    }

    //Validação antes de enviar
    function validateAll() {
        validateField("name");
        validateField("email");
        validateField("password");
        validateField("role");

        return !Object.values(errors.value).some(e => e);
    }

    async function submitUser() {
        if (!validateAll()) return;

        loading.value = true;

        try {
            await api.post("/register.php", form.value);

            alert("Usuário criado com sucesso!");
            router.push("/users"); // volta pra lista

        } catch (error) {
            // tratamento de erro do backend (email duplicado)
            if (error.response?.data?.message) {
                errors.value.email = error.response.data.message;
            } else {
                alert("Erro ao criar usuário");
            }
        } finally {
            loading.value = false;
        }
    }

    function cancel() {
        if (!loading.value) {
            router.push("/users");
        }
    }
</script>