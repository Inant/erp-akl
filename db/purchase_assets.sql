--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6
-- Dumped by pg_dump version 11.6

-- Started on 2020-08-09 17:44:05

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
-- TOC entry 372 (class 1259 OID 112592)
-- Name: purchase_assets; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.purchase_assets (
    id integer DEFAULT nextval('public.purchase_assets_id_seq'::regclass) NOT NULL,
    no character varying(20),
    base_price numeric(18,2),
    m_supplier_id integer,
    wop character varying(20),
    is_closed boolean DEFAULT false NOT NULL,
    is_special boolean DEFAULT false NOT NULL,
    site_id integer,
    purchase_date timestamp without time zone,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    is_receive boolean,
    ekspedisi character varying(60),
    discount double precision,
    discount_type character varying(20)
);


ALTER TABLE public.purchase_assets OWNER TO postgres;

--
-- TOC entry 3183 (class 2606 OID 112598)
-- Name: purchase_assets purchase_assets_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchase_assets
    ADD CONSTRAINT purchase_assets_pk PRIMARY KEY (id);


--
-- TOC entry 3184 (class 2606 OID 112599)
-- Name: purchase_assets m_suppliers_purchases_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchase_assets
    ADD CONSTRAINT m_suppliers_purchases_fk FOREIGN KEY (m_supplier_id) REFERENCES public.m_suppliers(id);


--
-- TOC entry 3185 (class 2606 OID 112604)
-- Name: purchase_assets purchases_sites_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchase_assets
    ADD CONSTRAINT purchases_sites_fk FOREIGN KEY (site_id) REFERENCES public.sites(id);


--
-- TOC entry 3312 (class 0 OID 0)
-- Dependencies: 372
-- Name: TABLE purchase_assets; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.purchase_assets FROM postgres;
GRANT ALL ON TABLE public.purchase_assets TO postgres WITH GRANT OPTION;


-- Completed on 2020-08-09 17:44:05

--
-- PostgreSQL database dump complete
--

