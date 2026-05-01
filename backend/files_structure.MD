Estrutura do backend

```
backend/
├─ api/
│  ├─ auth.php                     # Autenticar usuário
│  ├─ check_admin.php              # Checar se usuário é admin
│  ├─ check_email.php              # Checar se e-mail já existe no BD
│  ├─ cors.php                     # Responsável pelas requisições HTTP
│  ├─ delete_user.php              # Excluir usuário
│  ├─ get_user.php                 # Buscar informações de um usuário
│  ├─ logger.php                   # Registrar log (registro do que foi feito)
│  ├─ login.php                    # API de login
│  ├─ logout.php                   # API de logout
│  ├─ logs_pdf.php                 # Gera PDF dos logs
│  ├─ logs.php                     # Listagem de logs (listagem do que foi feito)
│  ├─ mailer.php                   # Envia e-mail
│  ├─ profile.php                  # Informações do usuário em questão
│  ├─ promote_user.php             # Promover usuário à admin
│  ├─ refresh.php                  # Gerar novo token JWT
│  ├─ register.php                 # Cadastrar usuário
│  ├─ request_password_reset.php   # Requisição da refinição de senha
│  ├─ reset_password.php           # Redefinir senha do usuário
│  ├─ update.php                   # Editar usuário
│  └─ users.php                    # Mostrar usuários
├─ config/
│  └─ Database.php                 # Conexão ao banco de dados
└─ index.php                       # Página inicial
```