--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6
-- Dumped by pg_dump version 11.6

-- Started on 2020-08-12 20:53:25

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
-- TOC entry 395 (class 1259 OID 137331)
-- Name: payment_suppliers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.payment_suppliers (
    id integer DEFAULT nextval('public.payment_suppliers_id_seq'::regclass) NOT NULL,
    purchase_id integer,
    purchase_asset_id integer,
    wop character varying(20),
    amount double precision,
    description character varying(200),
    bank_number character varying(100),
    ref_code character varying(100),
    no character varying(50),
    pay_date date,
    atas_nama character varying(100),
    id_bank integer,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now(),
    deleted_at timestamp without time zone
);


ALTER TABLE public.payment_suppliers OWNER TO postgres;

--
-- TOC entry 3227 (class 2606 OID 137341)
-- Name: payment_suppliers payment_suppliers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payment_suppliers
    ADD CONSTRAINT payment_suppliers_pkey PRIMARY KEY (id);


--
-- TOC entry 3354 (class 0 OID 0)
-- Dependencies: 395
-- Name: TABLE payment_suppliers; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.payment_suppliers FROM postgres;
GRANT ALL ON TABLE public.payment_suppliers TO postgres WITH GRANT OPTION;


-- Completed on 2020-08-12 20:53:26

--
-- PostgreSQL database dump complete
--

