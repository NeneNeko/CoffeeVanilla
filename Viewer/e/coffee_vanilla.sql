-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 05, 2016 at 04:35 PM
-- Server version: 5.7.10
-- PHP Version: 7.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `coffee_vanilla`
--

-- --------------------------------------------------------

--
-- Table structure for table `cv_chapter`
--

CREATE TABLE `cv_chapter` (
  `ch_id` int(8) NOT NULL,
  `ch_name_id` int(8) DEFAULT NULL,
  `ch_number` decimal(8,1) DEFAULT NULL,
  `ch_title` varchar(255) DEFAULT NULL,
  `ch_uri` text,
  `ch_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cv_image_blob`
--

CREATE TABLE `cv_image_blob` (
  `ib_id` int(8) NOT NULL,
  `ib_image` longblob,
  `ib_type` varchar(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cv_image_data`
--

CREATE TABLE `cv_image_data` (
  `im_id` int(8) NOT NULL,
  `im_name_id` int(8) DEFAULT NULL,
  `im_chapter` decimal(8,1) DEFAULT NULL,
  `im_page` int(8) DEFAULT NULL,
  `im_blob_id` int(8) DEFAULT NULL,
  `im_uri` text,
  `im_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cv_import`
--

CREATE TABLE `cv_import` (
  `im_id` int(8) NOT NULL,
  `im_domain` varchar(100) DEFAULT NULL,
  `im_type` varchar(1) DEFAULT NULL,
  `im_httpheader` tinyint(1) NOT NULL DEFAULT '1',
  `im_useragent` tinyint(1) NOT NULL DEFAULT '1',
  `im_refferent` tinyint(1) NOT NULL DEFAULT '1',
  `im_selfrefferent` tinyint(1) NOT NULL DEFAULT '1',
  `im_xpath` varchar(255) DEFAULT NULL,
  `im_attribute` varchar(100) DEFAULT NULL,
  `im_titlexpath` varchar(255) DEFAULT NULL,
  `im_linkmatch` varchar(255) DEFAULT NULL,
  `im_urlfix` varchar(255) DEFAULT NULL,
  `im_md5skip` varchar(255) DEFAULT NULL,
  `im_scripts` text,
  `im_match` varchar(255) DEFAULT NULL,
  `im_replace` varchar(255) DEFAULT NULL,
  `im_comment` varchar(255) DEFAULT NULL,
  `im_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cv_name`
--

CREATE TABLE `cv_name` (
  `na_id` int(8) NOT NULL,
  `na_sub_id` int(8) DEFAULT '0',
  `na_name` varchar(150) DEFAULT NULL,
  `na_detail` text,
  `na_image` mediumblob,
  `na_uri` text,
  `na_uri_template` text,
  `na_last` decimal(8,1) DEFAULT NULL,
  `na_end` tinyint(1) NOT NULL DEFAULT '0',
  `na_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cv_setting`
--

CREATE TABLE `cv_setting` (
  `se_name` varchar(255) NOT NULL DEFAULT '',
  `se_variable` varchar(255) DEFAULT NULL,
  `se_description` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cv_setting`
--

INSERT INTO `cv_setting` (`se_name`, `se_variable`, `se_description`) VALUES
('download_directory', 'Download/', 'โฟล์เดอร์หลักที่เก็บไฟล์'),
('database_directory', 'Download/.database/', 'โฟล์เดอร์ไฟล์ของรายชื่อ'),
('cache_directory', 'R:/.cache/', 'โฟล์เดอร์ไฟล์ชั่วคราว'),
('cookie_directory', 'Download/.cookie/', 'โฟล์เดอร์ไฟล์คุกกี้'),
('ca_cert_filename', 'Libraries/ca-bundle.crt', 'ชื่อไฟล์ CA สำหรับโปรโตคอล https'),
('cover_filename', 'Cover.jpg', 'ชื่อไฟล์หน้าปก'),
('skip_word', 'banner, cartoonclub-th.gif, transparent.gif, yengo, tap, tlc-cartoon-fanclub.jpg, thumbnail, topman, comment_face.png, tran.png, none.gif, smiley, 2hentai-th-a.jpg, next-2.jpg', 'รายชื่อไฟล์ที่ข้ามจากการค้นหา'),
('special_domains', '.co.uk, .org.uk, .co.th, .in.th', 'โดนเมนพิเศษ'),
('url_pattern', '/.*?((http|ftp|https)://[w#$&+,/:;=?@%20.-]+)[^w#$&+,/:;=?@.-]*?/i', 'แพทเทิร์นสำหรับค้นหา url'),
('javascript_pattern', '/<script[^>]*>(.*?)</script>/is', 'แพทเทิร์นสำหรับค้นหาลบ javascripts'),
('resume_download', '3', 'จำนวนที่ดาวน์โหลดซ้ำ'),
('browser_useragent', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0', 'useragent ที่ส่งให้ server'),
('recompassed', 'TRUE', 'เข้ารหัสไฟล์ภาพใหม่'),
('output_extension', 'jpg', 'ชนิดของไฟล์ที่บันทึก  jpg, png'),
('ImageMagick_filename', 'Libraries/ImageMagick/convert.exe', ''),
('PDFConvert_filename', 'Libraries/JPEGtoPD/JPEGtoPDF.exe', 'ที่อยู่ของโปรแกรมแปลงไฟล์ pdf'),
('png_optimizer_filename', 'Libraries/PngOptimizer/PngOptimizerCL.exe', 'ที่อยู่ของโปรแกรมบีบภาพ png'),
('png_optimizer', 'TRUE', 'เปิดใช้งานโปรแกรมบีบภาพ png'),
('png_compression', '9', 'ระดับการบีบอัด 0-9'),
('jpeg_quality', '90', 'คุณภาพของรูป 30-100%'),
('accept_dimension', '256', 'ขนาดของรูปที่ยอมรับได้ (Pixel)'),
('accept_bytes', '100', 'ขนาดไฟล์ที่ยอมรับได้ (Bytes)'),
('thumbnail_width', '120', 'ความกว้างของรูปตัวอย่าง'),
('thumbnail_height', '140', 'ความสูงของรูปตัวอย่าง'),
('episode_prefix', 'TRUE', 'ใส่ชื่อตอนขึ้นต้นไฟล์'),
('page_digit', '3', 'จำนวนหลักของตอน'),
('cache_download_file', 'TRUE', 'เปิดใช้งาน Cache ไฟล์'),
('auto_zip', 'TRUE', 'zip ไฟล์ที่โหลดอัตโนมัติ'),
('delete_after_zip', 'TRUE', 'ลบไฟล์ที่โหลดหลังจาก zip แล้ว'),
('max_width', '1800', 'ย่อขนาดภาพให้ความกว้างไม่เกินที่ระบุ'),
('max_height', '1200', 'ย่อขนาดภาพให้ความสูงไม่เกินที่ระบุ'),
('curl_timeout', '20', 'ระยะเวลาสูงสุดที่โหลดไฟล์ (วินาที)'),
('title', 'Neko Coffee&Vanilla', NULL),
('twitter', NULL, NULL),
('github', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cv_chapter`
--
ALTER TABLE `cv_chapter`
  ADD PRIMARY KEY (`ch_id`);

--
-- Indexes for table `cv_image_blob`
--
ALTER TABLE `cv_image_blob`
  ADD PRIMARY KEY (`ib_id`);

--
-- Indexes for table `cv_image_data`
--
ALTER TABLE `cv_image_data`
  ADD PRIMARY KEY (`im_id`);

--
-- Indexes for table `cv_import`
--
ALTER TABLE `cv_import`
  ADD PRIMARY KEY (`im_id`);

--
-- Indexes for table `cv_name`
--
ALTER TABLE `cv_name`
  ADD PRIMARY KEY (`na_id`);

--
-- Indexes for table `cv_setting`
--
ALTER TABLE `cv_setting`
  ADD PRIMARY KEY (`se_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cv_chapter`
--
ALTER TABLE `cv_chapter`
  MODIFY `ch_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3053;
--
-- AUTO_INCREMENT for table `cv_image_blob`
--
ALTER TABLE `cv_image_blob`
  MODIFY `ib_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62915;
--
-- AUTO_INCREMENT for table `cv_image_data`
--
ALTER TABLE `cv_image_data`
  MODIFY `im_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62915;
--
-- AUTO_INCREMENT for table `cv_import`
--
ALTER TABLE `cv_import`
  MODIFY `im_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;
--
-- AUTO_INCREMENT for table `cv_name`
--
ALTER TABLE `cv_name`
  MODIFY `na_id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
