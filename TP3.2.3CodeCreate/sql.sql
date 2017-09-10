-- MySQL dump 10.13  Distrib 5.6.28, for Win64 (x86_64)
--
-- Host: localhost    Database: code
-- ------------------------------------------------------
-- Server version	5.6.28

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `code_article`
--

DROP TABLE IF EXISTS `code_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `code_article` (
  `art_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `art_username` varchar(30) NOT NULL COMMENT '用户名',
  `art_password` char(32) NOT NULL COMMENT '密码',
  `art_title` varchar(30) NOT NULL DEFAULT '' COMMENT '文章标题',
  `art_money` decimal(10,0) unsigned NOT NULL COMMENT '价格',
  `art_url` varchar(100) NOT NULL COMMENT '链接地址',
  `art_email` varchar(100) NOT NULL COMMENT '邮箱地址',
  `art_pic_big` varchar(30) NOT NULL DEFAULT '' COMMENT '大图',
  `art_pic_middle` varchar(30) NOT NULL DEFAULT '' COMMENT '中图',
  `art_pic_small` varchar(30) NOT NULL DEFAULT '' COMMENT '小图',
  `art_file` varchar(200) NOT NULL DEFAULT '' COMMENT '文件上传',
  `art_content` text NOT NULL COMMENT '文章内容',
  `art_select` tinyint(3) unsigned NOT NULL COMMENT '多选一：性别(0-女,1-男)',
  `art_more` tinyint(3) unsigned DEFAULT NULL COMMENT '多选多(0-apple,1-雪梨,3-haha)',
  `art_more_checkbox` tinyint(3) unsigned DEFAULT NULL COMMENT 'checkbox(0-apple,1-xueli,2-xigua)',
  `art_time` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`art_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `code_article`
--

LOCK TABLES `code_article` WRITE;
/*!40000 ALTER TABLE `code_article` DISABLE KEYS */;
/*!40000 ALTER TABLE `code_article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `code_goods`
--

DROP TABLE IF EXISTS `code_goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `code_goods` (
  `goods_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `goods_name` varchar(120) NOT NULL COMMENT '商品名称',
  `goods_file` varchar(200) NOT NULL COMMENT '商品文件',
  `goods_price` decimal(10,0) unsigned NOT NULL DEFAULT '0' COMMENT '商品价格',
  `goods_desc` text NOT NULL COMMENT '商品描述',
  PRIMARY KEY (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `code_goods`
--

LOCK TABLES `code_goods` WRITE;
/*!40000 ALTER TABLE `code_goods` DISABLE KEYS */;
INSERT INTO `code_goods` VALUES (1,'商品名称','20170410/162928.ipa',1000,'描述');
/*!40000 ALTER TABLE `code_goods` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-04-15  9:48:24
