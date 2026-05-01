Estrutura do frontend

```
frontend/
├─ src/
│  ├─ api/                  # chamadas HTTP (Axios)
│  │  └─ axios.js           # ligação entre o frontend e o backend
│  ├─ assets/               # estilização
│  │  ├─ components.css     # estilização CSS (no caso, tailwind)
│  │  └─ tailwind.js        # arquivo de configuração
│  ├─ store/                # Pinia (estado global)
│  │  └─ userStore.js       # armazena dados do usuário (JWT, etc)
│  ├─ views/                # telas (pages)
│  │  ├─ CreateUser.vue     # tela de criar usuário
│  │  ├─ Dashboard.vue      # tela de dashboard
│  │  ├─ EditUser.vue       # tela de editar usuário
│  │  ├─ Login.vue          # pág. de login
│  │  ├─ Logs.vue           # pág. de logs
│  │  ├─ Profile.vue        # pág. de perfil
│  │  ├─ ResetPassword.vue  # pág. de redefinir senha
│  │  └─ User.vue           # pág. de mostrar usuários
│  ├─ components/           # componentes reaproveitáveis
│  │  └─ Layout.vue         # layout reutilizável nas views
│  ├─ router/               # rotas (Vue Router)
│  │  └─ index.js           # arquivo de rotas
│  ├─ App.vue               # componente principal do projeto, onde vão as rotas e toda a UI
│  └─ main.js               # módulo (script) inicial que vai dentro de index.html
└─ index.html               # pág. inicial do projeto
```