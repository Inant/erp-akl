--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6
-- Dumped by pg_dump version 11.6

-- Started on 2020-07-27 10:09:20

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'SQL_ASCII';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 363 (class 1259 OID 96001)
-- Name: ts_warehouses; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ts_warehouses (
    id integer NOT NULL,
    site_id integer,
    warehouse_to integer,
    warehouse_from integer,
    no character varying(30),
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.ts_warehouses OWNER TO postgres;

--
-- TOC entry 362 (class 1259 OID 95999)
-- Name: ts_warehouses_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.ts_warehouses_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.ts_warehouses_id_seq OWNER TO postgres;

--
-- TOC entry 3273 (class 0 OID 0)
-- Dependencies: 362
-- Name: ts_warehouses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.ts_warehouses_id_seq OWNED BY public.ts_warehouses.id;


--
-- TOC entry 3143 (class 2604 OID 96004)
-- Name: ts_warehouses id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ts_warehouses ALTER COLUMN id SET DEFAULT nextval('public.ts_warehouses_id_seq'::regclass);


--
-- TOC entry 3145 (class 2606 OID 96006)
-- Name: ts_warehouses ts_warehouses_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ts_warehouses
    ADD CONSTRAINT ts_warehouses_pkey PRIMARY KEY (id);


--
-- TOC entry 3272 (class 0 OID 0)
-- Dependencies: 363
-- Name: TABLE ts_warehouses; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.ts_warehouses FROM postgres;
GRANT ALL ON TABLE public.ts_warehouses TO postgres WITH GRANT OPTION;


-- Completed on 2020-07-27 10:09:21

--
-- PostgreSQL database dump complete
--

