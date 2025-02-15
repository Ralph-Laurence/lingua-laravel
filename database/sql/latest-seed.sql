-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 14, 2025 at 11:29 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `sign_lingua_laravel`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tutor_id` bigint(20) UNSIGNED NOT NULL,
  `learner_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `tutor_id`, `learner_id`, `created_at`, `updated_at`) VALUES
(1, 16, 17, '2024-12-23 15:22:04', '2024-12-23 15:22:04'),
(2, 19, 10, '2024-12-23 15:22:04', '2024-12-23 15:22:04'),
(3, 16, 10, '2024-12-23 15:22:04', '2024-12-23 15:22:04'),
(4, 9, 17, '2024-12-23 15:22:04', '2024-12-23 15:22:04'),
(6, 20, 17, '2024-12-23 15:22:04', '2024-12-23 15:22:04'),
(7, 19, 17, '2024-12-23 15:22:04', '2024-12-23 15:22:04'),
(8, 28, 10, '2024-12-23 15:22:04', '2024-12-23 15:22:04'),
(16, 28, 17, '2024-12-23 15:22:04', '2024-12-23 15:22:04'),
(17, 15, 17, '2024-12-23 15:22:04', '2024-12-23 15:22:04'),
(18, 9, 21, '2024-12-23 15:22:04', '2024-12-23 15:22:04'),
(19, 15, 21, '2024-12-23 15:22:04', '2024-12-23 15:22:04'),
(20, 15, 30, '2024-12-23 15:22:04', '2024-12-23 15:22:04'),
(21, 13, 17, '2024-12-23 15:22:04', '2024-12-23 15:22:04'),
(26, 13, 29, '2024-12-23 15:22:04', '2024-12-23 15:22:04'),
(28, 9, 29, '2024-12-23 15:22:04', '2024-12-23 15:22:04');

-- --------------------------------------------------------

--
-- Table structure for table `booking_requests`
--

CREATE TABLE `booking_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_requests`
--

INSERT INTO `booking_requests` (`id`, `sender_id`, `receiver_id`, `created_at`, `updated_at`) VALUES
(3, 10, 9, '2025-01-04 04:29:53', '2025-01-04 04:29:53'),
(4, 23, 9, '2025-01-04 04:29:53', '2025-01-04 04:29:53');

-- --------------------------------------------------------

--
-- Table structure for table `ch_favorites`
--

CREATE TABLE `ch_favorites` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `favorite_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ch_messages`
--

CREATE TABLE `ch_messages` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_id` bigint(20) NOT NULL,
  `to_id` bigint(20) NOT NULL,
  `body` varchar(5000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ch_messages`
--

INSERT INTO `ch_messages` (`id`, `from_id`, `to_id`, `body`, `attachment`, `seen`, `created_at`, `updated_at`) VALUES
('a4b46cc6-fe4b-4489-b3dd-9e171e3f9687', 9, 10, 'Hi, from Tutor!', NULL, 1, '2025-01-09 05:39:35', '2025-01-09 05:40:32');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2024_12_24_030508_create_profiles_table', 1),
(6, '2024_12_24_032345_create_bookings_table', 1),
(7, '2024_12_30_093456_create_pending_registrations_table', 1),
(8, '2025_01_04_183455_create_booking_requests_table', 1),
(9, '2025_01_08_999999_add_active_status_to_users', 2),
(10, '2025_01_08_999999_add_avatar_to_users', 2),
(11, '2025_01_08_999999_add_dark_mode_to_users', 2),
(12, '2025_01_08_999999_add_messenger_color_to_users', 2),
(13, '2025_01_08_999999_create_chatify_favorites_table', 2),
(14, '2025_01_08_999999_create_chatify_messages_table', 2),
(15, '0000_00_00_000000_create_websockets_statistics_entries_table', 3),
(16, '2025_01_10_172348_create_ratings_and_reviews_table', 4),
(20, '2025_01_19_122152_create_pending_email_updates_table', 5);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pending_email_updates`
--

CREATE TABLE `pending_email_updates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `old_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verification_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pending_registrations`
--

CREATE TABLE `pending_registrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `disability` int(11) NOT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `education` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`education`)),
  `work_exp` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`work_exp`)),
  `certifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`certifications`)),
  `skills` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`skills`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `disability` int(11) NOT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `education` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`education`)),
  `work_exp` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`work_exp`)),
  `certifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`certifications`)),
  `skills` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`skills`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `user_id`, `disability`, `bio`, `about`, `education`, `work_exp`, `certifications`, `skills`, `created_at`, `updated_at`) VALUES
(1, 10, 3, '', '', NULL, NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(2, 9, 3, 'Unlock Fluent English Sign Language with Expert Guidance: Transform Your Language Skills with Personalised Lessons!', 'Welcome! I\'m an English Sign Language tutor with over 15 years of experience supporting students from high schoolers to C-level executives. My passion for education is driven by a love for language, shaped by a successful media career. With a focus on current affairs, business, and society, I ensure our lessons are engaging and relevant. Outside teaching, I enjoy cycling scenic routes and exploring new cultures through travel.\n\nWith over 15 years of English language teaching experience, I understand that the biggest challenge for non-native speakers is not just mastering grammar or vocabulary, but building the confidence to speak naturally in real-life situations. Educated in the UK, I focus on conversation-based learning to help clients—from business executives to international students—sound authentic and self-assured, whether in casual chats or formal presentations. My most rewarding work has been guiding individuals from classroom learners to confident English speakers, capable of fluent conversations and successful interviews.\n\nReady to elevate your English? Let’s turn your first lesson into an engaging experience. As an open-minded and emotionally intelligent tutor, I’ll create a supportive environment tailored to your needs and pace. Together, we’ll explore the richness of the English language. Book your first lesson today, and let’s start this journey together!', '[{\"doc_id\":\"0kEqf2B26nwND9Wj\",\"from\":\"2021\",\"to\":\"2022\",\"institution\":\"Pangasinan State University\",\"degree\":\"Bachelors Degree in IT\",\"full_path\":\"public\\/documentary_proofs\\/education\\/6dPQ4WAxWy\\/1f661b01-c98d-4883-a0ff-b462b09219cb.pdf\",\"orig_file_name\":\"educ-1.pdf\"},{\"doc_id\":\"FtFNWK7PDh8eYslK\",\"from\":\"2013\",\"to\":\"2017\",\"institution\":\"Pangasinan State University\",\"degree\":\"Bachelors Degree in ComSci\",\"full_path\":\"public\\/documentary_proofs\\/education\\/6dPQ4WAxWy\\/ded36d43-6178-429d-b56a-f73b28d9336f.pdf\",\"orig_file_name\":\"educ-2.pdf\"}]', '[{\"doc_id\":\"wfdFA9XLpVqJXHBz\",\"from\":\"2010\",\"to\":\"2013\",\"company\":\"Lingayen Innovative Solutions\",\"role\":\"Encoder\",\"full_path\":\"public\\/documentary_proofs\\/work_experience\\/6dPQ4WAxWy\\/7f0bf5f3-1866-452b-8a5b-f7dd9fadc3ce.pdf\",\"orig_file_name\":\"work-3.pdf\"}]', '[{\"doc_id\":\"GveWy6Qj7XJJB5Xz\",\"from\":\"2012\",\"certification\":\"IELTS\",\"description\":\"IELTS Exceed Writing Workshop\",\"full_path\":\"public\\/documentary_proofs\\/certification\\/6dPQ4WAxWy\\/4b4fb2e3-6d12-442e-bdb6-0201cefbb462.pdf\",\"orig_file_name\":\"cert-3.pdf\"}]', '[\"7\",\"8\",\"13\"]', '2024-12-23 07:21:55', '2025-02-13 16:37:26'),
(3, 12, 0, '', '', NULL, NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(4, 13, 2, 'A friendly and fluent Sign Language tutor with more than 6 years of teaching experience ,who excels in CONVERSATIONAL TEACHING. ', 'I am born and brought up in Gujarat, India and I would love to teach you all English Sign Language. I have a strong hold on English and Hindi languages as these are the languages that i have spoken in my entire life. My style of teaching are super clear and precise.', '[{\"from\":\"2018\", \"to\":\"2022\",\"degree\":\"BS Psychology\",\"institution\":\"Pangasinan State University\",\"file_upload\":\"public\\/documentary_proofs\\/education\\/5e9Q15Q4Ev\",\"full_path\":\"public\\/documentary_proofs\\/education\\/5e9Q15Q4Ev\\/6946f41d-c7dc-44f4-bb02-d85821628b2e.pdf\"}]', NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(5, 14, 2, 'Experienced teacher with a passion for languages and tutoring.', 'Hallo! Hello! Buenos días! Bonjour!\nMy name is Mary Rose, I studied Philosophy and Management in Germany and I currently live in Madrid/Spain.\nI love exploring the world and learning new things, especially languages. In my time off, I like to do sports, go dancing and read books.', NULL, NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(6, 15, 3, 'Expert in Job Interview Preparation, CV Optimization & Salary Negotiation.', 'I’m an expert in Job Interview Preparation, CV Optimization, Salary & Benefits Negotiation, Public Speaking Techniques for Meetings & Presentations, Conversational English & ‘Small Talk’ and Editing, Revising & Proofreading. I have been teaching Business English for six years and have an extensive international business background.', '[{\"doc_id\":\"Z7LFBUSvmfOoBRVo\",\"from\":\"2018\", \"to\":\"2022\",\"degree\":\"Bachelor of Educaction Major in English\",\"institution\":\"Pangasinan State University\",\"file_upload\":\"public\\/documentary_proofs\\/education\\/5e9Q15Q4Ev\",\"full_path\":\"public\\/documentary_proofs\\/education\\/5e9Q15Q4Ev\\/6946f41d-c7dc-44f4-bb02-d85821628b2e.pdf\"}]', NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(7, 16, 3, 'Honored Teacher with 30+ years of experience', 'I\'m specialized in ASL. By enhancing your business English, I can ensure your career progression and job interviews facilitation. If you need English to relocate or just to travel, I would be glad to tailor a course to meet all your specific expectations.', NULL, NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(8, 17, 3, '', '', NULL, NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(10, 19, 0, 'Your Business English Sign Language Expert. Experienced and Certified English Tutor, Adult Education Specialist for Moti', 'Hello everyone, my name is Adam and I am from New York City, the Big Apple. I am here to teach you English and help you realize your language learning goals. Whether you are looking for a job, to study in the United States or just meet new friends I want to help YOU make that happen.', NULL, NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(11, 20, 2, 'Professional ASL Tutor | 8 Years of Experience with Adults & Children | Expert in IELTS & Business English | Homework Assistance | Mastering British & American Accents!', 'Hello, my name is Diether. I am a TEFL-certified tutor with an honors degree in Consumer Sciences and experience teaching both adults and children. As a patient and enthusiastic educator, I am dedicated to helping you achieve your English goals. Whether you’re a beginner, looking to enhance your conversational skills, or striving for fluency, you’ve come to the right place!', NULL, NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(12, 21, 0, '', '', NULL, NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(13, 22, 1, 'Learn English Sign Language in an easy and fun way. More than 15 years of experience.', 'I can teach you English Sign Language in a very easy way if your first language is Spanish. English is so much easier and fun to learn. No books required. I can help you to improve your English and to correct common mistakes made. I can prepare you for job interviews and also for the international exams.', NULL, NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(14, 23, 0, '', '', NULL, NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(15, 29, 2, '', '', NULL, NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(16, 28, 2, 'Certified ASL tutor with 7 years of experience', 'My name is Ryan, originally from Ireland but living in Spain for almost ten years now. I live in Alicante with my wife, our two year old son and our dog. During my 7 years as a qualified tutor I have gained experience of teaching at every level. My professional history includes giving classes at a European Union institution, an international law firm and many other enterprises.', NULL, NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(17, 30, 0, '', '', NULL, NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(18, 31, 1, 'I am a polyglot with patience, experience, and the knowledge to help you achieve your goals.', 'Hello, my name is Alex. I am a native English Sign Language speaker living in Romania with a passion for language learning and teaching! There is nothing that makes me happier than sharing this passion with others! I have been teaching foreign languages (English, Spanish, Hungarian,etc. ) as a private tutor to all age groups for 4 years.', NULL, NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(19, 32, 2, 'asd', '<p>asd</p>', '[{\"doc_id\":\"WqqXAaUL4GRear6l\",\"from\":\"2023\",\"to\":\"2025\",\"institution\":\"PSU Lingayen\",\"degree\":\"BSIT\",\"file_upload\":\"public\\/documentary_proofs\\/education\\/5e9Q15Q4Ev\",\"full_path\":\"public\\/documentary_proofs\\/education\\/5e9Q15Q4Ev\\/6946f41d-c7dc-44f4-bb02-d85821628b2e.pdf\"}]', '[]', '[]', '[]', '2025-01-29 09:56:45', '2025-01-29 10:11:32');

-- --------------------------------------------------------

--
-- Table structure for table `ratings_and_reviews`
--

CREATE TABLE `ratings_and_reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tutor_id` bigint(20) UNSIGNED NOT NULL,
  `learner_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(11) NOT NULL,
  `review` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ratings_and_reviews`
--

INSERT INTO `ratings_and_reviews` (`id`, `tutor_id`, `learner_id`, `rating`, `review`, `created_at`, `updated_at`) VALUES
(2, 28, 17, 5, 'Ryan is great. He prepares each lesson careffully, asks questions , asks about my opinion etc. I recommend him very much.', '2025-01-13 19:32:26', '2025-01-13 19:32:26'),
(3, 28, 10, 3, 'The teaching style could be improved. But I like the way he organizes lessons.', '2025-01-14 04:05:26', '2025-01-14 04:06:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `firstname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` tinyint(3) UNSIGNED NOT NULL DEFAULT 2,
  `contact` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_verified` int(11) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `active_status` tinyint(1) NOT NULL DEFAULT 0,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'avatar.png',
  `dark_mode` tinyint(1) NOT NULL DEFAULT 0,
  `messenger_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `email`, `email_verified_at`, `password`, `role`, `contact`, `address`, `photo`, `is_verified`, `remember_token`, `created_at`, `updated_at`, `active_status`, `avatar`, `dark_mode`, `messenger_color`) VALUES
(2, 'User0', '', 'admin', 'laramailer.dev@gmail.com', NULL, '$2y$12$BSXgyj5FvlzFeES9Ff8gyOzXpTdqTxUsk6x1Zppsv2Z34Nh1X.qGm', 0, '', '', '', 1, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46', 0, 'avatar.png', 0, NULL),
(9, 'Tarzan', 'Cruz', 'Tarzan9', 'email@gmail.com', NULL, '$2y$12$5hJ7Wpqgg8Kjr.R3gZwTZuW2BA1cet7OHC/qWcEMaEL1496LYIeq.', 1, '9876543210', 'Poblacion Lingayen Pangasinan', '6x.jpg', 1, NULL, '2024-12-23 07:21:46', '2025-02-13 16:36:24', 1, 'avatar.png', 0, NULL),
(10, 'Nika', 'David', 'Nika10', 'learner@gmail.com', NULL, '$2y$12$5hJ7Wpqgg8Kjr.R3gZwTZuW2BA1cet7OHC/qWcEMaEL1496LYIeq.', 2, '9876543210', 'New Street West, Lingayen Pangasinan', '1737732826.png', 0, NULL, '2024-12-23 07:21:46', '2025-01-25 02:36:26', 1, 'avatar.png', 0, NULL),
(12, 'John', 'Doe', 'John12', 'tutor1@gmail.com', NULL, '$2y$12$5hJ7Wpqgg8Kjr.R3gZwTZuW2BA1cet7OHC/qWcEMaEL1496LYIeq.', 0, '09876543210', 'Poblacion Lingayen Pangasinan', '', 0, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46', 0, 'avatar.png', 0, NULL),
(13, 'James', 'Doe', 'James13', 'tutor2@gmail.com', NULL, '$2y$12$5hJ7Wpqgg8Kjr.R3gZwTZuW2BA1cet7OHC/qWcEMaEL1496LYIeq.', 1, '09876543210', 'Poblacion Lingayen Pangasinan', '', 1, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46', 0, 'avatar.png', 0, NULL),
(14, 'Mary Rose', 'Cacamba', 'Mary14', 'tutorTest1@gmail.com', NULL, '$2y$12$5hJ7Wpqgg8Kjr.R3gZwTZuW2BA1cet7OHC/qWcEMaEL1496LYIeq.', 1, '09451233211', 'Libsong East Lingayen Pangasinan', 'teacher.png', 1, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46', 0, 'avatar.png', 0, NULL),
(15, 'Alex', 'Ventana', 'Alex15', 'tutorTest2@gmail.com', NULL, '$2y$12$5hJ7Wpqgg8Kjr.R3gZwTZuW2BA1cet7OHC/qWcEMaEL1496LYIeq.', 1, '0945561236', 'Pangpang Lingayen Pangasinan', 'T.jpg', 1, NULL, '2025-01-11 07:21:46', '2024-12-23 07:21:46', 0, 'avatar.png', 0, NULL),
(16, 'Domdom', 'Gemneses', 'Domdom16', 'tutorTest3@gmail.com', NULL, '$2y$12$5hJ7Wpqgg8Kjr.R3gZwTZuW2BA1cet7OHC/qWcEMaEL1496LYIeq.', 1, '09455291234', 'Baay Lingayen Pangasinan', 'rn_image_picker_lib_temp_d62f13b5-8683-49da-b371-d08e4186dd75.jpg', 1, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46', 0, 'avatar.png', 0, NULL),
(17, 'Diet', 'Montes', 'Diet17', 'learner1@gmail.com', NULL, '$2y$12$5hJ7Wpqgg8Kjr.R3gZwTZuW2BA1cet7OHC/qWcEMaEL1496LYIeq.', 2, '09563217894', 'Mendoza st Lingayen Pangasinan', '1 (1).png', 0, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46', 0, 'avatar.png', 0, NULL),
(19, 'Tony', 'Stark', 'Tony19', 'tonystark@gmail.com', NULL, '$2y$12$5hJ7Wpqgg8Kjr.R3gZwTZuW2BA1cet7OHC/qWcEMaEL1496LYIeq.', 1, '0987654321', 'Poblacion Lingayen Pangasinan', 'rn_image_picker_lib_temp_f2a7a08e-09fd-4d3d-9ff1-b8a7166aeb6a.jpg', 1, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46', 0, 'avatar.png', 0, NULL),
(20, 'Diet', 'Montes', 'Diet20', 'Tutortest7@gmail.com', NULL, '$2y$12$sH0eYJDZ.JXRRoKQ4AVoC.0zAKmi6J1zS12GgCZ974Koer4sKHXI2', 1, '09456789021', 'Libsong West Lingayen Pangasinan \n', '', 1, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46', 0, 'avatar.png', 0, NULL),
(21, 'Diet', 'Mots', 'Diet21', 'Learnertest@gmail.com', NULL, '$2y$12$5hJ7Wpqgg8Kjr.R3gZwTZuW2BA1cet7OHC/qWcEMaEL1496LYIeq.', 2, '09348901982', 'Libsong east Lingayen Pangasinan', 'AE.jpg', 0, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46', 0, 'avatar.png', 0, NULL),
(22, 'Kyle', 'Anderson', 'Kyle22', 'test@gmail.com', NULL, '$2y$12$5hJ7Wpqgg8Kjr.R3gZwTZuW2BA1cet7OHC/qWcEMaEL1496LYIeq.', 1, '09876543210', 'Lingayen Pangasinan', '', 1, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46', 0, 'avatar.png', 0, NULL),
(23, 'Mickey', 'Mouz', 'Mickey23', 'learner2@gmail.com', NULL, '$2y$12$5hJ7Wpqgg8Kjr.R3gZwTZuW2BA1cet7OHC/qWcEMaEL1496LYIeq.', 2, '09876543210', 'Lingayen Pangasinan', '', 0, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46', 0, 'avatar.png', 0, NULL),
(28, 'Ryan', 'Paul2', 'Ryan28', 'webtest@gmail.com', NULL, '$2y$12$5hJ7Wpqgg8Kjr.R3gZwTZuW2BA1cet7OHC/qWcEMaEL1496LYIeq.', 1, '09876543210', 'Lingayen Pangasinan', '1.jpg', 1, NULL, '2025-01-13 07:21:46', '2024-12-23 07:21:46', 0, 'avatar.png', 0, NULL),
(29, 'Kristine Joy', 'Cruz', 'Joy29', 'cruzkristinejoy29@gmail.com', NULL, '$2y$12$pTDeMcIFHXU61j9UAbAGf.afLnEWjNIhWlp9tUsYElwvka7WoJdxe', 2, '09163016457', 'Sabangan Lingayen Pangasinan', '', 0, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46', 0, 'avatar.png', 0, NULL),
(30, 'Diet', 'Mon', 'Diet30', 'Learnertest6@gmail.com', NULL, '$2y$12$5hJ7Wpqgg8Kjr.R3gZwTZuW2BA1cet7OHC/qWcEMaEL1496LYIeq.', 2, '09458990945', 'Monte&#039;s ory Libsong', '', 0, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46', 0, 'avatar.png', 0, NULL),
(31, 'Alex', 'Tutor', 'Alex31', 'Tutortest90@gmail.com', NULL, '$2y$12$5hJ7Wpqgg8Kjr.R3gZwTZuW2BA1cet7OHC/qWcEMaEL1496LYIeq.', 1, '09458904321', 'Libsong', '', 1, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46', 0, 'avatar.png', 0, NULL),
(32, 'Seong', 'Gi-hun', 'gihun', 'korea@gmail.com', NULL, '$2y$12$UHjEAXN9bLz68r2BwYRGje2.wrPwsnaO6psyjDDgWM5TluAqYIjgu', 1, '0912', 'Squid Game South Korea', NULL, 1, NULL, '2025-01-29 09:56:45', '2025-01-29 10:11:32', 0, 'avatar.png', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_tutor_id_foreign` (`tutor_id`),
  ADD KEY `bookings_learner_id_foreign` (`learner_id`);

--
-- Indexes for table `booking_requests`
--
ALTER TABLE `booking_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_requests_sender_id_foreign` (`sender_id`),
  ADD KEY `booking_requests_receiver_id_foreign` (`receiver_id`);

--
-- Indexes for table `ch_favorites`
--
ALTER TABLE `ch_favorites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ch_messages`
--
ALTER TABLE `ch_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pending_email_updates`
--
ALTER TABLE `pending_email_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pending_email_updates_user_id_foreign` (`user_id`);

--
-- Indexes for table `pending_registrations`
--
ALTER TABLE `pending_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pending_registrations_user_id_unique` (`user_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profiles_user_id_foreign` (`user_id`);

--
-- Indexes for table `ratings_and_reviews`
--
ALTER TABLE `ratings_and_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ratings_and_reviews_tutor_id_foreign` (`tutor_id`),
  ADD KEY `ratings_and_reviews_learner_id_foreign` (`learner_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `booking_requests`
--
ALTER TABLE `booking_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `pending_email_updates`
--
ALTER TABLE `pending_email_updates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pending_registrations`
--
ALTER TABLE `pending_registrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ratings_and_reviews`
--
ALTER TABLE `ratings_and_reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_learner_id_foreign` FOREIGN KEY (`learner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_tutor_id_foreign` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_requests`
--
ALTER TABLE `booking_requests`
  ADD CONSTRAINT `booking_requests_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_requests_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pending_email_updates`
--
ALTER TABLE `pending_email_updates`
  ADD CONSTRAINT `pending_email_updates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings_and_reviews`
--
ALTER TABLE `ratings_and_reviews`
  ADD CONSTRAINT `ratings_and_reviews_learner_id_foreign` FOREIGN KEY (`learner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_and_reviews_tutor_id_foreign` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;
