-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: qlyclb
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `clubapplication`
--

DROP TABLE IF EXISTS `clubapplication`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clubapplication` (
  `application_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `club_id` varchar(255) NOT NULL,
  `reason` text NOT NULL,
  `expectation` text NOT NULL,
  `skills` text NOT NULL,
  `desired_band` varchar(255) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `reviewed_by` varchar(255) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `reject_reason` text DEFAULT NULL,
  PRIMARY KEY (`application_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clubapplication`
--

LOCK TABLES `clubapplication` WRITE;
/*!40000 ALTER TABLE `clubapplication` DISABLE KEYS */;
INSERT INTO `clubapplication` VALUES (4,'hbinz','CLB001','aa','aa','a','DES','approved','2026-09-05 22:12:16','hbintramkem','2026-09-05 22:12:37',NULL),(5,'duonggg','CLB001','a','a','a','AI','approved','2026-09-07 09:55:34','hbintramkem','2026-09-07 09:56:41',NULL),(6,'lan','CLB001','a','a','a','WEB','approved','2026-09-07 09:56:20','hbintramkem','2026-09-07 09:56:38',NULL),(7,'hbintest','CLB001','a','a','a','DES','approved','2026-09-07 10:05:01','hbintramkem','2026-09-07 10:05:19',NULL);
/*!40000 ALTER TABLE `clubapplication` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clubband`
--

DROP TABLE IF EXISTS `clubband`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clubband` (
  `club_id` varchar(255) NOT NULL,
  `band_id` varchar(100) DEFAULT NULL,
  `band_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clubband`
--

LOCK TABLES `clubband` WRITE;
/*!40000 ALTER TABLE `clubband` DISABLE KEYS */;
INSERT INTO `clubband` VALUES ('CLB001','AI','AI and ...'),('CLB001','EVT','Ban Sự Kiện'),('CLB001','DES','Thiết kế'),('CLB001','IT','Công nghệ'),('CLB001','WEB','Thiết kế web');
/*!40000 ALTER TABLE `clubband` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clubbandmember`
--

DROP TABLE IF EXISTS `clubbandmember`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clubbandmember` (
  `username` varchar(100) DEFAULT NULL,
  `club_id` varchar(100) DEFAULT NULL,
  `band_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clubbandmember`
--

LOCK TABLES `clubbandmember` WRITE;
/*!40000 ALTER TABLE `clubbandmember` DISABLE KEYS */;
INSERT INTO `clubbandmember` VALUES ('hbin','CLB001','IT'),('poppy','CLB001','IT'),('member02','CLB001','WEB'),('member03','CLB001',NULL),('hbintramkem','CLB001','DES'),('hbinz','CLB001','DES'),('hbintest','CLB001','DES'),('duonggg','CLB001','AI');
/*!40000 ALTER TABLE `clubbandmember` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clubmember`
--

DROP TABLE IF EXISTS `clubmember`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clubmember` (
  `id` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `club_id` varchar(255) NOT NULL,
  `joined_at` datetime DEFAULT current_timestamp(),
  `position` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `club_id` (`club_id`),
  KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clubmember`
--

LOCK TABLES `clubmember` WRITE;
/*!40000 ALTER TABLE `clubmember` DISABLE KEYS */;
INSERT INTO `clubmember` VALUES ('','hbinz','CLB001','2026-09-05 22:12:37','Thành viên',1),('CM001','organizer01','CLB001','2026-08-23 14:35:06','Thành viên',1),('CM002','member01','CLB001','2026-08-23 14:35:06','Thành viên',1),('CM003','member02','CLB001','2026-08-23 14:35:06','Thành viên',1),('CM004','poppy','CLB001','2026-08-23 14:35:06','Thành viên',1),('CM005','organizer02','CLB001','2026-08-23 14:35:06','Thành viên',1),('CM006','member04','CLB001','2026-08-23 14:35:06','Thành viên',1),('CM007','hbin','CLB001','2026-08-27 22:55:17','Thành viên',1),('CM008','member03','CLB001','2026-09-04 01:38:56','Thành viên',1),('CM009','hbintramkem','CLB001','2026-09-05 16:31:48','Chủ nhiệm',1),('CM010','lan','CLB001','2026-09-07 09:56:38','Thành viên',1),('CM011','duonggg','CLB001','2026-09-07 09:56:41','Thành viên',1),('CM012','hbintest','CLB001','2026-09-07 10:05:19','Thành viên',1);
/*!40000 ALTER TABLE `clubmember` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clubs`
--

DROP TABLE IF EXISTS `clubs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clubs` (
  `club_id` varchar(255) NOT NULL,
  `club_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `rule` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `owner_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`club_id`),
  KEY `fk_club_creator` (`created_by`),
  KEY `fk_club_owner` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clubs`
--

LOCK TABLES `clubs` WRITE;
/*!40000 ALTER TABLE `clubs` DISABLE KEYS */;
INSERT INTO `clubs` VALUES ('CLB001','CLB Công nghệ thông tin','Câu lạc bộ dành cho sinh viên yêu thích công nghệ và lập trình.','Tôn trọng thành viên, tham gia đầy đủ các hoạt động của câu lạc bộ.','logo_it.png','hbin','organizer01','2026-08-23 07:34:34',1),('CLB002','sad','aaaa','aaaa','none.png','hbin','organizer02','2026-08-27 15:21:27',1);
/*!40000 ALTER TABLE `clubs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'unread',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `viewed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`contact_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact`
--

LOCK TABLES `contact` WRITE;
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;
INSERT INTO `contact` VALUES (1,'hbin','Tài khoản & mật khẩu','E muốn đổi username','read','2026-09-07 00:34:54','2026-09-07 11:52:11');
/*!40000 ALTER TABLE `contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` varchar(255) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `slots` int(11) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `organizer_id` varchar(255) NOT NULL,
  `status` varchar(30) DEFAULT 'upcoming',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event`
--

LOCK TABLES `event` WRITE;
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
INSERT INTO `event` VALUES (1,'CLB001','Workshop Kỹ năng truyền thông','2026-09-15','08:00:00','11:00:00',30,'Phòng A101','Workshop chia sẻ kỹ năng viết nội dung, thiết kế và truyền thông cho thành viên câu lạc bộ.','hbintramkem','upcoming','2026-09-02 16:09:35'),(2,'CLB001','Tiệc YEP','2026-09-04','01:00:00','23:59:00',100,'Sân ngoài trời','Tiệc cuối năm','organizer01','upcoming','2026-09-03 18:37:15');
/*!40000 ALTER TABLE `event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eventband`
--

DROP TABLE IF EXISTS `eventband`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eventband` (
  `event_id` int(11) NOT NULL,
  `band_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventband`
--

LOCK TABLES `eventband` WRITE;
/*!40000 ALTER TABLE `eventband` DISABLE KEYS */;
INSERT INTO `eventband` VALUES (1,NULL),(1,NULL);
/*!40000 ALTER TABLE `eventband` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eventrequirement`
--

DROP TABLE IF EXISTS `eventrequirement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eventrequirement` (
  `event_id` int(11) NOT NULL,
  `position` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventrequirement`
--

LOCK TABLES `eventrequirement` WRITE;
/*!40000 ALTER TABLE `eventrequirement` DISABLE KEYS */;
/*!40000 ALTER TABLE `eventrequirement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `notification_id` varchar(100) DEFAULT NULL,
  `club_id` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `posted_date` date DEFAULT NULL,
  `posted_by` varchar(100) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES ('882890','CLB001','Lorem ipsum dolor sit amet consectetur, adipisicing elit. Doloremque ','Lorem ipsum dolor sit amet consectetur, adipisicing elit. Doloremque cumque hic accusantium vel cum architecto laudantium eum aliquam sit veritatis ipsa quam, corporis laboriosam non ducimus, culpa voluptates vero explicabo.','2026-09-06','hbintramkem','https://images2.thanhnien.vn/528068263637045248/2023/3/21/jack-1679396385964143355875.jpeg');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `register_event`
--

DROP TABLE IF EXISTS `register_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `register_event` (
  `username` varchar(255) NOT NULL,
  `event_id` int(11) NOT NULL,
  `register_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `register_status` varchar(30) DEFAULT 'pending',
  `approved_by` varchar(255) DEFAULT NULL,
  `approved_time` timestamp NULL DEFAULT NULL,
  `reject_reason` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `register_event`
--

LOCK TABLES `register_event` WRITE;
/*!40000 ALTER TABLE `register_event` DISABLE KEYS */;
INSERT INTO `register_event` VALUES ('hbin',1,'2026-09-02 16:51:46','approved','organizer01','2026-09-03 17:53:54',NULL),('poppy',1,'2026-09-02 16:59:27','pending',NULL,NULL,NULL);
/*!40000 ALTER TABLE `register_event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES ('admin01','123456','admin','2026-08-23 07:31:15',1),('duonggg','$2y$12$hcUrQiV4OgJAWgb1H18Nm.dzlJQ0.R18la4tbmPBC1KMl5.o9IAUu','member','2026-09-07 02:55:34',1),('hbin','$2y$10$ST7a87BF8nBEua5qUGt4Wu8ieT5RgKccuYI2IHALW8JTMH82U3Dgi','member','2026-08-26 16:37:31',1),('hbintest','$2y$12$bYt8lODHlbL/KDtNdAUE..9d1jvFzi9sP/WNYtaYqa.FHlc8QNQdm','member','2026-09-07 03:05:01',1),('hbintramkem','$2y$10$ST7a87BF8nBEua5qUGt4Wu8ieT5RgKccuYI2IHALW8JTMH82U3Dgi','admin','2026-08-26 15:06:54',1),('hbinz','$2y$12$MEDMvacI5r8TiDpMsuu3bOHGziy45HBx75B.zGygULS2CyXZX/CQu','member','2026-09-05 15:12:16',1),('lan','$2y$12$uRnVnIBPbdJNNVPNAFtrW.XztJ/Mw/EIrLF4aQp.RO2Dm1sMo6oJG','member','2026-09-07 02:56:20',1),('member01','$2y$10$ST7a87BF8nBEua5qUGt4Wu8ieT5RgKccuYI2IHALW8JTMH82U3Dgi','member','2026-08-23 07:31:15',1),('member02','$2y$10$ST7a87BF8nBEua5qUGt4Wu8ieT5RgKccuYI2IHALW8JTMH82U3Dgi','member','2026-08-23 07:31:15',1),('member03','$2y$10$ST7a87BF8nBEua5qUGt4Wu8ieT5RgKccuYI2IHALW8JTMH82U3Dgi','member','2026-09-03 18:39:21',1),('member04','123456','member','2026-08-23 07:31:15',1),('organizer01','$2y$10$ST7a87BF8nBEua5qUGt4Wu8ieT5RgKccuYI2IHALW8JTMH82U3Dgi','organizer','2026-08-23 07:31:15',1),('organizer02','123456','organizer','2026-08-23 07:31:15',1),('poppy','$2y$10$ST7a87BF8nBEua5qUGt4Wu8ieT5RgKccuYI2IHALW8JTMH82U3Dgi','member','2026-08-23 07:31:15',1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userinfo`
--

DROP TABLE IF EXISTS `userinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `userinfo` (
  `username` varchar(100) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `gender` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `id_number` varchar(100) DEFAULT NULL,
  `avt_links` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userinfo`
--

LOCK TABLES `userinfo` WRITE;
/*!40000 ALTER TABLE `userinfo` DISABLE KEYS */;
INSERT INTO `userinfo` VALUES ('admin01',NULL,NULL,NULL,NULL,NULL,NULL,NULL),('duonggg','Dương',NULL,'1111-11-11','duong@gmail.com',NULL,'224001777',NULL),('hbin','Gia Hwngggg','Nam','2006-11-08','hbina@gmail.com','0332218836','2240017981','https://i.pinimg.com/236x/f2/17/52/f21752562ff69ec53b3b586838ea6785.jpg'),('hbintest','Gia Hwng',NULL,'2006-08-11','hungngt08112@gmail.com',NULL,'224001798',NULL),('hbintramkem','admin','Nam','2006-11-11','hungngt0822211@gmail.com','0332218836','224001798','https://images2.thanhnien.vn/528068263637045248/2023/3/21/jack-1679396385964143355875.jpeg'),('hbinz','huhuuh',NULL,'2005-08-11','hungngt0811a@gmail.com',NULL,'224001777',NULL),('lan','Lan',NULL,'2005-02-11','abc@gmail.com',NULL,'224001111',NULL),('member02','Memberhehe','Nam','2005-11-11','hungngt0811@gmail.com','0332115165','224001798','uploads/avatars/member02_1787938616.jpg'),('member03','Thành viên 3','Nam',NULL,NULL,NULL,NULL,NULL),('member04','Hehe',NULL,NULL,NULL,NULL,NULL,NULL),('organizer01','Ban tổ chức','',NULL,NULL,NULL,NULL,NULL),('poppy','Poppy','Nữ','2005-11-01','nguyet.fuwo@gmail.com','0206515615','224001999','https://images2.thanhnien.vn/528068263637045248/2023/3/21/jack-1679396385964143355875.jpeg');
/*!40000 ALTER TABLE `userinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'qlyclb'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-07 12:59:47
