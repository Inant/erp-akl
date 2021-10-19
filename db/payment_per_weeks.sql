--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6
-- Dumped by pg_dump version 11.6

-- Started on 2020-08-11 20:18:33

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
-- TOC entry 382 (class 1259 OID 129052)
-- Name: payment_per_weeks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.payment_per_weeks (
    id integer DEFAULT nextval('public.payment_per_weeks_id_seq'::regclass) NOT NULL,
    wop character varying(20),
    amount double precision,
    description character varying(200),
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now(),
    deleted_at timestamp without time zone,
    bank_number character varying(100),
    ref_code character varying(100),
    no character varying(50),
    pay_date date,
    atas_nama character varying(100),
    id_bank integer
);


ALTER TABLE public.payment_per_weeks OWNER TO postgres;

--
-- TOC entry 3206 (class 2606 OID 129058)
-- Name: payment_per_weeks payment_per_weeks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payment_per_weeks
    ADD CONSTRAINT payment_per_weeks_pkey PRIMARY KEY (id);


--
-- TOC entry 3333 (class 0 OID 0)
-- Dependencies: 382
-- Name: TABLE payment_per_weeks; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.payment_per_weeks FROM postgres;
GRANT ALL ON TABLE public.payment_per_weeks TO postgres WITH GRANT OPTION;


-- Completed on 2020-08-11 20:18:33

--
-- PostgreSQL database dump complete
--

