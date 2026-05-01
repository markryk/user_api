<template>
    <Layout title="Criar Usuário">
        <div class="p-4 border rounded m-2 max-w-md">
            <h2 class="text-lg font-bold mb-3"> Novo Usuário </h2>

            <!-- Nome -->
            <input v-model="form.name" @input="validateField('name')" type="text" placeholder="Nome" :class="inputClass(errors.name, form.name)"/>
            <span class="msg-error">{{ errors.name }}</span>

            <!-- E-mail -->
            <input v-model="form.email" @input="handleEmailInput" type="email" placeholder="Email" :class="inputClass(errors.email, form.email)"/>
            <span class="msg-error">{{ errors.email }}</span>
            <span v-if="checkingEmail" class="msg-loading-email">Verificando email...</span>

            <!-- Senha -->
            <div class="relative">
                <input v-model="form.password" @input="validateField('password')" :type="showPassword ? 'text' : 'password'" placeholder="Senha" :class="inputClass(errors.password, form.password)"/>

                <button type="button" @click="showPassword = !showPassword" class="absolute right-2 top-2 text-sm">
                    {{ showPassword ? "Ocultar" : "Mostrar" }}
                </button>
            </div>
            <span class="msg-error">{{ errors.password }}</span>

            <!-- Tipo de usuário -->
            <!--<select v-model="form.role" @change="validateField('role')" :class="inputClass(errors.role, form.role)">
                <option value=""> Tipo de usuário </option>
                <option value="user"> Usuário </option>
                <option value="admin"> Admin </option>
            </select>
            <span class="text-red-500 text-sm">{{ errors.role }}</span>-->


            <div class="mt-3">
                <!--O comando 'flex' permite que apareça o spinner ao lado do botão, mas faz o outro botão ficar abaixo-->
                <button @click="submitUser" :disabled="loading" class="btn-primary m-1 flex items-center gap-2">
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
    const checkingEmail = ref(false);
    const showPassword = ref(false);

    let emailTimeout = null;
    
    const form = ref({name: "", email: "", password: ""});
    const errors = ref({name: "", email: "", password: ""});

    //Classes dinâmicas estilo SaaS
    function inputClass(error, value) {
        return [
            "input m-1 p-2 border rounded w-full outline-none transition",
            error
            ? "border-red-500"
            : value
            ? "border-green-500"
            : "border-gray-300"
        ];
    }

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

            case "password":
                if (!form.value.password) {
                    errors.value.password = "Senha é obrigatória";
                } else if (form.value.password.length < 6) {
                    errors.value.password = "Mínimo 6 caracteres";
                } else {
                    errors.value.password = "";
                }
            break;

            /*case "role":
                errors.value.role = form.value.role ? "" : "Selecione um tipo de usuário";
            break;*/
        }
    }

    //E-mail com debounce + API
    function handleEmailInput() {
        validateEmailFormat();

        if (emailTimeout) clearTimeout(emailTimeout);

        emailTimeout = setTimeout(checkEmailExists, 500);
    }

    function validateEmailFormat() {
        if (!form.value.email.trim()) {
            errors.value.email = "Email é obrigatório";
        } else if (!isValidEmail(form.value.email)) {
            errors.value.email = "Email inválido";
        } else {
            errors.value.email = "";
        }
    }

    async function checkEmailExists() {
        if (errors.value.email) return;

        checkingEmail.value = true;

        try {
            const { data } = await api.post("/check_email.php", { email: form.value.email });

            if (data.exists) {
                errors.value.email = "Email já cadastrado";
            }
        } catch (e) {
            console.error(e);
        } finally {
            checkingEmail.value = false;
        }
    }

    //Validação antes de enviar
    function validateAll() {
        validateField("name");
        validateField("password");
        validateField("role");
        validateEmailFormat();

        return !Object.values(errors.value).some(e => e);
    }

    async function submitUser() {
        if (!validateAll()) return;

        loading.value = true;

        try {
            await api.post("/register.php", form.value);

            alert("Usuário criado com sucesso!");
            router.push("/users"); //volta pra lista

        } catch (error) {
            //Tratamento de erro do backend (email duplicado)
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