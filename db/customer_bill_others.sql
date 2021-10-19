--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6
-- Dumped by pg_dump version 11.6

-- Started on 2020-08-11 20:21:06

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
-- TOC entry 381 (class 1259 OID 120862)
-- Name: customer_bill_others; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.customer_bill_others (
    id integer NOT NULL,
    order_id integer,
    customer_id integer,
    description character varying(20),
    amount double precision,
    is_increase integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.customer_bill_others OWNER TO postgres;

--
-- TOC entry 380 (class 1259 OID 120860)
-- Name: customer_bill_other_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.customer_bill_other_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.customer_bill_other_id_seq OWNER TO postgres;

--
-- TOC entry 3334 (class 0 OID 0)
-- Dependencies: 380
-- Name: customer_bill_other_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.customer_bill_other_id_seq OWNED BY public.customer_bill_others.id;


--
-- TOC entry 3202 (class 2604 OID 120865)
-- Name: customer_bill_others id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer_bill_others ALTER COLUMN id SET DEFAULT nextval('public.customer_bill_other_id_seq'::regclass);


--
-- TOC entry 3327 (class 0 OID 120862)
-- Dependencies: 381
-- Data for Name: customer_bill_others; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.customer_bill_others (id, order_id, customer_id, description, amount, is_increase, created_at, updated_at, deleted_at) FROM stdin;
1	38	94	addendum	100000	1	2020-08-10 14:14:01	2020-08-10 14:14:01	\N
2	38	94	discount_payment	200000	0	2020-08-11 09:53:28	2020-08-11 09:53:28	\N
\.


--
-- TOC entry 3335 (class 0 OID 0)
-- Dependencies: 380
-- Name: customer_bill_other_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.customer_bill_other_id_seq', 2, true);


--
-- TOC entry 3204 (class 2606 OID 120867)
-- Name: customer_bill_others customer_bill_other_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer_bill_others
    ADD CONSTRAINT customer_bill_other_pkey PRIMARY KEY (id);


--
-- TOC entry 3333 (class 0 OID 0)
-- Dependencies: 381
-- Name: TABLE customer_bill_others; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.customer_bill_others FROM postgres;
GRANT ALL ON TABLE public.customer_bill_others TO postgres WITH GRANT OPTION;


-- Completed on 2020-08-11 20:21:06

--
-- PostgreSQL database dump complete
--

