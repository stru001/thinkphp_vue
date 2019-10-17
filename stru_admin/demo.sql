/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : demo

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-10-17 17:24:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_name` char(50) NOT NULL,
  `permission_slug` char(50) NOT NULL,
  `permission_desc` varchar(255) DEFAULT NULL,
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  `parent_id` int(11) NOT NULL,
  `icon` char(50) NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `index` char(10) NOT NULL COMMENT '//排序用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of permissions
-- ----------------------------
INSERT INTO `permissions` VALUES ('1', '后台首页', 'admin_index', '后台首页', '2019-10-14 10:52:55', '2019-10-14 10:52:58', '0', 'stru-iconiconindexnor', '/admin/index', '1');
INSERT INTO `permissions` VALUES ('2', '系统管理', 'admin_manage', '后台系统管理', '2019-10-14 10:53:26', '2019-10-14 10:53:29', '0', 'stru-iconmenu', '/admin/user', '99');
INSERT INTO `permissions` VALUES ('3', '用户管理', 'admin_user', '后台用户管理', '2019-10-14 10:53:48', '2019-10-14 10:53:54', '2', '', '/admin/user', '99-1');
INSERT INTO `permissions` VALUES ('4', '角色管理', 'admin_role', '后台角色管理', '2019-10-14 10:54:11', '2019-10-14 10:54:15', '2', '', '/admin/role', '99-2');
INSERT INTO `permissions` VALUES ('5', '权限管理', 'admin_permission', '后台权限管理', '2019-10-14 10:54:32', '2019-10-14 10:54:35', '2', '', '/admin/permission', '99-3');

-- ----------------------------
-- Table structure for permission_role
-- ----------------------------
DROP TABLE IF EXISTS `permission_role`;
CREATE TABLE `permission_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of permission_role
-- ----------------------------
INSERT INTO `permission_role` VALUES ('1', '1', '1', '2019-10-14 13:05:32', '2019-10-14 13:05:36');
INSERT INTO `permission_role` VALUES ('2', '2', '1', '2019-10-14 13:06:03', '2019-10-14 13:06:15');
INSERT INTO `permission_role` VALUES ('3', '3', '1', '2019-10-14 13:06:06', '2019-10-14 13:06:18');
INSERT INTO `permission_role` VALUES ('4', '4', '1', '2019-10-14 13:06:08', '2019-10-14 13:06:20');
INSERT INTO `permission_role` VALUES ('5', '5', '1', '2019-10-14 13:06:12', '2019-10-14 13:06:23');
INSERT INTO `permission_role` VALUES ('7', '1', '2', '2019-10-16 18:05:21', '2019-10-16 18:05:21');

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` char(50) NOT NULL,
  `role_slug` char(50) NOT NULL COMMENT '//角色标识',
  `role_desc` varchar(255) DEFAULT NULL,
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES ('1', '超级管理员', 'admin', '超级管理员', '2019-10-14 13:05:19', '2019-10-14 13:05:23');
INSERT INTO `roles` VALUES ('2', '普通用户', 'user', '普通用户', '2019-10-14 13:24:09', '2019-10-14 13:24:13');

-- ----------------------------
-- Table structure for role_user
-- ----------------------------
DROP TABLE IF EXISTS `role_user`;
CREATE TABLE `role_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `create_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of role_user
-- ----------------------------
INSERT INTO `role_user` VALUES ('1', '1', '1', '2019-10-14 13:06:54', '2019-10-14 13:06:57');
INSERT INTO `role_user` VALUES ('2', '2', '2', '2019-10-14 13:24:42', '2019-10-14 13:24:45');
INSERT INTO `role_user` VALUES ('3', '1', '3', '2019-10-15 12:10:34', '2019-10-15 12:10:34');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account` char(50) NOT NULL,
  `password` char(32) NOT NULL,
  `create_at` datetime DEFAULT NULL,
  `last_login_time` datetime DEFAULT NULL,
  `wx_id` varchar(255) DEFAULT NULL,
  `qq_id` varchar(255) DEFAULT NULL,
  `wb_id` varchar(255) DEFAULT NULL,
  `phone` char(11) DEFAULT NULL,
  `salt` char(20) DEFAULT NULL,
  `is_use` tinyint(1) DEFAULT NULL COMMENT '//启用禁用，0：禁用，1：启用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'admin', '417c043ca274f948e3d31bc11de37f86', '2019-10-14 17:35:25', '2019-10-17 16:52:34', null, null, null, '17600258121', '7a57a5a743894a0e', '1');
INSERT INTO `users` VALUES ('2', 'stru', 'c6c3980ccf6d32634806d9c5dc3aa2ba', '2019-10-14 17:35:27', '2019-10-17 16:39:32', null, null, null, '17600258121', '7f0049780e6d7fe0', '1');
INSERT INTO `users` VALUES ('3', 'ggle', 'e8dab116c4f2089b3d17e69c7c31d0a2', '2019-10-15 12:10:33', '2019-10-15 13:51:28', null, null, null, '17600258905', 'cccd32d1fdbf1729', '1');
