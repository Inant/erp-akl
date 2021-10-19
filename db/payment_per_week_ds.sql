--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6
-- Dumped by pg_dump version 11.6

-- Started on 2020-08-11 20:19:06

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
-- TOC entry 384 (class 1259 OID 129062)
-- Name: payment_per_week_ds; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.payment_per_week_ds (
    id integer DEFAULT nextval('public.payment_per_week_ds_id_seq'::regclass) NOT NULL,
    order_id integer,
    project_req_development_id integer,
    is_out_source boolean,
    amount double precision,
    note text,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now(),
    deleted_at timestamp without time zone,
    payment_per_week_id integer,
    is_production boolean
);


ALTER TABLE public.payment_per_week_ds OWNER TO postgres;

--
-- TOC entry 3206 (class 2606 OID 129071)
-- Name: payment_per_week_ds payment_per_week_ds_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payment_per_week_ds
    ADD CONSTRAINT payment_per_week_ds_pkey PRIMARY KEY (id);


--
-- TOC entry 3333 (class 0 OID 0)
-- Dependencies: 384
-- Name: TABLE payment_per_week_ds; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.payment_per_week_ds FROM postgres;
GRANT ALL ON TABLE public.payment_per_week_ds TO postgres WITH GRANT OPTION;


-- Completed on 2020-08-11 20:19:06

--
-- PostgreSQL database dump complete
--

