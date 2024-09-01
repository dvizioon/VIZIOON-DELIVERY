-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 20/02/2024 às 20:41
-- Versão do servidor: 10.6.16-MariaDB-cll-lve
-- Versão do PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `u280989637_cardapio`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `adm`
--

CREATE TABLE `adm` (
  `Id` int(11) NOT NULL,
  `dias` varchar(3) DEFAULT '7',
  `novocliente` varchar(1) DEFAULT '1',
  `nome` varchar(150) NOT NULL,
  `login` varchar(50) NOT NULL,
  `senha` varchar(60) NOT NULL,
  `linkpgmto` text NOT NULL,
  `statuslink` int(2) NOT NULL DEFAULT 1,
  `bloquear` int(2) NOT NULL DEFAULT 1,
  `celular` varchar(11) NOT NULL,
  `nomedosite` varchar(120) NOT NULL,
  `urlsite` varchar(160) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `adm`
--

INSERT INTO `adm` (`Id`, `dias`, `novocliente`, `nome`, `login`, `senha`, `linkpgmto`, `statuslink`, `bloquear`, `celular`, `nomedosite`, `urlsite`) VALUES
(1, '7', '1', 'admin', 'super_admin', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '<script src=\"https://www.mercadopago.com.br/integrations/v1/web-payment-checkout.js\"\r\ndata-preference-id=\"666738441-c1885f4d-d035-4ece-bef6-3ec12c1297cb\">\r\n</script>\r\n\r\n', 1, 1, '6782085883', 'CardaZAP®', 'https://cardazap.com.br/');

-- --------------------------------------------------------

--
-- Estrutura para tabela `metodospagamentos`
--


CREATE TABLE `metodospagamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idu` int(11) NOT NULL,
  `metodopagamento` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


-- --------------------------------------------------------

--
-- Estrutura para tabela `registrospagamentos`
--

-- Tabela Registro Geral
CREATE TABLE `registrospagamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idu` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nome` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `idpedido` varchar(70) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '1',
  `tipo` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `dados_pagamentos` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `mesa_registrada` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
 `data_registro` datetime NOT NULL,
  `vsubtotal` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `vtotal` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `valor_dinheiro` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `valor_troco` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `formapaga` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE registrospagamentos
MODIFY COLUMN dados_pagamentos VARCHAR(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL;

-- --------------------------------------------------------
--
-- Estrutura para tabela `Comissao`
--
CREATE TABLE `comissao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idu` int(11) NOT NULL,
  `statuso` ENUM('habilitado', 'desabilitado') NOT NULL DEFAULT 'desabilitado',
  `comissao` DECIMAL(10,2) NOT NULL,  -- Adicionando a coluna comissao para valores grandes
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `MotoBoy`
--
CREATE TABLE `motoboy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idu` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `codigo_funcionario` varchar(255) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `veiculo` varchar(50) NOT NULL,
  `placa_veiculo` varchar(20) NOT NULL,
  `data_contratacao` DATE NOT NULL,
  `tipo_motoboy` ENUM('avulso', 'contratado') NOT NULL DEFAULT 'contratado',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


-- --------------------------------------------------------
--
-- Estrutura para tabela `motivo_cancelamento`
--
CREATE TABLE motivo_cancelamento (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `idu` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `nome` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
    `id_pedido` INT NOT NULL,
    `status` INT NOT NULL,
    `motivo_cancelamento` TEXT,
    `data_pedido` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- --------------------------------------------------------

--
-- Estrutura para tabela `efeitosSonoros`
--

-- Tabela Registro Geral
CREATE TABLE `efeitosSonoros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idu` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `caminho` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `padrao` ENUM('h', 'd') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------
--
-- Estrutura para tabela `bairros`
--

CREATE TABLE `bairros` (
  `id` int(11) NOT NULL,
  `idu` int(11) NOT NULL,
  `bairro` varchar(30) NOT NULL,
  `taxa` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


-- --------------------------------------------------------


--
-- Estrutura para tabela `banner`
--

CREATE TABLE `banner` (
  `id` int(11) NOT NULL,
  `idu` int(11) NOT NULL,
  `img` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `url` varchar(100) NOT NULL,
  `idu` int(11) NOT NULL,
  `posicao` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `idu` int(11) NOT NULL,
  `nomeempresa` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nomeadmin` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `senha` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cpf` varchar(18) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `url` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `telefone` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `celular` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cep` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `rua` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `bairro` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cidade` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `uf` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `complemento` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `numero` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  `fuso` varchar(60) NOT NULL DEFAULT 'America/Sao_Paulo',
  `mesa` int(2) NOT NULL DEFAULT 2,
  `balcao` int(2) NOT NULL DEFAULT 1,
  `delivery` int(2) NOT NULL DEFAULT 1,
  `cupon` int(2) NOT NULL DEFAULT 1,
  `dom` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '08:00:00,12:00:00,14:00:00,23:00:00',
  `seg` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '08:00:00,12:00:00,14:00:00,23:00:00',
  `ter` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '08:00:00,12:00:00,14:00:00,23:00:00',
  `qua` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '08:00:00,12:00:00,14:00:00,23:00:00',
  `qui` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '08:00:00,12:00:00,14:00:00,23:00:00',
  `sex` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '08:00:00,12:00:00,14:00:00,23:00:00',
  `sab` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '08:00:00,12:00:00,14:00:00,23:00:00',
  `funcionamento` int(2) DEFAULT 1,
  `cormenu` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT '#FFFFFF',
  `corfundo` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT '#F0F2F7',
  `corrodape` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT '#E3E7EB',
  `corcarrinho` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT '#FFFFE6',
  `timerdelivery` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT '40 a 50min',
  `timerbalcao` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT '20min',
  `modelo` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT '1',
  `modelo2` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT '0',
  `modelo3` int(2) NOT NULL DEFAULT 1,
  `datacad` date NOT NULL,
  `dfree` varchar(9) NOT NULL DEFAULT '100.00',
  `insta` varchar(60) NOT NULL DEFAULT 'https://www.instagram.com',
  `expiracao` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


--
-- Estrutura para tabela `funcionarios`
--

CREATE TABLE `funcionarios` (
  `id` int(11) NOT NULL,
  `idu` int(9) NOT NULL,
  `nome` varchar(60) NOT NULL,
  `login` varchar(30) NOT NULL,
  `senha` varchar(60) NOT NULL,
  `acesso` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Estrutura para tabela `fundotopo`
--

CREATE TABLE `fundotopo` (
  `id` int(11) NOT NULL,
  `idu` int(9) NOT NULL,
  `foto` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


-- --------------------------------------------------------

--
-- Estrutura para tabela `grupos`
--

CREATE TABLE `grupos` (
  `Id` int(11) NOT NULL,
  `idu` int(9) DEFAULT 0,
  `nomegrupo` varchar(160) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '0',
  `nomeinterno` varchar(160) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT '0',
  `obrigatorio` int(2) DEFAULT 0,
  `posicao` int(11) DEFAULT 0,
  `status` int(11) DEFAULT 0,
  `quantidade` int(11) DEFAULT 0,
  `quantidade_minima` varchar(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


-- --------------------------------------------------------

--
-- Estrutura para tabela `limite_op`
--

CREATE TABLE `limite_op` (
  `Id` int(11) NOT NULL,
  `idp` varchar(9) DEFAULT NULL,
  `limite` varchar(3) DEFAULT '0',
  `idgrupo` int(11) DEFAULT 0,
  `meioameio` int(2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;



-- --------------------------------------------------------

--
-- Estrutura para tabela `logo`
--

CREATE TABLE `logo` (
  `id` int(11) NOT NULL,
  `idu` varchar(9) NOT NULL,
  `foto` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `data` varchar(10) NOT NULL,
  `status` int(2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mesas`
--

CREATE TABLE `mesas` (
  `id` int(11) NOT NULL,
  `idu` varchar(9) NOT NULL,
  `numero` int(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;



-- --------------------------------------------------------

--
-- Estrutura para tabela `opcionais`
--

CREATE TABLE `opcionais` (
  `id` int(11) NOT NULL,
  `idu` int(11) NOT NULL,
  `idp` int(11) NOT NULL,
  `idg` int(11) DEFAULT 0,
  `opnome` varchar(30) NOT NULL,
  `opdescricao` varchar(160) NOT NULL DEFAULT 'N',
  `valor` varchar(10) NOT NULL,
  `status` int(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--


CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `idu` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `idpedido` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `entrada` timestamp NOT NULL DEFAULT current_timestamp(),
  `fpagamento` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cidade` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `numero` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `complemento` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `rua` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `bairro` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `troco` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nome` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `data` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `hora` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `celular` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `taxa` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `mesa` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `pessoas` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `obs` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '1',
  `vsubtotal` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `vadcionais` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `vtotal` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `comissao` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `motoboy` VARCHAR(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `atendente` VARCHAR(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE pedidos
MODIFY COLUMN fpagamento VARCHAR(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL;

ALTER TABLE pedidos
ADD COLUMN atendente_criador VARCHAR(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL;


-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `idu` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `valor` varchar(100) NOT NULL,
  `valorde` varchar(10) DEFAULT '0.00',
  `ingredientes` text NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `foto` varchar(100) NOT NULL,
  `fotos2` varchar(255) DEFAULT 'off.jpg',
  `destaques` int(1) NOT NULL DEFAULT 0,
  `visivel` varchar(2) NOT NULL DEFAULT 'G',
  `status` int(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cozinha`
--


CREATE TABLE `cozinha` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idu` int(11) NOT NULL,
  `idpedido` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `data` varchar(100) NOT NULL,
  `status_cozinha` ENUM('entregue', 'fazendo' ,'finalizado' ,'parado') NOT NULL DEFAULT 'parado',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


-- --------------------------------------------------------

--
-- Estrutura para tabela `store`
--

CREATE TABLE `store` (
  `id` int(11) NOT NULL,
  `idu` int(10) NOT NULL,
  `idpedido` varchar(32) NOT NULL,
  `idsecao` int(20) NOT NULL,
  `produto_id` varchar(100) NOT NULL,
  `data` varchar(100) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `valor` varchar(100) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `tamanho` varchar(19) NOT NULL DEFAULT 'N',
  `obs` varchar(255) DEFAULT 'Não',
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `store`
ADD COLUMN `pedido_entregue` ENUM('sim', 'nao') NOT NULL DEFAULT 'nao';

ALTER TABLE `store`
ADD COLUMN `pedido_entregue_funcionario` ENUM('sim', 'nao') NOT NULL DEFAULT 'nao';

ALTER TABLE `store`
ADD COLUMN `referencia` varchar(100) NOT NULL;


-- --------------------------------------------------------

--
-- Estrutura para tabela `store_o`
--

CREATE TABLE `store_o` (
  `id` int(11) NOT NULL,
  `idu` int(10) NOT NULL,
  `idp` varchar(32) NOT NULL DEFAULT '0',
  `ids` int(30) NOT NULL,
  `nome` varchar(200) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT 0,
  `valor` varchar(10) NOT NULL,
  `quantidade` varchar(5) NOT NULL,
  `meioameio` varchar(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `store_o`
ADD COLUMN `pedido_entregue` ENUM('sim', 'nao') NOT NULL DEFAULT 'nao';

ALTER TABLE `store_o`
ADD COLUMN `pedido_entregue_funcionario` ENUM('sim', 'nao') NOT NULL DEFAULT 'nao';

ALTER TABLE `store_o`
ADD COLUMN `id_referencia` varchar(100) NOT NULL;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tamanhos`
--

CREATE TABLE `tamanhos` (
  `id` int(11) NOT NULL,
  `idu` int(9) NOT NULL,
  `idp` int(9) NOT NULL,
  `descricao` varchar(30) NOT NULL,
  `valor` varchar(30) NOT NULL,
  `status` int(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `adm`
--
ALTER TABLE `adm`
  ADD PRIMARY KEY (`Id`);

--
-- Índices de tabela `bairros`
--
ALTER TABLE `bairros`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `fundotopo`
--
ALTER TABLE `fundotopo`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`Id`);

--
-- Índices de tabela `limite_op`
--
ALTER TABLE `limite_op`
  ADD PRIMARY KEY (`Id`);

--
-- Índices de tabela `logo`
--
ALTER TABLE `logo`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `opcionais`
--
ALTER TABLE `opcionais`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `store`
--
ALTER TABLE `store`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `store_o`
--
ALTER TABLE `store_o`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tamanhos`
--
ALTER TABLE `tamanhos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `adm`
--
ALTER TABLE `adm`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `bairros`
--
ALTER TABLE `bairros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT de tabela `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT de tabela `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `fundotopo`
--
ALTER TABLE `fundotopo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de tabela `grupos`
--
ALTER TABLE `grupos`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT de tabela `limite_op`
--
ALTER TABLE `limite_op`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT de tabela `logo`
--
ALTER TABLE `logo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `opcionais`
--
ALTER TABLE `opcionais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=382;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=259;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=397;

--
-- AUTO_INCREMENT de tabela `store`
--
ALTER TABLE `store`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=848;

--
-- AUTO_INCREMENT de tabela `store_o`
--
ALTER TABLE `store_o`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1327;

--
-- AUTO_INCREMENT de tabela `tamanhos`
--
ALTER TABLE `tamanhos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
