-- Clean import script (pgAdmin-friendly)
BEGIN;

DROP TABLE IF EXISTS public.arsip_dimusnahkan CASCADE;
DROP TABLE IF EXISTS public.surat CASCADE;
DROP TABLE IF EXISTS public.users CASCADE;

DROP SEQUENCE IF EXISTS public.arsip_dimusnahkan_id_seq CASCADE;
DROP SEQUENCE IF EXISTS public.surat_id_surat_seq CASCADE;
DROP SEQUENCE IF EXISTS public.users_id_user_seq CASCADE;

CREATE SEQUENCE public.arsip_dimusnahkan_id_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
CREATE SEQUENCE public.surat_id_surat_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;
CREATE SEQUENCE public.users_id_user_seq AS integer START WITH 1 INCREMENT BY 1 NO MINVALUE NO MAXVALUE CACHE 1;

CREATE TABLE public.arsip_dimusnahkan (
    id integer NOT NULL DEFAULT nextval('public.arsip_dimusnahkan_id_seq'::regclass),
    kode_klasifikasi character varying(50) NOT NULL,
    nama_berkas character varying(255) NOT NULL,
    no_isi integer NOT NULL,
    pencipta character varying(150),
    no_surat character varying(100),
    uraian text,
    tanggal character varying(10),
    jumlah character varying(50),
    tingkat character varying(50),
    lokasi character varying(100),
    keterangan text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_tanggal_fleksibel CHECK (
        (tanggal::text ~ '^\d{4}$'::text)
        OR (tanggal::text ~ '^\d{4}-\d{2}-\d{2}$'::text)
    ),
    CONSTRAINT arsip_dimusnahkan_pkey PRIMARY KEY (id)
);

CREATE TABLE public.surat (
    id_surat integer NOT NULL DEFAULT nextval('public.surat_id_surat_seq'::regclass),
    jenis_surat character varying(10) NOT NULL,
    nomor_surat character varying(100) NOT NULL,
    kode_utama character varying(10),
    subkode character varying(20),
    nomor_urut character varying(10),
    unit_pengirim character varying(20),
    bulan character varying(10),
    tahun integer,
    nama_file character varying(255) NOT NULL,
    path_file text NOT NULL,
    tanggal_upload timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT surat_jenis_surat_check CHECK (
        (jenis_surat::text = ANY (ARRAY['masuk'::character varying, 'keluar'::character varying]::text[]))
    ),
    CONSTRAINT surat_pkey PRIMARY KEY (id_surat)
);

CREATE TABLE public.users (
    id_user integer NOT NULL DEFAULT nextval('public.users_id_user_seq'::regclass),
    username character varying(50) NOT NULL,
    password character varying(255) NOT NULL,
    nama_lengkap character varying(100) NOT NULL,
    email character varying(100),
    role character varying(20) DEFAULT 'user'::character varying,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    last_login timestamp without time zone,
    CONSTRAINT users_pkey PRIMARY KEY (id_user),
    CONSTRAINT users_username_key UNIQUE (username)
);

-- Data inserts (converted from COPY ... FROM stdin)
INSERT INTO public.arsip_dimusnahkan (id, kode_klasifikasi, nama_berkas, no_isi, pencipta, no_surat, uraian, tanggal, jumlah, tingkat, lokasi, keterangan, created_at)
VALUES (7, 'ME.002', 'Informasi Meteorologi Publik', 1, 'Pengelolaan Citra Radar', 'HM.002/003/DI/XII/2016', 'Hasil laporan kegiatan pemeliharaan AWOS', '2022', '3', 'Asli', '1', 'Baik', '2026-01-14 09:32:39.712204');

INSERT INTO public.arsip_dimusnahkan (id, kode_klasifikasi, nama_berkas, no_isi, pencipta, no_surat, uraian, tanggal, jumlah, tingkat, lokasi, keterangan, created_at)
VALUES (8, 'ME.002', 'Informasi Meteorologi Publik', 2, 'Pengelolaan Citra Radar', 'HM.002/003/DI/XII/2016', 'qwwwwwwwwww', '2022', '3', 'Scan', '1', 'Baik', '2026-01-14 13:48:07.869009');

INSERT INTO public.arsip_dimusnahkan (id, kode_klasifikasi, nama_berkas, no_isi, pencipta, no_surat, uraian, tanggal, jumlah, tingkat, lokasi, keterangan, created_at)
VALUES (9, 'ME.002', 'Informasi Meteorologi Publik', 1, 'pengelolaan citra radar', 'HM.002/003/DI/XII/2016', '12qwert', '2022', '3', 'Fotocopy', '1', 'Baik', '2026-01-14 13:48:36.263961');

INSERT INTO public.arsip_dimusnahkan (id, kode_klasifikasi, nama_berkas, no_isi, pencipta, no_surat, uraian, tanggal, jumlah, tingkat, lokasi, keterangan, created_at)
VALUES (10, 'ME.002', 'Informasi Meteorologi Publik', 1, 'Pengelolaan Citra Radar', 'HM.002/003/DI/XII/2016', 'qsdfghjkl', '2022', '3', 'Asli', '2', 'Baik', '2026-01-14 13:49:02.401499');

INSERT INTO public.arsip_dimusnahkan (id, kode_klasifikasi, nama_berkas, no_isi, pencipta, no_surat, uraian, tanggal, jumlah, tingkat, lokasi, keterangan, created_at)
VALUES (11, 'ME.002', 'Informasi Meteorologi Publik', 2, 'Pengelolaan Citra Radar', 'HM.002/003/DI/XII/2016', 'qwertyui123456', '2022', '2', 'Asli', '2', 'Baik', '2026-01-14 13:49:30.203548');

INSERT INTO public.arsip_dimusnahkan (id, kode_klasifikasi, nama_berkas, no_isi, pencipta, no_surat, uraian, tanggal, jumlah, tingkat, lokasi, keterangan, created_at)
VALUES (12, 'ME.002', 'Informasi Meteorologi Publik', 1, 'Pengelolaan Citra Radar', 'HM.002/003/DI/XII/2016', '123we4rtyugfd', '2022', '2', 'Asli', '2', 'Baik', '2026-01-14 13:49:50.735808');

INSERT INTO public.arsip_dimusnahkan (id, kode_klasifikasi, nama_berkas, no_isi, pencipta, no_surat, uraian, tanggal, jumlah, tingkat, lokasi, keterangan, created_at)
VALUES (13, 'ME.002', 'Informasi Meteorologi Publik', 1, 'Pengelolaan Citra Radar', 'HM.002/003/DI/XII/2016', '1', '2022', '1', 'Fotocopy', '1', 'Baik', '2026-01-14 13:50:10.271702');

INSERT INTO public.arsip_dimusnahkan (id, kode_klasifikasi, nama_berkas, no_isi, pencipta, no_surat, uraian, tanggal, jumlah, tingkat, lokasi, keterangan, created_at)
VALUES (14, 'ME.002', 'Informasi Meteorologi Publik', 1, 'Pengelolaan Citra Radar', 'HM.002/003/DI/XII/2016', 'we', '2022', '3 lembar', 'Fotocopy', '2', 'Baik', '2026-01-14 13:50:36.689916');

-- surat kosong di dump, jadi tidak ada INSERT untuk public.surat

INSERT INTO public.users (id_user, username, password, nama_lengkap, email, role, created_at, last_login)
VALUES (2, 'user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'User Biasa', 'user@example.com', 'user', '2026-01-08 14:15:06.991966', '2026-01-09 10:56:41.933228');

INSERT INTO public.users (id_user, username, password, nama_lengkap, email, role, created_at, last_login)
VALUES (1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@example.com', 'admin', '2026-01-08 14:15:06.991966', '2026-02-03 10:19:19.94325');

-- Reset sequences to match existing data
SELECT pg_catalog.setval('public.arsip_dimusnahkan_id_seq', 14, true);
SELECT pg_catalog.setval('public.surat_id_surat_seq', 33, true);
SELECT pg_catalog.setval('public.users_id_user_seq', 2, true);

COMMIT;
