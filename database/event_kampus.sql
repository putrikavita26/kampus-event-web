-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 16 Jun 2026 pada 08.17
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `event_kampus`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `event`
--

CREATE TABLE `event` (
  `id_event` int(11) NOT NULL,
  `id_hmj` int(11) NOT NULL,
  `id_prodi` int(11) DEFAULT NULL,
  `nama_event` varchar(200) NOT NULL,
  `kategori` enum('Lomba','Seminar','Open Recruitment','Workshop','Lainnya') DEFAULT 'Lainnya',
  `deskripsi` text DEFAULT NULL,
  `lokasi` varchar(200) DEFAULT NULL,
  `tanggal_event` date NOT NULL,
  `kuota` int(11) NOT NULL,
  `biaya` decimal(10,2) DEFAULT 0.00,
  `poster` varchar(255) DEFAULT NULL,
  `link_grup` varchar(255) DEFAULT NULL,
  `informasi_peserta` text DEFAULT NULL,
  `status` enum('Process','Disetujui','Ditolak') DEFAULT 'Process'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `event`
--

INSERT INTO `event` (`id_event`, `id_hmj`, `id_prodi`, `nama_event`, `kategori`, `deskripsi`, `lokasi`, `tanggal_event`, `kuota`, `biaya`, `poster`, `link_grup`, `informasi_peserta`, `status`) VALUES
(7, 2, NULL, 'Seminar Artificial Intelligence', 'Seminar', 'Seminar yang membahas perkembangan Artificial Intelligence (AI), Machine Learning, dan peluang karier di era digital. Peserta akan mendapatkan wawasan mengenai penerapan AI dalam dunia pendidikan, bisnis, dan industri teknologi.', 'Aula Fakultas Sains dan Teknologi UIN Walisongo Semarang', '2026-06-26', 100, 0.00, '1781281829_University_seminar_about_Artificial_Intelligence_and_Machine_Learning__students_attending_a_technology_conference__futuristic_AI_hologram__digital_brain_visualization__data_analytics_dashboard__modern_auditorium__.jpg', 'https://chat.whatsapp.com/ai-seminar-ti', 'Peserta diwajibkan membawa laptop untuk mengikuti sesi praktik pengenalan Machine Learning. E-Sertifikat diberikan kepada seluruh peserta yang mengikuti acara hingga selesai.', 'Disetujui'),
(8, 2, 5, 'TI Hackathon Smart Campus', 'Lomba', 'Kompetisi pengembangan aplikasi berbasis web dan mobile yang bertujuan menciptakan solusi digital untuk mendukung konsep Smart Campus. Peserta bekerja dalam tim untuk membangun prototype inovatif.', 'Laboratorium Komputer Teknologi Informasi', '2026-06-22', 50, 25000.00, '1781281898_University_hackathon_competition__students_coding_on_laptops__multiple_monitors_displaying_source_code__collaborative_teamwork__software_development__cybersecurity_and_programming_environment__modern_computer_labo.jpg', 'https://chat.whatsapp.com/hackathon-ti', 'Setiap tim terdiri dari 3-5 orang. Peserta wajib membawa laptop dan perangkat pendukung selama kompetisi berlangsung.', 'Disetujui'),
(9, 2, 5, 'UI/UX Design Bootcamp', 'Workshop', 'Pelatihan intensif mengenai User Interface dan User Experience Design menggunakan Figma. Peserta akan mempelajari wireframing, prototyping, design system, dan studi kasus aplikasi digital.', 'Laboratorium Multimedia FST', '2026-07-11', 30, 30000.00, '1781281989_Modern_UI_UX_design_workshop_for_university_students__designers_working_on_laptops_with_Figma_interface__mobile_app_wireframes_and_prototypes__creative_workspace__purple_gradient_theme__design_thinking_concept__pr.jpg', 'https://chat.whatsapp.com/uiux-bootcamp', 'Peserta diwajibkan membawa laptop yang telah terinstal browser terbaru dan memiliki akun Figma aktif.', 'Disetujui'),
(10, 5, 1, 'Seminar Nasional Bioteknologi dan Genetika Modern', 'Seminar', 'Seminar nasional yang membahas perkembangan terbaru dalam bidang bioteknologi, rekayasa genetika, dan pemanfaatannya dalam kesehatan, pertanian, serta industri. Acara menghadirkan dosen, peneliti, dan praktisi bioteknologi sebagai narasumber.', 'Aula Fakultas Sains dan Teknologi UIN Walisongo Semarang', '2026-06-26', 250, 0.00, '1781282629_National_biotechnology_seminar__DNA_double_helix__genetic_engineering_research__university_students_attending_scientific_conference__modern_laboratory_background__biotechnology_innovation__blue_and_green_scientifi.jpg', 'https://chat.whatsapp.com/seminar-biologi', 'Peserta yang mengikuti seminar hingga selesai akan memperoleh e-sertifikat dan materi seminar.', 'Disetujui'),
(11, 5, NULL, 'Workshop Mikroskopi dan Identifikasi Mikroorganisme', 'Workshop', 'Pelatihan praktis penggunaan mikroskop dan teknik identifikasi mikroorganisme yang sering ditemukan pada lingkungan sekitar. Peserta akan melakukan pengamatan secara langsung di laboratorium biologi.', 'Laboratorium Terpadu', '2026-07-02', 100, 50000.00, '1781282697_Biology_laboratory_workshop__students_using_microscopes__observing_microorganisms__petri_dishes__laboratory_equipment__scientific_research_environment__educational_atmosphere__realistic_biology_lab__green_and_blue.jpg', 'https://chat.whatsapp.com/workshop', 'Peserta wajib mengenakan jas laboratorium selama kegiatan berlangsung.', 'Disetujui'),
(12, 5, 1, 'Kompetisi Karya Tulis Ilmiah Biologi Lingkungan 2026', 'Lomba', 'Kompetisi karya tulis ilmiah yang mengangkat tema pelestarian lingkungan, biodiversitas, dan solusi inovatif terhadap permasalahan ekologi di Indonesia.', 'Gedung Convention Hall Kampus', '2026-07-11', 50, 100000.00, '1781282760_Environmental_biology_competition__biodiversity_conservation__university_students_presenting_scientific_research__ecosystem_and_sustainability_concept__forest_landscape__green_environment__scientific_posters__acad.jpg', 'https://chat.whatsapp.com/lomba-biologi', 'Peserta wajib mengunggah proposal penelitian sebelum batas waktu yang telah ditentukan.', 'Disetujui'),
(13, 6, NULL, 'Seminar Nasional Kimia Terapan dan Inovasi Industri 2026', 'Seminar', 'Seminar yang membahas penerapan ilmu kimia dalam berbagai bidang industri, seperti industri pangan, farmasi, kosmetik, energi, dan lingkungan. Kegiatan ini bertujuan untuk memperluas wawasan mahasiswa mengenai peran kimia dalam perkembangan teknologi dan dunia kerja.', 'Aula Fakultas Sains dan Teknologi UIN Walisongo Semarang', '2026-07-04', 500, 0.00, '1781282998_National_applied_chemistry_seminar__chemistry_students_attending_scientific_conference__laboratory_glassware__colorful_chemical_reactions__molecular_structures__periodic_table_elements__modern_chemistry_laboratory.jpg', 'https://chat.whatsapp.com/kimia-seminar2026', 'Peserta diwajibkan melakukan registrasi ulang 30 menit sebelum acara dimulai. E-sertifikat akan diberikan kepada peserta yang mengikuti seminar hingga selesai.', 'Disetujui');

-- --------------------------------------------------------

--
-- Struktur dari tabel `form_event`
--

CREATE TABLE `form_event` (
  `id_form` int(11) NOT NULL,
  `id_event` int(11) NOT NULL,
  `pertanyaan` varchar(255) NOT NULL,
  `tipe_input` enum('text','textarea','file','image') DEFAULT 'text'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `form_event`
--

INSERT INTO `form_event` (`id_form`, `id_event`, `pertanyaan`, `tipe_input`) VALUES
(6, 7, 'Nama Lengkap', 'text'),
(7, 7, 'NIM', 'text'),
(8, 7, 'Apa alasan Anda mengikuti seminar AI ini?', 'text'),
(9, 7, 'Upload KTM/Kartu Mahasiswa', 'image'),
(10, 7, 'Apakah Anda memiliki pengalaman menggunakan AI atau Machine Learning sebelumnya?', 'textarea'),
(11, 8, 'Nama Tim', 'text'),
(12, 8, 'Jumlah Anggota', 'text'),
(13, 8, 'Jelaskan ide atau solusi yang akan dikembangkan tim Anda', 'textarea'),
(14, 8, 'Upload Proposal atau Pitch Deck Tim', 'file'),
(15, 8, 'Upload Logo Tim (Opsional)', 'image'),
(16, 9, 'Nama Lengkap', 'text'),
(17, 9, 'Gmail', 'text'),
(18, 9, 'Seberapa jauh pengalaman Anda dalam UI/UX Design?', 'textarea'),
(19, 9, 'Upload Screenshot Hasil Desain Terbaik Anda (Opsional)', 'image'),
(20, 10, 'Nama Lengkap', 'text'),
(21, 10, 'Apa alasan Anda mengikuti seminar ini?', 'textarea'),
(22, 10, 'Upload KTM/Kartu Mahasiswa', 'image'),
(23, 11, 'Nama Lengkap', 'text'),
(24, 11, 'Apakah Anda pernah menggunakan mikroskop sebelumnya?', 'textarea'),
(25, 11, 'Upload Foto Praktikum Biologi Terbaik Anda', 'image'),
(26, 12, 'Nama Tim', 'text'),
(27, 12, 'Jelaskan secara singkat topik penelitian yang akan diajukan', 'textarea'),
(28, 13, 'Nama Lengkap', 'text'),
(29, 13, 'Alasan', 'textarea');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jawaban_form`
--

CREATE TABLE `jawaban_form` (
  `id_jawaban` int(11) NOT NULL,
  `id_pendaftaran` int(11) NOT NULL,
  `id_form` int(11) NOT NULL,
  `jawaban` text DEFAULT NULL,
  `file_upload` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jawaban_form`
--

INSERT INTO `jawaban_form` (`id_jawaban`, `id_pendaftaran`, `id_form`, `jawaban`, `file_upload`) VALUES
(4, 6, 26, 'ada', ''),
(5, 6, 27, 'adalah', ''),
(6, 7, 16, 'dewi', ''),
(7, 7, 17, 'ulya@studen.ac.id', ''),
(8, 7, 18, 'ssf', ''),
(9, 7, 19, '', '1781283421_0_National biotechnology seminar, DNA double helix, genetic engineering research, university students attending scientific conference, modern laboratory background, biotechnology innovation, blue and green scientifi.jpg'),
(10, 8, 20, 'ada', ''),
(11, 8, 21, 'ss', ''),
(12, 8, 22, '', '1781283463_0_Modern UI UX design workshop for university students, designers working on laptops with Figma interface, mobile app wireframes and prototypes, creative workspace, purple gradient theme, design thinking concept, pr.jpg'),
(13, 9, 28, 'aiii', ''),
(14, 9, 29, 'jha', ''),
(15, 10, 23, 'kn', ''),
(16, 10, 24, 'nk', ''),
(17, 10, 25, '', '1781314537_0_National biotechnology seminar, DNA double helix, genetic engineering research, university students attending scientific conference, modern laboratory background, biotechnology innovation, blue and green scientifi.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `nim` varchar(20) NOT NULL,
  `id_user` int(11) NOT NULL,
  `angkatan` year(4) DEFAULT NULL,
  `kelas` varchar(10) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mahasiswa`
--

INSERT INTO `mahasiswa` (`nim`, `id_user`, `angkatan`, `kelas`, `no_hp`) VALUES
('1111', 7, NULL, NULL, NULL),
('2222', 8, NULL, NULL, NULL),
('240501001', 3, '2024', '4A', '081234567890'),
('240501002', 4, '2024', '4B', '081298765432');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pendaftaran` int(11) NOT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `status` enum('Menunggu','Valid','Ditolak') DEFAULT 'Menunggu',
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pendaftaran`, `bukti_bayar`, `status`, `tanggal_upload`) VALUES
(5, 6, '1781283375_bukti_Environmental biology competition, biodiversity conservation, university students presenting scientific research, ecosystem and sustainability concept, forest landscape, green environment, scientific posters, acad.jpg', 'Valid', '2026-06-12 16:56:15'),
(6, 7, '1781283421_bukti_National biotechnology seminar, DNA double helix, genetic engineering research, university students attending scientific conference, modern laboratory background, biotechnology innovation, blue and green scientifi.jpg', 'Valid', '2026-06-12 16:57:01'),
(7, 10, '1781314537_bukti_National biotechnology seminar, DNA double helix, genetic engineering research, university students attending scientific conference, modern laboratory background, biotechnology innovation, blue and green scientifi.jpg', 'Menunggu', '2026-06-13 01:35:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftaran`
--

CREATE TABLE `pendaftaran` (
  `id_pendaftaran` int(11) NOT NULL,
  `id_event` int(11) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `tanggal_daftar` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Menunggu','Diterima','Ditolak') DEFAULT 'Menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendaftaran`
--

INSERT INTO `pendaftaran` (`id_pendaftaran`, `id_event`, `nim`, `tanggal_daftar`, `status`) VALUES
(6, 12, '2222', '2026-06-12 16:56:15', 'Diterima'),
(7, 9, '2222', '2026-06-12 16:57:01', 'Diterima'),
(8, 10, '240501001', '2026-06-12 16:57:43', 'Diterima'),
(9, 13, '2222', '2026-06-13 01:26:30', 'Diterima'),
(10, 11, '2222', '2026-06-13 01:35:37', 'Menunggu');

-- --------------------------------------------------------

--
-- Struktur dari tabel `prodi`
--

CREATE TABLE `prodi` (
  `id_prodi` int(11) NOT NULL,
  `nama_prodi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `prodi`
--

INSERT INTO `prodi` (`id_prodi`, `nama_prodi`) VALUES
(1, 'Biologi'),
(2, 'Fisika'),
(3, 'Kimia'),
(4, 'Matematika'),
(5, 'Teknologi Informasi'),
(6, 'Teknik Lingkungan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `role` enum('SEMA','HMJ','MAHASISWA') NOT NULL,
  `id_prodi` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama`, `email`, `password`, `foto`, `role`, `id_prodi`) VALUES
(1, 'SEMA FST', 'sema@kampus.ac.id', '$2y$10$eWrgn91tyvX/pNrC0q7/LuPID0DyohFgAtWmC4QmFi.BFlSujhjg.', NULL, 'SEMA', NULL),
(2, 'HMJ Teknologi Informasi', 'hmjti@kampus.ac.id', '$2y$10$1AVnVJbzTZWBUKfLTPb84uBYyhvV.OeeFQjuP7hTVC0a3YZMj9yMG', 'profile_2_1781156379.png', 'HMJ', 5),
(3, 'ULYA', 'ulya@student.ac.id', '$2y$10$ncfW./9iZ4N3AG2HHcf40..rOr91POlSMzxiGuzTqEMMHNWfvob6q', 'profile_3_1781161764.png', 'MAHASISWA', 5),
(4, 'Budi Santoso', 'budi.santoso@gmail.com', 'budi123', NULL, 'MAHASISWA', 5),
(5, 'HMJ Biologi', 'hmjbiologi@kampus.ac.id', '$2y$10$UlBwOD0EeL80ThLW0oaKLOOamtSsRqqfZm4RpN8h2btR4IteLKsgK', NULL, 'HMJ', NULL),
(6, 'HMJ Kimia', 'hmjkimia@kampus.ac.id', '$2y$10$eIcnxF.9z7.xSQWvF8Oi2uTJRzXOcmMgiwCzGNH8eZD8NrNL5Vgwe', NULL, 'HMJ', NULL),
(7, 'daffa', 'daffa@student.ac.id', 'daffa123', NULL, 'MAHASISWA', 1),
(8, 'nana', 'nana@student.ac.id', 'nana123', NULL, 'MAHASISWA', 4);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id_event`),
  ADD KEY `id_hmj` (`id_hmj`);

--
-- Indeks untuk tabel `form_event`
--
ALTER TABLE `form_event`
  ADD PRIMARY KEY (`id_form`),
  ADD KEY `id_event` (`id_event`);

--
-- Indeks untuk tabel `jawaban_form`
--
ALTER TABLE `jawaban_form`
  ADD PRIMARY KEY (`id_jawaban`),
  ADD KEY `id_pendaftaran` (`id_pendaftaran`),
  ADD KEY `id_form` (`id_form`);

--
-- Indeks untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`nim`),
  ADD UNIQUE KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_pendaftaran` (`id_pendaftaran`);

--
-- Indeks untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD PRIMARY KEY (`id_pendaftaran`),
  ADD KEY `id_event` (`id_event`),
  ADD KEY `nim` (`nim`);

--
-- Indeks untuk tabel `prodi`
--
ALTER TABLE `prodi`
  ADD PRIMARY KEY (`id_prodi`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_prodi` (`id_prodi`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `event`
--
ALTER TABLE `event`
  MODIFY `id_event` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `form_event`
--
ALTER TABLE `form_event`
  MODIFY `id_form` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `jawaban_form`
--
ALTER TABLE `jawaban_form`
  MODIFY `id_jawaban` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  MODIFY `id_pendaftaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `prodi`
--
ALTER TABLE `prodi`
  MODIFY `id_prodi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`id_hmj`) REFERENCES `users` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `form_event`
--
ALTER TABLE `form_event`
  ADD CONSTRAINT `form_event_ibfk_1` FOREIGN KEY (`id_event`) REFERENCES `event` (`id_event`);

--
-- Ketidakleluasaan untuk tabel `jawaban_form`
--
ALTER TABLE `jawaban_form`
  ADD CONSTRAINT `jawaban_form_ibfk_1` FOREIGN KEY (`id_pendaftaran`) REFERENCES `pendaftaran` (`id_pendaftaran`),
  ADD CONSTRAINT `jawaban_form_ibfk_2` FOREIGN KEY (`id_form`) REFERENCES `form_event` (`id_form`);

--
-- Ketidakleluasaan untuk tabel `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `mahasiswa_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pendaftaran`) REFERENCES `pendaftaran` (`id_pendaftaran`);

--
-- Ketidakleluasaan untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD CONSTRAINT `pendaftaran_ibfk_1` FOREIGN KEY (`id_event`) REFERENCES `event` (`id_event`),
  ADD CONSTRAINT `pendaftaran_ibfk_2` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`);

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_prodi`) REFERENCES `prodi` (`id_prodi`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
