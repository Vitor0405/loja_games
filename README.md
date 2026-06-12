# 🔵 uTorrent Azul — Loja de Jogos

Sistema web completo para gerenciamento de uma loja virtual de jogos digitais, desenvolvido como projeto da disciplina de **Desenvolvimento Web** — UNIFIPMoc.

---

## 👨‍💻 Integrantes

| Nome | E-mail |
|------|--------|
| Evandro | evandro@gmail.com |

---

## 🎮 Tema da Loja

**Loja de Jogos Digitais** — plataforma inspirada na Steam para venda e gerenciamento de games com painel administrativo completo e vitrine pública para clientes.

---

## 📋 Descrição do Sistema

O **uTorrent Azul** é uma aplicação web MVC desenvolvida em PHP 8 que permite:

- **Área pública**: vitrine de jogos com busca, filtro por categoria e carrinho de compras
- **Cadastro de clientes**: registro público com nome, e-mail, telefone e senha
- **Autenticação dupla**: login separado para clientes e para administradores
- **Área administrativa**: gerenciamento completo de jogos, categorias, clientes e pedidos
- **Carrinho de compras**: adição de itens, remoção e finalização de pedido
- **Dashboard**: métricas de jogos, estoque, clientes e faturamento

---

## 🛠️ Tecnologias Utilizadas

| Tecnologia | Versão | Uso |
|------------|--------|-----|
| PHP | 8.x | Backend e lógica de negócio |
| MySQL | 8.x | Banco de dados relacional |
| PDO | — | Conexão segura com prepared statements |
| HTML5 | — | Estrutura das páginas |
| CSS3 | — | Estilização com tema dark Steam-like |
| JavaScript | ES6+ | Interações, validações e modais |
| XAMPP | — | Servidor local (Apache + MySQL) |

---

## 🗂️ Diagrama de Classes

```
┌──────────────┐     ┌──────────────┐
│   Usuario    │     │   Categoria  │
├──────────────┤     ├──────────────┤
│ id           │     │ id           │
│ nome         │     │ nome         │
│ email        │     │ descricao    │
│ senha        │     └──────┬───────┘
│ tipo         │            │ 1
└──────────────┘            │
                            │ N
┌──────────────┐     ┌──────┴───────┐
│   Cliente    │     │     Game     │
├──────────────┤     ├──────────────┤
│ id           │     │ id           │
│ nome         │     │ titulo       │
│ email        │     │ descricao    │
│ telefone     │     │ preco        │
│ senha        │     │ estoque      │
│ ativo        │     │ categoria_id │
└──────┬───────┘     │ imagem       │
       │ 1           └──────────────┘
       │
       │ N
┌──────┴───────┐
│    Pedido    │
├──────────────┤
│ id           │
│ cliente_id   │
│ status       │
│ total        │
└──────┬───────┘
       │ 1
       │
       │ N
┌──────┴───────┐
│ ItensPedido  │
├──────────────┤
│ id           │
│ pedido_id    │
│ game_id      │
│ quantidade   │
│ preco_unit.  │
└──────────────┘
```

---

## 🗄️ Modelo do Banco de Dados

**Tabelas:** `usuarios`, `categorias`, `games`, `clientes`, `pedidos`, `itens_pedido`

**Relacionamentos:**
- `games.categoria_id` → `categorias.id` (ON DELETE SET NULL)
- `pedidos.cliente_id` → `clientes.id`
- `itens_pedido.pedido_id` → `pedidos.id` (ON DELETE CASCADE)
- `itens_pedido.game_id` → `games.id`

O script SQL completo está em `database.sql` na raiz do projeto.

---

## ⚙️ Instruções de Instalação e Execução

### Pré-requisitos
- [XAMPP](https://www.apachefriends.org/) instalado

### Passo a passo

**1.** Coloque a pasta `loja_games` dentro de `C:\xampp\htdocs\`

**2.** Abra o **XAMPP Control Panel** e inicie **Apache** e **MySQL**

**3.** Acesse `http://localhost/phpmyadmin` → aba **SQL**

**4.** Copie o conteúdo do arquivo `database.sql` e execute

**5.** Acesse a loja: `http://localhost/loja_games`

**6.** Acesse o painel admin: `http://localhost/loja_games/admin/views/gerenciar_games.php`

---

## 🔑 Usuário e Senha de Teste

### Painel Administrativo
| E-mail | Senha | Perfil |
|--------|-------|--------|
| evandro@gmail.com | admin123 | Admin |
| admin@gamestock.com | admin123 | Admin |

### Área do Cliente
| E-mail | Senha |
|--------|-------|
| lucas@email.com | cliente123 |
| ana@email.com | cliente123 |

---

## ✅ Funcionalidades Implementadas

**CRUDs completos (5 classes):**
- 🕹️ **Game** — Cadastro, listagem, edição e exclusão de jogos com upload de imagem
- 🏷️ **Categoria** — Gerenciamento de categorias com vínculo aos jogos
- 👤 **Cliente** — Cadastro público, login, gerenciamento admin
- 📦 **Pedido** — Criação via carrinho, listagem, alteração de status, exclusão
- 👑 **Usuario** — Usuários administrativos com controle de perfil

**Funcionalidades extras:**
- 🔍 Busca de jogos por título
- 🏷️ Filtro por categoria (navbar + barra de categorias)
- 🖼️ Upload de imagem com preview
- 🛒 Carrinho de compras (armazenado em sessão)
- 📊 Dashboard com métricas de jogos, estoque, clientes e faturamento
- 🔐 Autenticação com `password_hash()` + fallback SHA2 para dados legados
- ✔️ Validação de formulários com JavaScript (senhas, campos obrigatórios)
- 🔒 Proteção da área admin com sessão e verificação de perfil

---

## 📸 Principais Telas

- **Vitrine pública** — `http://localhost/loja_games/`
- **Registro de cliente** — `http://localhost/loja_games/registro.php`
- **Login do cliente** — `http://localhost/loja_games/login_cliente.php`
- **Carrinho** — `http://localhost/loja_games/carrinho.php`
- **Minha conta** — `http://localhost/loja_games/minha_conta.php`
- **Login admin** — `http://localhost/loja_games/login.php`
- **Dashboard admin** — `http://localhost/loja_games/admin/views/gerenciar_games.php`

---

## 🔗 Repositório

> Adicione aqui o link do seu repositório GitHub após o push:
> `https://github.com/Vitor0405/loja_games`

---

*Desenvolvido com PHP 8, MySQL, PDO e arquitetura MVC — UNIFIPMoc 2025*
