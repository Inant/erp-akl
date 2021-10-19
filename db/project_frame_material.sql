--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6
-- Dumped by pg_dump version 11.6

-- Started on 2020-08-21 20:40:16

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
-- TOC entry 398 (class 1259 OID 139049)
-- Name: dev_project_frame_material_workers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.dev_project_frame_material_workers (
    id integer NOT NULL,
    dev_project_frame_worker_id integer,
    m_item_id integer,
    m_unit_id integer,
    amount double precision,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    dev_project_frame_id integer
);


ALTER TABLE public.dev_project_frame_material_workers OWNER TO postgres;

--
-- TOC entry 397 (class 1259 OID 139047)
-- Name: dev_projects_frame_material_workers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.dev_projects_frame_material_workers_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dev_projects_frame_material_workers_id_seq OWNER TO postgres;

--
-- TOC entry 3363 (class 0 OID 0)
-- Dependencies: 397
-- Name: dev_projects_frame_material_workers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.dev_projects_frame_material_workers_id_seq OWNED BY public.dev_project_frame_material_workers.id;


--
-- TOC entry 3232 (class 2604 OID 139052)
-- Name: dev_project_frame_material_workers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dev_project_frame_material_workers ALTER COLUMN id SET DEFAULT nextval('public.dev_projects_frame_material_workers_id_seq'::regclass);


--
-- TOC entry 3234 (class 2606 OID 139054)
-- Name: dev_project_frame_material_workers dev_projects_frame_material_workers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dev_project_frame_material_workers
    ADD CONSTRAINT dev_projects_frame_material_workers_pkey PRIMARY KEY (id);


--
-- TOC entry 3235 (class 1259 OID 139077)
-- Name: fki_dev_projects_frame_worker_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_dev_projects_frame_worker_id ON public.dev_project_frame_material_workers USING btree (dev_project_frame_worker_id);


--
-- TOC entry 3236 (class 2606 OID 139072)
-- Name: dev_project_frame_material_workers dev_projects_frame_worker_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dev_project_frame_material_workers
    ADD CONSTRAINT dev_projects_frame_worker_id FOREIGN KEY (dev_project_frame_worker_id) REFERENCES public.dev_project_frame_workers(id) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


-- Completed on 2020-08-21 20:40:17

--
-- PostgreSQL database dump complete
--

