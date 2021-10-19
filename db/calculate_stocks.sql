--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6
-- Dumped by pg_dump version 11.6

-- Started on 2020-08-08 21:06:20

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
-- TOC entry 369 (class 1259 OID 112505)
-- Name: calculate_stocks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.calculate_stocks (
    id integer NOT NULL,
    site_id integer,
    m_warehouse_id integer,
    type character varying(20),
    m_item_id integer,
    m_unit_id integer,
    amount double precision,
    amount_in double precision,
    amount_out double precision,
    last_month character varying(10),
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.calculate_stocks OWNER TO postgres;

--
-- TOC entry 368 (class 1259 OID 112503)
-- Name: calculate_stocks_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.calculate_stocks_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.calculate_stocks_id_seq OWNER TO postgres;

--
-- TOC entry 3303 (class 0 OID 0)
-- Dependencies: 368
-- Name: calculate_stocks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.calculate_stocks_id_seq OWNED BY public.calculate_stocks.id;


--
-- TOC entry 3173 (class 2604 OID 112508)
-- Name: calculate_stocks id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.calculate_stocks ALTER COLUMN id SET DEFAULT nextval('public.calculate_stocks_id_seq'::regclass);


--
-- TOC entry 3175 (class 2606 OID 112510)
-- Name: calculate_stocks calculate_stocks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.calculate_stocks
    ADD CONSTRAINT calculate_stocks_pkey PRIMARY KEY (id);


--
-- TOC entry 3302 (class 0 OID 0)
-- Dependencies: 369
-- Name: TABLE calculate_stocks; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.calculate_stocks FROM postgres;
GRANT ALL ON TABLE public.calculate_stocks TO postgres WITH GRANT OPTION;


-- Completed on 2020-08-08 21:06:20

--
-- PostgreSQL database dump complete
--

