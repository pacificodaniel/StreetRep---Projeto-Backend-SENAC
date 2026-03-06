-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 04/11/2025 às 20:55
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
(3, 1, 1, '', 'Verdade!!! Fui eu!!1', '2025-10-23 16:54:03'),
(11, 4, 13, 'negativo', 'falso', '2025-10-27 15:28:52'),
(13, 1, 13, '', 'dfasff', '2025-10-27 14:50:51');

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
(1, 13, 'ROUBARAM MEU PÃO!!', 'na maldade!! kkkkk hhhhh', 'baixo', -23.19204107, -45.88783801, '2025-10-20 16:46:35', 0, 0),
(3, 13, 'USO DE DROGAS', 'Vi jovens fazendo uso de drogas na área verde!!!', 'baixo', -23.19401836, -45.89635670, '2025-10-23 15:06:05', 0, 0),
(4, 13, 'aaaaaaaaa', 'aaaaaaaaaaa', 'inofensivo', -23.19342665, -45.89034319, '2025-10-24 16:25:01', 0, 1),
(5, 13, 'sp teste', '11', 'inofensivo', -23.56792075, -46.62923813, '2025-10-24 16:35:47', 1, 0),
(6, 14, 'o_teste1', '1', 'alto', -23.26524024, -45.89697361, '2025-11-03 14:47:19', 0, 0),
(7, 14, 'o_teste2', '2', 'alto', -23.26776346, -45.89469910, '2025-11-03 14:47:42', 0, 0),
(8, 14, 'o_teste3', '3', 'alto', -23.26630473, -45.89615822, '2025-11-03 14:47:55', 0, 0),
(9, 14, 'o_teste4', '4', 'alto', -23.26675812, -45.89549303, '2025-11-03 14:48:08', 0, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `requisicoes_verificacao`
--

CREATE TABLE `requisicoes_verificacao` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nome_completo` varchar(100) DEFAULT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `rede_social` varchar(200) DEFAULT NULL,
  `status` enum('PENDENTE','APROVADO','REPROVADO') DEFAULT 'PENDENTE',
  `data_solicitacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `requisicoes_verificacao`
--

INSERT INTO `requisicoes_verificacao` (`id`, `usuario_id`, `nome_completo`, `cpf`, `email`, `rede_social`, `status`, `data_solicitacao`) VALUES
(1, 15, 'João Pereira', '37492837584', 'joaozinhonotverified@gmail.com', '@joaofake', 'APROVADO', '2025-11-03 13:57:11'),
(2, 16, 'teste40', '82372638264', 'teste40@gmail.com', 'nenhuma', 'PENDENTE', '2025-11-04 16:13:36');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `verificado` tinyint(1) DEFAULT 0,
  `tipo_usuario` enum('COMUM','ADMIN') DEFAULT 'COMUM'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `verificado`, `tipo_usuario`) VALUES
(1, 'João Silva', 'joao.silva@email.com', 'senha123', 0, 'COMUM'),
(2, 'Maria Oliveira', 'maria.oliveira@email.com', 'senha123', 0, 'COMUM'),
(3, 'Carlos Souza', 'carlos.souza@email.com', 'senha123', 0, 'COMUM'),
(4, 'Ana Costa', 'ana.costa@email.com', 'senha123', 0, 'COMUM'),
(5, 'Pedro Lima', 'pedro.lima@email.com', 'senha123', 0, 'COMUM'),
(6, 'Fernanda Rocha', 'fernanda.rocha@email.com', 'senha123', 0, 'COMUM'),
(7, 'Lucas Martins', 'lucas.martins@email.com', 'senha123', 0, 'COMUM'),
(8, 'Camila Fernandes', 'camila.fernandes@email.com', 'senha123', 0, 'COMUM'),
(9, 'Rafael Pereira', 'rafael.pereira@email.com', 'senha123', 0, 'COMUM'),
(10, 'Juliana Alves', 'juliana.alves@email.com', 'senha123', 0, 'COMUM'),
(11, 'Admin', 'admin@email.com', 'admin123', 1, 'ADMIN'),
(12, 'Usuário Teste', 'teste@teste.com', '$2y$10$SE1lPS06my51DKYdRKnZ0.RRLmlUP4NYoWbkyySGRKwqaFiAQyISG', 1, 'COMUM'),
(13, 'Garibaldison_thelegend', 'garibaldison@gmail.com', '$2y$10$pIdtfgPj4ra2Y/vRymNzLuJGngpn.Igfz/rm/IaeTv57PSldYVQtC', 1, 'COMUM'),
(14, 'ADMIN2', 'ADMIN2@ADMIN.COM', '$2y$10$Zh9J0QRyph5aIe4b3OoXYefiM3Ni/8lCi8TOMscy2HEmbOqz7pNxC', 1, 'ADMIN'),
(15, 'joaozinhofake', 'joaozinhonotverified@protonmail.com', '$2y$10$5hH7Q0FW/X4kCf711f8V0.fxo6/CbsoMIPryg6CzUGhHyt40QHhea', 1, 'COMUM'),
(16, 'teste40', 'teste40@gmail.com', '$2y$10$6WrawT.EUyKxQmqKTBAbHuIxMdBwp5TouFnyAISNoM8HvqJ3o47tS', 0, 'COMUM');

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
-- Índices de tabela `requisicoes_verificacao`
--
ALTER TABLE `requisicoes_verificacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `ocorrencias`
--
ALTER TABLE `ocorrencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `requisicoes_verificacao`
--
ALTER TABLE `requisicoes_verificacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`id_ocorrencia`) REFERENCES `ocorrencias` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `avaliacoes_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `ocorrencias`
--
ALTER TABLE `ocorrencias`
  ADD CONSTRAINT `ocorrencias_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `requisicoes_verificacao`
--
ALTER TABLE `requisicoes_verificacao`
  ADD CONSTRAINT `requisicoes_verificacao_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
