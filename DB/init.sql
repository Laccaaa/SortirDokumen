-- Clean import script (Docker / pgAdmin friendly)
BEGIN;

-- Drop tables first (dependent objects ikut kehapus via CASCADE)
DROP TABLE IF EXISTS public.arsip_dimusnahkan CASCADE;
DROP TABLE IF EXISTS public.surat CASCADE;
DROP TABLE IF EXISTS public.users CASCADE;

-- Drop sequences explicitly
DROP SEQUENCE IF EXISTS public.arsip_dimusnahkan_id_seq CASCADE;
DROP SEQUENCE IF EXISTS public.surat_id_surat_seq CASCADE;
DROP SEQUENCE IF EXISTS public.users_id_user_seq CASCADE;

-- Recreate sequences
CREATE SEQUENCE public.arsip_dimusnahkan_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE SEQUENCE public.surat_id_surat_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

CREATE SEQUENCE public.users_id_user_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

-- =========================
-- TABLE: arsip_dimusnahkan
-- =========================
CREATE TABLE public.arsip_dimusnahkan (
    id integer NOT NULL DEFAULT nextval('public.arsip_dimusnahkan_id_seq'::regclass),
    kode_klasifikasi character varying(50) NOT NULL,
    nama_berkas character varying(255) NOT NULL,
    nomor_berkas character varying (100),
    no_isi integer NOT NULL,
    pencipta character varying(150),
    no_surat character varying(100),

    -- kolom lama
    uraian text,
    tanggal character varying(10),
    jumlah character varying(50),
    tingkat character varying(50),
    lokasi character varying(100),
    keterangan text,

    -- kolom baru
    tujuan_surat character varying(255),
    uraian_informasi_1 text,
    uraian_informasi_2 text,
    tanggal_surat character varying(10),
    kurun_waktu character varying(50),
    skkad character varying(100),

    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_tanggal_fleksibel CHECK (
        (tanggal::text ~ '^\d{4}$'::text)
        OR (tanggal::text ~ '^\d{4}-\d{2}-\d{2}$'::text)
    ),
    CONSTRAINT arsip_dimusnahkan_pkey PRIMARY KEY (id)
);

-- =========================
-- TABLE: surat
-- =========================
CREATE TABLE public.surat (
    id_surat integer NOT NULL DEFAULT nextval('public.surat_id_surat_seq'::regclass),
    jenis_surat character varying(10) COLLATE pg_catalog."default" NOT NULL,
    nomor_surat character varying(100) COLLATE pg_catalog."default" NOT NULL,
    kode_utama character varying(10) COLLATE pg_catalog."default",
    subkode character varying(20) COLLATE pg_catalog."default",
    nomor_urut character varying(10) COLLATE pg_catalog."default",
    unit_pengirim character varying(20) COLLATE pg_catalog."default",
    bulan character varying(10) COLLATE pg_catalog."default",
    tahun integer,
    nama_file character varying(255) COLLATE pg_catalog."default" NOT NULL,
    path_file text COLLATE pg_catalog."default" NOT NULL,
    tanggal_upload timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    unit_pengolah character varying(100) COLLATE pg_catalog."default",
    nama_berkas character varying(255) COLLATE pg_catalog."default",
    nomor_isi character varying(50) COLLATE pg_catalog."default",
    pencipta_arsip character varying(150) COLLATE pg_catalog."default",
    tujuan_surat character varying(255) COLLATE pg_catalog."default",
    perihal text COLLATE pg_catalog."default",
    uraian_informasi text COLLATE pg_catalog."default",
    tanggal_surat_kurun character varying(50) COLLATE pg_catalog."default",
    jumlah character varying(50) COLLATE pg_catalog."default",
    lokasi_simpan character varying(150) COLLATE pg_catalog."default",
    tingkat character varying(50) COLLATE pg_catalog."default",
    keterangan text COLLATE pg_catalog."default",
    skkad character varying(100) COLLATE pg_catalog."default",
    jra_aktif character varying(20) COLLATE pg_catalog."default",
    jra_inaktif character varying(20) COLLATE pg_catalog."default",
    nasib character varying(100) COLLATE pg_catalog."default",
    CONSTRAINT surat_pkey PRIMARY KEY (id_surat)
);

-- =========================
-- TABLE: users
-- =========================
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

-- Optional: biar sequence "owned by" kolom terkait
ALTER SEQUENCE public.arsip_dimusnahkan_id_seq OWNED BY public.arsip_dimusnahkan.id;
ALTER SEQUENCE public.surat_id_surat_seq OWNED BY public.surat.id_surat;
ALTER SEQUENCE public.users_id_user_seq OWNED BY public.users.id_user;

-- surat kosong di dump, jadi tidak ada INSERT untuk public.surat
-- arsip_dimusnahkan juga tidak ada INSERT pada script ini

-- Seed users
INSERT INTO public.users (
    id_user, username, password, nama_lengkap, email, role, created_at, last_login
) VALUES (
    2,
    'user',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'User Biasa',
    'user@example.com',
    'user',
    '2026-01-08 14:15:06.991966',
    '2026-01-09 10:56:41.933228'
);

INSERT INTO public.users (
    id_user, username, password, nama_lengkap, email, role, created_at, last_login
) VALUES (
    1,
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Administrator',
    'admin@example.com',
    'admin',
    '2026-01-08 14:15:06.991966',
    '2026-02-03 10:19:19.94325'
);

-- Reset sequences to match existing seeded data / expected next IDs
SELECT pg_catalog.setval('public.arsip_dimusnahkan_id_seq', 14, true);
SELECT pg_catalog.setval('public.surat_id_surat_seq', 33, true);
SELECT pg_catalog.setval('public.users_id_user_seq', 2, true);

COMMIT;