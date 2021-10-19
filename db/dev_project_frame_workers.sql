--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6
-- Dumped by pg_dump version 11.6

-- Started on 2020-08-12 20:54:43

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
-- TOC entry 391 (class 1259 OID 137294)
-- Name: dev_project_frame_workers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.dev_project_frame_workers (
    id integer NOT NULL,
    dev_project_frame_id integer,
    name_worker character varying(50),
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.dev_project_frame_workers OWNER TO postgres;

--
-- TOC entry 390 (class 1259 OID 137292)
-- Name: dev_projects_frame_workers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.dev_projects_frame_workers_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dev_projects_frame_workers_id_seq OWNER TO postgres;

--
-- TOC entry 3355 (class 0 OID 0)
-- Dependencies: 390
-- Name: dev_projects_frame_workers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.dev_projects_frame_workers_id_seq OWNED BY public.dev_project_frame_workers.id;


--
-- TOC entry 3223 (class 2604 OID 137297)
-- Name: dev_project_frame_workers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dev_project_frame_workers ALTER COLUMN id SET DEFAULT nextval('public.dev_projects_frame_workers_id_seq'::regclass);


--
-- TOC entry 3225 (class 2606 OID 137299)
-- Name: dev_project_frame_workers dev_projects_frame_workers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dev_project_frame_workers
    ADD CONSTRAINT dev_projects_frame_workers_pkey PRIMARY KEY (id);


--
-- TOC entry 3226 (class 1259 OID 137328)
-- Name: fki_dev_project_frame_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_dev_project_frame_id ON public.dev_project_frame_workers USING btree (dev_project_frame_id);


--
-- TOC entry 3227 (class 2606 OID 137323)
-- Name: dev_project_frame_workers dev_project_frame_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dev_project_frame_workers
    ADD CONSTRAINT dev_project_frame_id FOREIGN KEY (dev_project_frame_id) REFERENCES public.dev_project_frames(id) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- TOC entry 3354 (class 0 OID 0)
-- Dependencies: 391
-- Name: TABLE dev_project_frame_workers; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.dev_project_frame_workers FROM postgres;
GRANT ALL ON TABLE public.dev_project_frame_workers TO postgres WITH GRANT OPTION;


-- Completed on 2020-08-12 20:54:43

--
-- PostgreSQL database dump complete
--

