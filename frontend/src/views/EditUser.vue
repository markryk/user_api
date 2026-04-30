<template>
    <Layout title="Editar Usuário">

        <div v-if="loadingPage" class="p-4"> Carregando... </div>

        <div v-else class="p-4 border rounded m-2 max-w-md">

            <h2 class="text-lg font-bold mb-3">Editar Usuário</h2>

            <!-- Nome -->
            <input v-model="form.name" @input="validateField('name')" :class="inputClass(errors.name, form.name)" placeholder="Nome"/>
            <span class="text-red-500 text-sm">{{ errors.name }}</span>

            <!-- E-mail -->
            <input v-model="form.email" @input="handleEmailInput" :class="inputClass(errors.email, form.email)" placeholder="Email"/>
            <span class="text-red-500 text-sm">{{ errors.email }}</span>
            <span v-if="checkingEmail" class="text-gray-500 text-sm">Verificando email...</span>

            <!-- Senha (opcional na edição) -->
            <div class="relative">
                <input
                v-model="form.password"
                @input="validateField('password')"
                :type="showPassword ? 'text' : 'password'"
                :class="inputClass(errors.password, form.password)"
                placeholder="Nova senha (opcional)"
                />

                <button type="button" @click="showPassword = !showPassword" class="absolute right-2 top-2 text-sm">
                    {{ showPassword ? "Ocultar" : "Mostrar" }}
                </button>
            </div>
            <span class="text-red-500 text-sm">{{ errors.password }}</span>

            <!-- Tipo de usuário -->
            <select v-model="form.role" @change="validateField('role')" :class="inputClass(errors.role, form.role)">
                <option value=""> Selecione o tipo de usuário </option>
                <option value="user"> Usuário </option>
                <option value="admin"> Admin </option>
            </select>
            <span class="text-red-500 text-sm">{{ errors.role }}</span>

            <!-- BOTÕES -->
            <div class="mt-3">
                <button @click="submit" :disabled="loading" class="btn-primary m-1 flex items-center gap-2">
                    <span v-if="loading" class="w-4 h-4 btn-spinner"></span>
                    {{ loading ? "Salvando..." : "Atualizar" }}
                </button>

                <button @click="cancel" :disabled="loading" class="btn-danger m-1">
                    Cancelar
                </button>
            </div>
        </div>
    </Layout>
</template>

<script setup>
    import { ref, onMounted } from "vue";
    import { useRouter, useRoute } from "vue-router";
    import api from "../api/axios";
    import Layout from "../components/Layout.vue";

    const router = useRouter();
    const route = useRoute();

    const id = route.params.id;

    const loading = ref(false);
    const loadingPage = ref(true);
    const checkingEmail = ref(false);
    const showPassword = ref(false);

    let emailTimeout = null;

    const form = ref({ name: "", email: "", password: "", role: "" });

    const originalEmail = ref("");

    const errors = ref({ name: "", email: "", password: "", role: "" });

    function inputClass(error, value) {
        return [
            "input m-1 p-2 border rounded w-full outline-none",
            error
            ? "border-red-500"
            : value
            ? "border-green-500"
            : "border-gray-300"
        ];
    }

    //Carregar usuário
    async function loadUser() {
        try {
            const { data } = await api.get(`/get_user.php?id=${id}`);

            form.value = {name: data.name, email: data.email, password: "", role: data.role};

            originalEmail.value = data.email;

        } catch (e) {
            alert("Erro ao carregar usuário");
            router.push("/users");
        } finally {
            loadingPage.value = false;
        }
    }

    //Regex simples de email
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    //Validação de dados (nome, email e senha)
    function validateField(field) {
        switch (field) {
            case "name":
                errors.value.name = form.value.name.trim() ? "" : "Nome obrigatório";
            break;

            case "password":
                if (form.value.password && form.value.password.length < 6) {
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

    //E-mail com debounce + API
    function handleEmailInput() {
        validateEmailFormat();

        if (emailTimeout) clearTimeout(emailTimeout);
        emailTimeout = setTimeout(checkEmail, 500);
    }

    function validateEmailFormat() {
        if (!form.value.email.trim()) {
            errors.value.email = "Email obrigatório";
        } else if (!isValidEmail(form.value.email)) {
            errors.value.email = "Email inválido";
        } else {
            errors.value.email = "";
        }
    }

    async function checkEmail() {
        if (errors.value.email) return;

        // não verifica se não mudou
        if (form.value.email === originalEmail.value) return;

        checkingEmail.value = true;

        try {
            const { data } = await api.post("/check_email.php", {email: form.value.email});

            if (data.exists) {
                errors.value.email = "Email já cadastrado";
            }
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

    async function submit() {
        if (!validateAll()) return;

        loading.value = true;

        try {
            await api.post("/update.php", {id, ...form.value});

            alert("Usuário atualizado!");
            router.push("/users");

        } catch (e) {
            alert("Erro ao atualizar");
        } finally {
            loading.value = false;
        }
    }

    function cancel() {
        router.push("/users");
    }

    onMounted(loadUser);
</script>