-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 24, 2024 at 04:26 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `sign_lingua`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(444) NOT NULL,
  `password` varchar(444) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `learner_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `tutor_id`, `learner_id`, `status`, `created_at`) VALUES
(1, 16, 17, 1, '2024-12-05 15:42:18'),
(2, 19, 10, 1, '2024-12-08 12:45:54'),
(3, 16, 10, 1, '2024-12-08 12:46:21'),
(4, 9, 17, 1, '2024-12-08 15:45:43'),
(5, 9, 17, 1, '2024-12-08 15:45:49'),
(6, 20, 17, 1, '2024-12-08 15:46:08'),
(7, 19, 17, 1, '2024-12-08 15:48:01'),
(8, 28, 10, 1, '2024-12-17 14:10:57'),
(22, 13, 17, 1, '2024-12-18 10:11:45'),
(21, 13, 17, 1, '2024-12-18 02:32:37'),
(20, 15, 30, 1, '2024-12-17 15:30:14'),
(19, 15, 21, 1, '2024-12-17 15:23:53'),
(18, 9, 21, 1, '2024-12-17 15:23:05'),
(17, 15, 17, 1, '2024-12-17 15:15:14'),
(16, 28, 17, 1, '2024-12-17 15:01:59'),
(23, 13, 17, 1, '2024-12-18 10:16:11'),
(24, 9, 29, 1, '2024-12-18 10:36:21'),
(25, 9, 29, 1, '2024-12-18 10:36:39'),
(26, 13, 29, 1, '2024-12-18 10:36:51'),
(27, 13, 29, 1, '2024-12-18 10:37:00'),
(28, 9, 29, 1, '2024-12-18 10:37:14');

-- --------------------------------------------------------

--
-- Table structure for table `tutor_files`
--

CREATE TABLE `tutor_files` (
  `id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL COMMENT 'user id with type 1',
  `src` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(555) NOT NULL,
  `lastname` varchar(555) NOT NULL,
  `email` varchar(555) NOT NULL,
  `password` text NOT NULL,
  `age` int(11) NOT NULL,
  `gender` varchar(123) NOT NULL,
  `contact` varchar(555) NOT NULL,
  `address` text NOT NULL,
  `user_type` int(11) NOT NULL COMMENT '1=tutor,2=learner',
  `fluency` int(11) NOT NULL DEFAULT 0 COMMENT '0 - Beginner\r\n1 - Intermediate\r\n2 - Advanced\r\n3 - Fluent',
  `resume` text NOT NULL,
  `bio` varchar(180) NOT NULL,
  `about_me` text NOT NULL,
  `profile_photo` text NOT NULL,
  `is_verified` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `password`, `age`, `gender`, `contact`, `address`, `user_type`, `fluency`, `resume`, `bio`, `about_me`, `profile_photo`, `is_verified`, `status`, `created_at`) VALUES
(10, 'Nika', 'David', 'learner@gmail.com', '12345', 18, 'Female', '09876543210', 'New Street West, Lingayen Pangasinan', 2, 0, '', '', '', '../uploads/profiles/img3.jpg', 0, 1, '2024-11-26 21:03:29'),
(9, 'Tarzan', 'Cruz', 'email@gmail.com', '12345', 25, 'male', '09876543210', 'Poblacion Lingayen Pangasinan', 1, 1, '', 'Unlock Fluent English Sign Language with Expert Guidance: Transform Your Language Skills with Personalised Lessons!', 'Welcome! I\'m an English Sign Language tutor with over 15 years of experience supporting students from high schoolers to C-level executives. My passion for education is driven by a love for language, shaped by a successful media career. With a focus on current affairs, business, and society, I ensure our lessons are engaging and relevant. Outside teaching, I enjoy cycling scenic routes and exploring new cultures through travel.\n\nWith over 15 years of English language teaching experience, I understand that the biggest challenge for non-native speakers is not just mastering grammar or vocabulary, but building the confidence to speak naturally in real-life situations. Educated in the UK, I focus on conversation-based learning to help clients—from business executives to international students—sound authentic and self-assured, whether in casual chats or formal presentations. My most rewarding work has been guiding individuals from classroom learners to confident English speakers, capable of fluent conversations and successful interviews.\n\nReady to elevate your English? Let’s turn your first lesson into an engaging experience. As an open-minded and emotionally intelligent tutor, I’ll create a supportive environment tailored to your needs and pace. Together, we’ll explore the richness of the English language. Book your first lesson today, and let’s start this journey together!', '../uploads/profiles/6x.jpg', 1, 1, '2024-11-26 19:41:19'),
(12, 'John', 'Doe', 'tutor1@gmail.com', '12345', 22, 'Male', '09876543210', 'Poblacion Lingayen Pangasinan', 0, 0, '', '', '', '', 0, 1, '2024-12-04 13:10:59'),
(13, 'James', 'Doe', 'tutor2@gmail.com', '12345', 22, 'Male', '09876543210', 'Poblacion Lingayen Pangasinan', 1, 0, '', 'A friendly and fluent Sign Language tutor with more than 6 years of teaching experience ,who excels in CONVERSATIONAL TEACHING. ', 'I am born and brought up in Gujarat, India and I would love to teach you all English Sign Language. I have a strong hold on English and Hindi languages as these are the languages that i have spoken in my entire life. My style of teaching are super clear and precise.', '', 1, 1, '2024-12-04 13:13:19'),
(14, 'Mary Rose', 'Cacamba', 'tutorTest1@gmail.com', '123456', 25, 'Female', '09451233211', 'Libsong East Lingayen Pangasinan', 1, 3, '', 'Experienced teacher with a passion for languages and tutoring.', 'Hallo! Hello! Buenos días! Bonjour!\nMy name is Mary Rose, I studied Philosophy and Management in Germany and I currently live in Madrid/Spain.\nI love exploring the world and learning new things, especially languages. In my time off, I like to do sports, go dancing and read books.', '../uploads/profiles/teacher.png', 1, 1, '2024-12-04 14:48:26'),
(15, 'Alex', 'Ventana', 'tutorTest2@gmail.com', '123456', 26, 'Female', '0945561236', 'Pangpang Lingayen Pangasinan', 1, 2, '', 'Expert in Job Interview Preparation, CV Optimization & Salary Negotiation.', 'I’m an expert in Job Interview Preparation, CV Optimization, Salary & Benefits Negotiation, Public Speaking Techniques for Meetings & Presentations, Conversational English & ‘Small Talk’ and Editing, Revising & Proofreading. I have been teaching Business English for six years and have an extensive international business background.', '../uploads/profiles/T.jpg', 1, 1, '2024-12-04 14:51:09'),
(16, 'Domdom', 'Gemneses', 'tutorTest3@gmail.com', '123456', 29, 'Male', '09455291234', 'Baay Lingayen Pangasinan', 1, 3, '', 'Honored Teacher with 30+ years of experience', 'I\'m specialized in ASL. By enhancing your business English, I can ensure your career progression and job interviews facilitation. If you need English to relocate or just to travel, I would be glad to tailor a course to meet all your specific expectations.', '../uploads/profiles/rn_image_picker_lib_temp_d62f13b5-8683-49da-b371-d08e4186dd75.jpg', 1, 1, '2024-12-04 14:54:23'),
(17, 'Diet', 'Montes', 'learner1@gmail.com', '123456', 30, 'Male', '09563217894', 'Mendoza st Lingayen Pangasinan', 2, 0, '', '', '', '../uploads/profiles/1 (1).png', 0, 1, '2024-12-04 14:56:31'),
(18, 'asdasd', 'asd', 'test@test.com', '12345', 23, 'Male', '123123123123', 'asdasdasd', 1, 0, '../uploads/files/sample pdf file download - Google Search.pdf', 'English, Math teacher from California for children/teenagers', 'Experience: 5 years teaching full-time in a VIP-level private school for children (LOS ANGELES, CALIFORNIA) +7 years full-time employment as a private online teacher. I teach ALL levels of Mathematics and the English language. I work with children of all ages, all levels and all nationalities.', '', -1, 1, '2024-12-05 17:31:11'),
(19, 'Tony', 'Stark', 'tonystark@gmail.com', '12345', 23, 'Male', '0987654321', 'Poblacion Lingayen Pangasinan', 1, 2, '../uploads/files/sample pdf file download - Google Search.pdf', 'Your Business English Sign Language Expert. Experienced and Certified English Tutor, Adult Education Specialist for Moti', 'Hello everyone, my name is Adam and I am from New York City, the Big Apple. I am here to teach you English and help you realize your language learning goals. Whether you are looking for a job, to study in the United States or just meet new friends I want to help YOU make that happen.', '../uploads/profiles/rn_image_picker_lib_temp_f2a7a08e-09fd-4d3d-9ff1-b8a7166aeb6a.jpg', 1, 1, '2024-12-05 17:33:49'),
(20, 'Diet', 'Montes', 'Tutortest7@gmail.com', 'Montes1234', 28, 'Male', '09456789021', 'Libsong West Lingayen Pangasinan \n', 1, 3, '../uploads/files/Screenshot_20241204-065916.png', 'Professional ASL Tutor | 8 Years of Experience with Adults & Children | Expert in IELTS & Business English | Homework Assistance | Mastering British & American Accents!', 'Hello, my name is Diether. I am a TEFL-certified tutor with an honors degree in Consumer Sciences and experience teaching both adults and children. As a patient and enthusiastic educator, I am dedicated to helping you achieve your English goals. Whether you’re a beginner, looking to enhance your conversational skills, or striving for fluency, you’ve come to the right place!', '', 1, 1, '2024-12-06 02:19:50'),
(21, 'Diet', 'Mots', 'Learnertest@gmail.com', '12345', 15, 'Male', '09348901982', 'Libsong east Lingayen Pangasinan', 2, 0, '', '', '', '../uploads/profiles/AE.jpg', 0, 1, '2024-12-06 02:24:07'),
(22, 'Kyle', 'Anderson', 'test@gmail.com', '12345', 33, 'Male', '09876543210', 'Lingayen Pangasinan', 1, 0, '', 'Learn English Sign Language in an easy and fun way. More than 15 years of experience.', 'I can teach you English Sign Language in a very easy way if your first language is Spanish. English is so much easier and fun to learn. No books required. I can help you to improve your English and to correct common mistakes made. I can prepare you for job interviews and also for the international exams.', '', 1, 1, '2024-12-08 14:21:35'),
(23, 'Mickey', 'Mouz', 'learner2@gmail.com', '1234567', 33, 'Male', '09876543210', 'Lingayen Pangasinan', 2, 0, '', '', '', '', 0, 1, '2024-12-08 14:30:05'),
(29, 'Kristine Joy', 'Cruz', 'cruzkristinejoy29@gmail.com', 'Joy_021428', 22, 'Male', '09163016457', 'Sabangan Lingayen Pangasinan', 2, 0, '', '', '', '', 0, 1, '2024-12-17 04:05:55'),
(28, 'Ryan', 'Paul2', 'webtest@gmail.com', '12345', 25, 'Male', '09876543210', 'Lingayen Pangasinan', 1, 3, '../uploads/files/resume-sample.pdf', 'Certified ASL tutor with 7 years of experience', 'My name is Ryan, originally from Ireland but living in Spain for almost ten years now. I live in Alicante with my wife, our two year old son and our dog. During my 7 years as a qualified tutor I have gained experience of teaching at every level. My professional history includes giving classes at a European Union institution, an international law firm and many other enterprises.', '../uploads/profiles/1.jpg', 1, 1, '2024-12-16 16:04:52'),
(30, 'Diey', 'Mon', 'Learnertest6@gmail.com', '123456', 26, 'Female', '09458990945', 'Monte&#039;s ory Libsong', 2, 0, '', '', '', '', 0, 1, '2024-12-17 15:29:49'),
(31, 'Alex', 'Tutor', 'Tutortest90@gmail.com', '1234567', 23, 'Male', '09458904321', 'Libsong', 1, 0, '../uploads/files/Screenshot_20241217-221000.png', 'I am a polyglot with patience, experience, and the knowledge to help you achieve your goals.', 'Hello, my name is Alex. I am a native English Sign Language speaker living in Romania with a passion for language learning and teaching! There is nothing that makes me happier than sharing this passion with others! I have been teaching foreign languages (English, Spanish, Hungarian,etc. ) as a private tutor to all age groups for 4 years.', '', 1, 1, '2024-12-18 02:04:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tutor_files`
--
ALTER TABLE `tutor_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `tutor_files`
--
ALTER TABLE `tutor_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
COMMIT;
