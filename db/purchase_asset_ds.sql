--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6
-- Dumped by pg_dump version 11.6

-- Started on 2020-08-09 17:44:40

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
-- TOC entry 373 (class 1259 OID 112609)
-- Name: purchase_asset_ds; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.purchase_asset_ds (
    id integer DEFAULT nextval('public.purchase_asset_ds_id_seq'::regclass) NOT NULL,
    purchase_asset_id integer NOT NULL,
    m_item_id integer NOT NULL,
    amount numeric(12,2),
    m_unit_id integer,
    base_price numeric(18,2),
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    buy_date date,
    price_before_discount double precision
);


ALTER TABLE public.purchase_asset_ds OWNER TO postgres;

--
-- TOC entry 3182 (class 2606 OID 112613)
-- Name: purchase_asset_ds purchase_asset_ds_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchase_asset_ds
    ADD CONSTRAINT purchase_asset_ds_pk PRIMARY KEY (id);


--
-- TOC entry 3180 (class 1259 OID 112629)
-- Name: fki_purchase_asset_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_purchase_asset_id ON public.purchase_asset_ds USING btree (purchase_asset_id);


--
-- TOC entry 3183 (class 2606 OID 112614)
-- Name: purchase_asset_ds m_items_purchase_ds_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchase_asset_ds
    ADD CONSTRAINT m_items_purchase_ds_fk FOREIGN KEY (m_item_id) REFERENCES public.m_items(id);


--
-- TOC entry 3184 (class 2606 OID 112619)
-- Name: purchase_asset_ds m_units_purchase_ds_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchase_asset_ds
    ADD CONSTRAINT m_units_purchase_ds_fk FOREIGN KEY (m_unit_id) REFERENCES public.m_units(id);


--
-- TOC entry 3185 (class 2606 OID 112624)
-- Name: purchase_asset_ds purchase_asset_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchase_asset_ds
    ADD CONSTRAINT purchase_asset_id FOREIGN KEY (purchase_asset_id) REFERENCES public.purchase_assets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3312 (class 0 OID 0)
-- Dependencies: 373
-- Name: TABLE purchase_asset_ds; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.purchase_asset_ds FROM postgres;
GRANT ALL ON TABLE public.purchase_asset_ds TO postgres WITH GRANT OPTION;


-- Completed on 2020-08-09 17:44:41

--
-- PostgreSQL database dump complete
--

