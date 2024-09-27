/*
 Navicat Premium Data Transfer

 Source Server         : local
 Source Server Type    : MySQL
 Source Server Version : 80200 (8.2.0)
 Source Host           : localhost:3306
 Source Schema         : terminos_y_condiciones

 Target Server Type    : MySQL
 Target Server Version : 80200 (8.2.0)
 File Encoding         : 65001

 Date: 27/09/2024 17:16:15
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for empresa
-- ----------------------------
DROP TABLE IF EXISTS `empresa`;
CREATE TABLE `empresa`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `link` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_spanish2_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of empresa
-- ----------------------------
INSERT INTO `empresa` VALUES (1, 'Vida', 'vida.com.uy/TOS/');
INSERT INTO `empresa` VALUES (2, 'Acompanar', 'acompaniar.com.uy/TOS/');
INSERT INTO `empresa` VALUES (3, 'Inspira', 'inspiracolonia.com.uy/TOS/');

-- ----------------------------
-- Table structure for empresa_servicio_tos
-- ----------------------------
DROP TABLE IF EXISTS `empresa_servicio_tos`;
CREATE TABLE `empresa_servicio_tos`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_empresa` int NOT NULL,
  `id_servicio` int NOT NULL,
  `id_tos` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_servicio`(`id_servicio` ASC) USING BTREE,
  INDEX `id_tos`(`id_tos` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 50 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of empresa_servicio_tos
-- ----------------------------
INSERT INTO `empresa_servicio_tos` VALUES (1, 1, 29, 7);
INSERT INTO `empresa_servicio_tos` VALUES (2, 1, 45, 3);
INSERT INTO `empresa_servicio_tos` VALUES (3, 1, 47, 4);
INSERT INTO `empresa_servicio_tos` VALUES (4, 1, 39, 39);
INSERT INTO `empresa_servicio_tos` VALUES (5, 1, 63, 5);
INSERT INTO `empresa_servicio_tos` VALUES (6, 1, 59, 9);
INSERT INTO `empresa_servicio_tos` VALUES (7, 1, 4, 6);
INSERT INTO `empresa_servicio_tos` VALUES (8, 1, 58, 11);
INSERT INTO `empresa_servicio_tos` VALUES (9, 2, 46, 1);
INSERT INTO `empresa_servicio_tos` VALUES (10, 2, 52, 2);
INSERT INTO `empresa_servicio_tos` VALUES (11, 2, 61, 3);
INSERT INTO `empresa_servicio_tos` VALUES (12, 2, 62, 3);
INSERT INTO `empresa_servicio_tos` VALUES (13, 3, 65, 1);
INSERT INTO `empresa_servicio_tos` VALUES (14, 3, 66, 1);
INSERT INTO `empresa_servicio_tos` VALUES (15, 1, 64, 5);
INSERT INTO `empresa_servicio_tos` VALUES (16, 1, 65, 5);
INSERT INTO `empresa_servicio_tos` VALUES (17, 1, 66, 5);
INSERT INTO `empresa_servicio_tos` VALUES (18, 1, 10, 17);
INSERT INTO `empresa_servicio_tos` VALUES (19, 1, 12, 18);
INSERT INTO `empresa_servicio_tos` VALUES (20, 1, 18, 19);
INSERT INTO `empresa_servicio_tos` VALUES (21, 1, 19, 20);
INSERT INTO `empresa_servicio_tos` VALUES (22, 1, 28, 21);
INSERT INTO `empresa_servicio_tos` VALUES (23, 1, 34, 22);
INSERT INTO `empresa_servicio_tos` VALUES (24, 1, 35, 23);
INSERT INTO `empresa_servicio_tos` VALUES (25, 1, 36, 24);
INSERT INTO `empresa_servicio_tos` VALUES (26, 1, 41, 25);
INSERT INTO `empresa_servicio_tos` VALUES (27, 1, 51, 8);
INSERT INTO `empresa_servicio_tos` VALUES (28, 1, 55, 26);
INSERT INTO `empresa_servicio_tos` VALUES (29, 1, 56, 27);
INSERT INTO `empresa_servicio_tos` VALUES (30, 1, 5, 28);
INSERT INTO `empresa_servicio_tos` VALUES (31, 1, 24, 29);
INSERT INTO `empresa_servicio_tos` VALUES (32, 1, 30, 30);
INSERT INTO `empresa_servicio_tos` VALUES (33, 1, 67, 31);
INSERT INTO `empresa_servicio_tos` VALUES (34, 1, 68, 31);
INSERT INTO `empresa_servicio_tos` VALUES (35, 1, 69, 31);
INSERT INTO `empresa_servicio_tos` VALUES (36, 1, 70, 32);
INSERT INTO `empresa_servicio_tos` VALUES (37, 1, 76, 33);
INSERT INTO `empresa_servicio_tos` VALUES (38, 1, 80, 34);
INSERT INTO `empresa_servicio_tos` VALUES (39, 1, 81, 35);
INSERT INTO `empresa_servicio_tos` VALUES (40, 1, 85, 36);
INSERT INTO `empresa_servicio_tos` VALUES (41, 1, 86, 37);
INSERT INTO `empresa_servicio_tos` VALUES (42, 1, 87, 37);
INSERT INTO `empresa_servicio_tos` VALUES (43, 1, 88, 37);
INSERT INTO `empresa_servicio_tos` VALUES (44, 1, 103, 38);
INSERT INTO `empresa_servicio_tos` VALUES (45, 1, 22, 40);
INSERT INTO `empresa_servicio_tos` VALUES (46, 1, 23, 41);
INSERT INTO `empresa_servicio_tos` VALUES (47, 1, 111, 42);
INSERT INTO `empresa_servicio_tos` VALUES (48, 1, 113, 42);
INSERT INTO `empresa_servicio_tos` VALUES (49, 1, 112, 43);

-- ----------------------------
-- Table structure for tos
-- ----------------------------
DROP TABLE IF EXISTS `tos`;
CREATE TABLE `tos`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_empresa` int NOT NULL,
  `identificador` varchar(4) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nombre_tos` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_empresa`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 44 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_spanish2_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of tos
-- ----------------------------
INSERT INTO `tos` VALUES (1, 1, '1', 'Promo Viajero - VIDA');
INSERT INTO `tos` VALUES (2, 1, '2', 'Amparo Plus - VIDA');
INSERT INTO `tos` VALUES (3, 1, '3', 'ATSU - VIDA');
INSERT INTO `tos` VALUES (4, 1, '4', 'COMAG - VIDA');
INSERT INTO `tos` VALUES (5, 1, '5', 'Grupo Familiar - VIDA');
INSERT INTO `tos` VALUES (6, 1, '6', 'Hotel - VIDA');
INSERT INTO `tos` VALUES (7, 1, '7', 'Producto Viajero - VIDA');
INSERT INTO `tos` VALUES (8, 1, '8', 'Promo 100 - VIDA');
INSERT INTO `tos` VALUES (9, 1, '9', 'Promo Prevencion2 -  VIDA');
INSERT INTO `tos` VALUES (10, 1, '10', 'Vida Assist Express - VIDA');
INSERT INTO `tos` VALUES (11, 1, '11', 'Vida Assist Express 2 - VIDA');
INSERT INTO `tos` VALUES (12, 2, '1', 'Competencia - INSPIRA');
INSERT INTO `tos` VALUES (13, 2, '2', 'Funcionario Publico - INSPIRA');
INSERT INTO `tos` VALUES (14, 2, '3', 'Grupo Familiar - INSPIRA');
INSERT INTO `tos` VALUES (15, 3, '1', 'Grupo Familiar - ACOMPANAR');
INSERT INTO `tos` VALUES (16, 3, '2', 'Promo 100 - ACOMPANAR');
INSERT INTO `tos` VALUES (17, 1, '12', 'Promo 500 - VIDA');
INSERT INTO `tos` VALUES (18, 1, '13', 'Vida Especial - VIDA');
INSERT INTO `tos` VALUES (19, 1, '14', 'Antel Sanatorio 8hs - VIDA');
INSERT INTO `tos` VALUES (20, 1, '15', 'Antel Sanatorio 16hs - VIDA');
INSERT INTO `tos` VALUES (21, 1, '16', 'Anio Free - VIDA');
INSERT INTO `tos` VALUES (22, 1, '17', 'Seguro Hogar (Sura) - VIDA');
INSERT INTO `tos` VALUES (23, 1, '18', 'Proteccion Via Publica (Sura) - VIDA');
INSERT INTO `tos` VALUES (24, 1, '19', 'Seguro de Vida Accidente (Sura) - VIDA');
INSERT INTO `tos` VALUES (25, 1, '20', 'Vida Assist Plus - VIDA');
INSERT INTO `tos` VALUES (26, 1, '21', 'Vida Express (Desafio) - VIDA');
INSERT INTO `tos` VALUES (27, 1, '22', 'Vida Plus (Comett) - VIDA');
INSERT INTO `tos` VALUES (28, 1, '23', 'Convalecencia Plus');
INSERT INTO `tos` VALUES (29, 1, '24', 'Anexo Emergencial');
INSERT INTO `tos` VALUES (30, 1, '25', 'Teleasistencia Centel');
INSERT INTO `tos` VALUES (31, 1, '26', 'Abitab 8 16 24');
INSERT INTO `tos` VALUES (32, 1, '27', 'OMT socio adicional');
INSERT INTO `tos` VALUES (33, 1, '28', 'Cremacion');
INSERT INTO `tos` VALUES (34, 1, '29', 'Amparo');
INSERT INTO `tos` VALUES (35, 1, '30', 'Vidaalert');
INSERT INTO `tos` VALUES (36, 1, '31', 'Udemm full emergencia');
INSERT INTO `tos` VALUES (37, 1, '32', 'Udemm rural');
INSERT INTO `tos` VALUES (38, 1, '33', 'Ucem plus vida');
INSERT INTO `tos` VALUES (39, 1, '34', 'COMAG AMPLIACION');
INSERT INTO `tos` VALUES (40, 1, '35', 'Grupo Familiar Viejo');
INSERT INTO `tos` VALUES (41, 1, '36', 'Premium');
INSERT INTO `tos` VALUES (42, 1, '37', 'Aldeas infantiles');
INSERT INTO `tos` VALUES (43, 1, '38', 'Sanatorio express 2 (igual a serv 58)');

-- ----------------------------
-- View structure for v_nexo
-- ----------------------------
DROP VIEW IF EXISTS `v_nexo`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `v_nexo` AS select `e`.`id` AS `id_empresa`,`e`.`empresa` AS `empresa`,`e`.`link` AS `link`,`t`.`id` AS `id_tos`,`t`.`identificador` AS `identificador`,`t`.`nombre_tos` AS `nombre_tos`,`nexo`.`id_servicio` AS `id_servicio` from ((`empresa_servicio_tos` `nexo` join `empresa` `e` on((`nexo`.`id_empresa` = `e`.`id`))) join `tos` `t` on((`nexo`.`id_tos` = `t`.`id`)));

SET FOREIGN_KEY_CHECKS = 1;
