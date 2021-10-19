--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6
-- Dumped by pg_dump version 11.6

-- Started on 2020-08-09 17:43:12

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
-- TOC entry 378 (class 1259 OID 120837)
-- Name: customer_bills; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.customer_bills (
    id integer DEFAULT nextval('public.customer_bills_id_seq'::regclass) NOT NULL,
    order_id integer,
    wop character varying(20),
    amount double precision,
    description character varying(30),
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now(),
    deleted_at timestamp without time zone,
    bank_number character varying(100),
    ref_code character varying(100),
    no character varying(50),
    customer_id integer,
    percent_total double precision,
    due_date date,
    atas_nama character varying(100),
    id_bank integer
);


ALTER TABLE public.customer_bills OWNER TO postgres;

--
-- TOC entry 3305 (class 0 OID 120837)
-- Dependencies: 378
-- Data for Name: customer_bills; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.customer_bills (id, order_id, wop, amount, description, created_at, updated_at, deleted_at, bank_number, ref_code, no, customer_id, percent_total, due_date, atas_nama, id_bank) VALUES (1, 40, 'cash', 660000, 'DP', '2020-08-09 13:12:40', '2020-08-09 13:12:40', NULL, NULL, NULL, '110/BILL/08/20/016', 94, 10, '2020-08-10', NULL, NULL);
INSERT INTO public.customer_bills (id, order_id, wop, amount, description, created_at, updated_at, deleted_at, bank_number, ref_code, no, customer_id, percent_total, due_date, atas_nama, id_bank) VALUES (2, 40, 'card', 1980000, 'TAG 1', '2020-08-09 13:19:56', '2020-08-09 13:19:56', NULL, NULL, '080980', '110/BILL/08/20/017', 94, 30, '2020-08-11', NULL, NULL);
INSERT INTO public.customer_bills (id, order_id, wop, amount, description, created_at, updated_at, deleted_at, bank_number, ref_code, no, customer_id, percent_total, due_date, atas_nama, id_bank) VALUES (3, 40, 'cash', 2640000, 'TAG 2', '2020-08-09 13:20:26', '2020-08-09 13:20:26', NULL, NULL, NULL, '110/BILL/08/20/018', 94, 40, '2020-08-20', NULL, NULL);
INSERT INTO public.customer_bills (id, order_id, wop, amount, description, created_at, updated_at, deleted_at, bank_number, ref_code, no, customer_id, percent_total, due_date, atas_nama, id_bank) VALUES (4, 40, 'cash', 1320000, 'BAST', '2020-08-09 13:27:38', '2020-08-09 13:27:38', NULL, NULL, NULL, '110/BILL/08/20/019', 94, 20, '2020-08-27', NULL, NULL);
INSERT INTO public.customer_bills (id, order_id, wop, amount, description, created_at, updated_at, deleted_at, bank_number, ref_code, no, customer_id, percent_total, due_date, atas_nama, id_bank) VALUES (5, 39, 'bank_transfer', 550000, 'DP', '2020-08-09 17:04:35', '2020-08-09 17:04:35', NULL, '894894749', NULL, '110/BILL/08/20/020', 93, 10, '2020-08-10', 'Wika', 2);
INSERT INTO public.customer_bills (id, order_id, wop, amount, description, created_at, updated_at, deleted_at, bank_number, ref_code, no, customer_id, percent_total, due_date, atas_nama, id_bank) VALUES (6, 39, 'cash', 1100000, 'TAG 1', '2020-08-09 17:12:05', '2020-08-09 17:12:05', NULL, NULL, NULL, '110/BILL/08/20/021', 93, 20, '2020-08-12', NULL, NULL);
INSERT INTO public.customer_bills (id, order_id, wop, amount, description, created_at, updated_at, deleted_at, bank_number, ref_code, no, customer_id, percent_total, due_date, atas_nama, id_bank) VALUES (7, 39, 'cash', 550000, 'TAG 2', '2020-08-09 17:13:09', '2020-08-09 17:13:09', NULL, NULL, NULL, '110/BILL/08/20/022', 93, 10, '2020-08-12', NULL, NULL);
INSERT INTO public.customer_bills (id, order_id, wop, amount, description, created_at, updated_at, deleted_at, bank_number, ref_code, no, customer_id, percent_total, due_date, atas_nama, id_bank) VALUES (8, 39, 'card', 2200000, 'TAG 3', '2020-08-09 17:13:38', '2020-08-09 17:13:38', NULL, NULL, '09098090', '110/BILL/08/20/023', 93, 40, '2020-08-13', NULL, NULL);
INSERT INTO public.customer_bills (id, order_id, wop, amount, description, created_at, updated_at, deleted_at, bank_number, ref_code, no, customer_id, percent_total, due_date, atas_nama, id_bank) VALUES (9, 39, 'cash', 1100000, 'BAST', '2020-08-09 17:14:04', '2020-08-09 17:14:04', NULL, NULL, NULL, '110/BILL/08/20/024', 93, 20, '2020-08-13', NULL, NULL);


--
-- TOC entry 3183 (class 2606 OID 120846)
-- Name: customer_bills customer_bills_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer_bills
    ADD CONSTRAINT customer_bills_pkey PRIMARY KEY (id);


--
-- TOC entry 3311 (class 0 OID 0)
-- Dependencies: 378
-- Name: TABLE customer_bills; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.customer_bills FROM postgres;
GRANT ALL ON TABLE public.customer_bills TO postgres WITH GRANT OPTION;


-- Completed on 2020-08-09 17:43:13

--
-- PostgreSQL database dump complete
--

