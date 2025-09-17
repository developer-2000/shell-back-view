-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: mysql:3306
-- Время создания: Сен 05 2025 г., 08:01
-- Версия сервера: 8.0.32
-- Версия PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `db_docker`
--

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `manager_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `required` tinyint(1) NOT NULL,
  `groups` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `manager_id`, `name`, `required`, `groups`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 5, 'Generic', 1, '[\"RBA\"]', '2024-11-12 10:35:34', '2024-11-12 10:35:34', NULL),
(2, 4, 'Car Wash', 1, '[\"RBA\"]', '2024-11-12 10:35:34', '2024-11-12 10:35:34', NULL),
(3, 4, 'Food', 1, '[\"RBA\"]', '2024-11-12 10:35:34', '2024-11-12 10:35:34', NULL),
(4, 4, 'Fuel', 1, '[\"RBA\"]', '2024-11-12 10:35:34', '2024-11-12 10:35:34', NULL),
(5, 5, 'Food Outside', 1, '[\"RBA\"]', '2024-11-12 10:35:34', '2024-11-12 10:35:34', NULL),
(6, 5, 'Trumf', 1, '[\"RBA\"]', '2024-11-12 10:35:34', '2024-11-18 10:05:50', '2024-11-18 10:05:50'),
(7, 5, 'Store', 1, '[\"RBA\"]', '2024-11-12 10:35:34', '2024-11-12 10:35:34', NULL),
(8, 4, 'Loality / Trumf', 1, '[\"RBA\"]', '2024-11-12 10:35:34', '2024-11-18 10:05:43', NULL),
(9, 5, 'Electric Car Station', 1, '[\"RBA\"]', '2024-11-12 10:35:34', '2024-11-12 10:35:34', NULL),
(10, 4, 'New Store Concept', 1, '[\"RBA\"]', '2024-11-12 10:35:34', '2024-11-12 10:35:34', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `company_planners`
--

CREATE TABLE `company_planners` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `surfaces` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `company_planners`
--

INSERT INTO `company_planners` (`id`, `user_id`, `surfaces`, `created_at`, `updated_at`) VALUES
(2, 6, '[\"1_5\", \"10_5\", \"8_5\", \"3_2\"]', '2024-11-12 13:15:14', '2024-12-12 11:46:49'),
(3, 8, '[\"3_1\", \"10_1\", \"8_1\", \"2_2\", \"1_3\"]', '2024-11-12 15:43:33', '2024-11-15 08:55:25');

-- --------------------------------------------------------

--
-- Структура таблицы `designs`
--

CREATE TABLE `designs` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `designs`
--

INSERT INTO `designs` (`id`, `name`, `category_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Cherry compote', 3, '2024-11-12 10:35:34', '2024-11-12 16:08:13', NULL),
(2, 'Diesel fuel', 4, '2024-11-12 10:35:34', '2024-11-12 16:06:57', NULL),
(3, 'Tasty hotdog', 3, '2024-11-12 10:35:34', '2024-11-12 16:06:22', NULL),
(4, 'Diesel fuel', 2, '2025-09-03 11:00:18', '2025-09-03 15:53:19', '2025-09-03 15:53:19');

-- --------------------------------------------------------

--
-- Структура таблицы `design_chats`
--

CREATE TABLE `design_chats` (
  `id` bigint UNSIGNED NOT NULL,
  `messages` json NOT NULL,
  `socket_users_ids` json NOT NULL,
  `job_timer_user_ids` json NOT NULL,
  `send_email_user_ids` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `design_chats`
--

INSERT INTO `design_chats` (`id`, `messages`, `socket_users_ids`, `job_timer_user_ids`, `send_email_user_ids`, `created_at`, `updated_at`, `deleted_at`) VALUES
(27, '[{\"id\": 1, \"text\": null, \"url_file\": \"\", \"file_name\": \"a_image.jpg\", \"file_size\": \"0.08 Mb\", \"sender_id\": 1, \"type_file\": \"Sketch\", \"url_images\": {\"w_200\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-1/images/w_200/67a2146a80ff6/a_image.jpg\", \"w_500\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-1/images/w_500/67a2146a291c3/a_image.jpg\", \"original\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-1/images/original/67a21468abe7d/a_image.jpg\"}, \"create_date\": \"2025-02-04T13:21:44.700381Z\", \"rating_file\": \"Approved\", \"delete_message\": false, \"type_extension\": \"image\", \"update_message\": false, \"comment_mess_id\": null, \"who_read_messages\": [1, 2]}, {\"id\": 2, \"text\": null, \"url_file\": \"\", \"file_name\": \"a_image.jpg\", \"file_size\": \"0.08 Mb\", \"sender_id\": 1, \"type_file\": \"HQ\", \"url_images\": {\"w_200\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-2/images/w_200/67b49919e08c0/a_image.jpg\", \"w_500\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-2/images/w_500/67b499197e235/a_image.jpg\", \"original\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-2/images/original/67b49917e346a/a_image.jpg\"}, \"create_date\": \"2025-02-18T14:28:39.927398Z\", \"rating_file\": null, \"delete_message\": false, \"type_extension\": \"image\", \"update_message\": false, \"comment_mess_id\": null, \"who_read_messages\": [1]}]', '[1, 2]', '[1]', '[]', '2025-02-04 13:19:49', '2025-02-18 14:28:42', NULL),
(28, '[{\"id\": 1, \"text\": null, \"url_file\": \"\", \"file_name\": \"rainbow-end-road-landscape.jpg\", \"file_size\": \"13.92 Mb\", \"sender_id\": 1, \"type_file\": \"Sketch\", \"url_images\": {\"w_200\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-1/images/w_200/67a214b407b6a/rainbow-end-road-landscape.jpg\", \"w_500\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-1/images/w_500/67a214b302f6d/rainbow-end-road-landscape.jpg\", \"original\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-1/images/original/67a214b0c7113/rainbow-end-road-landscape.jpg\"}, \"create_date\": \"2025-02-04T13:22:56.786065Z\", \"rating_file\": \"Approved\", \"delete_message\": false, \"type_extension\": \"image\", \"update_message\": false, \"comment_mess_id\": null, \"who_read_messages\": [1]}, {\"id\": 2, \"text\": null, \"url_file\": \"\", \"file_name\": \"rainbow-end-road-landscape.jpg\", \"file_size\": \"13.92 Mb\", \"sender_id\": 1, \"type_file\": \"HQ\", \"url_images\": {\"w_200\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-2/images/w_200/67a214c2d006f/rainbow-end-road-landscape.jpg\", \"w_500\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-2/images/w_500/67a214c1d5174/rainbow-end-road-landscape.jpg\", \"original\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-2/images/original/67a214bfdb56b/rainbow-end-road-landscape.jpg\"}, \"create_date\": \"2025-02-04T13:23:11.865911Z\", \"rating_file\": null, \"delete_message\": false, \"type_extension\": \"image\", \"update_message\": false, \"comment_mess_id\": null, \"who_read_messages\": [1]}]', '[1]', '[]', '[]', '2025-02-04 13:19:54', '2025-02-04 13:23:15', NULL),
(29, '[{\"id\": 1, \"text\": null, \"url_file\": \"\", \"file_name\": \"yes.jpg\", \"file_size\": \"0.07 Mb\", \"sender_id\": 1, \"type_file\": \"Sketch\", \"url_images\": {\"w_200\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-1/images/w_200/67a214fb28ce1/yes.jpg\", \"w_500\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-1/images/w_500/67a214fabfb16/yes.jpg\", \"original\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-1/images/original/67a214f919b2b/yes.jpg\"}, \"create_date\": \"2025-02-04T13:24:09.101723Z\", \"rating_file\": \"Approved\", \"delete_message\": false, \"type_extension\": \"image\", \"update_message\": false, \"comment_mess_id\": null, \"who_read_messages\": [1]}, {\"id\": 2, \"text\": null, \"url_file\": \"\", \"file_name\": \"yes.jpg\", \"file_size\": \"0.07 Mb\", \"sender_id\": 1, \"type_file\": \"HQ\", \"url_images\": {\"w_200\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-2/images/w_200/67a2150726146/yes.jpg\", \"w_500\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-2/images/w_500/67a21506b5f89/yes.jpg\", \"original\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-2/images/original/67a21505079a5/yes.jpg\"}, \"create_date\": \"2025-02-04T13:24:21.027134Z\", \"rating_file\": null, \"delete_message\": false, \"type_extension\": \"image\", \"update_message\": false, \"comment_mess_id\": null, \"who_read_messages\": [1]}]', '[1]', '[]', '[]', '2025-02-04 13:19:57', '2025-02-04 13:24:23', NULL),
(30, '[{\"id\": 1, \"text\": null, \"url_file\": \"\", \"file_name\": \"no.jpg\", \"file_size\": \"1.76 Mb\", \"sender_id\": 1, \"type_file\": \"Sketch\", \"url_images\": {\"w_200\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-1/images/w_200/67a21489d633d/no.jpg\", \"w_500\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-1/images/w_500/67a2148910860/no.jpg\", \"original\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-1/images/original/67a214875bb0b/no.jpg\"}, \"create_date\": \"2025-02-04T13:22:15.371790Z\", \"rating_file\": \"Approved\", \"delete_message\": false, \"type_extension\": \"image\", \"update_message\": false, \"comment_mess_id\": null, \"who_read_messages\": [1]}, {\"id\": 2, \"text\": null, \"url_file\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-2/documents/67a2149c1eb6f/txt file.txt\", \"file_name\": \"txt file.txt\", \"file_size\": \"0.00 Mb\", \"sender_id\": 1, \"type_file\": \"HQ\", \"url_images\": [], \"create_date\": \"2025-02-04T13:22:36.123568Z\", \"rating_file\": null, \"delete_message\": false, \"type_extension\": \"document\", \"update_message\": false, \"comment_mess_id\": null, \"who_read_messages\": [1]}]', '[1]', '[]', '[]', '2025-02-04 13:20:04', '2025-02-04 13:22:37', NULL),
(31, '[{\"id\": 1, \"text\": null, \"url_file\": \"\", \"file_name\": \"Rolled metal products.jpg\", \"file_size\": \"0.08 Mb\", \"sender_id\": 1, \"type_file\": \"Sketch\", \"url_images\": {\"w_200\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-1/images/w_200/67a214d8a75aa/Rolled metal products.jpg\", \"w_500\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-1/images/w_500/67a214d84efbb/Rolled metal products.jpg\", \"original\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-1/images/original/67a214d6a3e66/Rolled metal products.jpg\"}, \"create_date\": \"2025-02-04T13:23:34.667482Z\", \"rating_file\": \"Approved\", \"delete_message\": false, \"type_extension\": \"image\", \"update_message\": false, \"comment_mess_id\": null, \"who_read_messages\": [1]}, {\"id\": 2, \"text\": null, \"url_file\": \"\", \"file_name\": \"Rolled metal products.jpg\", \"file_size\": \"0.08 Mb\", \"sender_id\": 1, \"type_file\": \"HQ\", \"url_images\": {\"w_200\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-2/images/w_200/67a214e36ca23/Rolled metal products.jpg\", \"w_500\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-2/images/w_500/67a214e316711/Rolled metal products.jpg\", \"original\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/design-chat/design-chat-id-2/images/original/67a214e20afaf/Rolled metal products.jpg\"}, \"create_date\": \"2025-02-04T13:23:46.043616Z\", \"rating_file\": null, \"delete_message\": false, \"type_extension\": \"image\", \"update_message\": false, \"comment_mess_id\": null, \"who_read_messages\": [1]}]', '[1]', '[]', '[]', '2025-02-04 13:20:06', '2025-02-04 13:23:47', NULL),
(34, '[]', '[1]', '[]', '[]', '2025-09-02 14:50:26', '2025-09-02 14:50:36', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `distributor_trackers`
--

CREATE TABLE `distributor_trackers` (
  `id` bigint UNSIGNED NOT NULL,
  `promotion_id` bigint UNSIGNED NOT NULL,
  `company_id` bigint UNSIGNED NOT NULL,
  `tracker_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_surfaces` json NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `distributor_trackers`
--

INSERT INTO `distributor_trackers` (`id`, `promotion_id`, `company_id`, `tracker_number`, `sent_surfaces`, `description`, `created_at`, `updated_at`) VALUES
(3, 6, 6, '444', '[{\"name\": \"Big banner - Cherry compote\", \"amount\": \"5\", \"design_id\": \"1\", \"surface_id\": \"1\"}, {\"name\": \"Big banner - Tasty hotdog\", \"amount\": \"5\", \"design_id\": \"3\", \"surface_id\": \"1\"}, {\"name\": \"Big flag - Cherry compote\", \"amount\": \"1\", \"design_id\": \"1\", \"surface_id\": \"3\"}, {\"name\": \"Big flag - Diesel fuel\", \"amount\": \"1\", \"design_id\": \"2\", \"surface_id\": \"3\"}]', '444', '2025-02-04 14:10:10', '2025-02-04 14:10:10'),
(4, 6, 8, '555', '[{\"name\": \"Big banner - Cherry compote\", \"amount\": \"3\", \"design_id\": \"1\", \"surface_id\": \"1\"}, {\"name\": \"Big banner - Tasty hotdog\", \"amount\": \"3\", \"design_id\": \"3\", \"surface_id\": \"1\"}, {\"name\": \"Big flag - Cherry compote\", \"amount\": \"1\", \"design_id\": \"1\", \"surface_id\": \"3\"}, {\"name\": \"Big flag - Diesel fuel\", \"amount\": \"1\", \"design_id\": \"2\", \"surface_id\": \"3\"}, {\"name\": \"Mini banner - Cherry compote\", \"amount\": \"2\", \"design_id\": \"1\", \"surface_id\": \"2\"}]', '555', '2025-02-04 14:20:06', '2025-02-04 14:20:06');

-- --------------------------------------------------------

--
-- Структура таблицы `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `feedback_messages`
--

CREATE TABLE `feedback_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `messages` json NOT NULL,
  `from_user_id` bigint UNSIGNED NOT NULL,
  `to_user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `feedback_messages`
--

INSERT INTO `feedback_messages` (`id`, `title`, `messages`, `from_user_id`, `to_user_id`, `created_at`, `updated_at`) VALUES
(1, 'FullStack (PHP Laravel and React) Developer for Outdoor Gear and Adventure Equipment E-commerce Platform', '[{\"id\": 1, \"text\": \"<p>FullStack (PHP Laravel and React) Developer for Outdoor Gear and Adventure Equipment E-commerce Platform FullStack (PHP Laravel and React) Developer for Outdoor Gear and Adventure Equipment E-commerce Platform FullStack (PHP Laravel and React) Developer for Outdoor Gear and Adventure Equipment E-commerce Platform</p>\", \"sender_id\": 6, \"create_date\": \"2025-02-12T14:19:19.049743Z\", \"who_read_messages\": [6, 1]}]', 6, 1, '2025-02-12 14:19:19', '2025-02-14 11:45:23');

-- --------------------------------------------------------

--
-- Структура таблицы `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(442, 'default', '{\"uuid\":\"3c9a2055-2cbd-4055-a578-34b254e68165\",\"displayName\":\"App\\\\Jobs\\\\SendPromotionEmailsToPrinters\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\SendPromotionEmailsToPrinters\",\"command\":\"O:38:\\\"App\\\\Jobs\\\\SendPromotionEmailsToPrinters\\\":2:{s:11:\\\"\\u0000*\\u0000printers\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";s:12:\\\"Printer User\\\";s:5:\\\"email\\\";s:19:\\\"printer@printer.com\\\";}}s:14:\\\"\\u0000*\\u0000promotionId\\\";i:6;}\"}}', 0, NULL, 1739888922, 1739888922),
(443, 'default', '{\"uuid\":\"963f0191-bc8e-4919-a14d-e77c6e5cf2fc\",\"displayName\":\"App\\\\Jobs\\\\OneMinuteCheckUnreadMessagesJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\OneMinuteCheckUnreadMessagesJob\",\"command\":\"O:40:\\\"App\\\\Jobs\\\\OneMinuteCheckUnreadMessagesJob\\\":6:{s:11:\\\"\\u0000*\\u0000authorId\\\";i:1;s:16:\\\"\\u0000*\\u0000notifyUserIds\\\";a:1:{i:1;i:2;}s:9:\\\"\\u0000*\\u0000chatId\\\";i:27;s:12:\\\"\\u0000*\\u0000messageId\\\";i:2;s:8:\\\"\\u0000*\\u0000timer\\\";i:1;s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":4:{s:4:\\\"date\\\";s:26:\\\"2025-02-18 14:29:42.721091\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:3:\\\"UTC\\\";s:18:\\\"dumpDateProperties\\\";a:2:{s:4:\\\"date\\\";s:26:\\\"2025-02-18 14:29:42.721091\\\";s:8:\\\"timezone\\\";s:3:\\\"UTC\\\";}}}\"}}', 0, NULL, 1739888982, 1739888922);

-- --------------------------------------------------------

--
-- Структура таблицы `logs`
--

CREATE TABLE `logs` (
  `id` bigint UNSIGNED NOT NULL,
  `message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `object` json DEFAULT NULL,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `logs`
--

INSERT INTO `logs` (`id`, `message`, `object`, `text`, `created_at`, `updated_at`) VALUES
(1, 'Invalid URL format.', NULL, NULL, '2024-11-12 15:55:26', '2024-11-12 15:55:26');

-- --------------------------------------------------------

--
-- Структура таблицы `log_company_planners`
--

CREATE TABLE `log_company_planners` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `surface_id` bigint UNSIGNED NOT NULL,
  `old_value` int NOT NULL,
  `new_value` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `log_company_planners`
--

INSERT INTO `log_company_planners` (`id`, `user_id`, `surface_id`, `old_value`, `new_value`, `created_at`, `updated_at`) VALUES
(16, 1, 1, 0, 0, '2024-11-12 13:48:24', '2024-11-12 13:48:24'),
(17, 1, 1, 0, 3, '2024-11-12 13:49:45', '2024-11-12 13:49:45'),
(18, 1, 1, 0, 0, '2024-11-12 13:50:12', '2024-11-12 13:50:12'),
(19, 1, 1, 0, 3, '2024-11-12 13:57:25', '2024-11-12 13:57:25'),
(20, 1, 1, 3, 2, '2024-11-12 13:57:45', '2024-11-12 13:57:45'),
(21, 1, 1, 2, 0, '2024-11-12 13:58:00', '2024-11-12 13:58:00'),
(22, 1, 1, 0, 2, '2024-11-12 13:58:44', '2024-11-12 13:58:44'),
(23, 1, 3, 0, 0, '2024-11-12 13:58:53', '2024-11-12 13:58:53'),
(24, 1, 2, 1, 3, '2024-11-12 14:00:25', '2024-11-12 14:00:25'),
(25, 1, 3, 0, 0, '2024-11-12 14:00:53', '2024-11-12 14:00:53'),
(26, 1, 1, 2, 0, '2024-11-12 14:01:13', '2024-11-12 14:01:13'),
(27, 1, 2, 3, 0, '2024-11-12 14:01:25', '2024-11-12 14:01:25'),
(28, 1, 3, 0, 1, '2024-11-12 14:01:44', '2024-11-12 14:01:44'),
(29, 1, 1, 0, 2, '2024-11-12 14:11:24', '2024-11-12 14:11:24'),
(30, 1, 3, 1, 0, '2024-11-12 14:17:53', '2024-11-12 14:17:53'),
(31, 1, 3, 0, 5, '2024-11-12 14:25:38', '2024-11-12 14:25:38'),
(32, 1, 3, 0, 1, '2024-11-12 15:43:33', '2024-11-12 15:43:33'),
(33, 1, 2, 0, 2, '2024-11-12 15:43:34', '2024-11-12 15:43:34'),
(34, 1, 1, 0, 3, '2024-11-12 15:43:35', '2024-11-12 15:43:35'),
(35, 1, 1, 2, 5, '2024-11-13 11:24:29', '2024-11-13 11:24:29'),
(36, 1, 1, 5, 2, '2024-11-13 13:31:45', '2024-11-13 13:31:45'),
(37, 1, 1, 2, 5, '2024-11-13 15:01:03', '2024-11-13 15:01:03'),
(38, 1, 1, 5, 2, '2024-11-13 15:01:55', '2024-11-13 15:01:55'),
(39, 1, 3, 5, 0, '2024-11-19 11:03:03', '2024-11-19 11:03:03'),
(40, 1, 1, 2, 5, '2024-11-19 13:24:06', '2024-11-19 13:24:06'),
(41, 1, 1, 5, 2, '2024-11-19 13:57:43', '2024-11-19 13:57:43'),
(42, 1, 1, 2, 5, '2024-11-20 13:52:35', '2024-11-20 13:52:35'),
(43, 1, 3, 0, 2, '2024-12-12 11:46:49', '2024-12-12 11:46:49');

-- --------------------------------------------------------

--
-- Структура таблицы `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(166, '2014_10_12_000000_create_users_table', 1),
(167, '2014_10_12_000001_create_user_data_table', 1),
(168, '2014_10_12_000002_create_password_reset_tokens_table', 1),
(169, '2019_08_19_000000_create_failed_jobs_table', 1),
(170, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(171, '2024_09_03_150229_create_roles_table', 1),
(172, '2024_09_03_150610_create_role_user_table', 1),
(173, '2024_09_06_115709_create_categories_table', 1),
(174, '2024_09_09_040055_create_type_surfaces_table', 1),
(175, '2024_09_09_040056_create_size_surfaces_table', 1),
(176, '2024_09_09_040057_create_surfaces_table', 1),
(177, '2024_09_10_075857_create_tests_table', 1),
(178, '2024_09_10_141350_create_logs_table', 1),
(179, '2024_09_10_163211_create_products_table', 1),
(180, '2024_09_12_075350_create_designs_table', 1),
(181, '2024_09_13_102150_create_promotions_table', 1),
(182, '2024_09_16_143516_create_promotion_surfaces_table', 1),
(183, '2024_09_17_124357_create_design_chats_table', 1),
(184, '2024_09_17_124358_create_promotion_surface_designs_table', 1),
(185, '2024_10_22_083834_create_company_planners_table', 1),
(186, '2024_10_23_104315_create_re_logins_table', 1),
(187, '2024_10_24_085226_create_log_company_planners_table', 1),
(191, '2024_11_21_160247_create_jobs_table', 5),
(194, '2024_12_04_144625_create_print_promotion_reports_table', 8),
(198, '2024_12_08_175512_create_printed_promotions_table', 10),
(201, '2024_12_06_133653_create_system_settings_table', 11),
(220, '2024_12_28_144837_create_distributor_trackers_table', 12),
(234, '2025_02_12_112057_create_feedback_messages_table', 14),
(236, '2018_08_08_100000_create_telescope_entries_table', 15);

-- --------------------------------------------------------

--
-- Структура таблицы `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(8, 'App\\Models\\User', 1, 'Personal Access Token', 'c4745eeab058b2b38dac76b43b4b6e16bb3e6bbd908f4217a6f514232e1c5cf0', '[\"*\"]', '2024-11-13 19:24:10', NULL, '2024-11-12 15:45:24', '2024-11-13 19:24:10'),
(17, 'App\\Models\\User', 1, 'Personal Access Token', '6e10089efc33e4054a7d54c59c8592056c58f86a41e9d793223c74e9a8285ff1', '[\"*\"]', '2024-11-14 15:54:17', NULL, '2024-11-14 15:52:54', '2024-11-14 15:54:17'),
(20, 'App\\Models\\User', 1, 'Personal Access Token', 'bce0052c6ba3436a3956309970c89f52c2ff9b5a1a7c60dc5ddfbda1b3deb301', '[\"*\"]', '2024-11-15 08:58:58', NULL, '2024-11-15 08:58:55', '2024-11-15 08:58:58'),
(23, 'App\\Models\\User', 1, 'Personal Access Token', '6624f43d78fda8fd73b329cba12977a671f6534fafb5b9406bbc2a668ffb534f', '[\"*\"]', '2024-11-18 09:47:25', NULL, '2024-11-18 09:47:20', '2024-11-18 09:47:25'),
(34, 'App\\Models\\User', 1, 'Personal Access Token', '621412000655eb8c7a136533208fe80aca8d124889af8cb0aa0201bfe24174b7', '[\"*\"]', '2024-11-18 10:52:15', NULL, '2024-11-18 10:52:10', '2024-11-18 10:52:15'),
(35, 'App\\Models\\User', 1, 'Personal Access Token', '4df17647895d303850bde99bd5c2b00ecebc8329ddab0d3c4ebfdc62c664e4a5', '[\"*\"]', '2024-11-18 11:43:49', NULL, '2024-11-18 10:55:04', '2024-11-18 11:43:49'),
(36, 'App\\Models\\User', 1, 'Personal Access Token', '0e9208c8ceb7241e0292aa8fe2dc7f935fa70c2758c7fe42525426a9fc30e1c9', '[\"*\"]', '2024-11-18 11:45:08', NULL, '2024-11-18 11:44:17', '2024-11-18 11:45:08'),
(38, 'App\\Models\\User', 6, 'Personal Access Token', 'fcefcdc22ff8191b9f481d939c25f2ef70f5b2596fb8b04c12fc0e19326089cd', '[\"*\"]', '2024-11-19 12:12:07', NULL, '2024-11-19 07:55:48', '2024-11-19 12:12:07'),
(43, 'App\\Models\\User', 1, 'Personal Access Token', '6b6244580722db5a908c22561b7ef49769874f8a90370d58d2efea66fea399bb', '[\"*\"]', '2024-11-20 14:05:54', NULL, '2024-11-19 12:22:38', '2024-11-20 14:05:54'),
(44, 'App\\Models\\User', 6, 'Personal Access Token', '4da4a28c45677d699dc22e1d1426897a8df71be4a08604497dd78ddbfcdf33f4', '[\"*\"]', '2024-11-20 13:52:38', NULL, '2024-11-19 12:57:41', '2024-11-20 13:52:38'),
(50, 'App\\Models\\User', 1, 'Personal Access Token', '6e078ea6dc017eec9e0ad02eb357a2ffcdefc5b52144493874894d5b8faadbce', '[\"*\"]', '2024-11-21 14:53:33', NULL, '2024-11-21 09:58:42', '2024-11-21 14:53:33'),
(51, 'App\\Models\\User', 1, 'Personal Access Token', 'f6d7e7ae213fe3f429a17b50fc163d684e9ee8255476bf5b4ba15fba5426b878', '[\"*\"]', '2024-11-21 15:56:05', NULL, '2024-11-21 15:04:42', '2024-11-21 15:56:05'),
(52, 'App\\Models\\User', 1, 'Personal Access Token', '335cf6ea4e8d05e9fca0b571a268fb85e54ad452ee2c15cc16923a6ca2f73062', '[\"*\"]', '2024-11-22 13:07:11', NULL, '2024-11-22 07:56:51', '2024-11-22 13:07:11'),
(53, 'App\\Models\\User', 4, 'Personal Access Token', '9c3f351482bc6563b576673d3a4996e3d0537b7763fffae2432ab92772ec013d', '[\"*\"]', '2024-11-22 13:04:04', NULL, '2024-11-22 11:33:09', '2024-11-22 13:04:04'),
(55, 'App\\Models\\User', 6, 'Personal Access Token', '60e61c76a9b4dd201d2b6a3de1f0d5904867b12ba555faa4a1a638de326a8467', '[\"*\"]', '2024-11-25 13:36:40', NULL, '2024-11-25 12:23:17', '2024-11-25 13:36:40'),
(56, 'App\\Models\\User', 1, 'Personal Access Token', '63123fe059b4f3081e390c9a4d43aacf8411b4bc1fa23457ed1491037081cabb', '[\"*\"]', '2024-11-28 11:22:58', NULL, '2024-11-25 13:44:01', '2024-11-28 11:22:58'),
(65, 'App\\Models\\User', 1, 'Personal Access Token', 'c6eaaac765e03c41be4e9de7c627a0df0662ff0c1b41cc0e965f9d6fe08b4893', '[\"*\"]', '2024-12-05 13:57:33', NULL, '2024-12-05 13:18:52', '2024-12-05 13:57:33'),
(68, 'App\\Models\\User', 1, 'Personal Access Token', '82a5a3f9af0d5004c62c84d0e27afe17edef741df9dbd2ab6b3779c58ba68455', '[\"*\"]', '2024-12-05 14:33:50', NULL, '2024-12-05 14:27:09', '2024-12-05 14:33:50'),
(82, 'App\\Models\\User', 11, 'Personal Access Token', '84da8d5d02ca33ef6ef967ef51dba118b9453c2f4c568a3f4592f841632d9444', '[\"*\"]', '2024-12-11 14:14:28', NULL, '2024-12-11 13:38:41', '2024-12-11 14:14:28'),
(88, 'App\\Models\\User', 10, 'Personal Access Token', 'a028c0e2d7fe14acef1b3ad521f4c76a18248a1106359d3e8e44a9d8c085c309', '[\"*\"]', '2024-12-12 12:43:46', NULL, '2024-12-12 11:48:23', '2024-12-12 12:43:46'),
(90, 'App\\Models\\User', 1, 'Personal Access Token', '881d4f9eb5913cc664ba58fa2a2ccb0290ed7e9b39253800996c2d4e991001de', '[\"*\"]', '2024-12-13 13:26:00', NULL, '2024-12-13 09:11:51', '2024-12-13 13:26:00'),
(96, 'App\\Models\\User', 11, 'Personal Access Token', '7966ce41f30cfa610b1244162db83d7ea9c6840c03926ed844cb38ad9ec94bcc', '[\"*\"]', '2024-12-13 15:03:56', NULL, '2024-12-13 14:56:57', '2024-12-13 15:03:56'),
(98, 'App\\Models\\User', 1, 'Personal Access Token', 'c45444283b875b649793642e9cb9b90846ee255a9f91b3d7db8227b17b609a3d', '[\"*\"]', '2024-12-16 15:40:52', NULL, '2024-12-16 15:10:25', '2024-12-16 15:40:52'),
(124, 'App\\Models\\User', 11, 'Personal Access Token', '45b0401ca0b7869afba74bbf7b56e946a9bc30766c44b283406e98664f65fc10', '[\"*\"]', '2024-12-29 15:27:20', NULL, '2024-12-27 14:26:43', '2024-12-29 15:27:20'),
(129, 'App\\Models\\User', 1, 'Personal Access Token', '758ab5c75403acc3a4c1bc53e40063eac37e6898229ff46a8b77fb1880633d8d', '[\"*\"]', '2024-12-29 15:08:25', NULL, '2024-12-29 15:08:05', '2024-12-29 15:08:25'),
(137, 'App\\Models\\User', 10, 'Personal Access Token', 'fbff423e76465abb8486d86d90d3bcc25da69f34c13c6ad998ec75cab06d4e4b', '[\"*\"]', '2024-12-30 15:12:41', NULL, '2024-12-30 15:05:44', '2024-12-30 15:12:41'),
(138, 'App\\Models\\User', 11, 'Personal Access Token', '5ea8d9d4c1da6ebfd30d947f67794bcdf8b8e2f166d85a8731340c8c4849d8a9', '[\"*\"]', '2024-12-30 15:12:55', NULL, '2024-12-30 15:09:16', '2024-12-30 15:12:55'),
(149, 'App\\Models\\User', 11, 'Personal Access Token', '6ba307258c1da2627c4141ef7320ca8b320f6e6950176dd0027219958164ac91', '[\"*\"]', '2025-01-06 12:11:37', NULL, '2025-01-06 12:11:16', '2025-01-06 12:11:37'),
(154, 'App\\Models\\User', 12, 'Personal Access Token', 'c1ddac7dc1469be919d344002d199abe400eac7a4f2fca88a004fd01c28c977d', '[\"*\"]', '2025-01-06 12:18:34', NULL, '2025-01-06 12:16:14', '2025-01-06 12:18:34'),
(155, 'App\\Models\\User', 11, 'Personal Access Token', '338b68acaedecef41e761320348e1f0f764d46c0dbc4c5f9fe23295315deedbd', '[\"*\"]', '2025-01-06 12:19:20', NULL, '2025-01-06 12:18:45', '2025-01-06 12:19:20'),
(158, 'App\\Models\\User', 1, 'Personal Access Token', '16301f6427aa49ccbc658e46978aa25f142f3f70986983e7fd1b34b3cd59d6a9', '[\"*\"]', '2025-01-08 12:55:38', NULL, '2025-01-08 10:29:11', '2025-01-08 12:55:38'),
(159, 'App\\Models\\User', 1, 'Personal Access Token', 'd4f85e1b64616cdd26ba32dd7980bdfea855a4cf2180d7bd48a465ea210d20f6', '[\"*\"]', '2025-01-08 18:18:03', NULL, '2025-01-08 12:56:18', '2025-01-08 18:18:03'),
(168, 'App\\Models\\User', 11, 'Personal Access Token', 'dbf9cf48e61b811aab2769d9c58a0cfc76c7a77d1c33e330fa7df6d88af5cf8b', '[\"*\"]', '2025-01-11 11:05:35', NULL, '2025-01-11 10:59:44', '2025-01-11 11:05:35'),
(169, 'App\\Models\\User', 11, 'Personal Access Token', '630024ed703fb4341a2c1842b8b32c1c17a5dc5afdbf7780197dea15abf491c6', '[\"*\"]', '2025-01-11 11:05:34', NULL, '2025-01-11 11:02:38', '2025-01-11 11:05:34'),
(170, 'App\\Models\\User', 1, 'Personal Access Token', '66d24103a607a0933cfa12bbe966a362b3ab8cc4c3870d51b654e8ef39c27b16', '[\"*\"]', NULL, NULL, '2025-01-14 09:47:06', '2025-01-14 09:47:06'),
(179, 'App\\Models\\User', 1, 'Personal Access Token', '8629de4a8b17d6b9b7608f1a6b75db1dac39dc97b7b69f8f385df11760f00cc2', '[\"*\"]', '2025-01-15 16:56:50', NULL, '2025-01-15 13:48:02', '2025-01-15 16:56:50'),
(182, 'App\\Models\\User', 1, 'Personal Access Token', '9f6ce0b0bace378b45d3b090aa1dfaa9c8197a9388c4c96fde1dde296ecf7f5b', '[\"*\"]', '2025-01-16 12:29:07', NULL, '2025-01-16 12:29:01', '2025-01-16 12:29:07'),
(186, 'App\\Models\\User', 1, 'Personal Access Token', 'e31739d211c2e8a64169aee0775e03cb74151a42088c6b7cd813219b6b51330a', '[\"*\"]', '2025-01-16 17:07:38', NULL, '2025-01-16 13:18:19', '2025-01-16 17:07:38'),
(187, 'App\\Models\\User', 1, 'Personal Access Token', 'dd070e23e6a8e7a47cbf5bb68428492ed19fc79f79e9d29370edc29bf1c9ed13', '[\"*\"]', '2025-02-04 17:49:50', NULL, '2025-02-04 08:28:57', '2025-02-04 17:49:50'),
(196, 'App\\Models\\User', 6, 'Personal Access Token', '8a5f702120e963eb26bc4fca3f43a3ee0a9dc726b88ec1162c2a2868f13ad53a', '[\"*\"]', '2025-02-04 17:23:34', NULL, '2025-02-04 17:11:00', '2025-02-04 17:23:34'),
(197, 'App\\Models\\User', 1, 'Personal Access Token', 'a6960fb8401f9a6266cc0f04fdb03a9dad0b56995f4d4ab0c287024ebed4608e', '[\"*\"]', '2025-02-12 17:03:27', NULL, '2025-02-12 13:54:24', '2025-02-12 17:03:27'),
(198, 'App\\Models\\User', 1, 'Personal Access Token', '6b43b489fbbf25e8bf8662c021a11becbf8dad3ebd68e3384537767692419986', '[\"*\"]', '2025-02-13 12:29:52', NULL, '2025-02-13 08:37:55', '2025-02-13 12:29:52'),
(200, 'App\\Models\\User', 1, 'Personal Access Token', 'a3c835dec57b2be629c8b47b96b06802c9a152249811aec785d38ab48269b56d', '[\"*\"]', '2025-02-15 14:41:30', NULL, '2025-02-13 12:40:08', '2025-02-15 14:41:30'),
(201, 'App\\Models\\User', 1, 'Personal Access Token', '70047aa3fc45e0c9dc4213e9c66267a3aad498a9413586f8bf05ec2eb16f0d91', '[\"*\"]', '2025-02-15 14:45:00', NULL, '2025-02-15 14:43:25', '2025-02-15 14:45:00'),
(202, 'App\\Models\\User', 1, 'Personal Access Token', 'dfd9845305b40529fcc3fef09de2d11a022d7d4e4eac8a8ffa8b10f0765a2aed', '[\"*\"]', '2025-02-18 14:22:22', NULL, '2025-02-18 09:56:01', '2025-02-18 14:22:22'),
(205, 'App\\Models\\User', 9, 'Personal Access Token', '78068878fecee5bfa85aff2306fea74fa5a06fc42e25d120e5e98e980fd9dcc0', '[\"*\"]', '2025-02-18 14:20:58', NULL, '2025-02-18 13:54:36', '2025-02-18 14:20:58'),
(206, 'App\\Models\\User', 1, 'Personal Access Token', 'e010f01944901e005b74c95496f57b64408a5f28f45f86986f81da4296f48b7e', '[\"*\"]', '2025-02-19 11:57:21', NULL, '2025-02-18 14:28:12', '2025-02-19 11:57:21'),
(209, 'App\\Models\\User', 1, 'Personal Access Token', '88eddec1537e9d8775371e403a1932e98696d04571654d3a2c8920b8401bf51c', '[\"*\"]', '2025-09-04 19:50:46', NULL, '2025-09-03 10:59:16', '2025-09-04 19:50:46');

-- --------------------------------------------------------

--
-- Структура таблицы `printed_promotions`
--

CREATE TABLE `printed_promotions` (
  `id` bigint UNSIGNED NOT NULL,
  `promotion_id` bigint UNSIGNED NOT NULL,
  `printer_id` bigint UNSIGNED NOT NULL,
  `printer_tracker_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sent_surfaces` json NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `printed_promotions`
--

INSERT INTO `printed_promotions` (`id`, `promotion_id`, `printer_id`, `printer_tracker_number`, `sent_surfaces`, `description`, `created_at`, `updated_at`) VALUES
(41, 6, 10, '111', '[{\"design\": {\"id\": \"1\"}, \"surface\": {\"id\": \"1\"}}, {\"design\": {\"id\": \"2\"}, \"surface\": {\"id\": \"3\"}}]', '111', '2025-02-04 13:50:33', '2025-02-04 13:50:33'),
(42, 6, 10, '222', '[{\"design\": {\"id\": \"3\"}, \"surface\": {\"id\": \"1\"}}, {\"design\": {\"id\": \"1\"}, \"surface\": {\"id\": \"3\"}}]', '222', '2025-02-04 13:50:43', '2025-02-04 13:50:43'),
(44, 6, 12, '333', '[{\"design\": {\"id\": \"1\"}, \"surface\": {\"id\": \"2\"}}]', '333', '2025-02-04 14:06:40', '2025-02-04 14:06:40');

-- --------------------------------------------------------

--
-- Структура таблицы `print_promotion_reports`
--

CREATE TABLE `print_promotion_reports` (
  `id` bigint UNSIGNED NOT NULL,
  `promotion_id` bigint UNSIGNED NOT NULL,
  `percent` int NOT NULL DEFAULT '0',
  `description_cm` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `surfaces` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `print_promotion_reports`
--

INSERT INTO `print_promotion_reports` (`id`, `promotion_id`, `percent`, `description_cm`, `surfaces`, `created_at`, `updated_at`) VALUES
(13, 6, 8, 'sdf', '[{\"1 - 1\": 5}, {\"1 - 3\": 5}, {\"3 - 1\": 1}, {\"3 - 2\": 1}, {\"2 - 1\": 0}, {\"1 - 1\": 0}, {\"1 - 3\": 0}, {\"3 - 1\": 0}, {\"3 - 2\": 0}, {\"2 - 1\": 0}, {\"1 - 1\": 3}, {\"1 - 3\": 3}, {\"3 - 1\": 1}, {\"3 - 2\": 1}, {\"2 - 1\": 2}]', '2025-02-04 13:26:42', '2025-02-18 14:28:42');

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `ean` int UNSIGNED NOT NULL,
  `vendor_code` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `manufacturer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price_per_item` decimal(12,2) NOT NULL,
  `main_last_price` decimal(12,2) NOT NULL,
  `latest_price` decimal(12,2) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `tax` int UNSIGNED NOT NULL,
  `container_deposit` int UNSIGNED NOT NULL,
  `item_plan_bu_grp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `locally_owned` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `selling_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_item_ean` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_item_pack_qty` int UNSIGNED NOT NULL,
  `provider_item_vendor_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_images` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `ean`, `vendor_code`, `name`, `category`, `sub_category`, `provider_name`, `manufacturer`, `price_per_item`, `main_last_price`, `latest_price`, `status`, `tax`, `container_deposit`, `item_plan_bu_grp`, `locally_owned`, `selling_type`, `provider_item_ean`, `provider_item_name`, `provider_item_pack_qty`, `provider_item_vendor_code`, `url_images`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 25560854, 404, 'incidunt', 'Non Alcoholic Beverages', 'Sub Lubricants', 'Kulas LLC', 'Legros Group', 45.92, 9.21, 79.62, 1, 1, 9, 'a_0', 'b_0', 'c_0', 'd_0', 'e_0', 2, 'f_0', '[]', '2024-11-12 10:35:34', '2024-11-12 10:35:34', NULL),
(2, 82778399, 504, 'rerum', 'Food', 'Sub Car Wash', 'Greenfelder-Lind', 'Fisher-Glover', 53.84, 86.85, 18.40, 1, 8, 7, 'a_1', 'b_1', 'c_1', 'd_1', 'e_1', 9, 'f_1', '{\"w_200\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/product/images/w_200/6788f1897f6a0/a_image.jpg\", \"original\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/product/images/original/6788f188c6ea0/a_image.jpg\"}', '2024-11-12 10:35:34', '2025-01-16 11:46:17', NULL),
(3, 67062407, 747, 'earum', 'Car Wash', 'Sub Perishables', 'Grant-Luettgen', 'Gerhold-Walker', 58.57, 82.64, 22.18, 1, 4, 10, 'a_2', 'b_2', 'c_2', 'd_2', 'e_2', 10, 'f_2', '[]', '2024-11-12 10:35:34', '2024-11-12 10:35:34', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `promotions`
--

CREATE TABLE `promotions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `show_in_user_promotions` tinyint(1) NOT NULL DEFAULT '0',
  `notify_admin` datetime DEFAULT NULL,
  `send_to_printer` datetime DEFAULT NULL,
  `send_to_distributor` datetime DEFAULT NULL,
  `complete_distributor_work` datetime DEFAULT NULL,
  `period` json NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `surfaces` json NOT NULL,
  `who_created_id` bigint UNSIGNED DEFAULT NULL,
  `url_images` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `promotions`
--

INSERT INTO `promotions` (`id`, `name`, `status`, `show_in_user_promotions`, `notify_admin`, `send_to_printer`, `send_to_distributor`, `complete_distributor_work`, `period`, `description`, `surfaces`, `who_created_id`, `url_images`, `created_at`, `updated_at`, `deleted_at`) VALUES
(6, 'Sommer kampanje', 2, 1, '2025-02-04 17:42:09', '2025-02-04 14:06:40', '2025-02-04 14:06:40', '2025-02-04 14:20:06', '{\"to\": \"2025-03-01T22:00:00.000Z\", \"from\": \"2025-01-31T22:00:00.000Z\"}', NULL, '[{\"designs\": [{\"id\": \"27\", \"data\": {\"color\": \"No\", \"title\": null, \"status\": \"Completed\", \"ean_more\": null, \"plu_scan\": \"In the\", \"sub_title\": null, \"description\": null, \"text_italic\": null, \"need_for_price\": {\"title\": \"Need for price change in store data\", \"value\": \"false\"}, \"not_for_printing\": {\"title\": \"Not for printing\", \"value\": \"false\"}, \"promotional_offer\": \"0\", \"supplier_discount\": \"0%\", \"additional_description\": null}, \"name\": \"Cherry compote\", \"category\": \"3\"}, {\"id\": \"30\", \"data\": {\"color\": \"No\", \"title\": null, \"status\": \"Completed\", \"ean_more\": null, \"plu_scan\": \"In the\", \"sub_title\": null, \"description\": null, \"text_italic\": null, \"need_for_price\": {\"title\": \"Need for price change in store data\", \"value\": \"false\"}, \"not_for_printing\": {\"title\": \"Not for printing\", \"value\": \"false\"}, \"promotional_offer\": \"0\", \"supplier_discount\": \"0%\", \"additional_description\": null}, \"name\": \"Tasty hotdog\", \"category\": \"3\"}], \"surface\": {\"id\": \"1\", \"name\": \"Big banner\", \"printer_id\": \"10\"}}, {\"designs\": [{\"id\": \"28\", \"data\": {\"color\": \"No\", \"title\": null, \"status\": \"Completed\", \"ean_more\": null, \"plu_scan\": \"In the\", \"sub_title\": null, \"description\": null, \"text_italic\": null, \"need_for_price\": {\"title\": \"Need for price change in store data\", \"value\": \"false\"}, \"not_for_printing\": {\"title\": \"Not for printing\", \"value\": \"false\"}, \"promotional_offer\": \"0\", \"supplier_discount\": \"0%\", \"additional_description\": null}, \"name\": \"Cherry compote\", \"category\": \"3\"}, {\"id\": \"31\", \"data\": {\"color\": \"No\", \"title\": null, \"status\": \"Completed\", \"ean_more\": null, \"plu_scan\": \"In the\", \"sub_title\": null, \"description\": null, \"text_italic\": null, \"need_for_price\": {\"title\": \"Need for price change in store data\", \"value\": \"false\"}, \"not_for_printing\": {\"title\": \"Not for printing\", \"value\": \"false\"}, \"promotional_offer\": \"0\", \"supplier_discount\": \"0%\", \"additional_description\": null}, \"name\": \"Diesel fuel\", \"category\": \"4\"}], \"surface\": {\"id\": \"3\", \"name\": \"Big flag\", \"printer_id\": \"10\"}}, {\"designs\": [{\"id\": \"29\", \"data\": {\"color\": \"No\", \"title\": null, \"status\": \"Completed\", \"ean_more\": null, \"plu_scan\": \"In the\", \"sub_title\": null, \"description\": null, \"text_italic\": null, \"need_for_price\": {\"title\": \"Need for price change in store data\", \"value\": \"false\"}, \"not_for_printing\": {\"title\": \"Not for printing\", \"value\": \"false\"}, \"promotional_offer\": \"0\", \"supplier_discount\": \"0%\", \"additional_description\": null}, \"name\": \"Cherry compote\", \"category\": \"3\"}], \"surface\": {\"id\": \"2\", \"name\": \"Mini banner\", \"printer_id\": \"12\"}}]', 1, '{\"w_200\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/promotion/images/w_200/67a2141da4a30/no.jpg\", \"original\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/promotion/images/original/67a2141b65dd0/no.jpg\"}', '2025-02-04 13:19:19', '2025-02-18 14:28:42', NULL),
(7, 'Test', 0, 0, NULL, NULL, NULL, NULL, '{\"to\": \"2025-10-11T21:00:00.000Z\", \"from\": \"2025-09-01T21:00:00.000Z\"}', NULL, '[{\"designs\": [{\"id\": \"34\", \"data\": {\"color\": \"No\", \"title\": null, \"status\": \"Created\", \"ean_more\": null, \"plu_scan\": \"In the\", \"sub_title\": null, \"description\": null, \"text_italic\": null, \"need_for_price\": {\"title\": \"Need for price change in store data\", \"value\": \"false\"}, \"not_for_printing\": {\"title\": \"Not for printing\", \"value\": \"false\"}, \"promotional_offer\": \"0\", \"supplier_discount\": \"0%\", \"additional_description\": null}, \"name\": \"Cherry compote\", \"category\": \"3\"}], \"surface\": {\"id\": \"1\", \"name\": \"Big banner\", \"printer_id\": \"10\"}}]', 1, '{\"w_200\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/promotion/images/w_200/68b9501b54604/yes.jpg\", \"original\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/promotion/images/original/68b9501796e4c/yes.jpg\"}', '2025-09-02 14:22:27', '2025-09-04 08:38:53', NULL),
(8, 'апр', 0, 0, NULL, NULL, NULL, NULL, '{\"to\": \"2025-09-26T21:00:00.000Z\", \"from\": \"2025-09-02T21:00:00.000Z\"}', NULL, '[]', 1, '[]', '2025-09-04 08:00:50', '2025-09-04 08:01:01', '2025-09-04 08:01:01'),
(9, 'вап', 0, 0, NULL, NULL, NULL, NULL, '{\"to\": \"2025-09-17T21:00:00.000Z\", \"from\": \"2025-09-01T21:00:00.000Z\"}', NULL, '[]', 1, '[]', '2025-09-04 08:02:45', '2025-09-04 08:02:59', '2025-09-04 08:02:59');

-- --------------------------------------------------------

--
-- Структура таблицы `promotion_surfaces`
--

CREATE TABLE `promotion_surfaces` (
  `id` bigint UNSIGNED NOT NULL,
  `promotion_id` bigint UNSIGNED NOT NULL,
  `surface_id` bigint UNSIGNED NOT NULL,
  `designs` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `promotion_surfaces`
--

INSERT INTO `promotion_surfaces` (`id`, `promotion_id`, `surface_id`, `designs`, `created_at`, `updated_at`, `deleted_at`) VALUES
(14, 6, 1, '[]', '2025-02-04 13:19:35', '2025-02-04 13:19:35', NULL),
(15, 6, 3, '[]', '2025-02-04 13:19:39', '2025-02-04 13:19:39', NULL),
(16, 6, 2, '[]', '2025-02-04 13:19:40', '2025-02-04 13:19:40', NULL),
(18, 7, 1, '[]', '2025-09-02 14:24:07', '2025-09-02 14:24:07', NULL),
(19, 7, 3, '[]', '2025-09-03 19:06:05', '2025-09-03 19:06:15', '2025-09-03 19:06:15'),
(20, 7, 8, '[]', '2025-09-03 19:06:05', '2025-09-03 19:06:30', '2025-09-03 19:06:30');

-- --------------------------------------------------------

--
-- Структура таблицы `promotion_surface_designs`
--

CREATE TABLE `promotion_surface_designs` (
  `id` bigint UNSIGNED NOT NULL,
  `design_id` bigint UNSIGNED NOT NULL,
  `promotion_id` bigint UNSIGNED NOT NULL,
  `surface_id` bigint UNSIGNED NOT NULL,
  `chat_id` bigint UNSIGNED NOT NULL,
  `design_category_id` bigint UNSIGNED NOT NULL,
  `designer_id` bigint UNSIGNED DEFAULT NULL,
  `data` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `promotion_surface_designs`
--

INSERT INTO `promotion_surface_designs` (`id`, `design_id`, `promotion_id`, `surface_id`, `chat_id`, `design_category_id`, `designer_id`, `data`, `created_at`, `updated_at`, `deleted_at`) VALUES
(27, 1, 6, 1, 27, 3, 2, '{\"color\": \"No\", \"files\": [], \"title\": \"\", \"status\": \"Completed\", \"ean_more\": \"\", \"plu_scan\": \"In the\", \"products\": [], \"sub_title\": \"\", \"description\": \"\", \"text_italic\": \"\", \"need_for_price\": {\"title\": \"Need for price change in store data\", \"value\": false}, \"not_for_printing\": {\"title\": \"Not for printing\", \"value\": false}, \"promotional_offer\": 0, \"supplier_discount\": \"0%\", \"additional_description\": \"\"}', '2025-02-04 13:19:49', '2025-02-18 14:28:42', NULL),
(28, 1, 6, 3, 28, 3, NULL, '{\"color\": \"No\", \"files\": [], \"title\": \"\", \"status\": \"Completed\", \"ean_more\": \"\", \"plu_scan\": \"In the\", \"products\": [], \"sub_title\": \"\", \"description\": \"\", \"text_italic\": \"\", \"need_for_price\": {\"title\": \"Need for price change in store data\", \"value\": false}, \"not_for_printing\": {\"title\": \"Not for printing\", \"value\": false}, \"promotional_offer\": 0, \"supplier_discount\": \"0%\", \"additional_description\": \"\"}', '2025-02-04 13:19:54', '2025-02-04 13:23:15', NULL),
(29, 1, 6, 2, 29, 3, NULL, '{\"color\": \"No\", \"files\": [], \"title\": \"\", \"status\": \"Completed\", \"ean_more\": \"\", \"plu_scan\": \"In the\", \"products\": [], \"sub_title\": \"\", \"description\": \"\", \"text_italic\": \"\", \"need_for_price\": {\"title\": \"Need for price change in store data\", \"value\": false}, \"not_for_printing\": {\"title\": \"Not for printing\", \"value\": false}, \"promotional_offer\": 0, \"supplier_discount\": \"0%\", \"additional_description\": \"\"}', '2025-02-04 13:19:57', '2025-02-04 13:24:23', NULL),
(30, 3, 6, 1, 30, 3, NULL, '{\"color\": \"No\", \"files\": [], \"title\": \"\", \"status\": \"Completed\", \"ean_more\": \"\", \"plu_scan\": \"In the\", \"products\": [], \"sub_title\": \"\", \"description\": \"\", \"text_italic\": \"\", \"need_for_price\": {\"title\": \"Need for price change in store data\", \"value\": false}, \"not_for_printing\": {\"title\": \"Not for printing\", \"value\": false}, \"promotional_offer\": 0, \"supplier_discount\": \"0%\", \"additional_description\": \"\"}', '2025-02-04 13:20:04', '2025-02-04 13:22:37', NULL),
(31, 2, 6, 3, 31, 4, NULL, '{\"color\": \"No\", \"files\": [], \"title\": \"\", \"status\": \"Completed\", \"ean_more\": \"\", \"plu_scan\": \"In the\", \"products\": [], \"sub_title\": \"\", \"description\": \"\", \"text_italic\": \"\", \"need_for_price\": {\"title\": \"Need for price change in store data\", \"value\": false}, \"not_for_printing\": {\"title\": \"Not for printing\", \"value\": false}, \"promotional_offer\": 0, \"supplier_discount\": \"0%\", \"additional_description\": \"\"}', '2025-02-04 13:20:06', '2025-02-04 13:23:47', NULL),
(34, 1, 7, 1, 34, 3, NULL, '{\"color\": \"No\", \"files\": [], \"title\": \"\", \"status\": \"Created\", \"ean_more\": \"\", \"plu_scan\": \"In the\", \"products\": [], \"sub_title\": \"\", \"description\": \"\", \"text_italic\": \"\", \"need_for_price\": {\"title\": \"Need for price change in store data\", \"value\": false}, \"not_for_printing\": {\"title\": \"Not for printing\", \"value\": false}, \"promotional_offer\": 0, \"supplier_discount\": \"0%\", \"additional_description\": \"\"}', '2025-09-02 14:50:26', '2025-09-02 14:50:26', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `re_logins`
--

CREATE TABLE `re_logins` (
  `id` bigint UNSIGNED NOT NULL,
  `from_user_id` bigint UNSIGNED NOT NULL,
  `to_user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(2, 'admin'),
(3, 'cm'),
(4, 'cm-admin'),
(5, 'designer'),
(7, 'distributor'),
(6, 'printer'),
(1, 'user');

-- --------------------------------------------------------

--
-- Структура таблицы `role_user`
--

CREATE TABLE `role_user` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `role_user`
--

INSERT INTO `role_user` (`id`, `user_id`, `role_id`) VALUES
(1, 1, 2),
(2, 2, 5),
(3, 3, 5),
(4, 4, 3),
(5, 5, 3),
(6, 6, 1),
(7, 7, 1),
(8, 8, 1),
(9, 9, 4),
(10, 10, 6),
(11, 11, 7),
(13, 12, 6),
(14, 13, 1),
(15, 14, 1),
(16, 15, 1),
(17, 16, 1),
(18, 17, 1),
(19, 18, 1),
(20, 19, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `size_surfaces`
--

CREATE TABLE `size_surfaces` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `size_surfaces`
--

INSERT INTO `size_surfaces` (`id`, `title`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'H750 x W2500 х D10', NULL, '2024-11-12 10:35:34', '2024-11-12 10:35:34'),
(2, 'H900 x W2964 х D10', NULL, '2024-11-12 10:35:34', '2024-11-12 10:35:34');

-- --------------------------------------------------------

--
-- Структура таблицы `surfaces`
--

CREATE TABLE `surfaces` (
  `id` bigint UNSIGNED NOT NULL,
  `vendor_code` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_surface` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size_surface` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `divided_bool` tinyint(1) NOT NULL,
  `url_images` json DEFAULT NULL,
  `printer_id` bigint UNSIGNED DEFAULT NULL,
  `price` decimal(8,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `surfaces`
--

INSERT INTO `surfaces` (`id`, `vendor_code`, `name`, `type_surface`, `size_surface`, `description`, `status`, `divided_bool`, `url_images`, `printer_id`, `price`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 111, 'Big banner', 'Banner', 'H900 x W2964 х D10', NULL, '[]', 0, '{\"w_200\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/surface/images/w_200/67b0a6de0ffb3/Rolled metal products.jpg\", \"original\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/surface/images/original/67b0a6dd1a982/Rolled metal products.jpg\"}', 10, 0.00, '2024-11-12 10:35:34', '2025-02-15 14:38:22', NULL),
(2, 112, 'Mini banner', 'Banner', 'H900 x W2964 х D10', NULL, '[0]', 1, '{\"w_200\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/surface/images/w_200/67b0a6ceeffb3/yes.jpg\", \"original\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/surface/images/original/67b0a6cd41a46/yes.jpg\"}', 12, 0.00, '2024-11-12 10:35:34', '2025-02-15 14:38:07', NULL),
(3, 113, 'Big flag', 'Flag', 'H750 x W2500 х D10', NULL, '[1,0]', 1, '{\"w_200\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/surface/images/w_200/67b09e97a56e7/a_image.jpg\", \"original\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/surface/images/original/67b09e9721c3b/a_image.jpg\"}', 10, 1.23, '2024-11-12 10:35:34', '2025-02-19 11:57:20', NULL),
(8, 113, 'Big flag 2', 'Flag', 'H750 x W2500 х D10', NULL, '[0]', 1, '{\"w_200\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/surface/images/w_200/6788dece18803/no.jpg\", \"original\": \"https://pub-83b914fe3e7642f99f138cab9d242302.r2.dev/uploads/surface/images/original/6788decbc5800/no.jpg\"}', 10, 0.00, '2024-11-14 15:52:17', '2025-01-16 10:26:22', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `distributor_id` bigint UNSIGNED DEFAULT NULL,
  `admin_id` bigint UNSIGNED DEFAULT NULL,
  `percent_promotion_report` tinyint UNSIGNED DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `system_settings`
--

INSERT INTO `system_settings` (`id`, `distributor_id`, `admin_id`, `percent_promotion_report`, `created_at`, `updated_at`) VALUES
(1, 11, 1, 8, '2024-12-13 09:26:02', '2024-12-13 09:55:01');

-- --------------------------------------------------------

--
-- Структура таблицы `telescope_entries`
--

CREATE TABLE `telescope_entries` (
  `sequence` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `family_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `should_display_on_index` tinyint(1) NOT NULL DEFAULT '1',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `telescope_entries`
--

INSERT INTO `telescope_entries` (`sequence`, `uuid`, `batch_id`, `family_hash`, `should_display_on_index`, `type`, `content`, `created_at`) VALUES
(1, '9fcdb898-703c-4718-bed8-686c8771c36d', '9fcdb899-bda2-4b8a-af85-6cd64aaf21c4', NULL, 1, 'query', '{\"connection\":\"mysql\",\"driver\":\"mysql\",\"bindings\":[],\"sql\":\"select exists (select 1 from information_schema.tables where table_schema = \'db_docker\' and table_name = \'migrations\' and table_type in (\'BASE TABLE\', \'SYSTEM VERSIONED\')) as `exists`\",\"time\":\"27.98\",\"slow\":false,\"file\":\"\\/var\\/www\\/html\\/artisan\",\"line\":35,\"hash\":\"90385ea200cc8442451d82e16a0ec279\",\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:01'),
(2, '9fcdb898-9b6e-414c-8b69-ab5c061b412a', '9fcdb899-bda2-4b8a-af85-6cd64aaf21c4', NULL, 1, 'query', '{\"connection\":\"mysql\",\"driver\":\"mysql\",\"bindings\":[],\"sql\":\"select exists (select 1 from information_schema.tables where table_schema = \'db_docker\' and table_name = \'migrations\' and table_type in (\'BASE TABLE\', \'SYSTEM VERSIONED\')) as `exists`\",\"time\":\"1.08\",\"slow\":false,\"file\":\"\\/var\\/www\\/html\\/artisan\",\"line\":35,\"hash\":\"90385ea200cc8442451d82e16a0ec279\",\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:01'),
(3, '9fcdb898-a339-4ef4-9385-6a1bc641af46', '9fcdb899-bda2-4b8a-af85-6cd64aaf21c4', NULL, 1, 'query', '{\"connection\":\"mysql\",\"driver\":\"mysql\",\"bindings\":[],\"sql\":\"select `migration` from `migrations` order by `batch` asc, `migration` asc\",\"time\":\"7.60\",\"slow\":false,\"file\":\"\\/var\\/www\\/html\\/artisan\",\"line\":35,\"hash\":\"ed08a59c7f0b8851f0fd2291ca94d5c7\",\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:01'),
(4, '9fcdb898-a512-4410-b0aa-716f2bf798c4', '9fcdb899-bda2-4b8a-af85-6cd64aaf21c4', NULL, 1, 'query', '{\"connection\":\"mysql\",\"driver\":\"mysql\",\"bindings\":[],\"sql\":\"select `migration` from `migrations` order by `batch` asc, `migration` asc\",\"time\":\"0.57\",\"slow\":false,\"file\":\"\\/var\\/www\\/html\\/artisan\",\"line\":35,\"hash\":\"ed08a59c7f0b8851f0fd2291ca94d5c7\",\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:01'),
(5, '9fcdb898-a952-48e8-95c0-4e9ecc41bcdd', '9fcdb899-bda2-4b8a-af85-6cd64aaf21c4', NULL, 1, 'query', '{\"connection\":\"mysql\",\"driver\":\"mysql\",\"bindings\":[],\"sql\":\"select max(`batch`) as aggregate from `migrations`\",\"time\":\"0.49\",\"slow\":false,\"file\":\"\\/var\\/www\\/html\\/artisan\",\"line\":35,\"hash\":\"06e60d7b3d1a0c2de504de4e6f27735e\",\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:01'),
(6, '9fcdb899-0198-4cdb-8ef2-f3437c44b998', '9fcdb899-bda2-4b8a-af85-6cd64aaf21c4', NULL, 1, 'query', '{\"connection\":\"mysql\",\"driver\":\"mysql\",\"bindings\":[],\"sql\":\"create table `telescope_entries` (`sequence` bigint unsigned not null auto_increment primary key, `uuid` char(36) not null, `batch_id` char(36) not null, `family_hash` varchar(255) null, `should_display_on_index` tinyint(1) not null default \'1\', `type` varchar(20) not null, `content` longtext not null, `created_at` datetime null) default character set utf8mb4 collate \'utf8mb4_unicode_ci\'\",\"time\":\"38.24\",\"slow\":false,\"file\":\"\\/var\\/www\\/html\\/database\\/migrations\\/2018_08_08_100000_create_telescope_entries_table.php\",\"line\":24,\"hash\":\"d9429550f8856c1af1c89f24a6440cb5\",\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:01'),
(7, '9fcdb899-0eb1-4863-b211-9998bd4f9027', '9fcdb899-bda2-4b8a-af85-6cd64aaf21c4', NULL, 1, 'query', '{\"connection\":\"mysql\",\"driver\":\"mysql\",\"bindings\":[],\"sql\":\"alter table `telescope_entries` add unique `telescope_entries_uuid_unique`(`uuid`)\",\"time\":\"33.18\",\"slow\":false,\"file\":\"\\/var\\/www\\/html\\/database\\/migrations\\/2018_08_08_100000_create_telescope_entries_table.php\",\"line\":24,\"hash\":\"9fb859ae1faff74c6b9e0b70dfd8eea9\",\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:01'),
(8, '9fcdb899-2504-433c-80b7-b157cf2bcfe5', '9fcdb899-bda2-4b8a-af85-6cd64aaf21c4', NULL, 1, 'query', '{\"connection\":\"mysql\",\"driver\":\"mysql\",\"bindings\":[],\"sql\":\"alter table `telescope_entries` add index `telescope_entries_batch_id_index`(`batch_id`)\",\"time\":\"56.77\",\"slow\":false,\"file\":\"\\/var\\/www\\/html\\/database\\/migrations\\/2018_08_08_100000_create_telescope_entries_table.php\",\"line\":24,\"hash\":\"2b075509a9242d6e3f622536c5ccca07\",\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:01'),
(9, '9fcdb899-3847-4011-8a68-46efab6e7b86', '9fcdb899-bda2-4b8a-af85-6cd64aaf21c4', NULL, 1, 'query', '{\"connection\":\"mysql\",\"driver\":\"mysql\",\"bindings\":[],\"sql\":\"alter table `telescope_entries` add index `telescope_entries_family_hash_index`(`family_hash`)\",\"time\":\"48.94\",\"slow\":false,\"file\":\"\\/var\\/www\\/html\\/database\\/migrations\\/2018_08_08_100000_create_telescope_entries_table.php\",\"line\":24,\"hash\":\"3d25a2a244bd2028dfa0326d3dbf7f4c\",\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:01'),
(10, '9fcdb899-4312-4fd6-92df-369348973030', '9fcdb899-bda2-4b8a-af85-6cd64aaf21c4', NULL, 1, 'query', '{\"connection\":\"mysql\",\"driver\":\"mysql\",\"bindings\":[],\"sql\":\"alter table `telescope_entries` add index `telescope_entries_created_at_index`(`created_at`)\",\"time\":\"27.29\",\"slow\":false,\"file\":\"\\/var\\/www\\/html\\/database\\/migrations\\/2018_08_08_100000_create_telescope_entries_table.php\",\"line\":24,\"hash\":\"7352e7f84460fb7ffc450e7ea4de9dc7\",\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:01'),
(11, '9fcdb899-580e-4b00-9ba9-37f52ddf0499', '9fcdb899-bda2-4b8a-af85-6cd64aaf21c4', NULL, 1, 'query', '{\"connection\":\"mysql\",\"driver\":\"mysql\",\"bindings\":[],\"sql\":\"alter table `telescope_entries` add index `telescope_entries_type_should_display_on_index_index`(`type`, `should_display_on_index`)\",\"time\":\"53.36\",\"slow\":false,\"file\":\"\\/var\\/www\\/html\\/database\\/migrations\\/2018_08_08_100000_create_telescope_entries_table.php\",\"line\":24,\"hash\":\"7317a4cad2dfa1a5167548a6acd0b6a5\",\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:01'),
(12, '9fcdb899-67f9-4ab2-b275-272d1eab4985', '9fcdb899-bda2-4b8a-af85-6cd64aaf21c4', NULL, 1, 'query', '{\"connection\":\"mysql\",\"driver\":\"mysql\",\"bindings\":[],\"sql\":\"create table `telescope_entries_tags` (`entry_uuid` char(36) not null, `tag` varchar(255) not null, primary key (`entry_uuid`, `tag`)) default character set utf8mb4 collate \'utf8mb4_unicode_ci\'\",\"time\":\"33.66\",\"slow\":false,\"file\":\"\\/var\\/www\\/html\\/database\\/migrations\\/2018_08_08_100000_create_telescope_entries_table.php\",\"line\":41,\"hash\":\"f8c7e1e3c3d557b70e7a918609f839f2\",\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:01'),
(13, '9fcdb899-73b9-4201-8f69-0c1579c86f15', '9fcdb899-bda2-4b8a-af85-6cd64aaf21c4', NULL, 1, 'query', '{\"connection\":\"mysql\",\"driver\":\"mysql\",\"bindings\":[],\"sql\":\"alter table `telescope_entries_tags` add index `telescope_entries_tags_tag_index`(`tag`)\",\"time\":\"29.73\",\"slow\":false,\"file\":\"\\/var\\/www\\/html\\/database\\/migrations\\/2018_08_08_100000_create_telescope_entries_table.php\",\"line\":41,\"hash\":\"0bdb35d17e876d6225a7774a2c17647d\",\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:01'),
(14, '9fcdb899-92cd-411b-81a6-83ac49b2b162', '9fcdb899-bda2-4b8a-af85-6cd64aaf21c4', NULL, 1, 'query', '{\"connection\":\"mysql\",\"driver\":\"mysql\",\"bindings\":[],\"sql\":\"alter table `telescope_entries_tags` add constraint `telescope_entries_tags_entry_uuid_foreign` foreign key (`entry_uuid`) references `telescope_entries` (`uuid`) on delete cascade\",\"time\":\"79.18\",\"slow\":false,\"file\":\"\\/var\\/www\\/html\\/database\\/migrations\\/2018_08_08_100000_create_telescope_entries_table.php\",\"line\":41,\"hash\":\"662a818f80a3a9ba2570081fd7a6af2f\",\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:01'),
(15, '9fcdb899-a11c-4b61-b091-256259bc796a', '9fcdb899-bda2-4b8a-af85-6cd64aaf21c4', NULL, 1, 'query', '{\"connection\":\"mysql\",\"driver\":\"mysql\",\"bindings\":[],\"sql\":\"create table `telescope_monitoring` (`tag` varchar(255) not null, primary key (`tag`)) default character set utf8mb4 collate \'utf8mb4_unicode_ci\'\",\"time\":\"36.16\",\"slow\":false,\"file\":\"\\/var\\/www\\/html\\/database\\/migrations\\/2018_08_08_100000_create_telescope_entries_table.php\",\"line\":54,\"hash\":\"18d1fa09eade84a80848982d91caec5c\",\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:01'),
(16, '9fcdb899-a90f-42cc-82ae-85bc0c603507', '9fcdb899-bda2-4b8a-af85-6cd64aaf21c4', NULL, 1, 'query', '{\"connection\":\"mysql\",\"driver\":\"mysql\",\"bindings\":[],\"sql\":\"insert into `migrations` (`migration`, `batch`) values (\'2018_08_08_100000_create_telescope_entries_table\', 15)\",\"time\":\"8.29\",\"slow\":false,\"file\":\"\\/var\\/www\\/html\\/artisan\",\"line\":35,\"hash\":\"f2b8e8e4266db16aec6db940c643eb68\",\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:01'),
(17, '9fcdb899-b121-4eed-a5f5-46ad5ff9ba4a', '9fcdb899-bda2-4b8a-af85-6cd64aaf21c4', NULL, 1, 'command', '{\"command\":\"migrate\",\"exit_code\":0,\"arguments\":{\"command\":\"migrate\"},\"options\":{\"database\":null,\"force\":false,\"path\":[],\"realpath\":false,\"schema-path\":null,\"pretend\":false,\"seed\":false,\"seeder\":null,\"step\":false,\"graceful\":false,\"isolated\":false,\"help\":false,\"quiet\":false,\"verbose\":false,\"version\":false,\"ansi\":null,\"no-interaction\":false,\"env\":null},\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:01'),
(18, '9fcdb8be-c508-48a1-a0fc-9b826e4380ab', '9fcdb8bf-1278-4f2c-9d6d-1353a161c9cf', NULL, 1, 'view', '{\"name\":\"welcome\",\"path\":\"\\/resources\\/views\\/welcome.blade.php\",\"data\":[],\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:26'),
(19, '9fcdb8bf-01a9-4a53-a13d-ada297aeaa61', '9fcdb8bf-1278-4f2c-9d6d-1353a161c9cf', NULL, 1, 'request', '{\"ip_address\":\"172.20.0.1\",\"uri\":\"\\/\",\"method\":\"GET\",\"controller_action\":\"Closure\",\"middleware\":[\"web\"],\"headers\":{\"host\":\"localhost:1111\",\"user-agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko\\/20100101 Firefox\\/142.0\",\"accept\":\"text\\/html,application\\/xhtml+xml,application\\/xml;q=0.9,*\\/*;q=0.8\",\"accept-language\":\"ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3\",\"accept-encoding\":\"gzip, deflate, br, zstd\",\"connection\":\"keep-alive\",\"cookie\":\"pma_lang=ru; pmaUser-1=RApOeQSMtU5jPg8TcEdt2%2Fnm1VThBylUyDLlZPTGu5sWZjQjFGDgsDzmdGs%3D; CURRENT_USER={%22id%22:1%2C%22name%22:%22Admin%20User%22%2C%22email%22:%22admin@admin.com%22%2C%22email_verified_at%22:%222024-11-12T10:35:33.000000Z%22%2C%22status%22:true%2C%22created_at%22:%222024-11-12T10:35:33.000000Z%22%2C%22updated_at%22:%222024-11-12T10:35:33.000000Z%22%2C%22deleted_at%22:null%2C%22company_name%22:%22Admin%20Company%22%2C%22surname%22:%22User%20Surname%22%2C%22phone%22:%22+3801234567-1%22%2C%22name_invoice_recipient%22:%22Admin%20Recipient%22%2C%22company_number%22:%22999999999%22%2C%22email_invoice_recipient%22:%22admininvoice@admin.com%22%2C%22reference_number%22:%2288888888%22%2C%22c_o%22:%22Admin%20c\\/o%22%2C%22post_address%22:%22Admin%20Street%201%22%2C%22postcode%22:%22111111%22%2C%22phone_2%22:%22+3801234567-2%22%2C%22municipality_number%22:%222222%22%2C%22kommune%22:%22Admin%20Kommune%22%2C%22country%22:%22Admin%20Country%22%2C%22number_country%22:%2212%22%2C%22group%22:%22RBA%22%2C%22category_ids%22:[]%2C%22company_categories%22:[]%2C%22role%22:%22admin%22%2C%22re_login%22:null%2C%22categories%22:[]%2C%22company_planner%22:[]}; BEARER_TOKEN=Bearer%20209|QdGVCUgEHJ4nOmg5dZ4Tuj0YXx2sNo6hufZayQFDa09c54e5; phpMyAdmin=48075251a66ab59468c947d82074a89e; XSRF-TOKEN=eyJpdiI6IjhyWjAvV0dnK0JyYmc5OFVkeklrVlE9PSIsInZhbHVlIjoib2I0NEc2c2g5Si9XaFYzSGZIOC8vbXZ3aEVaTklTSml4Z0hUaEwyZ3JidStlY2loS2VjMFRnVk5CekhadlFpcEF0M3hEV1MyRUVKb1h2anhDU2NNWXhCSlZVK1VPSEE5TlpvZnRPZFJPQmxkSnVLMWxIVkZteGprSW42Z3g4OEYiLCJtYWMiOiIzM2Q1MTA2ZDFiODNiOGU1MDM1M2Q4NDhkNjEwYjNiN2U3OGMxMWJhMzRmY2YxNTUwZjk1Y2RkZWYzMGRhZjVmIiwidGFnIjoiIn0%3D; laravel_session=eyJpdiI6Ik1wTm5vZ24ycEMrenl4UXpPOGdpaEE9PSIsInZhbHVlIjoiL2JSNFlwbEZLLzZEWWh3REtCNGJTTEtsd25nNEZKOG01Y216Um9tZEJOWFVmUEgvdlZjUnNZNUM2RzYza3lNT1JzSEZ5SnBpUHJXTlZnWFZhWXJkT0M3dlNyZU0wdnlIMWtMc0k3MU9pMFQzaG1wTlB4bEFyUllFdlE3b0I2L3oiLCJtYWMiOiJjMjg1NzVkNjVjNmZlNzQ3MWE2NWE5NzMxOGNjZjAyNTgwODIwZjQyM2M4YzA1NjcwMGM3ZDUxNTUwMDZjOGU3IiwidGFnIjoiIn0%3D\",\"upgrade-insecure-requests\":\"1\",\"sec-fetch-dest\":\"document\",\"sec-fetch-mode\":\"navigate\",\"sec-fetch-site\":\"none\",\"sec-fetch-user\":\"?1\",\"priority\":\"u=0, i\"},\"payload\":[],\"session\":{\"_token\":\"sguVkGaRvlCZKTGo5WmTosbj7Kr0koVnayzFsqO4\",\"_previous\":{\"url\":\"http:\\/\\/localhost:1111\"},\"_flash\":{\"old\":[],\"new\":[]}},\"response_headers\":{\"content-type\":\"text\\/html; charset=UTF-8\",\"cache-control\":\"no-cache, private\",\"date\":\"Fri, 05 Sep 2025 07:59:25 GMT\",\"set-cookie\":\"XSRF-TOKEN=eyJpdiI6IlVXRDlWdStDS0EySm00OUlXUzJZUlE9PSIsInZhbHVlIjoiaS9hcTJIRDdVTUVyQmRSUCs2TStCd3Zob2wxSUJFakhuRDdicFd3d0NOSmNoeCsvT1VQTUdoMkNTKzQyQXc2UTY1VUw1Q1VselNFMkhxSzBzSzlBSkdNT3puUFpteDY5NTQ2YWppSEtLdVFjQWIxS0QrQml2YytmTytrQllCWWkiLCJtYWMiOiJkNmQyMzcyMzVhYmJiZmUzODU0ZWM2YTVkMDkxNThmYzJiMTFlZTcyZjQ0ODk1NzlkZWExYjIzMTM1MzFmYTY0IiwidGFnIjoiIn0%3D; expires=Fri, 05 Sep 2025 09:59:26 GMT; Max-Age=7200; path=\\/; samesite=lax, laravel_session=eyJpdiI6IlZnRGc2TjJ3emxWK25Na3J3VXcxVEE9PSIsInZhbHVlIjoicVVXbW4ySjZvdEFlc0wxbFBsdHBIVVgyZWJiY1lYRTFvckVyS0ZTVmRLUTU4ZVVGMWdrclBRSVB6d25lMFhPRlYrNXFpT3ZNZXIvVzVuaEtBU2RBL25LNlg0cXpBcFhOZ1NkNGNLUVNHQk1wNktIdjNZTUM2TGpxWDJmVnl5TTciLCJtYWMiOiI4NjEwMDcxZGViN2Y0YTIxM2FhMmY4YzY0OTc3NmU1ODAyMGFiZjY4ZTE2Mzg3YzAyZjRhOWQ1M2IyMDE1ZTgxIiwidGFnIjoiIn0%3D; expires=Fri, 05 Sep 2025 09:59:26 GMT; Max-Age=7200; path=\\/; httponly; samesite=lax\"},\"response_status\":200,\"response\":{\"view\":\"\\/var\\/www\\/html\\/resources\\/views\\/welcome.blade.php\",\"data\":[]},\"duration\":3605,\"memory\":10,\"hostname\":\"5dc86cc4e53f\"}', '2025-09-05 07:59:26');

-- --------------------------------------------------------

--
-- Структура таблицы `telescope_entries_tags`
--

CREATE TABLE `telescope_entries_tags` (
  `entry_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `telescope_monitoring`
--

CREATE TABLE `telescope_monitoring` (
  `tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `tests`
--

CREATE TABLE `tests` (
  `id` bigint UNSIGNED NOT NULL,
  `text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `type_surfaces`
--

CREATE TABLE `type_surfaces` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `type_surfaces`
--

INSERT INTO `type_surfaces` (`id`, `title`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Flag', NULL, '2024-11-12 10:35:34', '2024-11-12 10:35:34'),
(2, 'Mini flag', NULL, '2024-11-12 10:35:34', '2024-11-12 10:35:34'),
(3, 'Banner', NULL, '2024-11-12 10:35:34', '2024-11-12 10:35:34');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `status`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin User', 'admin@admin.com', '2024-11-12 10:35:33', '$2y$10$3jKCGzr4V3uiJaP4hxIxvedmSrznppDE7Y3n8wF5oCtbDRZXTdICG', 1, NULL, '2024-11-12 10:35:33', '2024-11-12 10:35:33', NULL),
(2, 'Designer User 1', 'designer1@designer.com', '2024-11-12 10:35:33', '$2y$10$o5gFBPz7xjFasfawRlpoaOnw07hDuzMFFrtvmS/WiDjMQE2lZ7Loq', 1, NULL, '2024-11-12 10:35:33', '2025-02-18 14:29:46', NULL),
(3, 'Designer User 2', 'designer2@designer.com', '2024-11-12 10:35:33', '$2y$10$OG9EepJceFgOZcp/.c3hkOfTfYX/gEwuoHBos6U9572Z/B8.yvsf6', 1, NULL, '2024-11-12 10:35:33', '2024-11-12 10:35:33', NULL),
(4, 'CM User 1', 'cm1@cm.com', '2024-11-12 10:35:33', '$2y$10$So4tjyJYl1fNQhbxGxDSn.QHuzaXqjJihirr5JltrLL88AXHyAEEW', 1, NULL, '2024-11-12 10:35:33', '2024-11-12 10:35:33', NULL),
(5, 'CM User 2', 'cm2@cm.com', '2024-11-12 10:35:33', '$2y$10$P2zf3gpiOty1RNRNsuXkV.zpitGV.THfpJ3vKz/SdAqxOMT5j7srW', 1, NULL, '2024-11-12 10:35:33', '2024-11-12 10:35:33', NULL),
(6, 'Regular User', 'user@user.com', '2024-11-12 10:35:33', '$2y$10$Ic6Uf3ZDy0LIMBMHrtrq9ex7SNwH1HBNv.sw7r3l5DiP4/w15vEqG', 1, NULL, '2024-11-12 10:35:33', '2025-02-04 17:44:08', NULL),
(7, 'Regular User 2', 'user2@user.com', '2024-11-12 10:35:33', '$2y$10$6J7bnAe.ukO3BG.45qkZb.txAbP6gK9WQgWoV37EyGaukTGiIfydi', 1, NULL, '2024-11-12 10:35:33', '2024-11-12 10:35:33', NULL),
(8, 'Regular User 3', 'user3@user.com', '2024-11-12 10:35:34', '$2y$10$5qMmWT1S2WB4/3Rj61NUS.zSQ59YhCpOQzpw8D9o/t6Vc0hcWR4Iy', 1, NULL, '2024-11-12 10:35:34', '2024-11-12 10:35:34', NULL),
(9, 'CM Admin User', 'cmadmin@cmadmin.com', '2024-11-12 10:35:34', '$2y$10$//.lOsX5jGr43OrYQeXCdemkr98q/bq94nobjM9ypPUTixZRdJ.7K', 1, NULL, '2024-11-12 10:35:34', '2024-11-12 10:35:34', NULL),
(10, 'Printer User', 'printer@printer.com', '2024-11-12 10:35:34', '$2y$10$5N3s2C06Uxl2AJm7lSAIjeAlg.V181mQEFyzvPAGdkMP7NmgVUx8W', 1, NULL, '2024-11-12 10:35:34', '2024-12-03 16:29:42', NULL),
(11, 'Distributor User', 'distributor@distributor.com', '2024-11-12 10:35:34', '$2y$10$eUVE6IKGlTEY6B0SvRxrm.qO98QKqjQGrf3DIBB8MPnYcHZ5VI5p.', 1, NULL, '2024-11-12 10:35:34', '2024-12-10 09:55:49', NULL),
(12, 'Printer User 2', 'printer2@printer.com', '2024-12-05 09:38:32', '$2y$10$MFzeJLgkNABvNTAy544YXenWzTzdeyguA2bmzmVVzqZAfjEA9S42m', 1, NULL, '2024-12-05 09:38:32', '2024-12-05 09:38:32', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `user_data`
--

CREATE TABLE `user_data` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `surname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_invoice_recipient` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_invoice_recipient` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_o` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `post_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `municipality_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kommune` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_ids` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `user_data`
--

INSERT INTO `user_data` (`id`, `user_id`, `company_name`, `surname`, `phone`, `name_invoice_recipient`, `company_number`, `email_invoice_recipient`, `reference_number`, `c_o`, `post_address`, `postcode`, `phone_2`, `municipality_number`, `kommune`, `country`, `number_country`, `group`, `category_ids`) VALUES
(1, 1, 'Admin Company', 'User Surname', '+3801234567-1', 'Admin Recipient', '999999999', 'admininvoice@admin.com', '88888888', 'Admin c/o', 'Admin Street 1', '111111', '+3801234567-2', '2222', 'Admin Kommune', 'Admin Country', '12', 'RBA', NULL),
(2, 2, 'Designer Company 1', 'User Surname', '+3801234567-3', 'Designer Recipient 1', '999999997', 'designerinvoice1@designer.com', '88888890', 'Designer 1 c/o', 'Designer Street 1', '111113', '+3801234567-4', '2224', 'Designer Kommune 1', 'Designer Country 1', '14', 'RBA', '[]'),
(3, 3, 'Designer Company 2', 'User Surname', '+3801234567-5', 'Designer Recipient 2', '999999996', 'designerinvoice2@designer.com', '88888891', 'Designer 2 c/o', 'Designer Street 2', '111114', '+3801234567-6', '2225', 'Designer Kommune 2', 'Designer Country 2', '15', 'RBA', NULL),
(4, 4, 'CM Company 1', 'User Surname', '+3801234567-7', 'CM Recipient 1', '999999995', 'cminvoice1@cm.com', '88888892', 'CM 1 c/o', 'CM Street 1', '111115', '+3801234567-8', '2226', 'CM Kommune 1', 'CM Country 1', '16', 'RBA', NULL),
(5, 5, 'CM Company 2', 'User Surname', '+3801234567-9', 'CM Recipient 2', '999999994', 'cminvoice2@cm.com', '88888893', 'CM 2 c/o', 'CM Street 2', '111116', '+3801234567-10', '2227', 'CM Kommune 2', 'CM Country 2', '17', 'RBA', NULL),
(6, 6, 'User Company', 'User Surname', '+3801234567-17', 'User Recipient', '999999990', 'userinvoice@user.com', '88888897', 'User c/o', 'User Street 1', '111120', '+3801234567-18', '2231', 'User Kommune', 'User Country', '21', 'RBA', '[\"1\", \"2\", \"3\", \"4\", \"5\", \"6\", \"7\", \"8\", \"9\", \"10\"]'),
(7, 7, 'User Company 2', 'User Surname', '+3801234567-23', 'User Recipient 2', '999999989', 'userinvoice2@user.com', '88888898', 'User 2 c/o', 'User Street 2', '111121', '+3801234567-24', '2232', 'User Kommune 2', 'User Country 2', '22', 'RBA', '[1]'),
(8, 8, 'User Company 3', 'User Surname', '+3801234567-25', 'User Recipient 3', '999999988', 'userinvoice3@user.com', '88888899', 'User 3 c/o', 'User Street 3', '111122', '+3801234567-26', '2233', 'User Kommune 3', 'User Country 3', '23', 'DO', '[\"1\", \"4\", \"8\"]'),
(9, 9, 'CM Admin Company', 'User Surname', '+3801234567-11', 'CM Admin Recipient', '999999993', 'cmadmininvoice@cmadmin.com', '88888894', 'CM Admin c/o', 'CM Admin Street 1', '111117', '+3801234567-12', '2228', 'CM Admin Kommune', 'CM Admin Country', '18', 'RBA', NULL),
(10, 10, 'Printer Company', 'User Surname', '+3801234567-13', 'Printer Recipient', '999999992', 'printerinvoice@printer.com', '88888895', 'Printer c/o', 'Printer Street 1', '111118', '+3801234567-14', '2229', 'Printer Kommune', 'Printer Country', '19', 'RBA', '[]'),
(11, 11, 'Distributor Company', 'User Surname', '+3801234567-15', 'Distributor Recipient', '999999991', 'distributorinvoice@distributor.com', '88888896', 'Distributor c/o', 'Distributor Street 1', '111119', '+3801234567-16', '2230', 'Distributor Kommune', 'Distributor Country', '20', 'RBA', '[]'),
(12, 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'RBA', '[]');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_unique` (`name`);

--
-- Индексы таблицы `company_planners`
--
ALTER TABLE `company_planners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_planners_user_id_foreign` (`user_id`);

--
-- Индексы таблицы `designs`
--
ALTER TABLE `designs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `designs_category_id_foreign` (`category_id`);

--
-- Индексы таблицы `design_chats`
--
ALTER TABLE `design_chats`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `distributor_trackers`
--
ALTER TABLE `distributor_trackers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `distributor_trackers_promotion_id_foreign` (`promotion_id`),
  ADD KEY `distributor_trackers_company_id_foreign` (`company_id`);

--
-- Индексы таблицы `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Индексы таблицы `feedback_messages`
--
ALTER TABLE `feedback_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedback_messages_from_user_id_foreign` (`from_user_id`),
  ADD KEY `feedback_messages_to_user_id_foreign` (`to_user_id`);

--
-- Индексы таблицы `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Индексы таблицы `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `log_company_planners`
--
ALTER TABLE `log_company_planners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `log_company_planners_user_id_foreign` (`user_id`),
  ADD KEY `log_company_planners_surface_id_foreign` (`surface_id`);

--
-- Индексы таблицы `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Индексы таблицы `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Индексы таблицы `printed_promotions`
--
ALTER TABLE `printed_promotions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `print_promotion_printeds_promotion_id_foreign` (`promotion_id`),
  ADD KEY `print_promotion_printeds_printer_id_foreign` (`printer_id`);

--
-- Индексы таблицы `print_promotion_reports`
--
ALTER TABLE `print_promotion_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `print_promotion_reports_promotion_id_foreign` (`promotion_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promotions_who_created_id_foreign` (`who_created_id`);

--
-- Индексы таблицы `promotion_surfaces`
--
ALTER TABLE `promotion_surfaces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promotion_surfaces_promotion_id_foreign` (`promotion_id`),
  ADD KEY `promotion_surfaces_surface_id_foreign` (`surface_id`);

--
-- Индексы таблицы `promotion_surface_designs`
--
ALTER TABLE `promotion_surface_designs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promotion_surface_designs_design_category_id_foreign` (`design_category_id`),
  ADD KEY `promotion_surface_designs_design_id_foreign` (`design_id`),
  ADD KEY `promotion_surface_designs_promotion_id_foreign` (`promotion_id`),
  ADD KEY `promotion_surface_designs_surface_id_foreign` (`surface_id`),
  ADD KEY `promotion_surface_designs_chat_id_foreign` (`chat_id`);

--
-- Индексы таблицы `re_logins`
--
ALTER TABLE `re_logins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `re_logins_from_user_id_foreign` (`from_user_id`),
  ADD KEY `re_logins_to_user_id_foreign` (`to_user_id`);

--
-- Индексы таблицы `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Индексы таблицы `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `size_surfaces`
--
ALTER TABLE `size_surfaces`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `size_surfaces_title_unique` (`title`);

--
-- Индексы таблицы `surfaces`
--
ALTER TABLE `surfaces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `surfaces_type_surface_foreign` (`type_surface`),
  ADD KEY `surfaces_size_surface_foreign` (`size_surface`),
  ADD KEY `surfaces_printer_id_foreign` (`printer_id`);

--
-- Индексы таблицы `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `system_settings_distributor_id_foreign` (`distributor_id`),
  ADD KEY `system_settings_admin_id_foreign` (`admin_id`);

--
-- Индексы таблицы `telescope_entries`
--
ALTER TABLE `telescope_entries`
  ADD PRIMARY KEY (`sequence`),
  ADD UNIQUE KEY `telescope_entries_uuid_unique` (`uuid`),
  ADD KEY `telescope_entries_batch_id_index` (`batch_id`),
  ADD KEY `telescope_entries_family_hash_index` (`family_hash`),
  ADD KEY `telescope_entries_created_at_index` (`created_at`),
  ADD KEY `telescope_entries_type_should_display_on_index_index` (`type`,`should_display_on_index`);

--
-- Индексы таблицы `telescope_entries_tags`
--
ALTER TABLE `telescope_entries_tags`
  ADD PRIMARY KEY (`entry_uuid`,`tag`),
  ADD KEY `telescope_entries_tags_tag_index` (`tag`);

--
-- Индексы таблицы `telescope_monitoring`
--
ALTER TABLE `telescope_monitoring`
  ADD PRIMARY KEY (`tag`);

--
-- Индексы таблицы `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `type_surfaces`
--
ALTER TABLE `type_surfaces`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_surfaces_title_unique` (`title`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Индексы таблицы `user_data`
--
ALTER TABLE `user_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_data_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `company_planners`
--
ALTER TABLE `company_planners`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `designs`
--
ALTER TABLE `designs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `design_chats`
--
ALTER TABLE `design_chats`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT для таблицы `distributor_trackers`
--
ALTER TABLE `distributor_trackers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `feedback_messages`
--
ALTER TABLE `feedback_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=444;

--
-- AUTO_INCREMENT для таблицы `logs`
--
ALTER TABLE `logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `log_company_planners`
--
ALTER TABLE `log_company_planners`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT для таблицы `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=237;

--
-- AUTO_INCREMENT для таблицы `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=210;

--
-- AUTO_INCREMENT для таблицы `printed_promotions`
--
ALTER TABLE `printed_promotions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT для таблицы `print_promotion_reports`
--
ALTER TABLE `print_promotion_reports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `promotion_surfaces`
--
ALTER TABLE `promotion_surfaces`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `promotion_surface_designs`
--
ALTER TABLE `promotion_surface_designs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT для таблицы `re_logins`
--
ALTER TABLE `re_logins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT для таблицы `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `role_user`
--
ALTER TABLE `role_user`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `size_surfaces`
--
ALTER TABLE `size_surfaces`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `surfaces`
--
ALTER TABLE `surfaces`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `telescope_entries`
--
ALTER TABLE `telescope_entries`
  MODIFY `sequence` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT для таблицы `tests`
--
ALTER TABLE `tests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3506;

--
-- AUTO_INCREMENT для таблицы `type_surfaces`
--
ALTER TABLE `type_surfaces`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT для таблицы `user_data`
--
ALTER TABLE `user_data`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `company_planners`
--
ALTER TABLE `company_planners`
  ADD CONSTRAINT `company_planners_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `designs`
--
ALTER TABLE `designs`
  ADD CONSTRAINT `designs_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Ограничения внешнего ключа таблицы `distributor_trackers`
--
ALTER TABLE `distributor_trackers`
  ADD CONSTRAINT `distributor_trackers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `distributor_trackers_promotion_id_foreign` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `feedback_messages`
--
ALTER TABLE `feedback_messages`
  ADD CONSTRAINT `feedback_messages_from_user_id_foreign` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_messages_to_user_id_foreign` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `log_company_planners`
--
ALTER TABLE `log_company_planners`
  ADD CONSTRAINT `log_company_planners_surface_id_foreign` FOREIGN KEY (`surface_id`) REFERENCES `surfaces` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `log_company_planners_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `printed_promotions`
--
ALTER TABLE `printed_promotions`
  ADD CONSTRAINT `print_promotion_printeds_printer_id_foreign` FOREIGN KEY (`printer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `print_promotion_printeds_promotion_id_foreign` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `print_promotion_reports`
--
ALTER TABLE `print_promotion_reports`
  ADD CONSTRAINT `print_promotion_reports_promotion_id_foreign` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `promotions`
--
ALTER TABLE `promotions`
  ADD CONSTRAINT `promotions_who_created_id_foreign` FOREIGN KEY (`who_created_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `promotion_surfaces`
--
ALTER TABLE `promotion_surfaces`
  ADD CONSTRAINT `promotion_surfaces_promotion_id_foreign` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotion_surfaces_surface_id_foreign` FOREIGN KEY (`surface_id`) REFERENCES `surfaces` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `promotion_surface_designs`
--
ALTER TABLE `promotion_surface_designs`
  ADD CONSTRAINT `promotion_surface_designs_chat_id_foreign` FOREIGN KEY (`chat_id`) REFERENCES `design_chats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotion_surface_designs_design_category_id_foreign` FOREIGN KEY (`design_category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotion_surface_designs_design_id_foreign` FOREIGN KEY (`design_id`) REFERENCES `designs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotion_surface_designs_promotion_id_foreign` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotion_surface_designs_surface_id_foreign` FOREIGN KEY (`surface_id`) REFERENCES `surfaces` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `re_logins`
--
ALTER TABLE `re_logins`
  ADD CONSTRAINT `re_logins_from_user_id_foreign` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `re_logins_to_user_id_foreign` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `surfaces`
--
ALTER TABLE `surfaces`
  ADD CONSTRAINT `surfaces_printer_id_foreign` FOREIGN KEY (`printer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `surfaces_size_surface_foreign` FOREIGN KEY (`size_surface`) REFERENCES `size_surfaces` (`title`),
  ADD CONSTRAINT `surfaces_type_surface_foreign` FOREIGN KEY (`type_surface`) REFERENCES `type_surfaces` (`title`);

--
-- Ограничения внешнего ключа таблицы `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `system_settings_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `system_settings_distributor_id_foreign` FOREIGN KEY (`distributor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `telescope_entries_tags`
--
ALTER TABLE `telescope_entries_tags`
  ADD CONSTRAINT `telescope_entries_tags_entry_uuid_foreign` FOREIGN KEY (`entry_uuid`) REFERENCES `telescope_entries` (`uuid`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `user_data`
--
ALTER TABLE `user_data`
  ADD CONSTRAINT `user_data_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
