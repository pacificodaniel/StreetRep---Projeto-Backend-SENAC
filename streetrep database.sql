-- Banco de Dados: STREETREP inical 

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  senha VARCHAR(255),
  verificado BOOLEAN DEFAULT 0
);

CREATE TABLE ocorrencias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT,
  titulo VARCHAR(100),
  descricao TEXT,
  gravidade ENUM('inofensivo', 'baixo', 'medio', 'alto'),
  latitude DECIMAL(10,8),
  longitude DECIMAL(11,8),
  data DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE SET NULL

);

CREATE TABLE avaliacoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ocorrencia INT,
  id_usuario INT,
  tipo ENUM('real', 'falso'),
  comentario TEXT NULL,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_ocorrencia) REFERENCES ocorrencias(id),
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE SET NULL
);
-- CREATE TABLE avaliacoes (
--   id INT AUTO_INCREMENT PRIMARY KEY,
--   id_ocorrencia INT,
--   id_usuario INT,
--   tipo ENUM('real', 'falso'),
--   FOREIGN KEY (id_ocorrencia) REFERENCES ocorrencias(id),
--   FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE SET NULL

-- );


-- 10 usuários comuns
INSERT INTO usuarios (nome, email, senha, verificado) VALUES
('João Silva', 'joao.silva@email.com', 'senha123', 0),
('Maria Oliveira', 'maria.oliveira@email.com', 'senha123', 0),
('Carlos Souza', 'carlos.souza@email.com', 'senha123', 0),
('Ana Costa', 'ana.costa@email.com', 'senha123', 0),
('Pedro Lima', 'pedro.lima@email.com', 'senha123', 0),
('Fernanda Rocha', 'fernanda.rocha@email.com', 'senha123', 0),
('Lucas Martins', 'lucas.martins@email.com', 'senha123', 0),
('Camila Fernandes', 'camila.fernandes@email.com', 'senha123', 0),
('Rafael Pereira', 'rafael.pereira@email.com', 'senha123', 0),
('Juliana Alves', 'juliana.alves@email.com', 'senha123', 0);

-- 1 admin
INSERT INTO usuarios (nome, email, senha, verificado) VALUES
('Admin', 'admin@email.com', 'admin123', 1);


--INDEXES PARA OTIMIZAÇÃO DE CONSULTAS
-- CREATE INDEX idx_location ON ocorrencias(latitude, longitude);
-- CREATE INDEX idx_gravidade ON ocorrencias(gravidade);

--ALTER TABLES (ALTERAÇÕES):

ALTER TABLE avaliacoes 
ADD COLUMN comentario TEXT NULL,
ADD COLUMN criado_em DATETIME DEFAULT CURRENT_TIMESTAMP;
