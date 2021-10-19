--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6
-- Dumped by pg_dump version 11.6

-- Started on 2020-08-23 08:40:46

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 399 (class 1259 OID 139109)
-- Name: material_assets; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.material_assets (
    id integer DEFAULT nextval('public.material_assets_id_seq'::regclass) NOT NULL,
    purchase_asset_id integer NOT NULL,
    m_item_id integer NOT NULL,
    amount numeric(12,2),
    m_unit_id integer,
    base_price numeric(18,2),
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    inv_trx_id integer,
    amount_amortisasi double precision,
    site_id integer,
    end_date_amortisasi character varying(10)
);


ALTER TABLE public.material_assets OWNER TO postgres;

--
-- TOC entry 3239 (class 2606 OID 139113)
-- Name: material_assets material_assets_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.material_assets
    ADD CONSTRAINT material_assets_pk PRIMARY KEY (id);


--
-- TOC entry 3366 (class 0 OID 0)
-- Dependencies: 399
-- Name: TABLE material_assets; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.material_assets TO PUBLIC;


-- Completed on 2020-08-23 08:40:47

--
-- PostgreSQL database dump complete
--

