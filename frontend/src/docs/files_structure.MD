Estrutura do frontend

```
frontend/
├─ src/
│  ├─ api/            # chamadas HTTP (Axios)
│  ├─ store/          # Pinia (estado global)
│  ├─ views/          # telas (pages)
│     ├─ CreateUser.vue
│     ├─ EditUser.vue
│  ├─ components/     # componentes reaproveitáveis
│     ├─ Layout.vue
│  ├─ router/         # rotas (Vue Router)
│     ├─ index.js
│  ├─ App.vue
│  └─ main.js
├─ package.json
```