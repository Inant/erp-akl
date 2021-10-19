--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6
-- Dumped by pg_dump version 11.6

-- Started on 2020-08-12 20:55:19

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
-- TOC entry 393 (class 1259 OID 137302)
-- Name: dev_project_frame_ds; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.dev_project_frame_ds (
    id integer DEFAULT nextval('public.dev_projects_frame_ds_id_seq'::regclass) NOT NULL,
    dev_project_frame_id integer,
    product_sub_id integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.dev_project_frame_ds OWNER TO postgres;

--
-- TOC entry 3225 (class 2606 OID 137307)
-- Name: dev_project_frame_ds dev_project_frame_ds_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dev_project_frame_ds
    ADD CONSTRAINT dev_project_frame_ds_pkey PRIMARY KEY (id);


--
-- TOC entry 3226 (class 1259 OID 137317)
-- Name: fki_dev_projects_frame_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_dev_projects_frame_id ON public.dev_project_frame_ds USING btree (dev_project_frame_id);


--
-- TOC entry 3227 (class 2606 OID 137312)
-- Name: dev_project_frame_ds dev_project_frame_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dev_project_frame_ds
    ADD CONSTRAINT dev_project_frame_id FOREIGN KEY (dev_project_frame_id) REFERENCES public.dev_project_frames(id) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3354 (class 0 OID 0)
-- Dependencies: 393
-- Name: TABLE dev_project_frame_ds; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.dev_project_frame_ds FROM postgres;
GRANT ALL ON TABLE public.dev_project_frame_ds TO postgres WITH GRANT OPTION;


-- Completed on 2020-08-12 20:55:19

--
-- PostgreSQL database dump complete
--

