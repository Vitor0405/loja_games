-- ============================================================
-- uTorrent Azul — GameStock 2025
-- Execute este arquivo no phpMyAdmin (aba SQL) ou no MySQL CLI
-- ============================================================

CREATE DATABASE IF NOT EXISTS loja_games
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE loja_games;

-- ----------------------------
-- Tabela: usuarios
-- ----------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(100)          NOT NULL,
    email      VARCHAR(150)          UNIQUE NOT NULL,
    senha      VARCHAR(255)          NOT NULL,
    tipo       ENUM('admin','user')  DEFAULT 'user',
    created_at TIMESTAMP             DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP             DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabela: categorias
-- ----------------------------
CREATE TABLE IF NOT EXISTS categorias (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(50)  NOT NULL,
    descricao  TEXT,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabela: games
-- ----------------------------
CREATE TABLE IF NOT EXISTS games (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    titulo       VARCHAR(100)   NOT NULL,
    descricao    TEXT,
    preco        DECIMAL(10,2)  NOT NULL,
    estoque      INT            DEFAULT 0,
    categoria_id INT            NULL,
    imagem       VARCHAR(255),
    created_at   TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabela: clientes
-- ----------------------------
CREATE TABLE IF NOT EXISTS clientes (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  UNIQUE NOT NULL,
    telefone   VARCHAR(20),
    senha      VARCHAR(255)  NOT NULL,
    ativo      TINYINT(1)    DEFAULT 1,
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabela: pedidos
-- ----------------------------
CREATE TABLE IF NOT EXISTS pedidos (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT           NOT NULL,
    status     ENUM('pendente','aprovado','cancelado') DEFAULT 'pendente',
    total      DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabela: itens_pedido
-- ----------------------------
CREATE TABLE IF NOT EXISTS itens_pedido (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id      INT           NOT NULL,
    game_id        INT           NOT NULL,
    quantidade     INT           DEFAULT 1,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (game_id)   REFERENCES games(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Dados de teste — usuarios
-- Senha admin: admin123 | Senha user: user123
-- ----------------------------
INSERT IGNORE INTO usuarios (nome, email, senha, tipo) VALUES
('Admin',   'admin@gamestock.com', SHA2('admin123', 256), 'admin'),
('Evandro', 'evandro@gmail.com',   SHA2('admin123', 256), 'admin'),
('Pedro',   'pedro@gmail.com',     SHA2('user123',  256), 'user');

-- ----------------------------
-- Dados de teste — categorias
-- ----------------------------
INSERT IGNORE INTO categorias (id, nome, descricao) VALUES
(1, 'Aventura', 'Jogos de exploração, narrativa e mundo aberto.'),
(2, 'Ação',     'Jogos com combate intenso e ritmo acelerado.'),
(3, 'Sandbox',  'Jogos com liberdade de criação e exploração livre.'),
(4, 'RPG',      'Jogos de interpretação de papéis com evolução de personagem.'),
(5, 'Esportes', 'Simuladores e jogos de competição esportiva.');

-- ----------------------------
-- Dados de teste — games
-- ----------------------------
INSERT IGNORE INTO games (titulo, descricao, preco, estoque, categoria_id, imagem) VALUES
('The Legend of Zelda: Breath of the Wild',
 'Uma épica aventura em mundo aberto pelos reinos de Hyrule, com exploração ilimitada e puzzles criativos.',
 299.99, 10, 1, 'zelda.jpg'),
('God of War',
 'Jogo de ação com Kratos em uma jornada épica pela mitologia nórdica ao lado do filho Atreus.',
 249.99, 15, 2, 'gow.jpg'),
('Minecraft',
 'Jogo de construção e sobrevivência em mundo aberto com possibilidades infinitas de criação.',
 129.99, 20, 3, 'minecraft.jpg'),
('Red Dead Redemption 2',
 'Uma épica aventura no Velho Oeste americano. Explore um vasto mundo aberto com gráficos deslumbrantes.',
 199.99, 8, 1, 'rdr2.jpg'),
('FIFA 25',
 'O melhor simulador de futebol do mundo com modo Ultimate Team aprimorado e realismo sem precedentes.',
 299.99, 25, 5, 'fifa25.jpg'),
('Cyberpunk 2077',
 'RPG de ação em mundo aberto ambientado em Night City, uma distopia futurista dominada por tecnologia.',
 149.99, 12, 4, 'cyberpunk.jpg'),
('Elden Ring',
 'Action RPG desafiador criado em parceria entre FromSoftware e George R.R. Martin.',
 249.99, 18, 4, 'eldenring.jpg'),
('Grand Theft Auto V',
 'Mundo aberto de crime organizado em Los Santos. História épica com três protagonistas jogáveis.',
 99.99, 30, 2, 'gtav.jpg');

-- ----------------------------
-- Dados de teste — clientes
-- Senha: cliente123
-- ----------------------------
INSERT IGNORE INTO clientes (nome, email, telefone, senha) VALUES
('Lucas Silva', 'lucas@email.com', '(31) 99999-1234', SHA2('cliente123', 256)),
('Ana Pereira', 'ana@email.com',   '(31) 98888-5678', SHA2('cliente123', 256));

-- ----------------------------
-- Dados de teste — pedidos
-- ----------------------------
INSERT IGNORE INTO pedidos (id, cliente_id, status, total) VALUES
(1, 1, 'aprovado', 449.98),
(2, 2, 'pendente', 129.99);

INSERT IGNORE INTO itens_pedido (pedido_id, game_id, quantidade, preco_unitario) VALUES
(1, 2, 1, 249.99),
(1, 8, 1,  99.99),
(2, 3, 1, 129.99);
