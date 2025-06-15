-- Schema for `job_portal` Database

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `status` enum('pending','selected','rejected') DEFAULT 'pending',
  `applied_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`application_id`),
  KEY `job_id` (`job_id`),
  KEY `company_id` (`company_id`),
  KEY `candidate_id` (`candidate_id`),
  CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `applications_ibfk_3` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`candidate_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `candidate_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `hash_token` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 0,
  `about_me` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `education` text DEFAULT NULL,
  `registered_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`candidate_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `city_id` int(11) NOT NULL AUTO_INCREMENT,
  `city_name` varchar(100) NOT NULL,
  PRIMARY KEY (`city_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_holder_name` varchar(100) DEFAULT NULL,
  `company_name` varchar(150) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `website` varchar(150) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `about_company` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `active` enum('pending','approved','rejected','deactivated') DEFAULT 'pending',
  `created_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `hash_token` varchar(255) NOT NULL,
  PRIMARY KEY (`company_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_type` enum('candidate','employer') NOT NULL,
  `feedback` text DEFAULT NULL,
  `submitted_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`feedback_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `job_title` varchar(150) DEFAULT NULL,
  `job_description` text DEFAULT NULL,
  `min_salary` decimal(10,2) DEFAULT NULL,
  `max_salary` decimal(10,2) DEFAULT NULL,
  `experience` varchar(50) DEFAULT NULL,
  `posted_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `max_age` int(11) DEFAULT NULL,
  `job_type` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  PRIMARY KEY (`job_id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `from_user_id` int(11) NOT NULL,
  `from_user_type` enum('candidate','company') NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `sent_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message_replies`
--

CREATE TABLE `message_replies` (
  `reply_id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `from_user_type` enum('candidate','company') NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `reply_message` text DEFAULT NULL,
  `reply_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reply_id`),
  KEY `message_id` (`message_id`),
  CONSTRAINT `message_replies_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`message_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_jobs`
--

CREATE TABLE `saved_jobs` (
  `saved_job_id` int(11) NOT NULL AUTO_INCREMENT,
  `candidate_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `saved_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`saved_job_id`),
  KEY `candidate_id` (`candidate_id`),
  KEY `job_id` (`job_id`),
  CONSTRAINT `saved_jobs_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`candidate_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `saved_jobs_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


