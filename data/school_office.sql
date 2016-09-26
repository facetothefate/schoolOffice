-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-09-26 02:58:14
-- 服务器版本： 10.1.13-MariaDB
-- PHP Version: 5.5.35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school_office`
--

-- --------------------------------------------------------

--
-- 表的结构 `so_courses`
--

CREATE TABLE `so_courses` (
  `course_code` varchar(10) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `credit` float DEFAULT '1',
  `grade` int(11) DEFAULT NULL,
  `prerequisite` varchar(100) DEFAULT NULL,
  `so_course_categories_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `so_courses`
--

INSERT INTO `so_courses` (`course_code`, `name`, `credit`, `grade`, `prerequisite`, `so_course_categories_id`) VALUES
('ADA1O', 'Drama, Opera', 1, 9, NULL, 9),
('ADA3M', 'Drama, University|College', 1, 11, 'ADA1O', 9),
('ADA4M', 'Drama, University|College', 1, 12, 'ADA3M', 9),
('BAF3M', 'Financial Accounting Fundamentals, University|College', 1, 11, NULL, 14),
('BAT4M', 'Financial Accounting Principles, University|College', 1, 12, 'BAF3M', 14),
('BBB4M', 'International Business Fundamentals, University|College', 1, 12, NULL, 14),
('BOH4M', 'Business Leadership: Management Fundamentals, University|College', 1, 12, NULL, 14),
('CGC1D', 'Geography of Canada, Academic', 1, 9, NULL, 6),
('CGD3M', 'The Americas: Geography Patterns and Issues, University|College', 1, 11, 'CGC1D', 6),
('CGW4U', 'Canadian and World Issues: A Geographic Analysis, University', 1, 12, 'CGD3M', 6),
('CHA3U', 'American History, University', 1, 11, 'CHC2D', 7),
('CHC2D', 'Canadian History Since World War I Academic', 1, 10, NULL, 7),
('CHI4U', 'Canada: History, Identity, and Culture', 1, 12, 'CHC3U', 7),
('CHV20', 'Civics', 0.5, 10, NULL, 10),
('ENG1D', 'English Academic', 1, 9, NULL, 1),
('ENG2D', 'English Academic', 1, 10, 'ENG1D|ESLDO', 1),
('ENG3U', 'English University', 1, 11, 'ENG2D|ESLEO', 1),
('ENG4U', 'English University', 1, 12, 'ENG3U', 1),
('ESLAO', 'ESL, Level 1', 1, 9, NULL, 2),
('ESLBO', 'ESL, Level 2', 1, 9, 'ESLAO', 2),
('ESLCO', 'ESL, Level 3', 1, 10, 'ESLBO', 2),
('ESLDO', 'ESL, Level 4', 1, 11, 'ESLCO', 2),
('ESLEO', 'ESL, Level 5', 1, 12, 'ESLDO', 2),
('FSF1D', 'Core French Academic', 1, 9, NULL, 12),
('FSF2D', 'Core French Academic', 1, 10, 'FSF1D', 12),
('FSF3U', 'Core French University', 1, 11, 'FSF2D', 12),
('FSF4U', 'Core French University', 1, 12, 'FSF3U', 12),
('GLC20', 'Career Studies, Opera(Half- Credit)', 0.5, 10, NULL, 10),
('ICS3U', 'Introduction to Computer Science University', 1, 11, NULL, 15),
('ICS4U', 'Computer Science University', 1, 12, 'ICS3U', 15),
('LYXAD', 'International Languages, Academic', 1, 9, NULL, 11),
('LYXBD', 'International Languages, Academic', 1, 10, 'LYXAD', 11),
('LYXCU', 'International Languages, University', 1, 11, 'LYXBD', 11),
('LYXDU', 'International Languages, University', 1, 12, 'LYXCU', 11),
('MCR3U', 'Functions', 1, 11, 'MPM2D', 3),
('MCV4U', 'Calculus and Vectors', 1, 12, 'MCR3U&MHF4U', 3),
('MDM4U', 'Mathematics of Data Management', 1, 12, 'MCR3U', 3),
('MHF4U', 'Advanced Functions', 1, 12, 'MCR3U', 3),
('MPM1D', 'Principles of Mathematics', 1, 9, NULL, 3),
('MPM2D', 'Principles of Mathematics', 1, 10, 'MPM1D', 3),
('OLC4O10', 'Literacy Course', 1, 10, 'Unsuccessfully tried OSSLT', 13),
('OLC4O11', 'Literacy Course', 1, 11, 'Unsuccessfully tried OSSLT', 13),
('OLC4O12', 'Literacy Course', 1, 12, 'Unsuccessfully tried OSSLT', 13),
('OLC4O9', 'Literacy Course', 1, 9, 'Unsuccessfully tried OSSLT', 13),
('PSE4U', 'Exercise Science, University', 1, 12, 'Any Grade11 university or university|college preparation courses in science', 8),
('SBI3U', 'Biology University', 1, 11, 'SNC2D', 4),
('SBI4U', 'Biology University', 1, 12, 'SBI3U', 4),
('SCH3U', 'Chemistry University', 1, 11, 'SNC2D', 4),
('SCH4U', 'Chemistry University', 1, 12, 'SCH3U', 4),
('SNC1D', 'Science Academic', 1, 9, NULL, 4),
('SNC2D', 'Science Academic', 1, 10, 'SNC1D', 4),
('SPH3U', 'Physics University', 1, 11, 'SNC2D', 4),
('SPH4U', 'Physics University', 1, 12, 'SPH3U', 4);

-- --------------------------------------------------------

--
-- 表的结构 `so_course_categories`
--

CREATE TABLE `so_course_categories` (
  `id` int(11) NOT NULL,
  `title` varchar(45) DEFAULT NULL,
  `code` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `so_course_categories`
--

INSERT INTO `so_course_categories` (`id`, `title`, `code`) VALUES
(1, 'English Department', 'ENG'),
(2, 'English as a Second Language', 'ESL'),
(3, 'Mathematics Department', 'MATH'),
(4, 'Science Department', 'SNC'),
(5, 'Canadian and World Studies Department', 'CWS'),
(6, 'Canadian Geography', 'CG'),
(7, 'Canadian History', 'CH'),
(8, 'Health and Physical Education Department', 'HPE'),
(9, 'Department of Arts', 'ART'),
(10, 'Civics and Careers', 'CC'),
(11, 'International Languages Department', 'LYX'),
(12, 'French Language Department', 'FSF'),
(13, 'Ontario Secondary School Literacy Course OLC4', 'OLC4O'),
(14, 'Business Studies', 'BS'),
(15, 'Computer Science', 'CS');

-- --------------------------------------------------------

--
-- 表的结构 `so_course_schedule`
--

CREATE TABLE `so_course_schedule` (
  `so_schedule_id` int(11) NOT NULL,
  `so_courses_course_code` varchar(10) NOT NULL,
  `so_semesters_id` int(11) NOT NULL,
  `so_teachers_so_users_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- 表的结构 `so_course_selections`
--

CREATE TABLE `so_course_selections` (
  `so_students_student_number` int(11) NOT NULL,
  `so_semesters_id` int(11) NOT NULL,
  `so_courses_course_code` varchar(10) NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- 表的结构 `so_diplomas`
--

CREATE TABLE `so_diplomas` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `so_diplomas`
--

INSERT INTO `so_diplomas` (`id`, `name`) VALUES
(1, 'Ontario Secondary School Diploma');

-- --------------------------------------------------------

--
-- 表的结构 `so_exam_results`
--

CREATE TABLE `so_exam_results` (
  `so_courses_course_code` varchar(10) NOT NULL,
  `so_students_student_number` int(11) NOT NULL,
  `so_semesters_id` int(11) NOT NULL,
  `grade` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `so_exam_schedule`
--

CREATE TABLE `so_exam_schedule` (
  `so_schedule_id` int(11) NOT NULL,
  `so_semesters_id` int(11) NOT NULL,
  `so_courses_course_code` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `so_graduation_conditions`
--

CREATE TABLE `so_graduation_conditions` (
  `id` int(11) NOT NULL,
  `grade` varchar(45) DEFAULT NULL,
  `credit` varchar(45) DEFAULT NULL,
  `course` varchar(45) DEFAULT NULL,
  `so_course_categories_id` int(11) NOT NULL,
  `so_diplomas_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `so_graduation_conditions`
--

INSERT INTO `so_graduation_conditions` (`id`, `grade`, `credit`, `course`, `so_course_categories_id`, `so_diplomas_id`) VALUES
(1, '9,12', '=1', NULL, 1, 1),
(2, '9,12', '+1', NULL, 2, 1),
(3, '11,12', '-1', NULL, 3, 1),
(4, '9,12', '+2', NULL, 4, 1),
(5, '9,12', '+1', NULL, 6, 1),
(6, '9,12', '+1', NULL, 7, 1),
(7, '9,12', '+1', NULL, 8, 1),
(8, '9,12', '+1', NULL, 9, 1),
(9, '10', '=0.5', 'GLC20', 10, 1),
(10, '10', '=0.5', 'GHV20', 10, 1),
(11, '9,12', '<1', NULL, 11, 1),
(12, '9,12', '<1', NULL, 12, 1),
(13, '9,12', '+1', NULL, 13, 1);

-- --------------------------------------------------------

--
-- 表的结构 `so_location`
--

CREATE TABLE `so_location` (
  `id` int(11) NOT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `label` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `so_location`
--

INSERT INTO `so_location` (`id`, `latitude`, `longitude`, `label`) VALUES
(1, 45.3436523, -75.7572005, 'CIA');

-- --------------------------------------------------------

--
-- 表的结构 `so_options`
--

CREATE TABLE `so_options` (
  `name` varchar(50) NOT NULL,
  `value_text` varchar(50) DEFAULT NULL,
  `value_int` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `so_schedule`
--

CREATE TABLE `so_schedule` (
  `id` int(11) NOT NULL,
  `start` date DEFAULT NULL,
  `end` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `repeat_day` tinyint(4) DEFAULT NULL COMMENT '0 monday 1 tuesday 2 Thursday 3 Wednesday 4 Thursday 5 Friday',
  `so_location_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- 表的结构 `so_semesters`
--

CREATE TABLE `so_semesters` (
  `id` int(11) NOT NULL,
  `semester` varchar(45) DEFAULT NULL,
  `open_for_register` tinyint(1) NOT NULL DEFAULT '0',
  `so_schedule_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `so_students`
--

CREATE TABLE `so_students` (
  `student_number` int(11) NOT NULL,
  `oen` varchar(45) DEFAULT NULL,
  `enter_grade` int(11) DEFAULT NULL,
  `enter_date` date DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `so_users_id` int(11) NOT NULL,
  `homeroom` int(11) NOT NULL,
  `so_diplomas_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- 表的结构 `so_teachers`
--

CREATE TABLE `so_teachers` (
  `so_users_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `so_teachers`
--

INSERT INTO `so_teachers` (`so_users_id`) VALUES
(2);

-- --------------------------------------------------------

--
-- 表的结构 `so_users`
--

CREATE TABLE `so_users` (
  `id` int(11) NOT NULL,
  `username` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `gender` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:not provided, 1: female, 2: male, 3:androgyne ',
  `email` varchar(45) DEFAULT NULL,
  `firstname` varchar(45) DEFAULT NULL,
  `lastname` varchar(45) DEFAULT NULL,
  `address1` varchar(45) DEFAULT NULL,
  `address2` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `state` varchar(45) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `role` int(11) NOT NULL COMMENT '1: student2: teacher3: admin',
  `token` char(96) DEFAULT NULL,
  `token_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `so_users`
--

INSERT INTO `so_users` (`id`, `username`, `password`, `gender`, `email`, `firstname`, `lastname`, `address1`, `address2`, `city`, `state`, `postal_code`, `telephone`, `role`, `token`, `token_expire`) VALUES
(1, 'root', MD5('aJb;#3Ge'), 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL, NULL);

INSERT INTO `so_users` (`id`, `username`, `password`, `gender`, `email`, `firstname`, `lastname`, `address1`, `address2`, `city`, `state`, `postal_code`, `telephone`, `role`, `token`, `token_expire`) VALUES
('2', 'DemoTeacher', MD5('123456'), '0', NULL, 'Demo', 'Teacher', NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, NULL);
--
-- Indexes for dumped tables
--

--
-- Indexes for table `so_courses`
--
ALTER TABLE `so_courses`
  ADD PRIMARY KEY (`course_code`),
  ADD KEY `fk_so_courses_so_course_categories1_idx` (`so_course_categories_id`);

--
-- Indexes for table `so_course_categories`
--
ALTER TABLE `so_course_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `so_course_schedule`
--
ALTER TABLE `so_course_schedule`
  ADD PRIMARY KEY (`so_schedule_id`,`so_courses_course_code`,`so_semesters_id`,`so_teachers_so_users_id`),
  ADD UNIQUE KEY `so_courses_course_code_2` (`so_courses_course_code`),
  ADD KEY `fk_so_course_schedule_so_courses1_idx` (`so_courses_course_code`),
  ADD KEY `fk_so_course_schedule_so_semesters1_idx` (`so_semesters_id`),
  ADD KEY `fk_so_course_schedule_so_teachers1_idx` (`so_teachers_so_users_id`);

--
-- Indexes for table `so_course_selections`
--
ALTER TABLE `so_course_selections`
  ADD PRIMARY KEY (`so_students_student_number`,`so_semesters_id`,`so_courses_course_code`),
  ADD KEY `fk_so_course_selections_so_semesters1_idx` (`so_semesters_id`),
  ADD KEY `fk_so_course_selections_so_courses1_idx` (`so_courses_course_code`);

--
-- Indexes for table `so_diplomas`
--
ALTER TABLE `so_diplomas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `so_exam_results`
--
ALTER TABLE `so_exam_results`
  ADD PRIMARY KEY (`so_courses_course_code`,`so_students_student_number`,`so_semesters_id`),
  ADD KEY `fk_so_exam_results_so_students1_idx` (`so_students_student_number`),
  ADD KEY `fk_so_exam_results_so_semesters1_idx` (`so_semesters_id`);

--
-- Indexes for table `so_exam_schedule`
--
ALTER TABLE `so_exam_schedule`
  ADD PRIMARY KEY (`so_schedule_id`,`so_semesters_id`,`so_courses_course_code`),
  ADD KEY `fk_so_exam_schedule_so_semesters1_idx` (`so_semesters_id`),
  ADD KEY `fk_so_exam_schedule_so_courses1_idx` (`so_courses_course_code`);

--
-- Indexes for table `so_graduation_conditions`
--
ALTER TABLE `so_graduation_conditions`
  ADD PRIMARY KEY (`id`,`so_course_categories_id`,`so_diplomas_id`),
  ADD KEY `fk_so_graduate_conditions_so_course_categories1_idx` (`so_course_categories_id`),
  ADD KEY `fk_so_graduate_conditions_so_diplomas1_idx` (`so_diplomas_id`);

--
-- Indexes for table `so_location`
--
ALTER TABLE `so_location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `so_options`
--
ALTER TABLE `so_options`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `so_schedule`
--
ALTER TABLE `so_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_so_schedule_so_location1_idx` (`so_location_id`);

--
-- Indexes for table `so_semesters`
--
ALTER TABLE `so_semesters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_so_semesters_so_schedule1_idx` (`so_schedule_id`);

--
-- Indexes for table `so_students`
--
ALTER TABLE `so_students`
  ADD PRIMARY KEY (`student_number`,`so_diplomas_id`),
  ADD KEY `fk_so_students_so_users1_idx` (`so_users_id`),
  ADD KEY `fk_so_students_so_teachers1_idx` (`homeroom`),
  ADD KEY `fk_so_students_so_diplomas1_idx` (`so_diplomas_id`);

--
-- Indexes for table `so_teachers`
--
ALTER TABLE `so_teachers`
  ADD PRIMARY KEY (`so_users_id`);

--
-- Indexes for table `so_users`
--
ALTER TABLE `so_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username_UNIQUE` (`username`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `so_schedule`
--
ALTER TABLE `so_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- 使用表AUTO_INCREMENT `so_semesters`
--
ALTER TABLE `so_semesters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- 使用表AUTO_INCREMENT `so_users`
--
ALTER TABLE `so_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- 限制导出的表
--

--
-- 限制表 `so_courses`
--
ALTER TABLE `so_courses`
  ADD CONSTRAINT `fk_so_courses_so_course_categories1` FOREIGN KEY (`so_course_categories_id`) REFERENCES `so_course_categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `so_course_schedule`
--
ALTER TABLE `so_course_schedule`
  ADD CONSTRAINT `fk_so_course_schedule_so_courses1` FOREIGN KEY (`so_courses_course_code`) REFERENCES `so_courses` (`course_code`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_so_course_schedule_so_schedule1` FOREIGN KEY (`so_schedule_id`) REFERENCES `so_schedule` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_so_course_schedule_so_semesters1` FOREIGN KEY (`so_semesters_id`) REFERENCES `so_semesters` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_so_course_schedule_so_teachers1` FOREIGN KEY (`so_teachers_so_users_id`) REFERENCES `so_teachers` (`so_users_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `so_course_selections`
--
ALTER TABLE `so_course_selections`
  ADD CONSTRAINT `fk_so_course_selections_so_courses1` FOREIGN KEY (`so_courses_course_code`) REFERENCES `so_courses` (`course_code`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_so_course_selections_so_semesters1` FOREIGN KEY (`so_semesters_id`) REFERENCES `so_semesters` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_so_course_selections_so_students1` FOREIGN KEY (`so_students_student_number`) REFERENCES `so_students` (`student_number`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `so_exam_results`
--
ALTER TABLE `so_exam_results`
  ADD CONSTRAINT `fk_so_exam_results_so_courses1` FOREIGN KEY (`so_courses_course_code`) REFERENCES `so_courses` (`course_code`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_so_exam_results_so_semesters1` FOREIGN KEY (`so_semesters_id`) REFERENCES `so_semesters` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_so_exam_results_so_students1` FOREIGN KEY (`so_students_student_number`) REFERENCES `so_students` (`student_number`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `so_exam_schedule`
--
ALTER TABLE `so_exam_schedule`
  ADD CONSTRAINT `fk_so_exam_schedule_so_courses1` FOREIGN KEY (`so_courses_course_code`) REFERENCES `so_courses` (`course_code`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_so_exam_schedule_so_schedule1` FOREIGN KEY (`so_schedule_id`) REFERENCES `so_schedule` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_so_exam_schedule_so_semesters1` FOREIGN KEY (`so_semesters_id`) REFERENCES `so_semesters` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `so_graduation_conditions`
--
ALTER TABLE `so_graduation_conditions`
  ADD CONSTRAINT `fk_so_graduate_conditions_so_course_categories1` FOREIGN KEY (`so_course_categories_id`) REFERENCES `so_course_categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_so_graduate_conditions_so_diplomas1` FOREIGN KEY (`so_diplomas_id`) REFERENCES `so_diplomas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `so_schedule`
--
ALTER TABLE `so_schedule`
  ADD CONSTRAINT `fk_so_schedule_so_location1` FOREIGN KEY (`so_location_id`) REFERENCES `so_location` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `so_semesters`
--
ALTER TABLE `so_semesters`
  ADD CONSTRAINT `fk_so_semesters_so_schedule1` FOREIGN KEY (`so_schedule_id`) REFERENCES `so_schedule` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `so_students`
--
ALTER TABLE `so_students`
  ADD CONSTRAINT `fk_so_students_so_diplomas1` FOREIGN KEY (`so_diplomas_id`) REFERENCES `so_diplomas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_so_students_so_teachers1` FOREIGN KEY (`homeroom`) REFERENCES `so_teachers` (`so_users_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_so_students_so_users1` FOREIGN KEY (`so_users_id`) REFERENCES `so_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `so_teachers`
--
ALTER TABLE `so_teachers`
  ADD CONSTRAINT `fk_so_teachers_so_users1` FOREIGN KEY (`so_users_id`) REFERENCES `so_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
