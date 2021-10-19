--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6
-- Dumped by pg_dump version 11.6

-- Started on 2020-08-11 20:20:14

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
-- TOC entry 386 (class 1259 OID 129078)
-- Name: payment_cost_others; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.payment_cost_others (
    id integer DEFAULT nextval('public.payment_cost_others_id_seq'::regclass) NOT NULL,
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


ALTER TABLE public.payment_cost_others OWNER TO postgres;

--
-- TOC entry 3206 (class 2606 OID 129087)
-- Name: payment_cost_others payment_cost_others_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payment_cost_others
    ADD CONSTRAINT payment_cost_others_pkey PRIMARY KEY (id);


--
-- TOC entry 3333 (class 0 OID 0)
-- Dependencies: 386
-- Name: TABLE payment_cost_others; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.payment_cost_others FROM postgres;
GRANT ALL ON TABLE public.payment_cost_others TO postgres WITH GRANT OPTION;


-- Completed on 2020-08-11 20:20:15

--
-- PostgreSQL database dump complete
--

