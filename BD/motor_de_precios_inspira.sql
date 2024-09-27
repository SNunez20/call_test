/*
 Navicat Premium Data Transfer

 Source Server         : local
 Source Server Type    : MySQL
 Source Server Version : 80200 (8.2.0)
 Source Host           : localhost:3306
 Source Schema         : motor_de_precios_inspira

 Target Server Type    : MySQL
 Target Server Version : 80200 (8.2.0)
 File Encoding         : 65001

 Date: 27/09/2024 17:15:49
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for bancos_emisores
-- ----------------------------
DROP TABLE IF EXISTS `bancos_emisores`;
CREATE TABLE `bancos_emisores`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `banco` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mostrar` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of bancos_emisores
-- ----------------------------
INSERT INTO `bancos_emisores` VALUES (1, 'BANCO DE LA REPUBLICA', '1');
INSERT INTO `bancos_emisores` VALUES (2, 'BANCO HIPOTECARIO DEL URUGUAY', '1');
INSERT INTO `bancos_emisores` VALUES (3, 'BANCO BANDES URUGUAY', '1');
INSERT INTO `bancos_emisores` VALUES (4, 'BANCO BILBAO VIZCAYA ARGENTARIA URUGUAY S.A', '1');
INSERT INTO `bancos_emisores` VALUES (5, 'BANCO DE LA NACION ARGENTINA (Sucursal Uruguay)', '1');
INSERT INTO `bancos_emisores` VALUES (6, 'BANCO ITAU URUGUAY S.A', '1');
INSERT INTO `bancos_emisores` VALUES (7, 'BANCO SANTANDER S.A.', '1');
INSERT INTO `bancos_emisores` VALUES (8, 'BANQUE HERITAGE (URUGUAY) S.A.', '1');
INSERT INTO `bancos_emisores` VALUES (9, 'CITIBANK N.A. (SUCURSAL URUGUAY)', '1');
INSERT INTO `bancos_emisores` VALUES (10, 'HSBC BANK (URUGUAY) S.A', '1');
INSERT INTO `bancos_emisores` VALUES (11, 'SCOTIABANK URUGUAY S.A.', '1');
INSERT INTO `bancos_emisores` VALUES (12, 'BBVA', '1');
INSERT INTO `bancos_emisores` VALUES (14, 'FUCAC', '1');
INSERT INTO `bancos_emisores` VALUES (15, 'SIN BANCO EMISOR', '1');
INSERT INTO `bancos_emisores` VALUES (16, 'CABAL VALOR', '1');
INSERT INTO `bancos_emisores` VALUES (17, 'CABAL BANDES', '1');
INSERT INTO `bancos_emisores` VALUES (18, 'CABAL FUSEREP', '1');
INSERT INTO `bancos_emisores` VALUES (19, 'CABAL CREDICOMPRAS', '1');
INSERT INTO `bancos_emisores` VALUES (20, 'CABAL VIACARD', '1');
INSERT INTO `bancos_emisores` VALUES (21, 'CABAL NATIVA', '1');
INSERT INTO `bancos_emisores` VALUES (22, 'CABAL COPAC', '1');
INSERT INTO `bancos_emisores` VALUES (23, 'CABAL VIA CONFORT', '1');
INSERT INTO `bancos_emisores` VALUES (24, 'CABAL CREDITO DE LA CASA', '1');
INSERT INTO `bancos_emisores` VALUES (25, 'CABAL RETOP', '1');
INSERT INTO `bancos_emisores` VALUES (26, 'CABAL CREDI YI', '1');

-- ----------------------------
-- Table structure for convenios
-- ----------------------------
DROP TABLE IF EXISTS `convenios`;
CREATE TABLE `convenios`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `sucursal_cobranzas` int NULL DEFAULT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `activo` int NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 5 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of convenios
-- ----------------------------
INSERT INTO `convenios` VALUES (1, 1373, 'AJUPECS', 1);
INSERT INTO `convenios` VALUES (2, 1374, 'SOJUPEN', 1);
INSERT INTO `convenios` VALUES (3, 1375, 'ADEOM', 1);
INSERT INTO `convenios` VALUES (4, 1376, 'SOJUROS', 1);

-- ----------------------------
-- Table structure for lista_de_precios
-- ----------------------------
DROP TABLE IF EXISTS `lista_de_precios`;
CREATE TABLE `lista_de_precios`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_servicio` int NULL DEFAULT NULL,
  `horas` int NULL DEFAULT NULL,
  `edad_desde` int NULL DEFAULT NULL,
  `edad_hasta` int NULL DEFAULT NULL,
  `precio` int NULL DEFAULT NULL,
  `activo` int NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 41 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = FIXED;

-- ----------------------------
-- Records of lista_de_precios
-- ----------------------------
INSERT INTO `lista_de_precios` VALUES (1, 1, 8, 0, 35, 560, 1);
INSERT INTO `lista_de_precios` VALUES (2, 1, 8, 36, 65, 715, 1);
INSERT INTO `lista_de_precios` VALUES (3, 1, 8, 66, NULL, 935, 1);
INSERT INTO `lista_de_precios` VALUES (4, 1, 16, 0, 35, 1120, 1);
INSERT INTO `lista_de_precios` VALUES (5, 1, 16, 36, 65, 1430, 1);
INSERT INTO `lista_de_precios` VALUES (6, 1, 16, 66, NULL, 1870, 1);
INSERT INTO `lista_de_precios` VALUES (7, 1, 24, 0, 35, 1680, 1);
INSERT INTO `lista_de_precios` VALUES (8, 1, 24, 36, 65, 2145, 1);
INSERT INTO `lista_de_precios` VALUES (9, 1, 24, 66, NULL, 2805, 1);
INSERT INTO `lista_de_precios` VALUES (10, 2, 8, 0, 35, 560, 1);
INSERT INTO `lista_de_precios` VALUES (11, 2, 8, 36, 65, 715, 1);
INSERT INTO `lista_de_precios` VALUES (12, 2, 8, 66, NULL, 935, 1);
INSERT INTO `lista_de_precios` VALUES (13, 2, 16, 0, 35, 1120, 1);
INSERT INTO `lista_de_precios` VALUES (14, 2, 16, 36, 65, 1430, 1);
INSERT INTO `lista_de_precios` VALUES (15, 2, 16, 66, NULL, 1870, 1);
INSERT INTO `lista_de_precios` VALUES (16, 2, 24, 0, 35, 1680, 1);
INSERT INTO `lista_de_precios` VALUES (17, 2, 24, 36, 65, 2145, 1);
INSERT INTO `lista_de_precios` VALUES (18, 2, 24, 66, NULL, 2805, 1);
INSERT INTO `lista_de_precios` VALUES (19, 4, 8, NULL, NULL, 115, 1);
INSERT INTO `lista_de_precios` VALUES (20, 5, 8, NULL, NULL, 280, 1);
INSERT INTO `lista_de_precios` VALUES (21, 6, 8, NULL, NULL, 605, 1);
INSERT INTO `lista_de_precios` VALUES (22, 8, 8, NULL, NULL, 530, 1);
INSERT INTO `lista_de_precios` VALUES (23, 8, 16, NULL, NULL, 1060, 1);
INSERT INTO `lista_de_precios` VALUES (24, 8, 24, NULL, NULL, 1590, 1);
INSERT INTO `lista_de_precios` VALUES (25, 9, 8, NULL, NULL, 530, 1);
INSERT INTO `lista_de_precios` VALUES (26, 9, 16, NULL, NULL, 1060, 1);
INSERT INTO `lista_de_precios` VALUES (27, 9, 24, NULL, NULL, 1590, 1);
INSERT INTO `lista_de_precios` VALUES (28, 10, 8, NULL, NULL, 250, 1);
INSERT INTO `lista_de_precios` VALUES (29, 11, 8, NULL, NULL, 390, 1);
INSERT INTO `lista_de_precios` VALUES (30, 12, 8, NULL, NULL, 325, 1);
INSERT INTO `lista_de_precios` VALUES (31, 13, 8, NULL, NULL, 1210, 1);
INSERT INTO `lista_de_precios` VALUES (32, 13, 16, NULL, NULL, 2195, 1);
INSERT INTO `lista_de_precios` VALUES (33, 13, 24, NULL, NULL, 2945, 1);
INSERT INTO `lista_de_precios` VALUES (34, 14, NULL, NULL, NULL, 0, 0);
INSERT INTO `lista_de_precios` VALUES (35, 15, 8, NULL, NULL, 1730, 1);
INSERT INTO `lista_de_precios` VALUES (36, 15, 16, NULL, NULL, 3140, 1);
INSERT INTO `lista_de_precios` VALUES (37, 15, 24, NULL, NULL, 4210, 1);
INSERT INTO `lista_de_precios` VALUES (38, 16, NULL, NULL, NULL, 0, 0);
INSERT INTO `lista_de_precios` VALUES (39, 18, 8, NULL, NULL, 425, 1);
INSERT INTO `lista_de_precios` VALUES (40, 19, 8, NULL, NULL, 600, 1);

-- ----------------------------
-- Table structure for localidades
-- ----------------------------
DROP TABLE IF EXISTS `localidades`;
CREATE TABLE `localidades`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `id_filial` int NULL DEFAULT NULL,
  `activo` int NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 192 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of localidades
-- ----------------------------
INSERT INTO `localidades` VALUES (167, 'Agraciada', 8, 1);
INSERT INTO `localidades` VALUES (169, 'Artilleros', 8, 1);
INSERT INTO `localidades` VALUES (170, 'Barker', 8, 1);
INSERT INTO `localidades` VALUES (171, 'Campana', 8, 1);
INSERT INTO `localidades` VALUES (172, 'Carmelo', 8, 1);
INSERT INTO `localidades` VALUES (173, 'Colonia', 8, 1);
INSERT INTO `localidades` VALUES (174, 'Colonia Valdense', 8, 1);
INSERT INTO `localidades` VALUES (175, 'Conchillas', 8, 1);
INSERT INTO `localidades` VALUES (176, 'Cufré', 8, 1);
INSERT INTO `localidades` VALUES (177, 'Florencio Sánchez', 8, 1);
INSERT INTO `localidades` VALUES (178, 'Juan Lacaze', 8, 1);
INSERT INTO `localidades` VALUES (179, 'La Estanzuela', 8, 1);
INSERT INTO `localidades` VALUES (180, 'La Paz', 8, 1);
INSERT INTO `localidades` VALUES (181, 'Los Pinos', 8, 1);
INSERT INTO `localidades` VALUES (182, 'Miguelete', 8, 1);
INSERT INTO `localidades` VALUES (183, 'Minuano', 8, 1);
INSERT INTO `localidades` VALUES (184, 'Nueva Helvecia', 8, 1);
INSERT INTO `localidades` VALUES (185, 'Nueva Palmira', 8, 1);
INSERT INTO `localidades` VALUES (186, 'Ombúes de Lavalle', 8, 1);
INSERT INTO `localidades` VALUES (187, 'Playa Fomento', 8, 1);
INSERT INTO `localidades` VALUES (188, 'Riachuelo', 8, 1);
INSERT INTO `localidades` VALUES (189, 'Rosario', 8, 1);
INSERT INTO `localidades` VALUES (190, 'Santa Ana', 8, 1);
INSERT INTO `localidades` VALUES (191, 'Tarariras', 8, 1);

-- ----------------------------
-- Table structure for log_errores
-- ----------------------------
DROP TABLE IF EXISTS `log_errores`;
CREATE TABLE `log_errores`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_archivo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `consulta` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `error` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `fecha_registro` datetime NULL DEFAULT NULL,
  `activo` int NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of log_errores
-- ----------------------------

-- ----------------------------
-- Table structure for metodos_de_pago
-- ----------------------------
DROP TABLE IF EXISTS `metodos_de_pago`;
CREATE TABLE `metodos_de_pago`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `radio` int NULL DEFAULT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `id_tipo_medios_pago` int NULL DEFAULT NULL,
  `activo` int NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 15 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of metodos_de_pago
-- ----------------------------
INSERT INTO `metodos_de_pago` VALUES (1, 293, 'ONAJPU', 3, 1);
INSERT INTO `metodos_de_pago` VALUES (2, 909, 'Promovida', 3, 0);
INSERT INTO `metodos_de_pago` VALUES (3, 1372, 'Inspira Cobrador Domiciliario', 1, 1);
INSERT INTO `metodos_de_pago` VALUES (4, 13721, 'MasterCard', 2, 1);
INSERT INTO `metodos_de_pago` VALUES (5, 13722, 'Visa', 2, 1);
INSERT INTO `metodos_de_pago` VALUES (6, 13723, 'Creditel', 2, 1);
INSERT INTO `metodos_de_pago` VALUES (7, 13724, 'Cabal', 2, 1);
INSERT INTO `metodos_de_pago` VALUES (8, 13725, 'Créditos Directos', 2, 1);
INSERT INTO `metodos_de_pago` VALUES (9, 13726, 'Pass Card', 2, 1);
INSERT INTO `metodos_de_pago` VALUES (10, 13727, 'Oca Card', 2, 1);
INSERT INTO `metodos_de_pago` VALUES (11, 13728, 'Costa de Oro', 2, 0);
INSERT INTO `metodos_de_pago` VALUES (12, 13729, 'Cobros Ya', 2, 1);
INSERT INTO `metodos_de_pago` VALUES (13, 13731, 'Red Pagos', 1, 1);

-- ----------------------------
-- Table structure for numeros
-- ----------------------------
DROP TABLE IF EXISTS `numeros`;
CREATE TABLE `numeros`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `grupo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `flag` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `no_contesta` int NULL DEFAULT NULL,
  `dep_localidad` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `activo` int NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of numeros
-- ----------------------------

-- ----------------------------
-- Table structure for numeros_servicios
-- ----------------------------
DROP TABLE IF EXISTS `numeros_servicios`;
CREATE TABLE `numeros_servicios`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_servicio` int NULL DEFAULT NULL,
  `numero_servicio` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `activo` int NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 23 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of numeros_servicios
-- ----------------------------
INSERT INTO `numeros_servicios` VALUES (1, 1, '01', 1);
INSERT INTO `numeros_servicios` VALUES (2, 2, '02', 1);
INSERT INTO `numeros_servicios` VALUES (3, 3, '03', 1);
INSERT INTO `numeros_servicios` VALUES (4, 4, '06', 1);
INSERT INTO `numeros_servicios` VALUES (5, 4, '08', 1);
INSERT INTO `numeros_servicios` VALUES (6, 5, '07', 1);
INSERT INTO `numeros_servicios` VALUES (7, 6, '09', 1);
INSERT INTO `numeros_servicios` VALUES (8, 6, '110', 1);
INSERT INTO `numeros_servicios` VALUES (9, 7, '16', 1);
INSERT INTO `numeros_servicios` VALUES (10, 7, '17', 1);
INSERT INTO `numeros_servicios` VALUES (11, 8, '37', 1);
INSERT INTO `numeros_servicios` VALUES (12, 9, '46', 1);
INSERT INTO `numeros_servicios` VALUES (13, 10, '52', 1);
INSERT INTO `numeros_servicios` VALUES (14, 11, '56', 1);
INSERT INTO `numeros_servicios` VALUES (15, 12, '59', 1);
INSERT INTO `numeros_servicios` VALUES (16, 13, '63', 1);
INSERT INTO `numeros_servicios` VALUES (17, 14, '64', 0);
INSERT INTO `numeros_servicios` VALUES (18, 15, '65', 1);
INSERT INTO `numeros_servicios` VALUES (19, 16, '66', 0);
INSERT INTO `numeros_servicios` VALUES (20, 17, '82', 1);
INSERT INTO `numeros_servicios` VALUES (21, 18, '106', 1);
INSERT INTO `numeros_servicios` VALUES (22, 19, '114', 1);

-- ----------------------------
-- Table structure for precios_promo_estaciones
-- ----------------------------
DROP TABLE IF EXISTS `precios_promo_estaciones`;
CREATE TABLE `precios_promo_estaciones`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `horas` int NULL DEFAULT NULL,
  `precio` int NULL DEFAULT NULL,
  `activo` int NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 4 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = FIXED;

-- ----------------------------
-- Records of precios_promo_estaciones
-- ----------------------------
INSERT INTO `precios_promo_estaciones` VALUES (1, 8, 530, 1);
INSERT INTO `precios_promo_estaciones` VALUES (2, 16, 1060, 1);
INSERT INTO `precios_promo_estaciones` VALUES (3, 24, 1590, 1);

-- ----------------------------
-- Table structure for promociones
-- ----------------------------
DROP TABLE IF EXISTS `promociones`;
CREATE TABLE `promociones`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero_promo` int NULL DEFAULT NULL,
  `nombre_promocion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `activo` int NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of promociones
-- ----------------------------
INSERT INTO `promociones` VALUES (1, 20, 'NP (2017)', 1);
INSERT INTO `promociones` VALUES (2, 22, 'PCI', 1);
INSERT INTO `promociones` VALUES (3, 73, 'PCI - Bienestar y Asistir', 1);

-- ----------------------------
-- Table structure for rutas_cobrador
-- ----------------------------
DROP TABLE IF EXISTS `rutas_cobrador`;
CREATE TABLE `rutas_cobrador`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `sucursal` varchar(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `radio` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `ruta` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `localidad` varchar(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `cobrador` varchar(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `emision` int NULL DEFAULT 1,
  `tipo_contrato` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `activo` int NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 49 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of rutas_cobrador
-- ----------------------------
INSERT INTO `rutas_cobrador` VALUES (1, '1372', '13721', '0000000000', 'MasterCard', 'MASTERCARD', 1, 'CENTRALIZADO', 1);
INSERT INTO `rutas_cobrador` VALUES (2, '1372', '13722', '0000000000', 'Visa', 'VISA', 1, 'CENTRALIZADO', 1);
INSERT INTO `rutas_cobrador` VALUES (3, '1372', '13723', '0000000000', 'Creditel', 'CREDITEL', 1, 'CENTRALIZADO', 1);
INSERT INTO `rutas_cobrador` VALUES (4, '1372', '13724', '0000000000', 'Cabal', 'CABAL', 1, 'CENTRALIZADO', 1);
INSERT INTO `rutas_cobrador` VALUES (5, '1372', '13725', '0000000000', 'Créditos Directos', 'CREDITOS DIRECTOS', 1, 'CENTRALIZADO', 1);
INSERT INTO `rutas_cobrador` VALUES (6, '1372', '13726', '0000000000', 'Pass Card', 'PASS CARD', 1, 'CENTRALIZADO', 1);
INSERT INTO `rutas_cobrador` VALUES (7, '1372', '13727', '0000000000', 'Oca Card', 'OCA CARD', 1, 'CENTRALIZADO', 1);
INSERT INTO `rutas_cobrador` VALUES (8, '1372', '1372', '0000000000', 'Colonia', 'FILIAL INSPIRA', 1, 'COBRANZA EMPLEADO', 1);
INSERT INTO `rutas_cobrador` VALUES (9, '1372', '1372', '00000000Z1', 'Colonia', 'DANIEL DURAN', 1, 'COBRANZA EMPLEADO', 1);
INSERT INTO `rutas_cobrador` VALUES (10, '1372', '1372', '00000000Z2', 'Colonia', 'JACINTO LEMOS', 1, 'COBRANZA EMPLEADO', 1);
INSERT INTO `rutas_cobrador` VALUES (11, '1372', '1372', '00000000MB', 'Ombúes de Lavalle', 'SEBASTIAN OLIVERA', 1, 'COBRANZA EMPLEADO', 1);
INSERT INTO `rutas_cobrador` VALUES (12, '1372', '1372', '00000000JL', 'Juan Lacaze', 'MARCELA PINTOS', 1, 'COBRANZA EMPLEADO', 1);
INSERT INTO `rutas_cobrador` VALUES (13, '1372', '1372', '00000000RO', 'Rosario', 'LUIS DE LEON', 1, 'COBRANZA EMPLEADO', 1);
INSERT INTO `rutas_cobrador` VALUES (14, '1372', '1372', '0000000CA2', 'Nueva Palmira', 'WALTER GONZALEZ', 1, 'COBRANZA EMPLEADO', 1);
INSERT INTO `rutas_cobrador` VALUES (15, '1372', '1372', '0000000RO2', 'Nueva Helvecia', 'LUIS DE LEON', 1, 'COBRANZA TERCERIZADA', 1);
INSERT INTO `rutas_cobrador` VALUES (16, '1372', '1372', '0000000RO3', 'Colonia Valdense', 'LUIS DE LEON', 1, 'COBRANZA TERCERIZADA', 1);
INSERT INTO `rutas_cobrador` VALUES (17, '1372', '1372', '0000000RO4', 'Miguelete', 'LUIS DE LEON', 1, 'COBRANZA TERCERIZADA', 1);
INSERT INTO `rutas_cobrador` VALUES (18, '1372', '1372', '00000000TA', 'Tarariras', 'CLAUDIA CABRERA', 1, 'COBRANZA TERCERIZADA', 1);
INSERT INTO `rutas_cobrador` VALUES (19, '1372', '1372', '0000000RO8', 'La Paz', 'LUIS DE LEON', 1, 'COBRANZA TERCERIZADA', 1);
INSERT INTO `rutas_cobrador` VALUES (20, '1372', '1372', '00000000LB', 'Libertad San José', '', 1, '', 0);
INSERT INTO `rutas_cobrador` VALUES (21, '1372', '1372', '00000000EP', 'Ecilda Paullier San José', '', 1, '', 0);
INSERT INTO `rutas_cobrador` VALUES (22, '1372', '1372', '0000000CA4', 'Conchillas', 'SEBASTIAN OLIVERA', 1, 'COBRANZA EMPLEADO', 1);
INSERT INTO `rutas_cobrador` VALUES (23, '1372', '1372', '0000000CA1', 'Carmelo', 'MARIA DE LOS ANGELES PERRETTI', 1, 'COBRANZA EMPLEADO', 1);
INSERT INTO `rutas_cobrador` VALUES (24, '1372', '1372', '000000SEMI      ', 'Semillero Tarariras', '', 1, '', 0);
INSERT INTO `rutas_cobrador` VALUES (25, '1372', '1372', '00000000ET', 'La Estanzuela', '', 1, '', 0);
INSERT INTO `rutas_cobrador` VALUES (26, '1372', '13728', '00000000BC', 'Barras de carrasco', 'DOMINGO PIREZ', 1, 'COBRANZA TERCERIZADA', 0);
INSERT INTO `rutas_cobrador` VALUES (27, '1372', '13728', '00000000PC', 'Paso Carrasco', 'DOMINGO PIREZ', 1, 'COBRANZA TERCERIZADA', 0);
INSERT INTO `rutas_cobrador` VALUES (28, '1372', '13728', '0000000ATL', 'Atlantida', 'DOMINGO PIREZ', 1, 'COBRANZA TERCERIZADA', 0);
INSERT INTO `rutas_cobrador` VALUES (29, '1372', '13728', '0000000FLO', 'Floresta', 'DOMINGO PIREZ', 1, 'COBRANZA TERCERIZADA', 0);
INSERT INTO `rutas_cobrador` VALUES (30, '1372', '13728', '0000000LAG', 'Lagomar', 'DOMINGO PIREZ', 1, 'COBRANZA TERCERIZADA', 0);
INSERT INTO `rutas_cobrador` VALUES (31, '1372', '13728', '0000000MAR', 'Marindia', 'DOMINGO PIREZ', 1, 'COBRANZA TERCERIZADA', 0);
INSERT INTO `rutas_cobrador` VALUES (32, '1372', '13728', '0000000NEP', 'Neptunia', 'DOMINGO PIREZ', 1, 'COBRANZA TERCERIZADA', 0);
INSERT INTO `rutas_cobrador` VALUES (33, '1372', '13728', '0000000PIN', 'Pinar', 'DOMINGO PIREZ', 1, 'COBRANZA TERCERIZADA', 0);
INSERT INTO `rutas_cobrador` VALUES (34, '1372', '13728', '0000000SAL', 'Salinas', 'DOMINGO PIREZ', 1, 'COBRANZA TERCERIZADA', 0);
INSERT INTO `rutas_cobrador` VALUES (35, '1372', '13728', '0000000SAN', 'San Luis', 'DOMINGO PIREZ', 1, 'COBRANZA TERCERIZADA', 0);
INSERT INTO `rutas_cobrador` VALUES (36, '1372', '13728', '0000000SHA', 'Shangrila', 'DOMINGO PIREZ', 1, 'COBRANZA TERCERIZADA', 0);
INSERT INTO `rutas_cobrador` VALUES (37, '1372', '13728', '0000000SJC', 'San José De Carrasco', 'DOMINGO PIREZ', 1, 'COBRANZA TERCERIZADA', 0);
INSERT INTO `rutas_cobrador` VALUES (38, '1372', '13728', '0000000SOL', 'Solimar', 'DOMINGO PIREZ', 1, 'COBRANZA TERCERIZADA', 0);
INSERT INTO `rutas_cobrador` VALUES (39, '1372', '13728', '000000PINA', 'Pinamar', 'DOMINGO PIREZ', 1, 'COBRANZA TERCERIZADA', 0);
INSERT INTO `rutas_cobrador` VALUES (40, '1372', '13728', '000000PQUE', 'Parque del plata', 'DOMINGO PIREZ', 1, 'COBRANZA TERCERIZADA', 0);
INSERT INTO `rutas_cobrador` VALUES (41, '1372', '1372', '0000000RO5', 'Cufré', 'LUIS DE LEON', 1, 'COBRANZA TERCERIZADA', 1);
INSERT INTO `rutas_cobrador` VALUES (42, '1372', '1372', '000AJUPECS', 'AJUPECS', 'CUALQUIER COBRADOR', 1, '', 1);
INSERT INTO `rutas_cobrador` VALUES (43, '1372', '1372', '0000000JL1', 'Juan Lacaze', 'GABRIELA ALVARIÑO', 1, 'COBRANZA EMPLEADO', 1);
INSERT INTO `rutas_cobrador` VALUES (44, '1372', '1372', '00000000CA', 'CARDONA', 'SERGIO ARABEITY', 1, 'COBRANZA TERCERIZADA', 0);
INSERT INTO `rutas_cobrador` VALUES (45, '1372', '1372', '0000000FJL', 'Juan Lacaze', 'FILIAL INSPIRA JUAN LACAZE', 1, 'COBRANZA EMPLEADO', 1);
INSERT INTO `rutas_cobrador` VALUES (46, '1372', '293', '0000000000', 'ONAJPU', 'ONAJPU', 1, 'CENTRALIZADO', 1);
INSERT INTO `rutas_cobrador` VALUES (47, '1372', '13729', '0000000000', 'Cobros Ya', 'Cobros Ya', 1, NULL, 1);
INSERT INTO `rutas_cobrador` VALUES (48, '1372', '13731', '0000000000', 'Red Pagos', 'Red Pagos', 1, NULL, 1);

-- ----------------------------
-- Table structure for servicio_promos
-- ----------------------------
DROP TABLE IF EXISTS `servicio_promos`;
CREATE TABLE `servicio_promos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_servicio` int NULL DEFAULT NULL,
  `id_promo` int NULL DEFAULT NULL,
  `activo` int NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 7 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = FIXED;

-- ----------------------------
-- Records of servicio_promos
-- ----------------------------
INSERT INTO `servicio_promos` VALUES (1, 1, 1, 1);
INSERT INTO `servicio_promos` VALUES (2, 1, 2, 1);
INSERT INTO `servicio_promos` VALUES (3, 8, 2, 1);
INSERT INTO `servicio_promos` VALUES (4, 9, 2, 1);

-- ----------------------------
-- Table structure for servicios
-- ----------------------------
DROP TABLE IF EXISTS `servicios`;
CREATE TABLE `servicios`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nro_servicio` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `nombre_servicio` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `horas_servicio` int NULL DEFAULT 1,
  `importe_manual` int NULL DEFAULT 0,
  `activo` int NULL DEFAULT 1,
  `mostrar` int NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 23 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of servicios
-- ----------------------------
INSERT INTO `servicios` VALUES (1, '01', 'Sanatorio', 1, 0, 1, 1);
INSERT INTO `servicios` VALUES (2, '02', 'Convalecencia', 1, 0, 1, 1);
INSERT INTO `servicios` VALUES (3, '03', 'Domicilio Especial', 1, 0, 0, 0);
INSERT INTO `servicios` VALUES (4, '06', 'Reintegro Opcional', 0, 0, 1, 1);
INSERT INTO `servicios` VALUES (5, '07', 'Reintegro Conjunto', 0, 0, 1, 1);
INSERT INTO `servicios` VALUES (6, '09', 'Inspira SATS', 0, 0, 1, 1);
INSERT INTO `servicios` VALUES (7, '16', 'SIMC (Sanatorio + Emergencia)', 1, 0, 0, 0);
INSERT INTO `servicios` VALUES (8, '37', 'Inspira Ajupecs y Sojupen / Adeom', 1, 0, 1, 1);
INSERT INTO `servicios` VALUES (9, '46', 'Inspira PCI (Promo Competencia)', 1, 0, 1, 1);
INSERT INTO `servicios` VALUES (10, '52', 'Inspira (Funcionarios publicos menos de 50)', 0, 0, 1, 1);
INSERT INTO `servicios` VALUES (11, '56', 'Inspira Plus', 0, 0, 1, 1);
INSERT INTO `servicios` VALUES (12, '59', 'Prevencion 2', 0, 0, 1, 1);
INSERT INTO `servicios` VALUES (13, '63', 'G5 - Grupo Familiar 1', 1, 0, 1, 1);
INSERT INTO `servicios` VALUES (14, '64', 'Grupo Familiar 1 - Beneficiario', 0, 0, 1, 0);
INSERT INTO `servicios` VALUES (15, '65', 'G6 - Grupo Familiar 2', 1, 0, 1, 1);
INSERT INTO `servicios` VALUES (16, '66', 'Grupo Familiar 2 - Beneficiario', 0, 0, 1, 0);
INSERT INTO `servicios` VALUES (17, '82', 'Mega Promo Bienestar', 0, 1, 1, 1);
INSERT INTO `servicios` VALUES (18, '106', 'Amparo Optativo', 0, 0, 1, 1);
INSERT INTO `servicios` VALUES (19, '114', 'GYM COCO', 0, 0, 1, 1);
INSERT INTO `servicios` VALUES (20, '08', 'Reintegro Opcional Doble', 1, 0, 1, 0);
INSERT INTO `servicios` VALUES (21, '110', 'Inspira SATS Doble', 1, 0, 1, 0);
INSERT INTO `servicios` VALUES (22, '17', 'SIMC (Sanatorio + Emergencia) Doble', 1, 0, 0, 0);

-- ----------------------------
-- Table structure for tipo_medios_pago
-- ----------------------------
DROP TABLE IF EXISTS `tipo_medios_pago`;
CREATE TABLE `tipo_medios_pago`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `nombre_comercial` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `mostrar` int NULL DEFAULT 1,
  `activo` int NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 4 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tipo_medios_pago
-- ----------------------------
INSERT INTO `tipo_medios_pago` VALUES (1, 'Domiciliario', 'Cobrador', 1, 1);
INSERT INTO `tipo_medios_pago` VALUES (2, 'Centralizado', 'Tarjeta', 1, 1);
INSERT INTO `tipo_medios_pago` VALUES (3, 'Convenio', 'Convenio', 1, 1);

SET FOREIGN_KEY_CHECKS = 1;
