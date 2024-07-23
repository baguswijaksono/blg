-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 24, 2024 at 02:26 AM
-- Server version: 10.5.24-MariaDB-cll-lve
-- PHP Version: 8.1.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bagx2515_main`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`bagx2515`@`localhost` PROCEDURE `delete_blog` (IN `p_blog_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Rollback the transaction if any error occurs
        ROLLBACK;
    END;

    -- Start the transaction
    START TRANSACTION;

    -- Delete associated tags using the parameter
    DELETE FROM blog_tags WHERE blog_id = p_blog_id;

    -- Delete associated views
    DELETE FROM views WHERE content_id = p_blog_id;

    -- Delete the blog itself
    DELETE FROM blogs WHERE id = p_blog_id;

    -- Commit the transaction
    COMMIT;
END$$

CREATE DEFINER=`bagx2515`@`localhost` PROCEDURE `delete_tag` (IN `p_tag_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Rollback the transaction if any error occurs
        ROLLBACK;
    END;

    -- Start the transaction
    START TRANSACTION;

    -- Delete associated blog tags using the parameter
    DELETE FROM blog_tags WHERE tag_id = p_tag_id;

    -- Delete the tag itself using the parameter
    DELETE FROM tags WHERE id = p_tag_id;

    -- Commit the transaction
    COMMIT;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(6) UNSIGNED NOT NULL,
  `topic` varchar(255) NOT NULL,
  `docname` varchar(255) DEFAULT NULL,
  `title` longtext NOT NULL,
  `hypertext` longtext DEFAULT NULL,
  `shortdesc` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `topic`, `docname`, `title`, `hypertext`, `shortdesc`, `created_at`) VALUES
(14, 'google-dorking', 'Google Dorking', 'Google Dorking', '<p data-sourcepos=\"9:1-9:282\">Google Dorking, juga dikenal sebagai Google Hacking, adalah seni membuat kueri penelusuran yang presisi menggunakan operator bawaan Google. Operator ini melampaui penelusuran kata kunci dasar, memungkinkan Anda menargetkan jenis file tertentu, struktur situs web, dan bahkan lokasi.</p>\r\n<h2>Operator Dasar dalam Google Dorking</h2>\r\n<p>Berikut adalah beberapa operator dasar yang sering digunakan dalam Google Dorking:</p>\r\n<h2>Mengapa Google Dorking Penting?</h2>\r\n<p>Google Dorking dapat digunakan untuk berbagai tujuan, seperti:</p>\r\n<ul>\r\n<li><strong>Penelitian Keamanan</strong>: Menemukan kerentanan di situs web dan aplikasi web.</li>\r\n<li><strong>Pengintaian</strong>: Mengumpulkan informasi tentang target sebelum melakukan serangan siber.</li>\r\n<li><strong>Pemulihan Data</strong>: Mencari data yang mungkin hilang atau tersembunyi di situs web.</li>\r\n<li><strong>Analisis Kompetitor</strong>: Mengidentifikasi informasi publik tentang kompetitor bisnis.</li>\r\n</ul>\r\n<h2>Operator Dasar dalam Google Dorking</h2>\r\n<p>Berikut adalah beberapa operator dasar yang sering digunakan dalam Google Dorking:</p>\r\n<ul>\r\n<li><strong>site:</strong> Membatasi hasil pencarian ke domain tertentu.\r\n<pre><code>site:example.com</code></pre>\r\n</li>\r\n<li><strong>intitle:</strong> Mencari halaman dengan kata kunci tertentu dalam judul.\r\n<pre><code>intitle:\"login page\"</code></pre>\r\n</li>\r\n<li><strong>inurl:</strong> Mencari URL yang mengandung kata kunci tertentu.\r\n<pre><code>inurl:admin</code></pre>\r\n</li>\r\n<li><strong>filetype:</strong> Mencari jenis file tertentu.\r\n<pre><code>filetype:pdf</code></pre>\r\n</li>\r\n<li><strong>allintext:</strong> Mencari halaman yang mengandung semua kata kunci dalam teks.\r\n<pre><code>allintext:username password</code></pre>\r\n</li>\r\n</ul>\r\n<h2>Contoh Google Dorking</h2>\r\n<ul>\r\n<li><strong>Mencari Halaman Login Admin</strong>\r\n<pre><code>intitle:\"admin login\" inurl:admin</code></pre>\r\n</li>\r\n<li><strong>Mencari File PDF tentang Keamanan Jaringan</strong>\r\n<pre><code>filetype:pdf \"network security\"</code></pre>\r\n</li>\r\n<li><strong>Mencari Informasi Sensitif di Situs Tertentu</strong>\r\n<pre><code>site:example.com intext:\"confidential\"</code></pre>\r\n</li>\r\n</ul>', 'seni membuat kueri penelusuran yang presisi menggunakan operator bawaan Google', '2024-03-10 07:35:00'),
(18, 'sql-injection', 'SQL Injection', 'SQL Injection', '<div id=\"gallery\" class=\"max-w-full overflow-hidden\"><img class=\"mx-auto w-full h-auto\" src=\"https://compsecurityconcepts.wordpress.com/wp-content/uploads/2013/11/dbcommunication.jpeg\" alt=\"Consequences of SQL injection | IT Security Concepts\" aria-hidden=\"false\" /></div>\r\n<p>SQL Injection adalah teknik injeksi kode yang mengeksploitasi kerentanan dalam perangkat lunak aplikasi dengan memasukkan pernyataan SQL berbahaya ke dalam kolom entri untuk dieksekusi (misalnya, untuk membuang konten database ke penyerang).</p>\r\n<h2>How SQL Injection Works</h2>\r\n<p>Injeksi SQL terjadi ketika input pengguna tidak dibersihkan dengan benar dan kemudian langsung disertakan dalam kueri SQL. Misalnya, pertimbangkan aplikasi web yang mengambil ID pengguna dari kolom input dan kemudian membuat kueri SQL:</p>\r\n<pre><code class=\"language-sql\">SELECT * FROM users WHERE user_id = \'1\';</code></pre>\r\n<p>Penyerang dapat memanipulasi input untuk memasukkan kode SQL. Misalnya, jika kolom masukan rentan, penyerang mungkin memasukkan:</p>\r\n<pre><code class=\"language-sql\">\' OR \'1\'=\'1</code></pre>\r\n<p>Ini menghasilkan kueri berikut:</p>\r\n<pre><code class=\"language-sql\">SELECT * FROM users WHERE user_id = \'\' OR \'1\'=\'1\';</code></pre>\r\n<p>The condition <code>\'1\'=\'1\'</code> is always true, causing the query to return all rows from the <code>users</code> table instead of just one.</p>\r\n<h4>Impact:</h4>\r\n<p>Depending on the query, this can lead to unauthorized data access, data modification, or even complete database control.</p>\r\n<h2>Examples of SQL Injection Login</h2>\r\n<h3>Bypass:</h3>\r\n<p>If a login form is vulnerable, an attacker can enter admin\'-- in the username field and anything in the password field.</p>\r\n<p>The query:</p>\r\n<pre><code class=\"language-sql\">SELECT * FROM users WHERE username = \'admin\'--\' AND password = \'\';</code></pre>\r\n<p>The <code>--</code>is a comment in SQL, so everything after it is ignored, effectively bypassing the password check.</p>\r\n<h3>Data Exfiltration:</h3>\r\n<p>An attacker can use SQL injection to extract data. For example, entering:</p>\r\n<pre><code class=\"language-sql\">\' UNION SELECT username, password FROM users; --</code></pre>\r\n<p>This can combine results from another table with the original query.</p>\r\n<h2>Preventing SQL Injection</h2>\r\n<h3>Parameterized Queries (Prepared Statements):</h3>\r\n<p>Use prepared statements which separate SQL logic from the data. This way, the database can distinguish between code and data, regardless of user input.<code class=\"language-python\">\r\n</code></p>\r\n<p>In PHP using PDO:</p>\r\n<pre><code class=\"language-php\">$stmt = $pdo-&gt;prepare(\'SELECT * FROM users WHERE user_id = :user_id\');\r\n$stmt-&gt;execute([\'user_id\' =&gt; $userId]);\r\n</code></pre>\r\n<h4>Stored Procedures:</h4>\r\n<p>Use stored procedures on the database side, which can encapsulate the SQL logic and prevent direct exposure to SQL statements.</p>\r\n<pre><code class=\"sql\">CREATE PROCEDURE GetUser(IN userId INT)<br />BEGIN<br />    SELECT * FROM users WHERE user_id = userId;<br />END;\r\n</code></pre>\r\n<h4>Input Validation:</h4>\r\n<p>Validate input to ensure it conforms to expected formats, lengths, and types. Reject or sanitize any input that does not meet these criteria.</p>\r\n<h4>Escaping User Input:</h4>\r\n<p>If parameterized queries are not available, ensure all user input is properly escaped before being included in SQL statements.</p>\r\n<h4>Least Privilege Principle:</h4>\r\n<p>Configure database accounts with the minimum permissions required. Avoid using high-privilege accounts for web applications.</p>', 'Injeksi SQL (SQLi) dan strategi mitigasi, menggabungkan wawasan dari peringkat yang diberikan dan mengatasi potensi kekurangan:', '2024-03-13 05:13:08'),
(38, 'firebase-godot', '', '', '', 'Cloud Store Procedure for godot engine', '2024-07-15 12:41:12');

-- --------------------------------------------------------

--
-- Table structure for table `blog_tags`
--

CREATE TABLE `blog_tags` (
  `blog_id` int(6) UNSIGNED NOT NULL,
  `tag_id` int(6) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(6) UNSIGNED NOT NULL,
  `tag_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `tag_name`) VALUES
(4, 'cyber-security'),
(13, 'cryptography'),
(14, 'cyber-attack-mitigation'),
(16, 'passive-reconnaissance'),
(17, 'active-reconnaissance'),
(19, 'game-development');

-- --------------------------------------------------------

--
-- Table structure for table `views`
--

CREATE TABLE `views` (
  `id` int(6) UNSIGNED NOT NULL,
  `content_id` int(6) UNSIGNED DEFAULT NULL,
  `views_count` int(6) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `views`
--

INSERT INTO `views` (`id`, `content_id`, `views_count`) VALUES
(334, 18, 6),
(337, 14, 2),
(338, 38, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_tags`
--
ALTER TABLE `blog_tags`
  ADD PRIMARY KEY (`blog_id`,`tag_id`),
  ADD KEY `blog_id` (`blog_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `views`
--
ALTER TABLE `views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_id` (`content_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `views`
--
ALTER TABLE `views`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=339;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog_tags`
--
ALTER TABLE `blog_tags`
  ADD CONSTRAINT `blog_tags_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`),
  ADD CONSTRAINT `blog_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`);

--
-- Constraints for table `views`
--
ALTER TABLE `views`
  ADD CONSTRAINT `views_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `blogs` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
