-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Jan 11, 2026 at 09:16 AM
-- Server version: 8.0.44
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `comtech_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `role` enum('superadmin','admin') NOT NULL DEFAULT 'admin',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `fullname`, `role`, `last_login`, `created_at`) VALUES
(1, 'admin', '$2y$10$tM2A/G5D7G9Gv0P1HjP.P.7Ff/rM7Y6C5G3X/N4A/B2S1G0G7K8K.', 'Super Admin', 'superadmin', '2026-01-10 20:48:14', '2026-01-10 08:29:41'),
(4, 'test', '$2y$10$.Yye8TpeLmTAWzhS.Ru5mej4vFE4VkfAVSsx0IGEp1z1bLXa0uos2', 'T', 'admin', '2026-01-10 19:34:38', '2026-01-10 19:33:53');

-- --------------------------------------------------------

--
-- Table structure for table `awards`
--

CREATE TABLE `awards` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL COMMENT 'ชื่อรางวัล',
  `award_date` date DEFAULT NULL COMMENT 'วันที่ได้รับรางวัล',
  `organizer` varchar(255) DEFAULT NULL COMMENT 'หน่วยงานที่จัด',
  `description` text COMMENT 'รายละเอียด/รายชื่อนักศึกษา',
  `image` varchar(255) DEFAULT NULL COMMENT 'รูปภาพรางวัล',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `awards`
--

INSERT INTO `awards` (`id`, `title`, `award_date`, `organizer`, `description`, `image`, `created_at`) VALUES
(1, 'รางวัลชนะเลิศ การแข่งขันหุ่นยนต์อาชีวศึกษา ระดับชาติ', '2025-02-15', 'สำนักงานคณะกรรมการการอาชีวศึกษา (สอศ.)', 'ทีมนักศึกษาแผนกคอมพิวเตอร์ธุรกิจ คว้าชัยชนะอันดับ 1 ในการแข่งขันหุ่นยนต์กู้ภัยระดับชาติ ประจำปี พ.ศ. 2568', 'robot_award_2025.jpg', '2026-01-10 18:10:33'),
(2, 'เหรียญทอง การแข่งขันเขียนโปรแกรมคอมพิวเตอร์ (ทักษะพื้นฐาน)', '2024-12-10', 'องค์การนักวิชาชีพในอนาคตแห่งประเทศไทย (อวท.)', 'นายสมชาย สายเขียว นักศึกษา ปวส.2 ได้รับรางวัลเหรียญทองจากการแข่งขันทักษะการพัฒนาโปรแกรมเชิงวัตถุ ระดับภาคเหนือ', 'coding_gold_2024.jpg', '2026-01-10 18:10:33'),
(3, 'รางวัลชมเชย การประกวดโครงงานนวัตกรรมสิ่งประดิษฐ์คนรุ่นใหม่', '2024-11-20', 'กระทรวงการอุดมศึกษา วิทยาศาสตร์ วิจัยและนวัตกรรม (อว.)', 'โครงงาน \"ระบบจัดการขยะอัจฉริยะด้วย IoT\" ได้รับรางวัลชมเชยระดับประเทศ จากผู้เข้าแข่งขันกว่า 200 ทีม', 'iot_innovation.jpg', '2026-01-10 18:10:33'),
(4, 'รางวัลรองชนะเลิศอันดับ 2 การแข่งขัน E-Sports ระดับจังหวัด', '2024-08-05', 'สมาคมกีฬาอีสปอร์ตแห่งประเทศไทย', 'ทีม ComTech UTC คว้าอันดับ 3 ในการแข่งขัน ROV อาชีวะสัมพันธ์ ระดับจังหวัดอุตรดิตถ์', 'esport_utc.jpg', '2026-01-10 18:10:33'),
(10, 'เหรียญเงิน พัฒนา Mobile Application', '2025-07-12', 'อวท. ระดับชาติ', 'แอปพลิเคชันช่วยเหลือผู้สูงอายุ', 'award6.jpg', '2026-01-10 18:27:06'),
(11, 'ชมเชย การแข่งขัน Network Security', '2025-06-25', 'มหาวิทยาลัยราชภัฏ', 'CTF Competition 2025', 'award7.jpg', '2026-01-10 18:27:06'),
(12, 'รองชนะเลิศอันดับ 2 ตัดต่อวิดีโอ', '2025-05-30', 'กรมประชาสัมพันธ์', 'หนังสั้นรณรงค์การขับขี่ปลอดภัย', 'award8.jpg', '2026-01-10 18:27:06'),
(13, 'รางวัลนวัตกรรมดีเด่น ระดับอาชีวศึกษา', '2025-04-15', 'สอศ.', 'ระบบจัดการสต็อกสินค้าอัจฉริยะ', 'award9.jpg', '2026-01-10 18:27:06'),
(14, 'เหรียญทองแดง การเขียน Web ด้วย PHP', '2025-03-20', 'อวท. ระดับภาค', 'การแข่งขันพัฒนาเว็บไซต์ E-Commerce', 'award10.jpg', '2026-01-10 18:27:06'),
(15, 'ชนะเลิศ ประกวดแผนธุรกิจ Startup', '2025-02-10', 'ธนาคารออมสิน', 'แผนธุรกิจแอปฯ สั่งอาหารท้องถิ่น', 'award11.jpg', '2026-01-10 18:27:06'),
(16, 'รองชนะเลิศอันดับ 1 ซ่อมบำรุงคอมพิวเตอร์', '2025-01-05', 'วิทยาลัยเทคนิค', 'แข่งขันทักษะวิชาชีพภายในสถาบัน', 'award12.jpg', '2026-01-10 18:27:06'),
(17, 'รางวัลบุคลากรดีเด่นด้านเทคโนโลยี', '2024-12-25', 'จังหวัดอุตรดิตถ์', 'มอบให้นักศึกษาที่มีจิตอาสาด้านไอที', 'award13.jpg', '2026-01-10 18:27:06'),
(18, 'เหรียญทอง ไมโครคอนโทรลเลอร์เบื้องต้น', '2024-11-18', 'อวท. ระดับจังหวัด', 'ระบบเปิดปิดไฟผ่านเสียง', 'award14.jpg', '2026-01-10 18:27:06'),
(19, 'ชนะเลิศ ถ่ายภาพเชิงสร้างสรรค์', '2024-10-02', 'การท่องเที่ยวแห่งประเทศไทย', 'ภาพถ่ายโปรโมทแหล่งท่องเที่ยวลับ', 'award15.jpg', '2026-01-10 18:27:06'),
(20, 'ชมเชย การแข่งขันประกอบเครื่องคอมฯ', '2024-09-15', 'Advice IT City', 'Speed Build PC Challenge', 'award16.jpg', '2026-01-10 18:27:06'),
(21, 'รองชนะเลิศอันดับ 2 ออกแบบ 3D Model', '2024-08-20', 'Bit Studio', 'ออกแบบตัวละครเกมออนไลน์', 'award17.jpg', '2026-01-10 18:27:06'),
(22, 'เหรียญเงิน การใช้โปรแกรม Microsoft Office', '2024-07-10', 'MOS Thailand', 'แข่งขันทักษะการใช้ Excel ขั้นสูง', 'award18.jpg', '2026-01-10 18:27:06'),
(23, 'รางวัลดีเด่น ด้านคุณธรรมจริยธรรม', '2024-06-05', 'กระทรวงศึกษาธิการ', 'นักศึกษาตัวอย่างของแผนกคอมพิวเตอร์', 'award19.jpg', '2026-01-10 18:27:06'),
(24, 'ชนะเลิศ การโต้วาทีภาษาอังกฤษด้านไอที', '2024-05-01', 'British Council', 'IT Debate Competition 2024', 'award20.jpg', '2026-01-10 18:27:06');

-- --------------------------------------------------------

--
-- Table structure for table `curriculum`
--

CREATE TABLE `curriculum` (
  `id` int NOT NULL,
  `subject_code` varchar(20) NOT NULL COMMENT 'รหัสวิชา',
  `subject_name` varchar(255) NOT NULL COMMENT 'ชื่อวิชา',
  `credits` varchar(10) NOT NULL COMMENT 'หน่วยกิต (ท-ป-น)',
  `category` varchar(100) DEFAULT NULL COMMENT 'หมวดวิชา',
  `level` enum('ปวช.','ปวส.') NOT NULL COMMENT 'ระดับชั้น',
  `year` enum('1','2','3') NOT NULL COMMENT 'ปีที่เรียน',
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `curriculum`
--

INSERT INTO `curriculum` (`id`, `subject_code`, `subject_name`, `credits`, `category`, `level`, `year`, `description`, `created_at`) VALUES
(1, '20901-1001', 'คอมพิวเตอร์และสารสนเทศเพื่องานอาชีพ', '1-2-2', 'สมรรถนะแกนกลาง', 'ปวช.', '1', NULL, '2026-01-10 17:39:20'),
(2, '20901-2001', 'คณิตศาสตร์คอมพิวเตอร์', '2-0-2', 'สมรรถนะวิชาชีพเฉพาะ', 'ปวช.', '1', NULL, '2026-01-10 17:39:20'),
(3, '20901-2002', 'การเขียนโปรแกรมคอมพิวเตอร์', '2-2-3', 'สมรรถนะวิชาชีพเฉพาะ', 'ปวช.', '2', NULL, '2026-01-10 17:39:20'),
(4, '20901-2005', 'ระบบเครือข่ายเบื้องต้น', '2-2-3', 'สมรรถนะวิชาชีพเฉพาะ', 'ปวช.', '2', NULL, '2026-01-10 17:39:20'),
(5, '20901-2101', 'การสร้างเว็บไซต์', '2-2-3', 'สมรรถนะวิชาชีพเลือก', 'ปวช.', '3', NULL, '2026-01-10 17:39:20'),
(6, '20901-8501', 'โครงงาน 1', '0-4-2', 'สมรรถนะวิชาชีพเฉพาะ', 'ปวช.', '3', NULL, '2026-01-10 17:39:20'),
(7, '30901-1001', 'เทคโนโลยีสารสนเทศเพื่อการจัดการอาชีพ', '2-2-3', 'สมรรถนะวิชาชีพพื้นฐาน', 'ปวส.', '1', NULL, '2026-01-10 17:39:20'),
(9, '30901-2004', 'การพัฒนาโปรแกรมบนอุปกรณ์เคลื่อนที่', '2-2-3', 'สมรรถนะวิชาชีพเฉพาะ', 'ปวส.', '2', NULL, '2026-01-10 17:39:20');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int NOT NULL,
  `topic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'ประชาสัมพันธ์',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `topic`, `content`, `category`, `image`, `created_at`) VALUES
(3, 'ฟหกฟหก', '<p>ฟหกฟหก</p>', 'ประชาสัมพันธ์', '', '2026-01-10 18:55:49');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int NOT NULL,
  `project_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ชื่อโครงงาน',
  `student_names` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'ชื่อผู้จัดทำ (เช่น นาย ก, นาย ข)',
  `advisor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'อาจารย์ที่ปรึกษา',
  `academic_year` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ปีการศึกษา (2568)',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'รายละเอียดโครงงาน',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'รูปพรีวิวผลงาน',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `project_name`, `student_names`, `advisor`, `academic_year`, `description`, `image`, `created_at`) VALUES
(3, 'ฟหกหฟ', 'ฟหก', 'ฟหก', '2569', '<p>ฟหกหฟกหฟก</p>', '', '2026-01-10 18:58:32');

-- --------------------------------------------------------

--
-- Table structure for table `student_projects`
--

CREATE TABLE `student_projects` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `image_url` varchar(255) DEFAULT 'default_project.jpg',
  `student_name` varchar(255) DEFAULT NULL,
  `project_year` year DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `education` text,
  `expertise` text,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `name`, `position`, `education`, `expertise`, `image`, `created_at`) VALUES
(7, 'sad', 'อาจารย์', 'asd', '', '', '2026-01-10 19:30:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `awards`
--
ALTER TABLE `awards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `curriculum`
--
ALTER TABLE `curriculum`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_projects`
--
ALTER TABLE `student_projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `awards`
--
ALTER TABLE `awards`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `curriculum`
--
ALTER TABLE `curriculum`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_projects`
--
ALTER TABLE `student_projects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
