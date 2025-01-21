-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2025 at 06:02 PM
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
(1, 9, 17, '2024-12-23 15:22:04', '2024-12-23 15:22:04'),
(2, 15, 17, '2024-12-23 15:22:04', '2024-12-23 15:22:04'),
(3, 9, 29, '2024-12-23 15:22:04', '2024-12-23 15:22:04'),;

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
(1, 10, 9, '2025-01-04 04:29:53', '2025-01-04 04:29:53');

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
(8, '2025_01_04_183455_create_booking_requests_table', 1);

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
-- Table structure for table `pending_registrations`
--

CREATE TABLE `pending_registrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `fluency` int(11) NOT NULL,
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
  `fluency` int(11) NOT NULL,
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

INSERT INTO `profiles` (`id`, `user_id`, `fluency`, `bio`, `about`, `education`, `work_exp`, `certifications`, `skills`, `created_at`, `updated_at`) VALUES
(1, 10, 3, '', '', NULL, NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(2, 9, 3, 'Unlock Fluent English Sign Language with Expert Guidance: Transform Your Language Skills with Personalised Lessons!', 'Welcome! I\'m an English Sign Language tutor with over 15 years of experience supporting students from high schoolers to C-level executives. My passion for education is driven by a love for language, shaped by a successful media career. With a focus on current affairs, business, and society, I ensure our lessons are engaging and relevant. Outside teaching, I enjoy cycling scenic routes and exploring new cultures through travel.\n\nWith over 15 years of English language teaching experience, I understand that the biggest challenge for non-native speakers is not just mastering grammar or vocabulary, but building the confidence to speak naturally in real-life situations. Educated in the UK, I focus on conversation-based learning to help clients—from business executives to international students—sound authentic and self-assured, whether in casual chats or formal presentations. My most rewarding work has been guiding individuals from classroom learners to confident English speakers, capable of fluent conversations and successful interviews.\n\nReady to elevate your English? Let’s turn your first lesson into an engaging experience. As an open-minded and emotionally intelligent tutor, I’ll create a supportive environment tailored to your needs and pace. Together, we’ll explore the richness of the English language. Book your first lesson today, and let’s start this journey together!', '[{\"from\":\"2012\", \"to\":\"2016\",\"degree\":\"Bachelors Degree in PolSci\",\"institution\":\"Pangasinan State University\"},{\"from\":\"2017\", \"to\":\"2021\",\"degree\":\"Masters Degree in Mass Comm\",\"institution\":\"University of Oxford\"}]', '[{\"from\":\"2013\", \"to\":\"2016\",\"role\":\"Maintenance Service Crew\",\"company\":\"Amianan Motors\"},{\"from\":\"2016\", \"to\":\"2018\",\"role\":\"Fryer\",\"company\":\"Jollibee Foods\"}]', '[{\"year\":\"2010\",\"certification\":\"IELTS\",\"description\":\"IELTS Exceed Writing Workshop\"}]', '[\"8\",\"11\",\"14\"]', '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(3, 15, 3, 'Expert in Job Interview Preparation, CV Optimization & Salary Negotiation.', 'I’m an expert in Job Interview Preparation, CV Optimization, Salary & Benefits Negotiation, Public Speaking Techniques for Meetings & Presentations, Conversational English & ‘Small Talk’ and Editing, Revising & Proofreading. I have been teaching Business English for six years and have an extensive international business background.', '[{\"from\":\"2018\", \"to\":\"2022\",\"degree\":\"Bachelor of Educaction Major in English\",\"institution\":\"Pangasinan State University\"}]', NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(4, 17, 3, '', '', NULL, NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(5, 29, 2, '', '', NULL, NULL, NULL, NULL, '2024-12-23 07:21:55', '2024-12-23 07:21:55'),
(6, 34, 3, 'asdasd', '<p>asdasd</p>', '[{\"from\":\"2025\",\"to\":\"2025\",\"institution\":\"PSU Lingayen\",\"degree\":\"BSIT\",\"file_upload\":\"public\\/documentary_proofs\\/education\\/1LmGdmqMkR\",\"full_path\":\"public\\/documentary_proofs\\/education\\/1LmGdmqMkR\\/9551015d-48e3-4cb8-a88a-710b6adbbc79.pdf\"}]', '[]', '[]', '[]', '2025-01-06 08:50:28', '2025-01-06 08:55:50');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `email`, `email_verified_at`, `password`, `role`, `contact`, `address`, `photo`, `is_verified`, `remember_token`, `created_at`, `updated_at`) VALUES
(2, 'User0', '', 'admin', 'laramailer.dev@gmail.com', NULL, '$2y$12$BSXgyj5FvlzFeES9Ff8gyOzXpTdqTxUsk6x1Zppsv2Z34Nh1X.qGm', 0, '', '', '', 1, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46'),
(9, 'Mary Rose', 'Camba', 'rose', 'mrrscamba@gmail.com', NULL, '$2y$12$507OdVIRXrghTITn1uKHU.lAOM2KzhbuRHrlklc5DD2iVM641zzAq', 1, '09876543210', 'Poblacion Lingayen Pangasinan', 'rose.png', 1, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46'),
(10, 'Liesley', 'Ventanilla', 'Lexi', 'abalosenzo665@gmail.com', NULL, '$2y$12$507OdVIRXrghTITn1uKHU.lAOM2KzhbuRHrlklc5DD2iVM641zzAq', 2, '09876543210', 'New Street West, Lingayen Pangasinan', 'liesley.jpg', 0, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46'),
(15, 'Dominic', 'Jimenez', 'Alex15', 'noahmadriaga005@gmail.com', NULL, '$2y$12$507OdVIRXrghTITn1uKHU.lAOM2KzhbuRHrlklc5DD2iVM641zzAq', 1, '0945561236', 'Pangpang Lingayen Pangasinan', 'doms.png', 1, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46'),
(17, 'Diet', 'Montes', 'Diet17', 'montezdiether@gmail.com', NULL, '$2y$12$507OdVIRXrghTITn1uKHU.lAOM2KzhbuRHrlklc5DD2iVM641zzAq', 2, '09563217894', 'Mendoza st Lingayen Pangasinan', '1 (1).png', 0, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46'),
(29, 'Kristine Joy', 'Cruz', 'Joy29', 'cruzkristinejoy29@gmail.com', NULL, '$2y$12$507OdVIRXrghTITn1uKHU.lAOM2KzhbuRHrlklc5DD2iVM641zzAq', 2, '09163016457', 'Sabangan Lingayen Pangasinan', 'tine.png', 0, NULL, '2024-12-23 07:21:46', '2024-12-23 07:21:46'),
(34, 'FirstName', 'Lastname', 'stickfiggaz', 'bluescreen512@gmail.com', NULL, '$2y$12$5MojqBSxelSQE.kRQ6gOjeUMO8Utr8eqftUalBPfCIbwLYPmzHXc6', 1, '0911', '#123 House', NULL, 1, NULL, '2025-01-06 08:50:28', '2025-01-06 08:55:50');

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pending_registrations`
--
ALTER TABLE `pending_registrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

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
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;
