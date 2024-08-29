-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29-Ago-2024 às 05:04
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `cardapio`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `adm`
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
-- Extraindo dados da tabela `adm`
--

INSERT INTO `adm` (`Id`, `dias`, `novocliente`, `nome`, `login`, `senha`, `linkpgmto`, `statuslink`, `bloquear`, `celular`, `nomedosite`, `urlsite`) VALUES
(1, '7', '1', 'Seu DeliveryÂ®', 'super_admin', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '<script src=\"https://www.mercadopago.com.br/integrations/v1/web-payment-checkout.js\"\r\ndata-preference-id=\"666738441-c1885f4d-d035-4ece-bef6-3ec12c1297cb\">\r\n</script>\r\n\r\n', 1, 1, '6782085883', 'CardaZAP®', 'https://cardazap.com.br/');

-- --------------------------------------------------------

--
-- Estrutura da tabela `bairros`
--

CREATE TABLE `bairros` (
  `id` int(11) NOT NULL,
  `idu` int(11) NOT NULL,
  `bairro` varchar(30) NOT NULL,
  `taxa` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Extraindo dados da tabela `bairros`
--

INSERT INTO `bairros` (`id`, `idu`, `bairro`, `taxa`) VALUES
(135, 41, 'Bairro 1', '0.00'),
(136, 41, 'Bairro 2', '5.00'),
(137, 41, 'Bairro 3', '7.00'),
(138, 41, 'Bairro 4', '10.00'),
(139, 44, 'Anjo Da Guarda', '5.00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `banner`
--

CREATE TABLE `banner` (
  `id` int(11) NOT NULL,
  `idu` int(11) NOT NULL,
  `img` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Extraindo dados da tabela `banner`
--

INSERT INTO `banner` (`id`, `idu`, `img`) VALUES
(46, 41, '16163390934284.JPG'),
(49, 44, 'off.jpg');

-- --------------------------------------------------------

--
-- Estrutura da tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `url` varchar(100) NOT NULL,
  `idu` int(11) NOT NULL,
  `posicao` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Extraindo dados da tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `url`, `idu`, `posicao`) VALUES
(99, 'Exclusivos Delivery', '16163302943990.jpg', 41, 1),
(100, 'Combos', '16163304792576.jpg', 41, 2),
(101, 'HambÃºrgueres', '16163305401431.jpg', 41, 3),
(103, 'Pizzas', '16163306443504.jpg', 41, 5),
(104, 'PorÃ§Ãµes', '16163310034537.jpg', 41, 6),
(105, 'Bebidas', '16163311503223.jpg', 41, 7),
(106, 'Sobremesas', '16163312244490.jpg', 41, 8),
(107, 'Massas', 'off.jpg', 44, 1),
(108, 'Bebidas', 'off.jpg', 44, 2),
(109, 'Pizzas', 'off.jpg', 44, 3),
(110, 'Hambugue', 'off.jpg', 44, 4);

-- --------------------------------------------------------

--
-- Estrutura da tabela `comissao`
--

CREATE TABLE `comissao` (
  `id` int(11) NOT NULL,
  `idu` int(11) NOT NULL,
  `statuso` enum('habilitado','desabilitado') NOT NULL DEFAULT 'desabilitado',
  `comissao` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Extraindo dados da tabela `comissao`
--

INSERT INTO `comissao` (`id`, `idu`, `statuso`, `comissao`) VALUES
(1, 44, 'habilitado', 5.00);

-- --------------------------------------------------------

--
-- Estrutura da tabela `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `idu` int(11) NOT NULL,
  `nomeempresa` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `nomeadmin` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `senha` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `cpf` varchar(18) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `telefone` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `celular` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `cep` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `rua` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `bairro` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `cidade` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `uf` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `complemento` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `numero` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  `fuso` varchar(60) NOT NULL DEFAULT 'America/Sao_Paulo',
  `mesa` int(2) NOT NULL DEFAULT 2,
  `balcao` int(2) NOT NULL DEFAULT 1,
  `delivery` int(2) NOT NULL DEFAULT 1,
  `cupon` int(2) NOT NULL DEFAULT 1,
  `dom` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '08:00:00,12:00:00,14:00:00,23:00:00',
  `seg` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '08:00:00,12:00:00,14:00:00,23:00:00',
  `ter` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '08:00:00,12:00:00,14:00:00,23:00:00',
  `qua` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '08:00:00,12:00:00,14:00:00,23:00:00',
  `qui` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '08:00:00,12:00:00,14:00:00,23:00:00',
  `sex` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '08:00:00,12:00:00,14:00:00,23:00:00',
  `sab` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '08:00:00,12:00:00,14:00:00,23:00:00',
  `funcionamento` int(2) DEFAULT 1,
  `cormenu` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '#FFFFFF',
  `corfundo` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '#F0F2F7',
  `corrodape` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '#E3E7EB',
  `corcarrinho` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '#FFFFE6',
  `timerdelivery` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '40 a 50min',
  `timerbalcao` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '20min',
  `modelo` varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '1',
  `modelo2` varchar(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '0',
  `modelo3` int(2) NOT NULL DEFAULT 1,
  `datacad` date NOT NULL,
  `dfree` varchar(9) NOT NULL DEFAULT '100.00',
  `insta` varchar(60) NOT NULL DEFAULT 'https://www.instagram.com',
  `expiracao` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Extraindo dados da tabela `config`
--

INSERT INTO `config` (`id`, `idu`, `nomeempresa`, `nomeadmin`, `email`, `senha`, `cpf`, `url`, `telefone`, `celular`, `cep`, `rua`, `bairro`, `cidade`, `uf`, `complemento`, `numero`, `status`, `fuso`, `mesa`, `balcao`, `delivery`, `cupon`, `dom`, `seg`, `ter`, `qua`, `qui`, `sex`, `sab`, `funcionamento`, `cormenu`, `corfundo`, `corrodape`, `corcarrinho`, `timerdelivery`, `timerbalcao`, `modelo`, `modelo2`, `modelo3`, `datacad`, `dfree`, `insta`, `expiracao`) VALUES
(41, 1, 'CardaZAP® Lanches', 'CardaZAP®', 'seudeliverydigital@gmail.com', '779a923d69b2e072747b11975ba86949de167037', '111111111', 'clientedemo', '00000000', '22999235594', '85000000', 'Rua Padre Anchieta', 'Centro', 'SÃ£o Paulo', 'SP', 'Em Frente ao Shopping', '123', 1, 'America/Sao_Paulo', 1, 1, 1, 1, '00:01:00,12:00:00,12:01:00,23:59:00', '08:00:00,12:00:00,14:00:00,23:55:00', '00:01:00,12:00:00,12:01:00,23:59:00', '00:01:00,12:00:00,12:01:00,23:59:00', '00:01:00,12:00:00,12:01:00,23:59:00', '00:01:00,12:00:00,12:01:00,23:59:00', '00:01:00,12:00:00,12:01:00,23:59:00', 1, '#FFFFFF', '#F0F2F7', '#FFFFFF', '#FFFFE6', '40 a 50min', '20min', '1', '1', 1, '2020-10-18', '100.00', 'https://www.instagram.com', '2021-04-19'),
(44, 1, 'daniel', 'daniel', 'daniel@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '50118124218', 'teste', '00000000', '98979828388', '000000000', 'xx', 'xx', 'xx', 'xx', 'xx', '00', 1, 'America/Sao_Paulo', 1, 1, 1, 1, '08:00:00,12:00:00,12:00:00,23:59:00', '08:00:00,12:00:00,14:00:00,23:55:00', '08:00:00,12:00:00,14:00:00,23:55:00', '00:00:00,08:00:00,14:00:00,23:55:00', '00:00:00,08:00:00,14:00:00,23:55:00', '08:00:00,12:00:00,14:00:00,23:00:00', '08:00:00,12:00:00,14:00:00,23:00:00', 1, '#FFFFFF', '#F0F2F7', '#E3E7EB', '#FFFFE6', '40 a 50min', '20min', '1', '1', 1, '2024-08-17', '100.00', 'https://www.instagram.com', '2024-09-23');

-- --------------------------------------------------------

--
-- Estrutura da tabela `efeitossonoros`
--

CREATE TABLE `efeitossonoros` (
  `id` int(11) NOT NULL,
  `idu` varchar(20) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `caminho` varchar(150) NOT NULL,
  `padrao` enum('h','d') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `efeitossonoros`
--

INSERT INTO `efeitossonoros` (`id`, `idu`, `nome`, `caminho`, `padrao`) VALUES
(1, '44', 'Campainha', '../pdv/sounds/campainha.mp3', 'h'),
(2, '44', 'AUDIO NOVO', '../pdv/sounds/correct-2-46134.mp3', 'd');

-- --------------------------------------------------------

--
-- Estrutura da tabela `funcionarios`
--

CREATE TABLE `funcionarios` (
  `id` int(11) NOT NULL,
  `idu` int(9) NOT NULL,
  `nome` varchar(60) NOT NULL,
  `login` varchar(30) NOT NULL,
  `senha` varchar(60) NOT NULL,
  `acesso` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Extraindo dados da tabela `funcionarios`
--

INSERT INTO `funcionarios` (`id`, `idu`, `nome`, `login`, `senha`, `acesso`) VALUES
(15, 42, 'Lucas', '123456', '7c4a8d09ca3762af61e59520943dc26494f8941b', '1'),
(16, 42, 'Lucas', '12345', '8cb2237d0679ca88db6464eac60da96345513964', '2'),
(17, 41, 'Maycon', 'PDV', '779a923d69b2e072747b11975ba86949de167037', '1'),
(18, 41, 'Cyntia', 'COZINHA', '779a923d69b2e072747b11975ba86949de167037', '2'),
(19, 44, 'daniel', 'daniel', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '1');

-- --------------------------------------------------------

--
-- Estrutura da tabela `fundotopo`
--

CREATE TABLE `fundotopo` (
  `id` int(11) NOT NULL,
  `idu` int(9) NOT NULL,
  `foto` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Extraindo dados da tabela `fundotopo`
--

INSERT INTO `fundotopo` (`id`, `idu`, `foto`) VALUES
(42, 41, '16162649352812.JPG'),
(45, 44, 'off.jpg');

-- --------------------------------------------------------

--
-- Estrutura da tabela `grupos`
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

--
-- Extraindo dados da tabela `grupos`
--

INSERT INTO `grupos` (`Id`, `idu`, `nomegrupo`, `nomeinterno`, `obrigatorio`, `posicao`, `status`, `quantidade`, `quantidade_minima`) VALUES
(84, 43, 'teste', 'TEste', 2, 1, 1, 4, '0'),
(87, 41, 'Molhos', 'Molhos', 1, 1, 1, 1, '0'),
(92, 44, 'Pizza GG', 'Pizza GG', 3, 3, 1, 3, '0'),
(93, 44, 'Tipos', 'Tipos', 3, 4, 1, 4, '0'),
(94, 44, 'molho branco', 'molho branco', 2, 5, 1, 3, '0');

-- --------------------------------------------------------

--
-- Estrutura da tabela `limite_op`
--

CREATE TABLE `limite_op` (
  `Id` int(11) NOT NULL,
  `idp` varchar(9) DEFAULT NULL,
  `limite` varchar(3) DEFAULT '0',
  `idgrupo` int(11) DEFAULT 0,
  `meioameio` int(2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Extraindo dados da tabela `limite_op`
--

INSERT INTO `limite_op` (`Id`, `idp`, `limite`, `idgrupo`, `meioameio`) VALUES
(127, '379', '0', 76, 1),
(128, '379', '0', 77, 2),
(129, '380', '0', 78, 3),
(130, '380', '0', 79, 1),
(131, '384', '0', 80, 2),
(132, '384', '0', 81, 2),
(133, '384', '0', 82, 2),
(134, '384', '0', 83, 2),
(135, '387', '0', 87, 1),
(136, '386', '0', 87, 1),
(137, '388', '0', 87, 1),
(138, '389', '0', 87, 1),
(139, '390', '0', 87, 1),
(140, '392', '0', 87, 1),
(147, '400', '0', 92, 3),
(148, '401', '0', 93, 3),
(150, '401', '0', 94, 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `logo`
--

CREATE TABLE `logo` (
  `id` int(11) NOT NULL,
  `idu` varchar(9) NOT NULL,
  `foto` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Extraindo dados da tabela `logo`
--

INSERT INTO `logo` (`id`, `idu`, `foto`) VALUES
(41, '41', '16163382803721.jpg'),
(44, '44', 'off.jpg');

-- --------------------------------------------------------

--
-- Estrutura da tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `data` varchar(10) NOT NULL,
  `status` int(2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;

-- --------------------------------------------------------

--
-- Estrutura da tabela `mesas`
--

CREATE TABLE `mesas` (
  `id` int(11) NOT NULL,
  `idu` varchar(9) NOT NULL,
  `numero` int(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Extraindo dados da tabela `mesas`
--

INSERT INTO `mesas` (`id`, `idu`, `numero`) VALUES
(10, '44', 1),
(11, '44', 2),
(12, '44', 3);

-- --------------------------------------------------------

--
-- Estrutura da tabela `metodospagamentos`
--

CREATE TABLE `metodospagamentos` (
  `id` int(11) NOT NULL,
  `idu` int(11) NOT NULL,
  `metodopagamento` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Extraindo dados da tabela `metodospagamentos`
--

INSERT INTO `metodospagamentos` (`id`, `idu`, `metodopagamento`) VALUES
(11, 44, 'Pix'),
(12, 44, 'Cartão'),
(13, 44, 'Dinheiro');

-- --------------------------------------------------------

--
-- Estrutura da tabela `motivo_cancelamento`
--

CREATE TABLE `motivo_cancelamento` (
  `id` int(11) NOT NULL,
  `idu` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `nome` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `motivo_cancelamento` text DEFAULT NULL,
  `data_pedido` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `motoboy`
--

CREATE TABLE `motoboy` (
  `id` int(11) NOT NULL,
  `idu` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `codigo_funcionario` varchar(255) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `veiculo` varchar(50) NOT NULL,
  `placa_veiculo` varchar(20) NOT NULL,
  `data_contratacao` date NOT NULL,
  `tipo_motoboy` enum('avulso','contratado') NOT NULL DEFAULT 'contratado'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Extraindo dados da tabela `motoboy`
--

INSERT INTO `motoboy` (`id`, `idu`, `nome`, `codigo_funcionario`, `telefone`, `endereco`, `veiculo`, `placa_veiculo`, `data_contratacao`, `tipo_motoboy`) VALUES
(2, 44, 'DANIEL ESTEVÃO', '#motoboy_5ec0ac0ac105a62f', '98970149903', 'Anjo da Guarda', 'Moto-Cros', 'DM343', '2024-08-17', 'contratado'),
(3, 44, 'JOÃO MARTINS', '#motoboy_8707259c77f2b6de', '98970149906', 'Anjo da Guarda', 'Honda - 250', 'jME44', '2024-08-18', 'contratado');

-- --------------------------------------------------------

--
-- Estrutura da tabela `opcionais`
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

--
-- Extraindo dados da tabela `opcionais`
--

INSERT INTO `opcionais` (`id`, `idu`, `idp`, `idg`, `opnome`, `opdescricao`, `valor`, `status`) VALUES
(380, 41, 0, 87, 'Maionese Temperada', 'Maionese temperada especial da casa.', '1', 1),
(381, 41, 0, 87, 'Molho Barbecue', 'Molho Barbecue defumado', '2', 1),
(390, 44, 0, 92, 'Pizza Nodestina', 'N', '10.00', 1),
(391, 44, 0, 92, 'Pizza de Chocolate', 'N', '20.00', 1),
(392, 44, 0, 92, 'Pizza Calabresa', 'N', '40.00', 1),
(393, 44, 0, 93, 'X-tudo', 'N', '20.00', 1),
(394, 44, 0, 93, 'X-Calabresa', 'molho', '15.00', 1),
(395, 44, 0, 94, 'Molho Branco', 'N', '2.00', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `idu` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `idpedido` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `entrada` timestamp NOT NULL DEFAULT current_timestamp(),
  `fpagamento` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `cidade` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `numero` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `complemento` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `rua` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `bairro` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `troco` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `nome` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `data` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `hora` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `celular` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `taxa` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `mesa` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `pessoas` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `obs` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `vsubtotal` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `vadcionais` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `vtotal` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `comissao` decimal(10,2) NOT NULL DEFAULT 0.00,
  `motoboy` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `atendente` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `atendente_criador` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
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

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`id`, `idu`, `nome`, `valor`, `valorde`, `ingredientes`, `categoria`, `foto`, `fotos2`, `destaques`, `visivel`, `status`) VALUES
(386, 41, 'HambÃºrguer California ', '30.00', '0.00', 'PÃƒO ESPECIAL, BATATA CHIPS, ALFACE, TOMATE, 2 CARNES 140G, BARBECUE, CHEDDAR CREMOSO, CALABRESA E OVO', '99', '16163410253152.jpg', 'off.jpg', 0, 'G', 1),
(387, 41, 'Massa Molho Mix', '0.00', '0.00', 'MACARRÃƒO COM MOLHO Ã€ BASE DE TOMATE, CEBOLA, BACON, CALABRESA, PARMESÃƒO RALADO, FILÃ‰ DE FRANGO, FILÃ‰ DE BOI, MILHO, SHOYO E TEMPERO VERDE.', '99', '16163411723091.jpg', 'off.jpg', 0, 'G', 1),
(388, 41, 'Combo Big Cheddar', '65.00', '0.00', '2 BIG CHEDDAR + 1 PORÃ‡AO DE BATATA FRITA + 1 COCA 1,5L + 2 COOKIES MEIO AMARGO', '100', '16163414403205.jpg', 'off.jpg', 0, 'G', 1),
(389, 41, 'Bacon Crispy', '20.00', '0.00', 'PÃƒO DO ESPECIAL, CARNE ARTESANAL 140G, BACON, MOLHO BARBECUE, MUÃ‡ARELA, MAIONESE DE BACON E CEBOLA CRISPY.', '101', '16163415642182.jpg', 'off.jpg', 0, 'G', 1),
(390, 41, 'X-Burger Artesanal', '16.00', '0.00', 'PÃƒO DO ESPECIAL, CARNE ARTESANAL 140G,MUÃ‡ARELA,ALFACE ,TOMATE', '101', '16163416451685.jpg', 'off.jpg', 0, 'G', 1),
(391, 41, 'X-Especial da Casa', '19.00', '0.00', 'PÃƒO, CARNE DE HAMBÃšRGUER TRADICIONAL, MUÃ‡ARELA, 2 OVOS, BACON, BANANA, TOMATE, ALFACE, MILHO E BATATA PALHA.', '101', '16163417321837.jpg', 'off.jpg', 0, 'G', 1),
(392, 41, 'Frango com Catupiry', '0.00', '0.00', 'PIZZA COM MASSA ESPECIAL DA CASA, FRANGO DE ALTA QUALIDADE E CATUPIRY ORIGINAL, PRESERVANDO O SABOR.', '103', '16163419564476.jpg', 'off.jpg', 0, 'G', 1),
(393, 41, 'Coca Cola 1,5l', '9.00', '0.00', 'Deliciosa Coca Cola 1,5l', '105', '16163422351512.jpg', 'off.jpg', 0, 'G', 1),
(394, 41, 'Sprite 350ml', '5.00', '0.00', 'Deliciosa Sprite 350ml', '105', '16163422751396.jpg', 'off.jpg', 0, 'G', 1),
(395, 41, 'Bolo de Pote', '9.00', '0.00', 'BOLO DE CHOCOLATE MOLHADINHO COM BRIGADEIRO CREMOSO E RASPAS DE CHOCOLATE MEIO AMARGO', '106', '16163423602539.jpg', 'off.jpg', 0, 'G', 1),
(396, 41, 'Bolo de pote', '9.00', '0.00', 'BOLO DE BAUNILHA COM CREME DE NUTELLA E NINHO EXTREMAMENTE CREMOSO E SABOROSO', '106', '16163424022997.jpg', 'off.jpg', 0, 'G', 1),
(398, 44, 'Água - Mineral', '5.00', '0.00', 'N', '108', 'off.jpg', 'off.jpg', 0, 'G', 1),
(400, 44, 'Massas', '0.00', '0.00', 'N', '109', 'off.jpg', 'off.jpg', 0, 'G', 1),
(401, 44, 'Hambugue', '0.00', '0.00', 'N', '110', 'off.jpg', 'off.jpg', 0, 'G', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `registrospagamentos`
--

CREATE TABLE `registrospagamentos` (
  `id` int(11) NOT NULL,
  `idu` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `nome` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `idpedido` varchar(70) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `tipo` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `dados_pagamentos` varchar(1000) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `mesa_registrada` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `data_registro` datetime NOT NULL,
  `vsubtotal` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `vtotal` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `valor_dinheiro` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `valor_troco` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `formapaga` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Extraindo dados da tabela `registrospagamentos`
--

INSERT INTO `registrospagamentos` (`id`, `idu`, `nome`, `idpedido`, `status`, `tipo`, `dados_pagamentos`, `mesa_registrada`, `data_registro`, `vsubtotal`, `vtotal`, `valor_dinheiro`, `valor_troco`, `formapaga`) VALUES
(8, '44', 'CLIENTE CONSUMIDOR', '172613', '5', 'MESA', '{\n    \"tipo\": \"a vista\",\n    \"dados\": [\n        {\n            \"metodo\": \"Pix\",\n            \"quantidade\": 10\n        }\n    ]\n}', '2', '2024-08-19 23:49:44', '10', '10', '0', '0', 'a vista'),
(9, '44', 'DANIEL ESTEVAO MARTINS MENDES', '188349PW', '5', 'DELIVERY', '{\n    \"tipo\": \"a vista\",\n    \"dados\": [\n        {\n            \"metodo\": \"Cartão\",\n            \"quantidade\": 10\n        }\n    ]\n}', '0', '2024-08-25 17:38:46', '10', '10', '0', '0', 'a vista'),
(10, '44', 'DANIEL ESTEVAO MARTINS MENDES', '151372', '5', 'BALCÃO', '{\n    \"tipo\": \"a vista\",\n    \"dados\": [\n        {\n            \"metodo\": \"Cartão\",\n            \"quantidade\": 25\n        }\n    ]\n}', '0', '2024-08-28 23:08:18', '25', '25', '0', '0', 'a vista');

-- --------------------------------------------------------

--
-- Estrutura da tabela `store`
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
  `pedido_entregue` enum('sim','nao') NOT NULL DEFAULT 'nao',
  `referencia` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `store_o`
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
  `meioameio` varchar(2) NOT NULL DEFAULT '0',
  `pedido_entregue` enum('sim','nao') NOT NULL DEFAULT 'nao',
  `id_referencia` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tamanhos`
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
-- Extraindo dados da tabela `tamanhos`
--

INSERT INTO `tamanhos` (`id`, `idu`, `idp`, `descricao`, `valor`, `status`) VALUES
(64, 41, 387, 'PorÃ§Ã£o Pequena', '12.00', 1),
(65, 41, 387, 'PorÃ§Ã£o MÃ©dia', '18.00', 1),
(66, 41, 387, 'PorÃ§Ã£o Grande', '22.00', 1),
(67, 41, 392, 'Brotinho', '14.00', 1),
(68, 41, 392, 'Grande', '22.00', 1),
(69, 41, 392, 'FamÃ­lia', '32.00', 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `adm`
--
ALTER TABLE `adm`
  ADD PRIMARY KEY (`Id`);

--
-- Índices para tabela `bairros`
--
ALTER TABLE `bairros`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `comissao`
--
ALTER TABLE `comissao`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `efeitossonoros`
--
ALTER TABLE `efeitossonoros`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `fundotopo`
--
ALTER TABLE `fundotopo`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`Id`);

--
-- Índices para tabela `limite_op`
--
ALTER TABLE `limite_op`
  ADD PRIMARY KEY (`Id`);

--
-- Índices para tabela `logo`
--
ALTER TABLE `logo`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `metodospagamentos`
--
ALTER TABLE `metodospagamentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `motivo_cancelamento`
--
ALTER TABLE `motivo_cancelamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `motoboy`
--
ALTER TABLE `motoboy`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `opcionais`
--
ALTER TABLE `opcionais`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `registrospagamentos`
--
ALTER TABLE `registrospagamentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `store`
--
ALTER TABLE `store`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `store_o`
--
ALTER TABLE `store_o`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tamanhos`
--
ALTER TABLE `tamanhos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT de tabela `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT de tabela `comissao`
--
ALTER TABLE `comissao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de tabela `efeitossonoros`
--
ALTER TABLE `efeitossonoros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `fundotopo`
--
ALTER TABLE `fundotopo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de tabela `grupos`
--
ALTER TABLE `grupos`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT de tabela `limite_op`
--
ALTER TABLE `limite_op`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT de tabela `logo`
--
ALTER TABLE `logo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `metodospagamentos`
--
ALTER TABLE `metodospagamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `motivo_cancelamento`
--
ALTER TABLE `motivo_cancelamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `motoboy`
--
ALTER TABLE `motoboy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `opcionais`
--
ALTER TABLE `opcionais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=396;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=485;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=402;

--
-- AUTO_INCREMENT de tabela `registrospagamentos`
--
ALTER TABLE `registrospagamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `store`
--
ALTER TABLE `store`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de tabela `store_o`
--
ALTER TABLE `store_o`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1561;

--
-- AUTO_INCREMENT de tabela `tamanhos`
--
ALTER TABLE `tamanhos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
