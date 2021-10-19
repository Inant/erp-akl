--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6
-- Dumped by pg_dump version 11.6

-- Started on 2020-07-27 10:09:51

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
-- TOC entry 365 (class 1259 OID 96009)
-- Name: ts_warehouse_ds; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ts_warehouse_ds (
    id integer NOT NULL,
    ts_warehouse_id integer,
    m_item_id integer,
    m_unit_id integer,
    amount double precision,
    notes text,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now(),
    deleted_at timestamp without time zone
);


ALTER TABLE public.ts_warehouse_ds OWNER TO postgres;

--
-- TOC entry 364 (class 1259 OID 96007)
-- Name: ts_warehous_ds_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.ts_warehous_ds_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.ts_warehous_ds_id_seq OWNER TO postgres;

--
-- TOC entry 3277 (class 0 OID 0)
-- Dependencies: 364
-- Name: ts_warehous_ds_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.ts_warehous_ds_id_seq OWNED BY public.ts_warehouse_ds.id;


--
-- TOC entry 3143 (class 2604 OID 96012)
-- Name: ts_warehouse_ds id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ts_warehouse_ds ALTER COLUMN id SET DEFAULT nextval('public.ts_warehous_ds_id_seq'::regclass);


--
-- TOC entry 3148 (class 2606 OID 96019)
-- Name: ts_warehouse_ds ts_warehous_ds_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ts_warehouse_ds
    ADD CONSTRAINT ts_warehous_ds_pkey PRIMARY KEY (id);


--
-- TOC entry 3146 (class 1259 OID 96025)
-- Name: fki_ts_warehouse_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_ts_warehouse_id ON public.ts_warehouse_ds USING btree (ts_warehouse_id);


--
-- TOC entry 3149 (class 2606 OID 96020)
-- Name: ts_warehouse_ds ts_warehouse_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ts_warehouse_ds
    ADD CONSTRAINT ts_warehouse_id FOREIGN KEY (ts_warehouse_id) REFERENCES public.ts_warehouses(id) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3276 (class 0 OID 0)
-- Dependencies: 365
-- Name: TABLE ts_warehouse_ds; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.ts_warehouse_ds FROM postgres;
GRANT ALL ON TABLE public.ts_warehouse_ds TO postgres WITH GRANT OPTION;


-- Completed on 2020-07-27 10:09:51

--
-- PostgreSQL database dump complete
--

