--
-- PostgreSQL database dump
--

-- Dumped from database version 17.7 (Postgres.app)
-- Dumped by pg_dump version 18.0

-- Started on 2026-02-03 09:35:04 WIB

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 220 (class 1259 OID 16476)
-- Name: arsip_dimusnahkan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.arsip_dimusnahkan (
    id integer NOT NULL,
    kode_klasifikasi character varying(50) NOT NULL,
    nama_berkas character varying(255) NOT NULL,
    no_isi integer NOT NULL,
    pencipta character varying(255) NOT NULL,
    tanggal character varying(50) NOT NULL,
    jumlah integer NOT NULL,
    tingkat_perkembangan character varying(255) NOT NULL,
    keterangan text
);

ALTER TABLE public.arsip_dimusnahkan OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 16475)
-- Name: arsip_dimusnahkan_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.arsip_dimusnahkan_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE public.arsip_dimusnahkan_id_seq OWNER TO postgres;

--
-- TOC entry 3535 (class 0 OID 0)
-- Dependencies: 219
-- Name: arsip_dimusnahkan_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.arsip_dimusnahkan_id_seq OWNED BY public.arsip_dimusnahkan.id;

--
-- TOC entry 222 (class 1259 OID 16493)
-- Name: surat; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.surat (
    id integer NOT NULL,
    no_surat character varying(100) NOT NULL,
    tanggal_surat date NOT NULL,
    jenis character varying(20) NOT NULL,
    pengirim character varying(255),
    penerima character varying(255),
    perihal text NOT NULL,
    sifat character varying(50),
    lampiran character varying(255),
    file_path text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE public.surat OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 16492)
-- Name: surat_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.surat_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE public.surat_id_seq OWNER TO postgres;

--
-- TOC entry 3536 (class 0 OID 0)
-- Dependencies: 221
-- Name: surat_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.surat_id_seq OWNED BY public.surat.id;

--
-- TOC entry 224 (class 1259 OID 16506)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id_user integer NOT NULL,
    nama_lengkap character varying(100) NOT NULL,
    username character varying(50) NOT NULL,
    password_hash text NOT NULL,
    role character varying(20) DEFAULT 'user'::character varying,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 16505)
-- Name: users_id_user_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_user_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE public.users_id_user_seq OWNER TO postgres;

--
-- TOC entry 3537 (class 0 OID 0)
-- Dependencies: 223
-- Name: users_id_user_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_user_seq OWNED BY public.users.id_user;

--
-- TOC entry 3382 (class 2604 OID 16479)
-- Name: arsip_dimusnahkan id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.arsip_dimusnahkan ALTER COLUMN id SET DEFAULT nextval('public.arsip_dimusnahkan_id_seq'::regclass);

--
-- TOC entry 3383 (class 2604 OID 16496)
-- Name: surat id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.surat ALTER COLUMN id SET DEFAULT nextval('public.surat_id_seq'::regclass);

--
-- TOC entry 3385 (class 2604 OID 16509)
-- Name: users id_user; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id_user SET DEFAULT nextval('public.users_id_user_seq'::regclass);

--
-- TOC entry 3527 (class 0 OID 16476)
-- Dependencies: 220
-- Data for Name: arsip_dimusnahkan; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.arsip_dimusnahkan (id, kode_klasifikasi, nama_berkas, no_isi, pencipta, tanggal, jumlah, tingkat_perkembangan, keterangan) FROM stdin;
\.

--
-- TOC entry 3529 (class 0 OID 16493)
-- Dependencies: 222
-- Data for Name: surat; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.surat (id, no_surat, tanggal_surat, jenis, pengirim, penerima, perihal, sifat, lampiran, file_path, created_at) FROM stdin;
\.

--
-- TOC entry 3531 (class 0 OID 16506)
-- Dependencies: 224
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id_user, nama_lengkap, username, password_hash, role, created_at) FROM stdin;
1	admin	admin	$2y$10$KZJByj1OLqZgC3yQmH3V.e5h5Zj0/1sU8gppB6w2JQ5Zr6gSxJY2W	admin	2026-02-03 09:33:33.548
\.

--
-- TOC entry 3538 (class 0 OID 0)
-- Dependencies: 219
-- Name: arsip_dimusnahkan_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.arsip_dimusnahkan_id_seq', 1, false);

--
-- TOC entry 3539 (class 0 OID 0)
-- Dependencies: 221
-- Name: surat_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.surat_id_seq', 1, false);

--
-- TOC entry 3540 (class 0 OID 0)
-- Dependencies: 223
-- Name: users_id_user_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_user_seq', 1, false);

--
-- Completed on 2026-02-03 09:35:04 WIB
--

--
-- PostgreSQL database dump complete
--
