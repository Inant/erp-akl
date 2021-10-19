--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6
-- Dumped by pg_dump version 11.6

-- Started on 2020-08-11 20:18:02

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
-- TOC entry 361 (class 1259 OID 79570)
-- Name: payments; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.payments (
    id integer NOT NULL,
    wop character varying(20),
    amount double precision,
    payment_type character varying(20),
    name character varying(200),
    note text,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now(),
    deleted_at timestamp without time zone,
    order_id integer,
    project_req_development_id integer,
    description character varying(100),
    no character varying(30),
    pay_date date,
    ref_code character varying(100),
    bank_number character varying(60),
    atas_nama character varying(100),
    id_bank integer,
    is_out_source boolean,
    is_production boolean
);


ALTER TABLE public.payments OWNER TO postgres;

--
-- TOC entry 360 (class 1259 OID 79568)
-- Name: payments_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.payments_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.payments_id_seq OWNER TO postgres;

--
-- TOC entry 3334 (class 0 OID 0)
-- Dependencies: 360
-- Name: payments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.payments_id_seq OWNED BY public.payments.id;


--
-- TOC entry 3202 (class 2604 OID 79573)
-- Name: payments id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payments ALTER COLUMN id SET DEFAULT nextval('public.payments_id_seq'::regclass);


--
-- TOC entry 3206 (class 2606 OID 79578)
-- Name: payments payments_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payments
    ADD CONSTRAINT payments_pkey PRIMARY KEY (id);


--
-- TOC entry 3333 (class 0 OID 0)
-- Dependencies: 361
-- Name: TABLE payments; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.payments FROM postgres;
GRANT ALL ON TABLE public.payments TO postgres WITH GRANT OPTION;


-- Completed on 2020-08-11 20:18:02

--
-- PostgreSQL database dump complete
--

