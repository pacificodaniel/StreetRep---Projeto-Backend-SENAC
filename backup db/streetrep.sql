-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 24/10/2025 às 18:38
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `streetrep`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `id` int(11) NOT NULL,
  `id_ocorrencia` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `tipo` enum('positivo','negativo') DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `avaliacoes`
--

INSERT INTO `avaliacoes` (`id`, `id_ocorrencia`, `id_usuario`, `tipo`, `comentario`, `criado_em`) VALUES
(1, 3, 1, '', 'É mentira!', '2025-10-23 16:42:35'),
(3, 1, 1, '', 'Verdade!!! Fui eu!!1', '2025-10-23 16:54:03');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ocorrencias`
--

CREATE TABLE `ocorrencias` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `gravidade` enum('inofensivo','baixo','medio','alto') DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `data` datetime DEFAULT current_timestamp(),
  `aval_positivo` int(11) DEFAULT 0,
  `aval_negativo` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ocorrencias`
--

INSERT INTO `ocorrencias` (`id`, `id_usuario`, `titulo`, `descricao`, `gravidade`, `latitude`, `longitude`, `data`, `aval_positivo`, `aval_negativo`) VALUES
(1, 13, 'ROUBARAM MEU PÃO!!', 'na maldade!!', 'baixo', -23.19204107, -45.88783801, '2025-10-20 16:46:35', 0, 0),
(2, 13, 'ROUBARAM OUTRO PÃO!', 'EITA!', 'baixo', -23.19242075, -45.89942515, '2025-10-20 17:08:06', 0, 0),
(3, 13, 'USO DE DROGAS', 'Vi jovens fazendo uso de drogas na área verde!!!', 'baixo', -23.19401836, -45.89635670, '2025-10-23 15:06:05', 0, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `verificado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `verificado`) VALUES
(1, 'João Silva', 'joao.silva@email.com', 'senha123', 0),
(2, 'Maria Oliveira', 'maria.oliveira@email.com', 'senha123', 0),
(3, 'Carlos Souza', 'carlos.souza@email.com', 'senha123', 0),
(4, 'Ana Costa', 'ana.costa@email.com', 'senha123', 0),
(5, 'Pedro Lima', 'pedro.lima@email.com', 'senha123', 0),
(6, 'Fernanda Rocha', 'fernanda.rocha@email.com', 'senha123', 0),
(7, 'Lucas Martins', 'lucas.martins@email.com', 'senha123', 0),
(8, 'Camila Fernandes', 'camila.fernandes@email.com', 'senha123', 0),
(9, 'Rafael Pereira', 'rafael.pereira@email.com', 'senha123', 0),
(10, 'Juliana Alves', 'juliana.alves@email.com', 'senha123', 0),
(11, 'Admin', 'admin@email.com', 'admin123', 1),
(12, 'Usuário Teste', 'teste@teste.com', '$2y$10$SE1lPS06my51DKYdRKnZ0.RRLmlUP4NYoWbkyySGRKwqaFiAQyISG', 1),
(13, 'Garibaldison_thelegend', 'garibaldison@gmail.com', '$2y$10$pIdtfgPj4ra2Y/vRymNzLuJGngpn.Igfz/rm/IaeTv57PSldYVQtC', 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unica_avaliacao` (`id_ocorrencia`,`id_usuario`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `ocorrencias`
--
ALTER TABLE `ocorrencias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `idx_location` (`latitude`,`longitude`),
  ADD KEY `idx_gravidade` (`gravidade`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `ocorrencias`
--
ALTER TABLE `ocorrencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`id_ocorrencia`) REFERENCES `ocorrencias` (`id`),
  ADD CONSTRAINT `avaliacoes_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `ocorrencias`
--
ALTER TABLE `ocorrencias`
  ADD CONSTRAINT `ocorrencias_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
