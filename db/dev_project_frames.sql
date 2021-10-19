--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6
-- Dumped by pg_dump version 11.6

-- Started on 2020-08-12 20:54:06

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
-- TOC entry 389 (class 1259 OID 137286)
-- Name: dev_project_frames; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.dev_project_frames (
    id integer NOT NULL,
    project_req_development_id integer,
    is_out_source boolean,
    date_frame date,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    user_id integer
);


ALTER TABLE public.dev_project_frames OWNER TO postgres;

--
-- TOC entry 388 (class 1259 OID 137284)
-- Name: dev_projects_frames_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.dev_projects_frames_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.dev_projects_frames_id_seq OWNER TO postgres;

--
-- TOC entry 3353 (class 0 OID 0)
-- Dependencies: 388
-- Name: dev_projects_frames_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.dev_projects_frames_id_seq OWNED BY public.dev_project_frames.id;


--
-- TOC entry 3223 (class 2604 OID 137289)
-- Name: dev_project_frames id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dev_project_frames ALTER COLUMN id SET DEFAULT nextval('public.dev_projects_frames_id_seq'::regclass);


--
-- TOC entry 3225 (class 2606 OID 137291)
-- Name: dev_project_frames dev_projects_frames_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dev_project_frames
    ADD CONSTRAINT dev_projects_frames_pkey PRIMARY KEY (id);


--
-- TOC entry 3352 (class 0 OID 0)
-- Dependencies: 389
-- Name: TABLE dev_project_frames; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.dev_project_frames FROM postgres;
GRANT ALL ON TABLE public.dev_project_frames TO postgres WITH GRANT OPTION;


-- Completed on 2020-08-12 20:54:06

--
-- PostgreSQL database dump complete
--

