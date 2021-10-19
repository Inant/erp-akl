--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6
-- Dumped by pg_dump version 11.6

-- Started on 2020-05-16 13:10:12

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
-- TOC entry 196 (class 1259 OID 16394)
-- Name: customer_financials; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.customer_financials (
    id integer NOT NULL,
    customer_id integer NOT NULL,
    finance_type character varying NOT NULL,
    description character varying NOT NULL,
    amount numeric(18,2) NOT NULL,
    frequency character varying,
    state character(1) NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.customer_financials OWNER TO postgres;

--
-- TOC entry 3789 (class 0 OID 0)
-- Dependencies: 196
-- Name: COLUMN customer_financials.finance_type; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.customer_financials.finance_type IS 'Income , debt';


--
-- TOC entry 3790 (class 0 OID 0)
-- Dependencies: 196
-- Name: COLUMN customer_financials.frequency; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.customer_financials.frequency IS 'daily, weekly,..., undefined';


--
-- TOC entry 3791 (class 0 OID 0)
-- Dependencies: 196
-- Name: COLUMN customer_financials.state; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.customer_financials.state IS 'D or C Debit or credit';


--
-- TOC entry 197 (class 1259 OID 16400)
-- Name: customer_financials_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.customer_financials_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.customer_financials_id_seq OWNER TO postgres;

--
-- TOC entry 3792 (class 0 OID 0)
-- Dependencies: 197
-- Name: customer_financials_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.customer_financials_id_seq OWNED BY public.customer_financials.id;


--
-- TOC entry 198 (class 1259 OID 16402)
-- Name: customers_family_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.customers_family_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.customers_family_id_seq OWNER TO postgres;

--
-- TOC entry 199 (class 1259 OID 16404)
-- Name: customers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.customers (
    id integer NOT NULL,
    name character varying(50) NOT NULL,
    prospect_level smallint,
    birth_place character varying,
    birth_date date,
    gender character varying,
    religion character varying,
    marital_status character varying,
    address character varying,
    rt character varying(3),
    rw character varying(3),
    kelurahan character varying,
    kecamatan character varying,
    city character varying,
    zipcode character varying,
    notes character varying,
    profile_picture text,
    id_picture text,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    family_id integer DEFAULT nextval('public.customers_family_id_seq'::regclass),
    family_role character varying,
    id_no character varying,
    phone_no character varying,
    m_employee_id integer NOT NULL,
    coorporate_name character varying(200),
    email character varying(100)
);


ALTER TABLE public.customers OWNER TO postgres;

--
-- TOC entry 200 (class 1259 OID 16411)
-- Name: customers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.customers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.customers_id_seq OWNER TO postgres;

--
-- TOC entry 3795 (class 0 OID 0)
-- Dependencies: 200
-- Name: customers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.customers_id_seq OWNED BY public.customers.id;


--
-- TOC entry 201 (class 1259 OID 16413)
-- Name: discount_requests; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.discount_requests (
    id integer NOT NULL,
    no character varying,
    sale_trx_id integer,
    amount numeric(18,2) NOT NULL,
    is_approved boolean,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    project_id integer,
    amount_requested numeric(18,2)
);


ALTER TABLE public.discount_requests OWNER TO postgres;

--
-- TOC entry 202 (class 1259 OID 16419)
-- Name: discount_requests_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.discount_requests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.discount_requests_id_seq OWNER TO postgres;

--
-- TOC entry 3797 (class 0 OID 0)
-- Dependencies: 202
-- Name: discount_requests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.discount_requests_id_seq OWNED BY public.discount_requests.id;


--
-- TOC entry 203 (class 1259 OID 16421)
-- Name: followup_histories; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.followup_histories (
    id integer NOT NULL,
    no character varying,
    customer_id integer NOT NULL,
    m_employee_id integer NOT NULL,
    project_id integer,
    customer_budget numeric(18,2),
    followup_schedule date NOT NULL,
    followup_remark character varying,
    followup_result character varying,
    followup_status character varying NOT NULL,
    prospect_result character varying,
    notes character varying,
    manager_notes character varying,
    supervisor_notes character varying,
    info_source character varying,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.followup_histories OWNER TO postgres;

--
-- TOC entry 3798 (class 0 OID 0)
-- Dependencies: 203
-- Name: COLUMN followup_histories.followup_remark; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.followup_histories.followup_remark IS 'penjelasan basic sebelum marketing followup';


--
-- TOC entry 3799 (class 0 OID 0)
-- Dependencies: 203
-- Name: COLUMN followup_histories.followup_result; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.followup_histories.followup_result IS 'FAIL, UPDATE,dkk';


--
-- TOC entry 3800 (class 0 OID 0)
-- Dependencies: 203
-- Name: COLUMN followup_histories.followup_status; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.followup_histories.followup_status IS 'NEW, FINISH, PENDING';


--
-- TOC entry 204 (class 1259 OID 16427)
-- Name: followup_histories_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.followup_histories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.followup_histories_id_seq OWNER TO postgres;

--
-- TOC entry 3801 (class 0 OID 0)
-- Dependencies: 204
-- Name: followup_histories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.followup_histories_id_seq OWNED BY public.followup_histories.id;


--
-- TOC entry 205 (class 1259 OID 16429)
-- Name: gallery_photos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.gallery_photos (
    id bigint NOT NULL,
    filename character varying(300),
    creator integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


ALTER TABLE public.gallery_photos OWNER TO postgres;

--
-- TOC entry 206 (class 1259 OID 16432)
-- Name: gallery_photos_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.gallery_photos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.gallery_photos_id_seq OWNER TO postgres;

--
-- TOC entry 3803 (class 0 OID 0)
-- Dependencies: 206
-- Name: gallery_photos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.gallery_photos_id_seq OWNED BY public.gallery_photos.id;


--
-- TOC entry 207 (class 1259 OID 16434)
-- Name: general_setting_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.general_setting_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.general_setting_id_seq OWNER TO postgres;

--
-- TOC entry 208 (class 1259 OID 16436)
-- Name: general_settings; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.general_settings (
    id integer DEFAULT nextval('public.general_setting_id_seq'::regclass) NOT NULL,
    gs_code character varying(30) NOT NULL,
    gs_value character varying NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.general_settings OWNER TO postgres;

--
-- TOC entry 339 (class 1259 OID 51065)
-- Name: inv_orders; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.inv_orders (
    id integer NOT NULL,
    order_id integer,
    project_id integer,
    rab_id integer,
    order_d_id integer,
    product_sub_id integer,
    status integer,
    product_id integer
);


ALTER TABLE public.inv_orders OWNER TO postgres;

--
-- TOC entry 338 (class 1259 OID 51063)
-- Name: inv_orders_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.inv_orders_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.inv_orders_id_seq OWNER TO postgres;

--
-- TOC entry 3805 (class 0 OID 0)
-- Dependencies: 338
-- Name: inv_orders_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.inv_orders_id_seq OWNED BY public.inv_orders.id;


--
-- TOC entry 209 (class 1259 OID 16443)
-- Name: inv_request_ds; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.inv_request_ds (
    id integer NOT NULL,
    inv_request_id integer NOT NULL,
    m_item_id integer NOT NULL,
    amount numeric(12,2),
    m_unit_id integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    notes character varying(200),
    amount_auth numeric,
    detail_notes text
);


ALTER TABLE public.inv_request_ds OWNER TO postgres;

--
-- TOC entry 210 (class 1259 OID 16449)
-- Name: inv_request_ds_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.inv_request_ds_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.inv_request_ds_id_seq OWNER TO postgres;

--
-- TOC entry 3806 (class 0 OID 0)
-- Dependencies: 210
-- Name: inv_request_ds_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.inv_request_ds_id_seq OWNED BY public.inv_request_ds.id;


--
-- TOC entry 211 (class 1259 OID 16451)
-- Name: inv_requests; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.inv_requests (
    id integer NOT NULL,
    req_type character varying(10) NOT NULL,
    rab_id integer,
    m_warehouses_id integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    no character varying(20),
    user_auth character varying(60),
    contractor character varying(60),
    site_id integer
);


ALTER TABLE public.inv_requests OWNER TO postgres;

--
-- TOC entry 3807 (class 0 OID 0)
-- Dependencies: 211
-- Name: COLUMN inv_requests.user_auth; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.inv_requests.user_auth IS 'email user';


--
-- TOC entry 212 (class 1259 OID 16454)
-- Name: inv_requests_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.inv_requests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.inv_requests_id_seq OWNER TO postgres;

--
-- TOC entry 3808 (class 0 OID 0)
-- Dependencies: 212
-- Name: inv_requests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.inv_requests_id_seq OWNED BY public.inv_requests.id;


--
-- TOC entry 213 (class 1259 OID 16456)
-- Name: inv_return_ds; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.inv_return_ds (
    id integer NOT NULL,
    inv_return_id integer,
    m_item_id integer,
    m_unit_id integer,
    amount double precision,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.inv_return_ds OWNER TO postgres;

--
-- TOC entry 214 (class 1259 OID 16459)
-- Name: inv_return_ds_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.inv_return_ds_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.inv_return_ds_id_seq OWNER TO postgres;

--
-- TOC entry 3810 (class 0 OID 0)
-- Dependencies: 214
-- Name: inv_return_ds_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.inv_return_ds_id_seq OWNED BY public.inv_return_ds.id;


--
-- TOC entry 215 (class 1259 OID 16461)
-- Name: inv_returns; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.inv_returns (
    id integer NOT NULL,
    rab_id integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    no character varying(20),
    user_id integer
);


ALTER TABLE public.inv_returns OWNER TO postgres;

--
-- TOC entry 216 (class 1259 OID 16464)
-- Name: inv_returns_id_inv_return_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.inv_returns_id_inv_return_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.inv_returns_id_inv_return_seq OWNER TO postgres;

--
-- TOC entry 3812 (class 0 OID 0)
-- Dependencies: 216
-- Name: inv_returns_id_inv_return_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.inv_returns_id_inv_return_seq OWNED BY public.inv_returns.id;


--
-- TOC entry 217 (class 1259 OID 16466)
-- Name: inv_sale_ds; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.inv_sale_ds (
    id integer NOT NULL,
    inv_sale_id integer NOT NULL,
    m_item_id integer NOT NULL,
    amount numeric NOT NULL,
    base_price numeric NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.inv_sale_ds OWNER TO postgres;

--
-- TOC entry 218 (class 1259 OID 16472)
-- Name: inv_sale_ds_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.inv_sale_ds_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.inv_sale_ds_id_seq OWNER TO postgres;

--
-- TOC entry 3814 (class 0 OID 0)
-- Dependencies: 218
-- Name: inv_sale_ds_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.inv_sale_ds_id_seq OWNED BY public.inv_sale_ds.id;


--
-- TOC entry 219 (class 1259 OID 16474)
-- Name: inv_sales; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.inv_sales (
    id integer NOT NULL,
    site_id integer NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    no character varying(30)
);


ALTER TABLE public.inv_sales OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 16477)
-- Name: inv_sales_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.inv_sales_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.inv_sales_id_seq OWNER TO postgres;

--
-- TOC entry 3817 (class 0 OID 0)
-- Dependencies: 220
-- Name: inv_sales_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.inv_sales_id_seq OWNED BY public.inv_sales.id;


--
-- TOC entry 221 (class 1259 OID 16479)
-- Name: inv_trx_ds; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.inv_trx_ds (
    id integer NOT NULL,
    inv_trx_id integer NOT NULL,
    m_item_id integer NOT NULL,
    amount numeric(12,2),
    m_unit_id integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    notes character varying(120),
    use_amount numeric(12,2),
    value numeric,
    purchase_d_id integer
);


ALTER TABLE public.inv_trx_ds OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 16485)
-- Name: inv_trx_ds_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.inv_trx_ds_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.inv_trx_ds_id_seq OWNER TO postgres;

--
-- TOC entry 3819 (class 0 OID 0)
-- Dependencies: 222
-- Name: inv_trx_ds_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.inv_trx_ds_id_seq OWNED BY public.inv_trx_ds.id;


--
-- TOC entry 341 (class 1259 OID 51416)
-- Name: inv_trx_rest_ds; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.inv_trx_rest_ds (
    id integer NOT NULL,
    inv_trx_id integer,
    m_item_id integer,
    amount double precision,
    m_unit_id integer,
    notes text,
    use_amount double precision,
    value double precision,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    purchase_d_id integer
);


ALTER TABLE public.inv_trx_rest_ds OWNER TO postgres;

--
-- TOC entry 340 (class 1259 OID 51414)
-- Name: inv_trx_rest_ds_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.inv_trx_rest_ds_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.inv_trx_rest_ds_id_seq OWNER TO postgres;

--
-- TOC entry 3822 (class 0 OID 0)
-- Dependencies: 340
-- Name: inv_trx_rest_ds_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.inv_trx_rest_ds_id_seq OWNED BY public.inv_trx_rest_ds.id;


--
-- TOC entry 223 (class 1259 OID 16487)
-- Name: inv_trxes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.inv_trxes (
    id integer NOT NULL,
    m_warehouse_id integer NOT NULL,
    trx_type character varying(10) NOT NULL,
    purchase_id integer,
    inv_request_id integer,
    no character varying(20),
    inv_trx_date timestamp without time zone DEFAULT now() NOT NULL,
    site_id integer,
    is_entry boolean,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    transfer_stock_id integer,
    inv_sale_id integer,
    inv_return_id integer
);


ALTER TABLE public.inv_trxes OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 16491)
-- Name: inv_trxes_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.inv_trxes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.inv_trxes_id_seq OWNER TO postgres;

--
-- TOC entry 3823 (class 0 OID 0)
-- Dependencies: 224
-- Name: inv_trxes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.inv_trxes_id_seq OWNED BY public.inv_trxes.id;


--
-- TOC entry 225 (class 1259 OID 16493)
-- Name: invoices; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.invoices (
    id integer NOT NULL,
    no character varying,
    sale_trx_id integer,
    payment_method character varying NOT NULL,
    amount numeric(18,2) NOT NULL,
    due_date date,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.invoices OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 16499)
-- Name: invoices_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.invoices_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.invoices_id_seq OWNER TO postgres;

--
-- TOC entry 3824 (class 0 OID 0)
-- Dependencies: 226
-- Name: invoices_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.invoices_id_seq OWNED BY public.invoices.id;


--
-- TOC entry 227 (class 1259 OID 16501)
-- Name: kpr_simulation; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kpr_simulation (
    id bigint NOT NULL,
    bank_id integer,
    link_url character varying(300),
    created_at timestamp(6) without time zone,
    updated_at timestamp(6) without time zone
);


ALTER TABLE public.kpr_simulation OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 16504)
-- Name: kpr_simulation_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kpr_simulation_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.kpr_simulation_id_seq OWNER TO postgres;

--
-- TOC entry 3826 (class 0 OID 0)
-- Dependencies: 228
-- Name: kpr_simulation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.kpr_simulation_id_seq OWNED BY public.kpr_simulation.id;


--
-- TOC entry 229 (class 1259 OID 16506)
-- Name: list_bank; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.list_bank (
    id_bank integer NOT NULL,
    bank_name character varying(50),
    bank_code character varying(5),
    status character varying(20)
);


ALTER TABLE public.list_bank OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 16509)
-- Name: m_best_prices; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.m_best_prices (
    id integer NOT NULL,
    m_supplier_id integer NOT NULL,
    m_item_id integer NOT NULL,
    best_price numeric NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.m_best_prices OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 16515)
-- Name: m_best_price_materials_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.m_best_price_materials_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_best_price_materials_id_seq OWNER TO postgres;

--
-- TOC entry 3828 (class 0 OID 0)
-- Dependencies: 231
-- Name: m_best_price_materials_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.m_best_price_materials_id_seq OWNED BY public.m_best_prices.id;


--
-- TOC entry 232 (class 1259 OID 16517)
-- Name: m_cities; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.m_cities (
    id integer NOT NULL,
    city character varying(30) NOT NULL,
    province character varying(30),
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.m_cities OWNER TO postgres;

--
-- TOC entry 233 (class 1259 OID 16520)
-- Name: m_cities_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.m_cities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_cities_id_seq OWNER TO postgres;

--
-- TOC entry 3829 (class 0 OID 0)
-- Dependencies: 233
-- Name: m_cities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.m_cities_id_seq OWNED BY public.m_cities.id;


--
-- TOC entry 234 (class 1259 OID 16522)
-- Name: m_doc_types; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.m_doc_types (
    id integer NOT NULL,
    type character varying(20) NOT NULL,
    code character varying(20) NOT NULL,
    name character varying(50) NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    is_mandatory boolean
);


ALTER TABLE public.m_doc_types OWNER TO postgres;

--
-- TOC entry 235 (class 1259 OID 16525)
-- Name: m_doc_types_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.m_doc_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_doc_types_id_seq OWNER TO postgres;

--
-- TOC entry 3831 (class 0 OID 0)
-- Dependencies: 235
-- Name: m_doc_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.m_doc_types_id_seq OWNED BY public.m_doc_types.id;


--
-- TOC entry 236 (class 1259 OID 16527)
-- Name: m_employees; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.m_employees (
    id integer NOT NULL,
    name character varying(50) NOT NULL,
    division character varying,
    role character varying,
    "position" character varying,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    id_user integer,
    position_id integer DEFAULT 0 NOT NULL,
    telp character varying(20),
    site_id integer DEFAULT 0,
    email character varying(100),
    address text
);


ALTER TABLE public.m_employees OWNER TO postgres;

--
-- TOC entry 237 (class 1259 OID 16533)
-- Name: m_employees_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.m_employees_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_employees_id_seq OWNER TO postgres;

--
-- TOC entry 3832 (class 0 OID 0)
-- Dependencies: 237
-- Name: m_employees_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.m_employees_id_seq OWNED BY public.m_employees.id;


--
-- TOC entry 343 (class 1259 OID 59158)
-- Name: m_item_prices; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.m_item_prices (
    id integer NOT NULL,
    m_item_id integer,
    amount double precision,
    price double precision,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now(),
    deleted_at timestamp without time zone,
    site_id integer,
    m_unit_id integer
);


ALTER TABLE public.m_item_prices OWNER TO postgres;

--
-- TOC entry 342 (class 1259 OID 59156)
-- Name: m_item_price_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.m_item_price_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_item_price_id_seq OWNER TO postgres;

--
-- TOC entry 3834 (class 0 OID 0)
-- Dependencies: 342
-- Name: m_item_price_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.m_item_price_id_seq OWNED BY public.m_item_prices.id;


--
-- TOC entry 238 (class 1259 OID 16535)
-- Name: m_items; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.m_items (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    category character varying(60),
    volume numeric(8,4),
    late_time integer,
    m_unit_id integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    type integer DEFAULT 1 NOT NULL,
    no character varying(30),
    status character varying(15)
);


ALTER TABLE public.m_items OWNER TO postgres;

--
-- TOC entry 3835 (class 0 OID 0)
-- Dependencies: 238
-- Name: COLUMN m_items.type; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.m_items.type IS '1 : material habis pakai, 2 : upah kerja, 3 : sewa alat kembali, 4 : alat kerja habis pakai, ';


--
-- TOC entry 239 (class 1259 OID 16539)
-- Name: m_items_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.m_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_items_id_seq OWNER TO postgres;

--
-- TOC entry 3836 (class 0 OID 0)
-- Dependencies: 239
-- Name: m_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.m_items_id_seq OWNED BY public.m_items.id;


--
-- TOC entry 240 (class 1259 OID 16541)
-- Name: m_kpr_bank_payments; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.m_kpr_bank_payments (
    id integer NOT NULL,
    bank_name character varying(50) NOT NULL,
    progress_category character varying(50) NOT NULL,
    payment_percent numeric(6,2) NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    bank_code character varying(5)
);


ALTER TABLE public.m_kpr_bank_payments OWNER TO postgres;

--
-- TOC entry 241 (class 1259 OID 16544)
-- Name: m_kpr_bank_payments_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.m_kpr_bank_payments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_kpr_bank_payments_id_seq OWNER TO postgres;

--
-- TOC entry 3838 (class 0 OID 0)
-- Dependencies: 241
-- Name: m_kpr_bank_payments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.m_kpr_bank_payments_id_seq OWNED BY public.m_kpr_bank_payments.id;


--
-- TOC entry 329 (class 1259 OID 26384)
-- Name: m_positions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.m_positions (
    id integer NOT NULL,
    name character varying(100),
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.m_positions OWNER TO postgres;

--
-- TOC entry 328 (class 1259 OID 26382)
-- Name: m_positions_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.m_positions_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_positions_id_seq OWNER TO postgres;

--
-- TOC entry 3840 (class 0 OID 0)
-- Dependencies: 328
-- Name: m_positions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.m_positions_id_seq OWNED BY public.m_positions.id;


--
-- TOC entry 242 (class 1259 OID 16546)
-- Name: m_references; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.m_references (
    id integer NOT NULL,
    code character varying(20) NOT NULL,
    value character varying(60) NOT NULL,
    name character varying(60) NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.m_references OWNER TO postgres;

--
-- TOC entry 243 (class 1259 OID 16549)
-- Name: m_references_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.m_references_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_references_id_seq OWNER TO postgres;

--
-- TOC entry 3841 (class 0 OID 0)
-- Dependencies: 243
-- Name: m_references_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.m_references_id_seq OWNED BY public.m_references.id;


--
-- TOC entry 244 (class 1259 OID 16551)
-- Name: m_sequences_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.m_sequences_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_sequences_id_seq OWNER TO postgres;

--
-- TOC entry 245 (class 1259 OID 16553)
-- Name: m_sequences; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.m_sequences (
    id integer DEFAULT nextval('public.m_sequences_id_seq'::regclass) NOT NULL,
    seq_code character varying(10),
    period_year character varying(4),
    period_month character varying(2),
    site_id integer,
    seq_length integer,
    seq_no integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.m_sequences OWNER TO postgres;

--
-- TOC entry 246 (class 1259 OID 16557)
-- Name: m_suppliers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.m_suppliers (
    id integer NOT NULL,
    name character varying(60) NOT NULL,
    address text,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    no character varying(20),
    city character varying(50),
    phone character varying(20),
    notes character varying(30)
);


ALTER TABLE public.m_suppliers OWNER TO postgres;

--
-- TOC entry 247 (class 1259 OID 16563)
-- Name: m_suppliers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.m_suppliers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_suppliers_id_seq OWNER TO postgres;

--
-- TOC entry 3842 (class 0 OID 0)
-- Dependencies: 247
-- Name: m_suppliers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.m_suppliers_id_seq OWNED BY public.m_suppliers.id;


--
-- TOC entry 248 (class 1259 OID 16565)
-- Name: m_units; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.m_units (
    id integer NOT NULL,
    name character varying(20) NOT NULL,
    code character varying(5) NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.m_units OWNER TO postgres;

--
-- TOC entry 249 (class 1259 OID 16568)
-- Name: m_units_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.m_units_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_units_id_seq OWNER TO postgres;

--
-- TOC entry 3843 (class 0 OID 0)
-- Dependencies: 249
-- Name: m_units_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.m_units_id_seq OWNED BY public.m_units.id;


--
-- TOC entry 250 (class 1259 OID 16570)
-- Name: m_warehouses; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.m_warehouses (
    id integer NOT NULL,
    name character varying(20) NOT NULL,
    code character varying(5) NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.m_warehouses OWNER TO postgres;

--
-- TOC entry 251 (class 1259 OID 16573)
-- Name: m_warehouses_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.m_warehouses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_warehouses_id_seq OWNER TO postgres;

--
-- TOC entry 3844 (class 0 OID 0)
-- Dependencies: 251
-- Name: m_warehouses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.m_warehouses_id_seq OWNED BY public.m_warehouses.id;


--
-- TOC entry 252 (class 1259 OID 16575)
-- Name: material_prices; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.material_prices (
    id integer NOT NULL,
    m_supplier_id integer NOT NULL,
    m_item_id integer NOT NULL,
    base_price numeric NOT NULL,
    created_at time without time zone,
    updated_at time without time zone,
    deleted_at time without time zone
);


ALTER TABLE public.material_prices OWNER TO postgres;

--
-- TOC entry 253 (class 1259 OID 16581)
-- Name: material_prices_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.material_prices_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.material_prices_id_seq OWNER TO postgres;

--
-- TOC entry 3845 (class 0 OID 0)
-- Dependencies: 253
-- Name: material_prices_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.material_prices_id_seq OWNED BY public.material_prices.id;


--
-- TOC entry 254 (class 1259 OID 16583)
-- Name: menus_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.menus_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.menus_id_seq OWNER TO postgres;

--
-- TOC entry 255 (class 1259 OID 16585)
-- Name: menus; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.menus (
    id integer DEFAULT nextval('public.menus_id_seq'::regclass) NOT NULL,
    title character varying(255) NOT NULL,
    url character varying(255) NOT NULL,
    icon character varying(255) NOT NULL,
    is_main_menu integer NOT NULL,
    is_active integer NOT NULL,
    seq_no integer NOT NULL,
    created_at timestamp(0) without time zone DEFAULT now(),
    updated_at timestamp(0) without time zone DEFAULT now(),
    is_deleted boolean DEFAULT false
);


ALTER TABLE public.menus OWNER TO postgres;

--
-- TOC entry 256 (class 1259 OID 16595)
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO postgres;

--
-- TOC entry 257 (class 1259 OID 16597)
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer DEFAULT nextval('public.migrations_id_seq'::regclass) NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- TOC entry 337 (class 1259 OID 35763)
-- Name: order_ds; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.order_ds (
    id integer NOT NULL,
    order_id integer,
    product_id integer,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now(),
    total double precision DEFAULT 0,
    in_rab integer DEFAULT 0,
    deleted_at timestamp without time zone
);


ALTER TABLE public.order_ds OWNER TO postgres;

--
-- TOC entry 336 (class 1259 OID 35761)
-- Name: order_ds_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.order_ds_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.order_ds_id_seq OWNER TO postgres;

--
-- TOC entry 3847 (class 0 OID 0)
-- Dependencies: 336
-- Name: order_ds_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.order_ds_id_seq OWNED BY public.order_ds.id;


--
-- TOC entry 335 (class 1259 OID 35755)
-- Name: orders; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.orders (
    id integer NOT NULL,
    customer_id integer,
    order_name character varying(200),
    order_date date,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    order_no character varying(30),
    site_id integer,
    is_done integer DEFAULT 0
);


ALTER TABLE public.orders OWNER TO postgres;

--
-- TOC entry 334 (class 1259 OID 35753)
-- Name: orders_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.orders_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.orders_id_seq OWNER TO postgres;

--
-- TOC entry 3849 (class 0 OID 0)
-- Dependencies: 334
-- Name: orders_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.orders_id_seq OWNED BY public.orders.id;


--
-- TOC entry 258 (class 1259 OID 16601)
-- Name: password_resets; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_resets (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_resets OWNER TO postgres;

--
-- TOC entry 259 (class 1259 OID 16607)
-- Name: payment_receives; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.payment_receives (
    id integer NOT NULL,
    no character varying,
    invoice_id integer NOT NULL,
    payment_type character varying NOT NULL,
    bank_account_no character varying,
    amount numeric(18,2) NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.payment_receives OWNER TO postgres;

--
-- TOC entry 260 (class 1259 OID 16613)
-- Name: payment_receives_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.payment_receives_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.payment_receives_id_seq OWNER TO postgres;

--
-- TOC entry 3850 (class 0 OID 0)
-- Dependencies: 260
-- Name: payment_receives_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.payment_receives_id_seq OWNED BY public.payment_receives.id;


--
-- TOC entry 333 (class 1259 OID 35625)
-- Name: product_subs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.product_subs (
    id integer NOT NULL,
    product_id integer,
    price double precision DEFAULT 0,
    no character varying(30),
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    order_d_id integer
);


ALTER TABLE public.product_subs OWNER TO postgres;

--
-- TOC entry 332 (class 1259 OID 35623)
-- Name: product_subs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.product_subs_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.product_subs_id_seq OWNER TO postgres;

--
-- TOC entry 3852 (class 0 OID 0)
-- Dependencies: 332
-- Name: product_subs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.product_subs_id_seq OWNED BY public.product_subs.id;


--
-- TOC entry 331 (class 1259 OID 34582)
-- Name: products; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.products (
    id integer NOT NULL,
    name character varying(100),
    description text,
    image text,
    price double precision DEFAULT 0,
    m_unit_id integer,
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now(),
    deleted_at timestamp without time zone,
    is_active boolean
);


ALTER TABLE public.products OWNER TO postgres;

--
-- TOC entry 330 (class 1259 OID 34580)
-- Name: products_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.products_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.products_id_seq OWNER TO postgres;

--
-- TOC entry 3854 (class 0 OID 0)
-- Dependencies: 330
-- Name: products_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.products_id_seq OWNED BY public.products.id;


--
-- TOC entry 261 (class 1259 OID 16615)
-- Name: programs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.programs (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    user_id integer NOT NULL,
    status text NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.programs OWNER TO postgres;

--
-- TOC entry 262 (class 1259 OID 16621)
-- Name: programs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.programs_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.programs_id_seq OWNER TO postgres;

--
-- TOC entry 3856 (class 0 OID 0)
-- Dependencies: 262
-- Name: programs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.programs_id_seq OWNED BY public.programs.id;


--
-- TOC entry 263 (class 1259 OID 16623)
-- Name: project_works; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.project_works (
    id integer NOT NULL,
    rab_id integer,
    project_id integer NOT NULL,
    name character varying(50) NOT NULL,
    base_price numeric(18,2),
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.project_works OWNER TO postgres;

--
-- TOC entry 264 (class 1259 OID 16626)
-- Name: project_works_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.project_works_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_works_id_seq OWNER TO postgres;

--
-- TOC entry 3857 (class 0 OID 0)
-- Dependencies: 264
-- Name: project_works_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.project_works_id_seq OWNED BY public.project_works.id;


--
-- TOC entry 265 (class 1259 OID 16628)
-- Name: project_worksub_ds; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.project_worksub_ds (
    id integer NOT NULL,
    project_worksub_id integer NOT NULL,
    m_item_id integer NOT NULL,
    amount numeric(12,2),
    m_unit_id integer,
    base_price numeric(18,2),
    buy_date date,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.project_worksub_ds OWNER TO postgres;

--
-- TOC entry 266 (class 1259 OID 16631)
-- Name: project_worksub_ds_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.project_worksub_ds_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_worksub_ds_id_seq OWNER TO postgres;

--
-- TOC entry 3858 (class 0 OID 0)
-- Dependencies: 266
-- Name: project_worksub_ds_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.project_worksub_ds_id_seq OWNED BY public.project_worksub_ds.id;


--
-- TOC entry 267 (class 1259 OID 16633)
-- Name: project_worksubs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.project_worksubs (
    id integer NOT NULL,
    project_work_id integer NOT NULL,
    name character varying(50) NOT NULL,
    base_price numeric(18,2),
    amount numeric(12,2),
    m_unit_id integer NOT NULL,
    work_start date,
    work_end date,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.project_worksubs OWNER TO postgres;

--
-- TOC entry 268 (class 1259 OID 16636)
-- Name: project_worksubs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.project_worksubs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_worksubs_id_seq OWNER TO postgres;

--
-- TOC entry 3859 (class 0 OID 0)
-- Dependencies: 268
-- Name: project_worksubs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.project_worksubs_id_seq OWNED BY public.project_worksubs.id;


--
-- TOC entry 269 (class 1259 OID 16638)
-- Name: projects; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.projects (
    id integer NOT NULL,
    site_id integer NOT NULL,
    name character varying(100) NOT NULL,
    area character varying(10),
    base_price numeric(18,2),
    sale_status character varying NOT NULL,
    customer_id integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    product_id integer,
    order_id integer
);


ALTER TABLE public.projects OWNER TO postgres;

--
-- TOC entry 270 (class 1259 OID 16644)
-- Name: projects_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.projects_id_seq
    START WITH 246
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.projects_id_seq OWNER TO postgres;

--
-- TOC entry 3860 (class 0 OID 0)
-- Dependencies: 270
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.projects_id_seq OWNED BY public.projects.id;


--
-- TOC entry 271 (class 1259 OID 16646)
-- Name: purchase_ds; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.purchase_ds (
    id integer NOT NULL,
    purchase_id integer NOT NULL,
    m_item_id integer NOT NULL,
    amount numeric(12,2),
    m_unit_id integer,
    base_price numeric(18,2),
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    buy_date date
);


ALTER TABLE public.purchase_ds OWNER TO postgres;

--
-- TOC entry 272 (class 1259 OID 16649)
-- Name: purchase_ds_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.purchase_ds_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.purchase_ds_id_seq OWNER TO postgres;

--
-- TOC entry 3861 (class 0 OID 0)
-- Dependencies: 272
-- Name: purchase_ds_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.purchase_ds_id_seq OWNED BY public.purchase_ds.id;


--
-- TOC entry 273 (class 1259 OID 16651)
-- Name: purchases; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.purchases (
    id integer NOT NULL,
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
    ekspedisi character varying(60)
);


ALTER TABLE public.purchases OWNER TO postgres;

--
-- TOC entry 274 (class 1259 OID 16656)
-- Name: purchases_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.purchases_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.purchases_id_seq OWNER TO postgres;

--
-- TOC entry 3862 (class 0 OID 0)
-- Dependencies: 274
-- Name: purchases_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.purchases_id_seq OWNED BY public.purchases.id;


--
-- TOC entry 275 (class 1259 OID 16658)
-- Name: rab_request_ds_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.rab_request_ds_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.rab_request_ds_id_seq OWNER TO postgres;

--
-- TOC entry 276 (class 1259 OID 16660)
-- Name: rab_request_ds; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rab_request_ds (
    id integer DEFAULT nextval('public.rab_request_ds_id_seq'::regclass) NOT NULL,
    no character varying,
    rab_request_id integer NOT NULL,
    additional_work character varying,
    is_approved boolean,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.rab_request_ds OWNER TO postgres;

--
-- TOC entry 277 (class 1259 OID 16667)
-- Name: rab_requests; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rab_requests (
    id integer NOT NULL,
    no character varying,
    sale_trx_id integer NOT NULL,
    amount numeric(18,2),
    is_approved boolean,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    additional_work character varying,
    project_id integer,
    amount_requested numeric(18,2),
    customer_id integer
);


ALTER TABLE public.rab_requests OWNER TO postgres;

--
-- TOC entry 278 (class 1259 OID 16673)
-- Name: rab_requests_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.rab_requests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.rab_requests_id_seq OWNER TO postgres;

--
-- TOC entry 3864 (class 0 OID 0)
-- Dependencies: 278
-- Name: rab_requests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.rab_requests_id_seq OWNED BY public.rab_requests.id;


--
-- TOC entry 279 (class 1259 OID 16675)
-- Name: rabs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rabs (
    id integer NOT NULL,
    project_id integer NOT NULL,
    no character varying(30) NOT NULL,
    base_price numeric(18,2),
    is_final boolean DEFAULT false NOT NULL,
    stats_code character varying(10) NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    order_d_id integer
);


ALTER TABLE public.rabs OWNER TO postgres;

--
-- TOC entry 280 (class 1259 OID 16679)
-- Name: rabs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.rabs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.rabs_id_seq OWNER TO postgres;

--
-- TOC entry 3865 (class 0 OID 0)
-- Dependencies: 280
-- Name: rabs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.rabs_id_seq OWNED BY public.rabs.id;


--
-- TOC entry 281 (class 1259 OID 16681)
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.roles_id_seq OWNER TO postgres;

--
-- TOC entry 282 (class 1259 OID 16683)
-- Name: roles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.roles (
    id integer DEFAULT nextval('public.roles_id_seq'::regclass) NOT NULL,
    role_code character varying(20) NOT NULL,
    role_name character varying(60) NOT NULL,
    created_at timestamp(0) without time zone DEFAULT now(),
    updated_at timestamp(0) without time zone DEFAULT now(),
    created_by character varying(60),
    updated_by character varying(60)
);


ALTER TABLE public.roles OWNER TO postgres;

--
-- TOC entry 283 (class 1259 OID 16689)
-- Name: sale_trx_docs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sale_trx_docs (
    id integer NOT NULL,
    sale_trx_id integer NOT NULL,
    no character varying,
    name character varying,
    due_date date,
    is_checked boolean,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    m_doc_type_id integer NOT NULL
);


ALTER TABLE public.sale_trx_docs OWNER TO postgres;

--
-- TOC entry 284 (class 1259 OID 16695)
-- Name: sale_trx_docs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sale_trx_docs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sale_trx_docs_id_seq OWNER TO postgres;

--
-- TOC entry 3867 (class 0 OID 0)
-- Dependencies: 284
-- Name: sale_trx_docs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sale_trx_docs_id_seq OWNED BY public.sale_trx_docs.id;


--
-- TOC entry 285 (class 1259 OID 16697)
-- Name: sale_trx_ds; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sale_trx_ds (
    id integer NOT NULL,
    sale_trx_id integer NOT NULL,
    trx_d_code character varying NOT NULL,
    seq_no integer DEFAULT 1 NOT NULL,
    tenor integer DEFAULT 1,
    due_day integer DEFAULT 1,
    amount numeric(18,2),
    due_date date,
    project_id integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.sale_trx_ds OWNER TO postgres;

--
-- TOC entry 3868 (class 0 OID 0)
-- Dependencies: 285
-- Name: COLUMN sale_trx_ds.trx_d_code; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.sale_trx_ds.trx_d_code IS 'Kavling, INHOUSE, KPR,

';


--
-- TOC entry 3869 (class 0 OID 0)
-- Dependencies: 285
-- Name: COLUMN sale_trx_ds.seq_no; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.sale_trx_ds.seq_no IS 'Kavling, INHOUSE, KPR,

';


--
-- TOC entry 286 (class 1259 OID 16706)
-- Name: sale_trx_ds_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sale_trx_ds_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sale_trx_ds_id_seq OWNER TO postgres;

--
-- TOC entry 3870 (class 0 OID 0)
-- Dependencies: 286
-- Name: sale_trx_ds_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sale_trx_ds_id_seq OWNED BY public.sale_trx_ds.id;


--
-- TOC entry 287 (class 1259 OID 16708)
-- Name: sale_trx_kpr_bank_payments; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sale_trx_kpr_bank_payments (
    id integer NOT NULL,
    sale_trx_id integer NOT NULL,
    m_kpr_bank_payment_id integer,
    plan_at timestamp without time zone,
    payment_amount numeric(18,2),
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.sale_trx_kpr_bank_payments OWNER TO postgres;

--
-- TOC entry 288 (class 1259 OID 16711)
-- Name: sale_trx_kpr_bank_payments_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sale_trx_kpr_bank_payments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sale_trx_kpr_bank_payments_id_seq OWNER TO postgres;

--
-- TOC entry 3872 (class 0 OID 0)
-- Dependencies: 288
-- Name: sale_trx_kpr_bank_payments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sale_trx_kpr_bank_payments_id_seq OWNED BY public.sale_trx_kpr_bank_payments.id;


--
-- TOC entry 289 (class 1259 OID 16713)
-- Name: sale_trxes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sale_trxes (
    id integer NOT NULL,
    no character varying,
    customer_id integer NOT NULL,
    m_employee_id integer NOT NULL,
    follow_history_id integer,
    trx_type character varying NOT NULL,
    payment_method character varying NOT NULL,
    total_amount numeric(18,2),
    base_amount numeric(18,2),
    cash_amount numeric(18,2),
    nup_planned_date date,
    spu_planned_date date,
    project_id integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    dp_inhouse_amount numeric(18,2),
    dp_kpr_amount numeric(18,2),
    is_printed boolean,
    is_validated boolean,
    bank_account character varying,
    total_discount numeric(18,2),
    additional_amount numeric(18,2),
    ppn_amount numeric(18,2),
    pbhtb_amount numeric(18,2),
    address character varying(150),
    sale_trx_id integer,
    specup_amount numeric(18,2),
    fasum_fee numeric(18,2),
    notary_fee numeric(18,2),
    booking_amount numeric(18,2),
    owner_name character varying(50),
    residence_address character varying(255),
    legal_address character varying(255),
    deal_type character varying(100),
    residence_rt character varying(3),
    residence_rw character varying(3),
    residence_kelurahan character varying(50),
    residence_kecamatan character varying(50),
    residence_city character varying(20),
    residence_zipcode character varying(10),
    legal_rt character varying(3),
    legal_rw character varying(3),
    legal_kelurahan character varying(50),
    legal_kecamatan character varying(50),
    legal_city character varying(20),
    legal_zipcode character varying(10)
);


ALTER TABLE public.sale_trxes OWNER TO postgres;

--
-- TOC entry 3873 (class 0 OID 0)
-- Dependencies: 289
-- Name: COLUMN sale_trxes.trx_type; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.sale_trxes.trx_type IS 'FOLLOWUP, NUP, SPU, PPJB';


--
-- TOC entry 3874 (class 0 OID 0)
-- Dependencies: 289
-- Name: COLUMN sale_trxes.payment_method; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.sale_trxes.payment_method IS 'Free, Cash, kpr, inhouse';


--
-- TOC entry 290 (class 1259 OID 16719)
-- Name: sale_trxes_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sale_trxes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sale_trxes_id_seq OWNER TO postgres;

--
-- TOC entry 3875 (class 0 OID 0)
-- Dependencies: 290
-- Name: sale_trxes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sale_trxes_id_seq OWNED BY public.sale_trxes.id;


--
-- TOC entry 291 (class 1259 OID 16721)
-- Name: sequence_id_bank; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sequence_id_bank
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sequence_id_bank OWNER TO postgres;

--
-- TOC entry 292 (class 1259 OID 16723)
-- Name: sites; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sites (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    m_city_id integer NOT NULL,
    address character varying(100),
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    code character varying(10)
);


ALTER TABLE public.sites OWNER TO postgres;

--
-- TOC entry 293 (class 1259 OID 16726)
-- Name: sites_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.sites_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sites_id_seq OWNER TO postgres;

--
-- TOC entry 3876 (class 0 OID 0)
-- Dependencies: 293
-- Name: sites_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.sites_id_seq OWNED BY public.sites.id;


--
-- TOC entry 294 (class 1259 OID 16728)
-- Name: stock_opname_ds; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.stock_opname_ds (
    id integer NOT NULL,
    stock_opname_id integer NOT NULL,
    m_item_id integer NOT NULL,
    amount numeric NOT NULL,
    real_amount numeric NOT NULL,
    notes character varying(200),
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone
);


ALTER TABLE public.stock_opname_ds OWNER TO postgres;

--
-- TOC entry 295 (class 1259 OID 16734)
-- Name: stock_opname_ds_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.stock_opname_ds_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.stock_opname_ds_id_seq OWNER TO postgres;

--
-- TOC entry 3877 (class 0 OID 0)
-- Dependencies: 295
-- Name: stock_opname_ds_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.stock_opname_ds_id_seq OWNED BY public.stock_opname_ds.id;


--
-- TOC entry 296 (class 1259 OID 16736)
-- Name: stock_opnames; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.stock_opnames (
    id integer NOT NULL,
    no character varying(30) NOT NULL,
    site_id integer NOT NULL,
    date date,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    is_closed boolean NOT NULL
);


ALTER TABLE public.stock_opnames OWNER TO postgres;

--
-- TOC entry 297 (class 1259 OID 16739)
-- Name: stock_opnames_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.stock_opnames_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.stock_opnames_id_seq OWNER TO postgres;

--
-- TOC entry 3878 (class 0 OID 0)
-- Dependencies: 297
-- Name: stock_opnames_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.stock_opnames_id_seq OWNED BY public.stock_opnames.id;


--
-- TOC entry 317 (class 1259 OID 26329)
-- Name: tbl_absensi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tbl_absensi (
    id_absensi integer NOT NULL,
    m_employee_id integer,
    jam_datang time without time zone,
    tanggal date,
    id_shift integer,
    durasi_lembur integer,
    uang_lembur double precision,
    keterangan text,
    dtm_crt timestamp without time zone,
    dtm_upd timestamp without time zone,
    jam_pulang time without time zone
);


ALTER TABLE public.tbl_absensi OWNER TO postgres;

--
-- TOC entry 316 (class 1259 OID 26327)
-- Name: tbl_absensi_id_absensi_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tbl_absensi_id_absensi_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_absensi_id_absensi_seq OWNER TO postgres;

--
-- TOC entry 3880 (class 0 OID 0)
-- Dependencies: 316
-- Name: tbl_absensi_id_absensi_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tbl_absensi_id_absensi_seq OWNED BY public.tbl_absensi.id_absensi;


--
-- TOC entry 307 (class 1259 OID 18063)
-- Name: tbl_akun; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tbl_akun (
    id_akun integer NOT NULL,
    no_akun character varying(15),
    nama_akun character varying(100),
    level integer,
    id_main_akun integer,
    sifat_debit integer,
    sifat_kredit integer,
    dtm_crt timestamp without time zone DEFAULT now(),
    dtm_upd timestamp without time zone DEFAULT now()
);


ALTER TABLE public.tbl_akun OWNER TO postgres;

--
-- TOC entry 309 (class 1259 OID 18071)
-- Name: tbl_akun_detail; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tbl_akun_detail (
    id_akun_d integer NOT NULL,
    id_akun integer,
    id_parent integer,
    turunan1 integer DEFAULT 0,
    turunan2 integer DEFAULT 0,
    turunan3 integer DEFAULT 0,
    dtm_crt timestamp without time zone DEFAULT now(),
    dtm_upd timestamp without time zone DEFAULT now()
);


ALTER TABLE public.tbl_akun_detail OWNER TO postgres;

--
-- TOC entry 308 (class 1259 OID 18069)
-- Name: tbl_akun_detail_id_akun_d_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tbl_akun_detail_id_akun_d_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_akun_detail_id_akun_d_seq OWNER TO postgres;

--
-- TOC entry 3883 (class 0 OID 0)
-- Dependencies: 308
-- Name: tbl_akun_detail_id_akun_d_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tbl_akun_detail_id_akun_d_seq OWNED BY public.tbl_akun_detail.id_akun_d;


--
-- TOC entry 306 (class 1259 OID 18061)
-- Name: tbl_akun_id_akun_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tbl_akun_id_akun_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_akun_id_akun_seq OWNER TO postgres;

--
-- TOC entry 3884 (class 0 OID 0)
-- Dependencies: 306
-- Name: tbl_akun_id_akun_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tbl_akun_id_akun_seq OWNED BY public.tbl_akun.id_akun;


--
-- TOC entry 319 (class 1259 OID 26340)
-- Name: tbl_cuti; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tbl_cuti (
    id integer NOT NULL,
    m_employee_id integer,
    tanggal date,
    dtm_crt timestamp without time zone DEFAULT now(),
    dtm_upd timestamp without time zone DEFAULT now()
);


ALTER TABLE public.tbl_cuti OWNER TO postgres;

--
-- TOC entry 318 (class 1259 OID 26338)
-- Name: tbl_cuti_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tbl_cuti_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_cuti_id_seq OWNER TO postgres;

--
-- TOC entry 3886 (class 0 OID 0)
-- Dependencies: 318
-- Name: tbl_cuti_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tbl_cuti_id_seq OWNED BY public.tbl_cuti.id;


--
-- TOC entry 327 (class 1259 OID 26372)
-- Name: tbl_other_gaji; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tbl_other_gaji (
    id integer NOT NULL,
    potongan_bpjs double precision,
    cicilan double precision,
    kasbon double precision,
    komisi_langsung double precision,
    bulan character varying,
    m_employee_id integer,
    dtm_crt timestamp without time zone,
    dtm_upd timestamp without time zone
);


ALTER TABLE public.tbl_other_gaji OWNER TO postgres;

--
-- TOC entry 326 (class 1259 OID 26370)
-- Name: tbl_other_gaji_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tbl_other_gaji_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_other_gaji_id_seq OWNER TO postgres;

--
-- TOC entry 3888 (class 0 OID 0)
-- Dependencies: 326
-- Name: tbl_other_gaji_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tbl_other_gaji_id_seq OWNED BY public.tbl_other_gaji.id;


--
-- TOC entry 321 (class 1259 OID 26348)
-- Name: tbl_ref_gaji; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tbl_ref_gaji (
    id_ref_gaji integer NOT NULL,
    id_jabatan integer,
    gaji_pokok double precision,
    uang_kehadiran double precision,
    uang_makan double precision,
    uang_transport double precision,
    uang_lembur double precision,
    dtm_crt timestamp without time zone,
    dtm_upd timestamp without time zone
);


ALTER TABLE public.tbl_ref_gaji OWNER TO postgres;

--
-- TOC entry 320 (class 1259 OID 26346)
-- Name: tbl_ref_gaji_id_ref_gaji_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tbl_ref_gaji_id_ref_gaji_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_ref_gaji_id_ref_gaji_seq OWNER TO postgres;

--
-- TOC entry 3890 (class 0 OID 0)
-- Dependencies: 320
-- Name: tbl_ref_gaji_id_ref_gaji_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tbl_ref_gaji_id_ref_gaji_seq OWNED BY public.tbl_ref_gaji.id_ref_gaji;


--
-- TOC entry 311 (class 1259 OID 18089)
-- Name: tbl_saldo_akun; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tbl_saldo_akun (
    id_saldo integer NOT NULL,
    id_akun integer,
    tanggal date,
    jumlah_saldo double precision,
    is_updated integer,
    dtm_crt timestamp without time zone,
    dtm_upd timestamp without time zone,
    location_id integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.tbl_saldo_akun OWNER TO postgres;

--
-- TOC entry 310 (class 1259 OID 18087)
-- Name: tbl_saldo_akun_id_saldo_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tbl_saldo_akun_id_saldo_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_saldo_akun_id_saldo_seq OWNER TO postgres;

--
-- TOC entry 3892 (class 0 OID 0)
-- Dependencies: 310
-- Name: tbl_saldo_akun_id_saldo_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tbl_saldo_akun_id_saldo_seq OWNED BY public.tbl_saldo_akun.id_saldo;


--
-- TOC entry 323 (class 1259 OID 26356)
-- Name: tbl_setting_gaji; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tbl_setting_gaji (
    id_setting_gaji integer NOT NULL,
    m_employee_id integer,
    gaji_pokok double precision,
    denda double precision,
    komisi double precision DEFAULT 0,
    dtm_crt timestamp without time zone DEFAULT now(),
    dtm_upd timestamp without time zone DEFAULT now(),
    denda_telat double precision DEFAULT 0 NOT NULL
);


ALTER TABLE public.tbl_setting_gaji OWNER TO postgres;

--
-- TOC entry 322 (class 1259 OID 26354)
-- Name: tbl_setting_gaji_id_setting_gaji_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tbl_setting_gaji_id_setting_gaji_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_setting_gaji_id_setting_gaji_seq OWNER TO postgres;

--
-- TOC entry 3894 (class 0 OID 0)
-- Dependencies: 322
-- Name: tbl_setting_gaji_id_setting_gaji_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tbl_setting_gaji_id_setting_gaji_seq OWNED BY public.tbl_setting_gaji.id_setting_gaji;


--
-- TOC entry 325 (class 1259 OID 26364)
-- Name: tbl_shift; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tbl_shift (
    id_shift integer NOT NULL,
    nama_shift character varying(30),
    jam_datang time without time zone,
    jam_pulang time without time zone,
    site_id integer,
    dtm_crt timestamp without time zone,
    dtm_upd timestamp without time zone
);


ALTER TABLE public.tbl_shift OWNER TO postgres;

--
-- TOC entry 324 (class 1259 OID 26362)
-- Name: tbl_shift_id_shift_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tbl_shift_id_shift_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_shift_id_shift_seq OWNER TO postgres;

--
-- TOC entry 3896 (class 0 OID 0)
-- Dependencies: 324
-- Name: tbl_shift_id_shift_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tbl_shift_id_shift_seq OWNED BY public.tbl_shift.id_shift;


--
-- TOC entry 313 (class 1259 OID 18102)
-- Name: tbl_trx_akuntansi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tbl_trx_akuntansi (
    id_trx_akun integer NOT NULL,
    deskripsi text,
    tanggal date,
    dtm_crt timestamp without time zone DEFAULT now(),
    dtm_upd timestamp without time zone DEFAULT now(),
    location_id integer DEFAULT 0
);


ALTER TABLE public.tbl_trx_akuntansi OWNER TO postgres;

--
-- TOC entry 315 (class 1259 OID 18113)
-- Name: tbl_trx_akuntansi_detail; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tbl_trx_akuntansi_detail (
    id_trx_akun_detail integer NOT NULL,
    id_trx_akun integer,
    id_akun integer,
    jumlah double precision,
    tipe character varying,
    keterangan character varying,
    dtm_crt timestamp without time zone,
    dtm_upd timestamp without time zone
);


ALTER TABLE public.tbl_trx_akuntansi_detail OWNER TO postgres;

--
-- TOC entry 314 (class 1259 OID 18111)
-- Name: tbl_trx_akuntansi_detail_id_trx_akun_detail_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tbl_trx_akuntansi_detail_id_trx_akun_detail_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_trx_akuntansi_detail_id_trx_akun_detail_seq OWNER TO postgres;

--
-- TOC entry 3899 (class 0 OID 0)
-- Dependencies: 314
-- Name: tbl_trx_akuntansi_detail_id_trx_akun_detail_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tbl_trx_akuntansi_detail_id_trx_akun_detail_seq OWNED BY public.tbl_trx_akuntansi_detail.id_trx_akun_detail;


--
-- TOC entry 312 (class 1259 OID 18100)
-- Name: tbl_trx_akuntansi_id_trx_akun_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tbl_trx_akuntansi_id_trx_akun_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tbl_trx_akuntansi_id_trx_akun_seq OWNER TO postgres;

--
-- TOC entry 3900 (class 0 OID 0)
-- Dependencies: 312
-- Name: tbl_trx_akuntansi_id_trx_akun_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tbl_trx_akuntansi_id_trx_akun_seq OWNED BY public.tbl_trx_akuntansi.id_trx_akun;


--
-- TOC entry 298 (class 1259 OID 16741)
-- Name: transfer_stock_ds; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.transfer_stock_ds (
    id integer NOT NULL,
    transfer_stock_id integer NOT NULL,
    m_item_id integer NOT NULL,
    amount numeric NOT NULL,
    m_unit_id integer NOT NULL,
    notes character varying(200),
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    actual_amount numeric
);


ALTER TABLE public.transfer_stock_ds OWNER TO postgres;

--
-- TOC entry 299 (class 1259 OID 16747)
-- Name: transfer_stock_ds_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.transfer_stock_ds_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.transfer_stock_ds_id_seq OWNER TO postgres;

--
-- TOC entry 3901 (class 0 OID 0)
-- Dependencies: 299
-- Name: transfer_stock_ds_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.transfer_stock_ds_id_seq OWNED BY public.transfer_stock_ds.id;


--
-- TOC entry 300 (class 1259 OID 16749)
-- Name: transfer_stocks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.transfer_stocks (
    id integer NOT NULL,
    site_from integer,
    site_to integer,
    due_date_receive date,
    is_sent boolean,
    is_receive boolean,
    shipping character varying(120),
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    no character varying(60),
    created_at timestamp without time zone
);


ALTER TABLE public.transfer_stocks OWNER TO postgres;

--
-- TOC entry 301 (class 1259 OID 16752)
-- Name: transfer_stocks_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.transfer_stocks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.transfer_stocks_id_seq OWNER TO postgres;

--
-- TOC entry 3902 (class 0 OID 0)
-- Dependencies: 301
-- Name: transfer_stocks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.transfer_stocks_id_seq OWNED BY public.transfer_stocks.id;


--
-- TOC entry 302 (class 1259 OID 16754)
-- Name: user_permission_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.user_permission_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_permission_id_seq OWNER TO postgres;

--
-- TOC entry 303 (class 1259 OID 16756)
-- Name: user_permission; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.user_permission (
    id integer DEFAULT nextval('public.user_permission_id_seq'::regclass) NOT NULL,
    role_id integer NOT NULL,
    menu_id integer NOT NULL,
    created_at timestamp(0) without time zone DEFAULT now(),
    updated_at timestamp(0) without time zone DEFAULT now(),
    created_by character varying(60),
    updated_by character varying(60)
);


ALTER TABLE public.user_permission OWNER TO postgres;

--
-- TOC entry 304 (class 1259 OID 16762)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO postgres;

--
-- TOC entry 305 (class 1259 OID 16764)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id integer DEFAULT nextval('public.users_id_seq'::regclass) NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    is_active integer DEFAULT 1 NOT NULL,
    is_deleted boolean DEFAULT false,
    role_id integer DEFAULT 0 NOT NULL,
    site_id integer,
    m_employee_id integer
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 3156 (class 2604 OID 16774)
-- Name: customer_financials id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer_financials ALTER COLUMN id SET DEFAULT nextval('public.customer_financials_id_seq'::regclass);


--
-- TOC entry 3158 (class 2604 OID 16775)
-- Name: customers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customers ALTER COLUMN id SET DEFAULT nextval('public.customers_id_seq'::regclass);


--
-- TOC entry 3159 (class 2604 OID 16776)
-- Name: discount_requests id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discount_requests ALTER COLUMN id SET DEFAULT nextval('public.discount_requests_id_seq'::regclass);


--
-- TOC entry 3160 (class 2604 OID 16777)
-- Name: followup_histories id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.followup_histories ALTER COLUMN id SET DEFAULT nextval('public.followup_histories_id_seq'::regclass);


--
-- TOC entry 3161 (class 2604 OID 16778)
-- Name: gallery_photos id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.gallery_photos ALTER COLUMN id SET DEFAULT nextval('public.gallery_photos_id_seq'::regclass);


--
-- TOC entry 3274 (class 2604 OID 51068)
-- Name: inv_orders id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_orders ALTER COLUMN id SET DEFAULT nextval('public.inv_orders_id_seq'::regclass);


--
-- TOC entry 3163 (class 2604 OID 16779)
-- Name: inv_request_ds id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_request_ds ALTER COLUMN id SET DEFAULT nextval('public.inv_request_ds_id_seq'::regclass);


--
-- TOC entry 3164 (class 2604 OID 16780)
-- Name: inv_requests id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_requests ALTER COLUMN id SET DEFAULT nextval('public.inv_requests_id_seq'::regclass);


--
-- TOC entry 3165 (class 2604 OID 16781)
-- Name: inv_return_ds id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_return_ds ALTER COLUMN id SET DEFAULT nextval('public.inv_return_ds_id_seq'::regclass);


--
-- TOC entry 3166 (class 2604 OID 16782)
-- Name: inv_returns id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_returns ALTER COLUMN id SET DEFAULT nextval('public.inv_returns_id_inv_return_seq'::regclass);


--
-- TOC entry 3167 (class 2604 OID 16783)
-- Name: inv_sale_ds id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_sale_ds ALTER COLUMN id SET DEFAULT nextval('public.inv_sale_ds_id_seq'::regclass);


--
-- TOC entry 3168 (class 2604 OID 16784)
-- Name: inv_sales id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_sales ALTER COLUMN id SET DEFAULT nextval('public.inv_sales_id_seq'::regclass);


--
-- TOC entry 3169 (class 2604 OID 16785)
-- Name: inv_trx_ds id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_trx_ds ALTER COLUMN id SET DEFAULT nextval('public.inv_trx_ds_id_seq'::regclass);


--
-- TOC entry 3275 (class 2604 OID 51419)
-- Name: inv_trx_rest_ds id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_trx_rest_ds ALTER COLUMN id SET DEFAULT nextval('public.inv_trx_rest_ds_id_seq'::regclass);


--
-- TOC entry 3171 (class 2604 OID 16786)
-- Name: inv_trxes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_trxes ALTER COLUMN id SET DEFAULT nextval('public.inv_trxes_id_seq'::regclass);


--
-- TOC entry 3172 (class 2604 OID 16787)
-- Name: invoices id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.invoices ALTER COLUMN id SET DEFAULT nextval('public.invoices_id_seq'::regclass);


--
-- TOC entry 3173 (class 2604 OID 16788)
-- Name: kpr_simulation id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kpr_simulation ALTER COLUMN id SET DEFAULT nextval('public.kpr_simulation_id_seq'::regclass);


--
-- TOC entry 3174 (class 2604 OID 16789)
-- Name: m_best_prices id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_best_prices ALTER COLUMN id SET DEFAULT nextval('public.m_best_price_materials_id_seq'::regclass);


--
-- TOC entry 3175 (class 2604 OID 16790)
-- Name: m_cities id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_cities ALTER COLUMN id SET DEFAULT nextval('public.m_cities_id_seq'::regclass);


--
-- TOC entry 3176 (class 2604 OID 16791)
-- Name: m_doc_types id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_doc_types ALTER COLUMN id SET DEFAULT nextval('public.m_doc_types_id_seq'::regclass);


--
-- TOC entry 3177 (class 2604 OID 16792)
-- Name: m_employees id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_employees ALTER COLUMN id SET DEFAULT nextval('public.m_employees_id_seq'::regclass);


--
-- TOC entry 3276 (class 2604 OID 59161)
-- Name: m_item_prices id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_item_prices ALTER COLUMN id SET DEFAULT nextval('public.m_item_price_id_seq'::regclass);


--
-- TOC entry 3181 (class 2604 OID 16793)
-- Name: m_items id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_items ALTER COLUMN id SET DEFAULT nextval('public.m_items_id_seq'::regclass);


--
-- TOC entry 3182 (class 2604 OID 16794)
-- Name: m_kpr_bank_payments id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_kpr_bank_payments ALTER COLUMN id SET DEFAULT nextval('public.m_kpr_bank_payments_id_seq'::regclass);


--
-- TOC entry 3258 (class 2604 OID 26387)
-- Name: m_positions id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_positions ALTER COLUMN id SET DEFAULT nextval('public.m_positions_id_seq'::regclass);


--
-- TOC entry 3183 (class 2604 OID 16795)
-- Name: m_references id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_references ALTER COLUMN id SET DEFAULT nextval('public.m_references_id_seq'::regclass);


--
-- TOC entry 3185 (class 2604 OID 16796)
-- Name: m_suppliers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_suppliers ALTER COLUMN id SET DEFAULT nextval('public.m_suppliers_id_seq'::regclass);


--
-- TOC entry 3186 (class 2604 OID 16797)
-- Name: m_units id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_units ALTER COLUMN id SET DEFAULT nextval('public.m_units_id_seq'::regclass);


--
-- TOC entry 3187 (class 2604 OID 16798)
-- Name: m_warehouses id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_warehouses ALTER COLUMN id SET DEFAULT nextval('public.m_warehouses_id_seq'::regclass);


--
-- TOC entry 3188 (class 2604 OID 16799)
-- Name: material_prices id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.material_prices ALTER COLUMN id SET DEFAULT nextval('public.material_prices_id_seq'::regclass);


--
-- TOC entry 3269 (class 2604 OID 35766)
-- Name: order_ds id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_ds ALTER COLUMN id SET DEFAULT nextval('public.order_ds_id_seq'::regclass);


--
-- TOC entry 3267 (class 2604 OID 35758)
-- Name: orders id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders ALTER COLUMN id SET DEFAULT nextval('public.orders_id_seq'::regclass);


--
-- TOC entry 3194 (class 2604 OID 16800)
-- Name: payment_receives id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payment_receives ALTER COLUMN id SET DEFAULT nextval('public.payment_receives_id_seq'::regclass);


--
-- TOC entry 3265 (class 2604 OID 35628)
-- Name: product_subs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_subs ALTER COLUMN id SET DEFAULT nextval('public.product_subs_id_seq'::regclass);


--
-- TOC entry 3261 (class 2604 OID 34585)
-- Name: products id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products ALTER COLUMN id SET DEFAULT nextval('public.products_id_seq'::regclass);


--
-- TOC entry 3195 (class 2604 OID 16801)
-- Name: programs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.programs ALTER COLUMN id SET DEFAULT nextval('public.programs_id_seq'::regclass);


--
-- TOC entry 3196 (class 2604 OID 16802)
-- Name: project_works id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_works ALTER COLUMN id SET DEFAULT nextval('public.project_works_id_seq'::regclass);


--
-- TOC entry 3197 (class 2604 OID 16803)
-- Name: project_worksub_ds id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_worksub_ds ALTER COLUMN id SET DEFAULT nextval('public.project_worksub_ds_id_seq'::regclass);


--
-- TOC entry 3198 (class 2604 OID 16804)
-- Name: project_worksubs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_worksubs ALTER COLUMN id SET DEFAULT nextval('public.project_worksubs_id_seq'::regclass);


--
-- TOC entry 3199 (class 2604 OID 16805)
-- Name: projects id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects ALTER COLUMN id SET DEFAULT nextval('public.projects_id_seq'::regclass);


--
-- TOC entry 3200 (class 2604 OID 16806)
-- Name: purchase_ds id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchase_ds ALTER COLUMN id SET DEFAULT nextval('public.purchase_ds_id_seq'::regclass);


--
-- TOC entry 3203 (class 2604 OID 16807)
-- Name: purchases id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchases ALTER COLUMN id SET DEFAULT nextval('public.purchases_id_seq'::regclass);


--
-- TOC entry 3205 (class 2604 OID 16808)
-- Name: rab_requests id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rab_requests ALTER COLUMN id SET DEFAULT nextval('public.rab_requests_id_seq'::regclass);


--
-- TOC entry 3207 (class 2604 OID 16809)
-- Name: rabs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rabs ALTER COLUMN id SET DEFAULT nextval('public.rabs_id_seq'::regclass);


--
-- TOC entry 3211 (class 2604 OID 16810)
-- Name: sale_trx_docs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_trx_docs ALTER COLUMN id SET DEFAULT nextval('public.sale_trx_docs_id_seq'::regclass);


--
-- TOC entry 3215 (class 2604 OID 16811)
-- Name: sale_trx_ds id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_trx_ds ALTER COLUMN id SET DEFAULT nextval('public.sale_trx_ds_id_seq'::regclass);


--
-- TOC entry 3216 (class 2604 OID 16812)
-- Name: sale_trx_kpr_bank_payments id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_trx_kpr_bank_payments ALTER COLUMN id SET DEFAULT nextval('public.sale_trx_kpr_bank_payments_id_seq'::regclass);


--
-- TOC entry 3217 (class 2604 OID 16813)
-- Name: sale_trxes id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_trxes ALTER COLUMN id SET DEFAULT nextval('public.sale_trxes_id_seq'::regclass);


--
-- TOC entry 3218 (class 2604 OID 16814)
-- Name: sites id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sites ALTER COLUMN id SET DEFAULT nextval('public.sites_id_seq'::regclass);


--
-- TOC entry 3219 (class 2604 OID 16815)
-- Name: stock_opname_ds id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stock_opname_ds ALTER COLUMN id SET DEFAULT nextval('public.stock_opname_ds_id_seq'::regclass);


--
-- TOC entry 3220 (class 2604 OID 16816)
-- Name: stock_opnames id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stock_opnames ALTER COLUMN id SET DEFAULT nextval('public.stock_opnames_id_seq'::regclass);


--
-- TOC entry 3246 (class 2604 OID 26332)
-- Name: tbl_absensi id_absensi; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_absensi ALTER COLUMN id_absensi SET DEFAULT nextval('public.tbl_absensi_id_absensi_seq'::regclass);


--
-- TOC entry 3230 (class 2604 OID 18066)
-- Name: tbl_akun id_akun; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_akun ALTER COLUMN id_akun SET DEFAULT nextval('public.tbl_akun_id_akun_seq'::regclass);


--
-- TOC entry 3233 (class 2604 OID 18074)
-- Name: tbl_akun_detail id_akun_d; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_akun_detail ALTER COLUMN id_akun_d SET DEFAULT nextval('public.tbl_akun_detail_id_akun_d_seq'::regclass);


--
-- TOC entry 3247 (class 2604 OID 26343)
-- Name: tbl_cuti id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_cuti ALTER COLUMN id SET DEFAULT nextval('public.tbl_cuti_id_seq'::regclass);


--
-- TOC entry 3257 (class 2604 OID 26375)
-- Name: tbl_other_gaji id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_other_gaji ALTER COLUMN id SET DEFAULT nextval('public.tbl_other_gaji_id_seq'::regclass);


--
-- TOC entry 3250 (class 2604 OID 26351)
-- Name: tbl_ref_gaji id_ref_gaji; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_ref_gaji ALTER COLUMN id_ref_gaji SET DEFAULT nextval('public.tbl_ref_gaji_id_ref_gaji_seq'::regclass);


--
-- TOC entry 3239 (class 2604 OID 18092)
-- Name: tbl_saldo_akun id_saldo; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_saldo_akun ALTER COLUMN id_saldo SET DEFAULT nextval('public.tbl_saldo_akun_id_saldo_seq'::regclass);


--
-- TOC entry 3251 (class 2604 OID 26359)
-- Name: tbl_setting_gaji id_setting_gaji; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_setting_gaji ALTER COLUMN id_setting_gaji SET DEFAULT nextval('public.tbl_setting_gaji_id_setting_gaji_seq'::regclass);


--
-- TOC entry 3256 (class 2604 OID 26367)
-- Name: tbl_shift id_shift; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_shift ALTER COLUMN id_shift SET DEFAULT nextval('public.tbl_shift_id_shift_seq'::regclass);


--
-- TOC entry 3241 (class 2604 OID 18105)
-- Name: tbl_trx_akuntansi id_trx_akun; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_trx_akuntansi ALTER COLUMN id_trx_akun SET DEFAULT nextval('public.tbl_trx_akuntansi_id_trx_akun_seq'::regclass);


--
-- TOC entry 3245 (class 2604 OID 18116)
-- Name: tbl_trx_akuntansi_detail id_trx_akun_detail; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_trx_akuntansi_detail ALTER COLUMN id_trx_akun_detail SET DEFAULT nextval('public.tbl_trx_akuntansi_detail_id_trx_akun_detail_seq'::regclass);


--
-- TOC entry 3221 (class 2604 OID 16817)
-- Name: transfer_stock_ds id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transfer_stock_ds ALTER COLUMN id SET DEFAULT nextval('public.transfer_stock_ds_id_seq'::regclass);


--
-- TOC entry 3222 (class 2604 OID 16818)
-- Name: transfer_stocks id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transfer_stocks ALTER COLUMN id SET DEFAULT nextval('public.transfer_stocks_id_seq'::regclass);


--
-- TOC entry 3636 (class 0 OID 16394)
-- Dependencies: 196
-- Data for Name: customer_financials; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.customer_financials (id, customer_id, finance_type, description, amount, frequency, state, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 3639 (class 0 OID 16404)
-- Dependencies: 199
-- Data for Name: customers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.customers (id, name, prospect_level, birth_place, birth_date, gender, religion, marital_status, address, rt, rw, kelurahan, kecamatan, city, zipcode, notes, profile_picture, id_picture, created_at, updated_at, deleted_at, family_id, family_role, id_no, phone_no, m_employee_id, coorporate_name, email) FROM stdin;
92	hary tano	\N	\N	\N	\N	\N	\N	jl mangga	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	2	main	\N	09809090980	1	pt perindo	hary@gmail.com
\.


--
-- TOC entry 3641 (class 0 OID 16413)
-- Dependencies: 201
-- Data for Name: discount_requests; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.discount_requests (id, no, sale_trx_id, amount, is_approved, created_at, updated_at, deleted_at, project_id, amount_requested) FROM stdin;
\.


--
-- TOC entry 3643 (class 0 OID 16421)
-- Dependencies: 203
-- Data for Name: followup_histories; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.followup_histories (id, no, customer_id, m_employee_id, project_id, customer_budget, followup_schedule, followup_remark, followup_result, followup_status, prospect_result, notes, manager_notes, supervisor_notes, info_source, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 3645 (class 0 OID 16429)
-- Dependencies: 205
-- Data for Name: gallery_photos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.gallery_photos (id, filename, creator, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 3648 (class 0 OID 16436)
-- Dependencies: 208
-- Data for Name: general_settings; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.general_settings (id, gs_code, gs_value, created_at, updated_at, deleted_at) FROM stdin;
1	HARI_PEMBELIAN_RUTIN	1	\N	\N	\N
2	pphpercent	5	\N	\N	\N
3	pbhtbpercent	10	\N	\N	\N
4	spufee	1000000	\N	\N	\N
5	notaryfee	5000000	\N	\N	\N
6	fasumfee	1000000	\N	\N	\N
7	notaryfee	5000000	\N	\N	\N
8	fasumfee	1000000	\N	\N	\N
\.


--
-- TOC entry 3779 (class 0 OID 51065)
-- Dependencies: 339
-- Data for Name: inv_orders; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.inv_orders (id, order_id, project_id, rab_id, order_d_id, product_sub_id, status, product_id) FROM stdin;
\.


--
-- TOC entry 3649 (class 0 OID 16443)
-- Dependencies: 209
-- Data for Name: inv_request_ds; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.inv_request_ds (id, inv_request_id, m_item_id, amount, m_unit_id, created_at, updated_at, deleted_at, notes, amount_auth, detail_notes) FROM stdin;
84	75	439	4.00	17	2020-05-16 12:39:07	2020-05-16 12:39:07	\N	\N	\N	\N
\.


--
-- TOC entry 3651 (class 0 OID 16451)
-- Dependencies: 211
-- Data for Name: inv_requests; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.inv_requests (id, req_type, rab_id, m_warehouses_id, created_at, updated_at, deleted_at, no, user_auth, contractor, site_id) FROM stdin;
75	REQ_ITEM	36	\N	2020-05-16 12:39:06	2020-05-16 12:50:18	\N	110/07/05/20/012	\N	supriadi	\N
\.


--
-- TOC entry 3653 (class 0 OID 16456)
-- Dependencies: 213
-- Data for Name: inv_return_ds; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.inv_return_ds (id, inv_return_id, m_item_id, m_unit_id, amount, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 3655 (class 0 OID 16461)
-- Dependencies: 215
-- Data for Name: inv_returns; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.inv_returns (id, rab_id, created_at, updated_at, deleted_at, no, user_id) FROM stdin;
\.


--
-- TOC entry 3657 (class 0 OID 16466)
-- Dependencies: 217
-- Data for Name: inv_sale_ds; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.inv_sale_ds (id, inv_sale_id, m_item_id, amount, base_price, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 3659 (class 0 OID 16474)
-- Dependencies: 219
-- Data for Name: inv_sales; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.inv_sales (id, site_id, created_at, updated_at, deleted_at, no) FROM stdin;
\.


--
-- TOC entry 3661 (class 0 OID 16479)
-- Dependencies: 221
-- Data for Name: inv_trx_ds; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.inv_trx_ds (id, inv_trx_id, m_item_id, amount, m_unit_id, created_at, updated_at, deleted_at, notes, use_amount, value, purchase_d_id) FROM stdin;
441	185	439	10.00	17	2020-05-16 11:28:02	2020-05-16 11:28:02	\N	A8	\N	\N	489
442	186	439	10.00	17	2020-05-16 11:29:07	2020-05-16 11:29:07	\N	A8	\N	\N	490
443	187	439	4.00	17	2020-05-16 12:50:18	2020-05-16 12:50:18	\N	\N	\N	6000	\N
\.


--
-- TOC entry 3781 (class 0 OID 51416)
-- Dependencies: 341
-- Data for Name: inv_trx_rest_ds; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.inv_trx_rest_ds (id, inv_trx_id, m_item_id, amount, m_unit_id, notes, use_amount, value, created_at, updated_at, deleted_at, purchase_d_id) FROM stdin;
\.


--
-- TOC entry 3663 (class 0 OID 16487)
-- Dependencies: 223
-- Data for Name: inv_trxes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.inv_trxes (id, m_warehouse_id, trx_type, purchase_id, inv_request_id, no, inv_trx_date, site_id, is_entry, created_at, updated_at, deleted_at, transfer_stock_id, inv_sale_id, inv_return_id) FROM stdin;
185	1	RECEIPT	148	\N	110/06/05/20/021	2020-05-16 00:00:00	1	t	2020-05-16 11:28:02	2020-05-16 11:28:02	\N	\N	\N	\N
186	1	RECEIPT	149	\N	110/06/05/20/022	2020-05-16 00:00:00	1	t	2020-05-16 11:29:07	2020-05-16 11:29:07	\N	\N	\N	\N
187	1	REQ_ITEM	\N	75	110/09/05/20/007	2020-05-16 00:00:00	1	f	2020-05-16 12:50:18	2020-05-16 12:50:18	\N	\N	\N	\N
\.


--
-- TOC entry 3665 (class 0 OID 16493)
-- Dependencies: 225
-- Data for Name: invoices; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.invoices (id, no, sale_trx_id, payment_method, amount, due_date, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 3667 (class 0 OID 16501)
-- Dependencies: 227
-- Data for Name: kpr_simulation; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.kpr_simulation (id, bank_id, link_url, created_at, updated_at) FROM stdin;
9	1	https://www.youtube.com/watch?v=LDojLdsUSlQ	2019-08-06 01:41:47	2019-08-06 01:42:25
\.


--
-- TOC entry 3669 (class 0 OID 16506)
-- Dependencies: 229
-- Data for Name: list_bank; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.list_bank (id_bank, bank_name, bank_code, status) FROM stdin;
1	MANDIRI SYARIAH (BSM)	001	sudah terpakai
2	BNI Syariah	002	sudah terpakai
3	JATIM SYARIAH (BJS)	003	sudah terpakai
4	BRI SYARIAH	004	sudah terpakai
5	BRI (KONVENSIONAL)	005	sudah terpakai
6	PANIN	006	sudah terpakai
8	BANK BCA	014	belum terpakai
10	CITIBANK	031	belum terpakai
7	BANK MUAMALAT	147	belum terpakai
9	BANK MEGA	426	belum terpakai
\.


--
-- TOC entry 3670 (class 0 OID 16509)
-- Dependencies: 230
-- Data for Name: m_best_prices; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.m_best_prices (id, m_supplier_id, m_item_id, best_price, created_at, updated_at, deleted_at) FROM stdin;
30	9	1260	50000	2019-05-30 14:18:45	2019-05-30 14:18:45	\N
31	26	1185	8000	2019-05-30 14:28:43	2019-05-30 14:28:43	\N
32	26	417	3000	2019-05-30 14:28:45	2019-05-30 14:28:45	\N
111	60	1247	75000	2019-06-01 00:50:22	2019-06-01 00:50:22	\N
35	29	1297	5000	2019-05-30 15:16:24	2019-05-30 15:16:24	\N
36	29	1204	3000	2019-05-30 15:16:26	2019-05-30 15:16:26	\N
37	26	1273	3250	2019-05-30 15:24:37	2019-05-30 15:24:37	\N
33	6	1339	50000	2019-05-30 14:32:00	2019-05-30 15:35:34	\N
39	6	424	82000	2019-05-30 16:02:10	2019-05-30 16:02:10	\N
40	26	1186	15000	2019-05-30 16:35:33	2019-05-30 16:35:33	\N
41	26	426	125	2019-05-30 16:35:34	2019-05-30 16:35:34	\N
42	26	1159	750	2019-05-30 16:35:36	2019-05-30 16:35:36	\N
43	26	1183	1880	2019-05-30 16:35:37	2019-05-30 16:35:37	\N
44	26	1220	6500	2019-05-30 16:35:38	2019-05-30 16:35:38	\N
46	6	1340	50000	2019-05-30 16:38:45	2019-05-30 16:38:45	\N
47	18	428	45000	2019-05-30 16:49:22	2019-05-30 16:49:22	\N
48	59	429	195000	2019-05-30 16:56:07	2019-05-30 16:56:07	\N
49	60	1200	104000	2019-05-31 23:08:23	2019-05-31 23:08:23	\N
50	60	1201	180000	2019-05-31 23:08:24	2019-05-31 23:08:24	\N
51	60	1205	575000	2019-05-31 23:08:25	2019-05-31 23:08:25	\N
52	60	1209	17000	2019-05-31 23:08:27	2019-05-31 23:08:27	\N
53	60	1214	121000	2019-05-31 23:08:28	2019-05-31 23:08:28	\N
54	60	1212	11500	2019-05-31 23:08:29	2019-05-31 23:08:29	\N
55	60	1218	113680	2019-05-31 23:08:31	2019-05-31 23:08:31	\N
56	60	1219	10000	2019-05-31 23:08:32	2019-05-31 23:08:32	\N
57	60	1221	118000	2019-05-31 23:08:34	2019-05-31 23:08:34	\N
58	60	1222	40575	2019-05-31 23:22:35	2019-05-31 23:22:35	\N
59	60	1225	55000	2019-05-31 23:22:36	2019-05-31 23:22:36	\N
61	60	1229	45000	2019-05-31 23:22:39	2019-05-31 23:22:39	\N
62	60	1230	49500	2019-05-31 23:22:40	2019-05-31 23:22:40	\N
64	60	1232	110	2019-05-31 23:22:43	2019-05-31 23:22:43	\N
65	60	1195	2500	2019-05-31 23:22:44	2019-05-31 23:22:44	\N
66	60	1235	29625	2019-05-31 23:22:45	2019-05-31 23:22:45	\N
68	60	1370	34000	2019-06-01 00:06:33	2019-06-01 00:06:33	\N
69	60	1369	7500	2019-06-01 00:06:34	2019-06-01 00:06:34	\N
70	60	1368	61344	2019-06-01 00:06:35	2019-06-01 00:06:35	\N
71	60	1367	32160	2019-06-01 00:06:36	2019-06-01 00:06:36	\N
72	60	1357	120	2019-06-01 00:06:38	2019-06-01 00:06:38	\N
73	60	1365	9984	2019-06-01 00:06:39	2019-06-01 00:06:39	\N
74	60	1364	37000	2019-06-01 00:06:40	2019-06-01 00:06:40	\N
75	60	1363	33000	2019-06-01 00:06:42	2019-06-01 00:06:42	\N
76	60	1362	7167	2019-06-01 00:06:43	2019-06-01 00:06:43	\N
77	60	1361	5500	2019-06-01 00:06:45	2019-06-01 00:06:45	\N
78	60	1356	2334	2019-06-01 00:16:22	2019-06-01 00:16:22	\N
79	60	1352	5750	2019-06-01 00:16:23	2019-06-01 00:16:23	\N
80	60	1349	8000	2019-06-01 00:16:24	2019-06-01 00:16:24	\N
81	60	1347	17000	2019-06-01 00:16:25	2019-06-01 00:16:25	\N
82	60	1343	125000	2019-06-01 00:16:27	2019-06-01 00:16:27	\N
83	60	1342	135	2019-06-01 00:16:28	2019-06-01 00:16:28	\N
84	60	1336	34000	2019-06-01 00:16:29	2019-06-01 00:16:29	\N
85	60	1335	350000	2019-06-01 00:16:31	2019-06-01 00:16:31	\N
86	60	1334	67000	2019-06-01 00:16:32	2019-06-01 00:16:32	\N
87	60	1333	67000	2019-06-01 00:16:33	2019-06-01 00:16:33	\N
88	60	1332	67000	2019-06-01 00:24:11	2019-06-01 00:24:11	\N
90	60	1331	8000	2019-06-01 00:24:14	2019-06-01 00:24:14	\N
91	60	1330	200000	2019-06-01 00:24:15	2019-06-01 00:24:15	\N
93	60	1329	10000	2019-06-01 00:24:17	2019-06-01 00:24:17	\N
94	60	1328	15000	2019-06-01 00:24:19	2019-06-01 00:24:19	\N
95	60	1327	4000	2019-06-01 00:24:20	2019-06-01 00:24:20	\N
96	60	1325	3000	2019-06-01 00:24:21	2019-06-01 00:24:21	\N
97	60	1324	4000	2019-06-01 00:24:22	2019-06-01 00:24:22	\N
98	60	1322	67000	2019-06-01 00:38:39	2019-06-01 00:38:39	\N
99	60	1320	22910	2019-06-01 00:38:40	2019-06-01 00:38:40	\N
100	60	1317	12500	2019-06-01 00:38:42	2019-06-01 00:38:42	\N
101	60	1315	44167	2019-06-01 00:38:43	2019-06-01 00:38:43	\N
102	60	1313	65834	2019-06-01 00:38:44	2019-06-01 00:38:44	\N
103	60	1239	125	2019-06-01 00:50:11	2019-06-01 00:50:11	\N
104	60	1240	111000	2019-06-01 00:50:12	2019-06-01 00:50:12	\N
105	60	1241	9500	2019-06-01 00:50:14	2019-06-01 00:50:14	\N
106	60	1242	23000	2019-06-01 00:50:15	2019-06-01 00:50:15	\N
107	60	1243	22500	2019-06-01 00:50:16	2019-06-01 00:50:16	\N
108	60	1244	451000	2019-06-01 00:50:17	2019-06-01 00:50:17	\N
109	60	1173	8125	2019-06-01 00:50:18	2019-06-01 00:50:18	\N
110	60	1245	221000	2019-06-01 00:50:20	2019-06-01 00:50:20	\N
92	60	1246	4500	2019-06-01 00:24:16	2019-06-01 00:50:21	\N
112	60	1182	750	2019-06-01 01:16:11	2019-06-01 01:16:11	\N
113	60	433	2350	2019-06-01 01:16:12	2019-06-01 01:16:12	\N
117	60	1254	67000	2019-06-01 01:16:18	2019-06-01 01:16:18	\N
118	60	1255	73000	2019-06-01 01:16:19	2019-06-01 01:16:19	\N
119	60	1198	7000	2019-06-01 01:16:20	2019-06-01 01:16:20	\N
120	60	1184	12750	2019-06-01 01:16:22	2019-06-01 01:16:22	\N
121	60	1263	2830	2019-06-01 01:16:23	2019-06-01 01:16:23	\N
122	60	1264	2830	2019-06-01 13:50:22	2019-06-01 13:50:22	\N
123	60	1265	1960000	2019-06-01 13:50:24	2019-06-01 13:50:24	\N
124	60	1266	1710000	2019-06-01 13:50:25	2019-06-01 13:50:25	\N
125	60	1267	7900	2019-06-01 13:50:26	2019-06-01 13:50:26	\N
126	60	1268	270970	2019-06-01 13:50:27	2019-06-01 13:50:27	\N
115	60	1250	13750	2019-06-01 01:16:15	2019-06-01 13:50:28	\N
127	60	1269	16500	2019-06-01 13:50:30	2019-06-01 13:50:30	\N
128	60	1270	16500	2019-06-01 13:50:31	2019-06-01 13:50:31	\N
129	60	1272	3250	2019-06-01 13:50:34	2019-06-01 13:50:34	\N
130	60	443	180000	2019-06-01 14:14:40	2019-06-01 14:14:40	\N
131	60	444	325000	2019-06-01 14:14:42	2019-06-01 14:14:42	\N
132	60	445	4000	2019-06-01 14:14:43	2019-06-01 14:14:43	\N
133	60	1215	109810	2019-06-01 14:14:44	2019-06-01 14:14:44	\N
134	60	447	85320	2019-06-01 14:14:45	2019-06-01 14:14:45	\N
135	60	1223	1350000	2019-06-01 14:14:47	2019-06-01 14:14:47	\N
136	60	449	2000	2019-06-01 14:14:48	2019-06-01 14:14:48	\N
137	60	450	13400	2019-06-01 14:14:49	2019-06-01 14:14:49	\N
138	60	451	4000	2019-06-01 14:14:50	2019-06-01 14:14:50	\N
139	60	1256	36000	2019-06-01 14:14:52	2019-06-01 14:14:52	\N
140	60	453	70000	2019-06-01 14:31:39	2019-06-01 14:31:39	\N
141	60	454	180000	2019-06-01 14:31:40	2019-06-01 14:31:40	\N
142	60	455	10060	2019-06-01 14:31:41	2019-06-01 14:31:41	\N
143	60	1274	300000	2019-06-01 14:31:44	2019-06-01 14:31:44	\N
144	60	1275	1500	2019-06-01 14:31:45	2019-06-01 14:31:45	\N
145	60	1276	12500	2019-06-01 14:31:46	2019-06-01 14:31:46	\N
146	60	1277	38750	2019-06-01 14:31:47	2019-06-01 14:31:47	\N
147	60	1278	10625	2019-06-01 14:31:49	2019-06-01 14:31:49	\N
116	60	1252	2000	2019-06-01 01:16:16	2019-06-02 12:59:59	\N
67	60	1237	800	2019-05-31 23:22:47	2019-06-02 13:00:02	\N
63	60	1231	59	2019-05-31 23:22:41	2019-06-10 12:51:40	\N
114	60	1248	13334	2019-06-01 01:16:13	2019-06-10 12:51:43	\N
60	7	1228	4500	2019-05-31 23:22:37	2019-06-13 08:37:10	\N
34	9	1249	13889	2019-05-30 14:44:15	2019-06-13 14:59:29	\N
148	60	1279	291500	2019-06-01 14:31:50	2019-06-01 14:31:50	\N
149	60	1280	227600	2019-06-01 14:45:37	2019-06-01 14:45:37	\N
151	60	1282	8400	2019-06-01 14:45:40	2019-06-01 14:45:40	\N
152	60	1283	17500	2019-06-01 14:45:42	2019-06-01 14:45:42	\N
155	60	1285	313	2019-06-01 14:45:45	2019-06-01 14:45:45	\N
156	60	1286	188	2019-06-01 14:45:47	2019-06-01 14:45:47	\N
157	60	1287	5500	2019-06-01 14:45:48	2019-06-01 14:45:48	\N
158	60	1139	7000	2019-06-01 14:45:49	2019-06-01 14:45:49	\N
159	60	1291	250000	2019-06-01 15:01:10	2019-06-01 15:01:10	\N
160	60	1199	2750	2019-06-01 15:01:12	2019-06-01 15:01:12	\N
161	60	1292	27500	2019-06-01 15:01:13	2019-06-01 15:01:13	\N
162	60	1293	34000	2019-06-01 15:01:14	2019-06-01 15:01:14	\N
163	60	1294	22400	2019-06-01 15:01:15	2019-06-01 15:01:15	\N
164	60	1295	28000	2019-06-01 15:01:17	2019-06-01 15:01:17	\N
167	60	1299	247500	2019-06-01 15:05:23	2019-06-01 15:05:23	\N
168	60	1188	21000	2019-06-01 15:05:24	2019-06-01 15:05:24	\N
169	60	1301	41000	2019-06-01 15:05:25	2019-06-01 15:05:25	\N
170	60	1128	11167	2019-06-01 21:31:40	2019-06-01 21:31:40	\N
171	60	1161	5500	2019-06-01 21:31:41	2019-06-01 21:31:41	\N
172	60	1129	556	2019-06-01 21:31:42	2019-06-01 21:31:42	\N
173	60	1130	174	2019-06-01 21:31:44	2019-06-01 21:31:44	\N
174	60	1131	1500	2019-06-01 21:31:47	2019-06-01 21:31:47	\N
177	60	1134	5000	2019-06-01 21:31:51	2019-06-01 21:31:51	\N
178	60	1310	10000	2019-06-02 08:58:31	2019-06-02 08:58:31	\N
179	60	1181	22917	2019-06-02 08:58:32	2019-06-02 08:58:32	\N
180	60	1309	22917	2019-06-02 08:58:33	2019-06-02 08:58:33	\N
182	60	1121	1388	2019-06-02 09:10:07	2019-06-02 09:10:07	\N
183	60	1304	8500	2019-06-02 09:10:08	2019-06-02 09:10:08	\N
184	60	1302	47000	2019-06-02 09:10:09	2019-06-02 09:10:09	\N
186	60	419	1038	2019-06-02 09:41:53	2019-06-02 09:41:53	\N
187	60	1135	8784	2019-06-02 12:47:57	2019-06-02 12:47:57	\N
188	60	441	15000	2019-06-02 12:48:01	2019-06-02 12:48:01	\N
189	60	1137	11250	2019-06-02 12:48:02	2019-06-02 12:48:02	\N
190	60	440	1388	2019-06-02 12:48:04	2019-06-02 12:48:04	\N
191	60	1140	12500	2019-06-02 12:48:07	2019-06-02 12:48:07	\N
192	60	1141	8000	2019-06-02 12:48:09	2019-06-02 12:48:09	\N
154	60	1192	3000	2019-06-01 14:45:44	2019-06-02 12:59:57	\N
194	60	1144	19000	2019-06-02 13:00:03	2019-06-02 13:00:03	\N
195	60	1145	9500	2019-06-02 13:00:06	2019-06-02 13:00:06	\N
196	60	1146	13000	2019-06-02 13:00:07	2019-06-02 13:00:07	\N
197	60	1147	10500	2019-06-02 13:00:08	2019-06-02 13:00:08	\N
198	60	1148	4500	2019-06-02 13:00:09	2019-06-02 13:00:09	\N
199	60	1149	875	2019-06-02 13:22:30	2019-06-02 13:22:30	\N
200	60	1150	1750	2019-06-02 13:22:32	2019-06-02 13:22:32	\N
201	60	1151	1750	2019-06-02 13:22:33	2019-06-02 13:22:33	\N
202	60	1152	1750	2019-06-02 13:22:34	2019-06-02 13:22:34	\N
204	60	1154	11083	2019-06-02 13:22:37	2019-06-02 13:22:37	\N
205	60	1155	4650	2019-06-02 13:22:38	2019-06-02 13:22:38	\N
206	60	1341	300	2019-06-02 13:22:39	2019-06-02 13:22:39	\N
207	60	1158	1000	2019-06-02 13:22:41	2019-06-02 13:22:41	\N
175	60	1132	8271	2019-06-01 21:31:49	2019-06-10 12:15:04	\N
193	60	1143	1375	2019-06-02 13:00:00	2019-06-10 12:33:18	\N
208	60	1160	575000	2019-06-10 12:51:36	2019-06-10 12:51:36	\N
209	60	1163	2125	2019-06-10 12:51:39	2019-06-10 12:51:39	\N
210	60	1164	11500	2019-06-10 12:51:44	2019-06-10 12:51:44	\N
212	60	1326	5000	2019-06-10 13:11:19	2019-06-10 13:11:19	\N
213	60	1166	600	2019-06-10 13:11:20	2019-06-10 13:11:20	\N
214	60	1167	17000	2019-06-10 13:11:21	2019-06-10 13:11:21	\N
89	60	1103	2125	2019-06-01 00:24:12	2019-06-10 13:11:24	\N
215	60	1170	15000	2019-06-10 13:11:27	2019-06-10 13:11:27	\N
216	60	1172	3000	2019-06-10 13:11:30	2019-06-10 13:11:30	\N
217	60	466	7500	2019-06-10 13:16:06	2019-06-10 13:16:06	\N
219	7	456	9000	2019-06-12 16:21:43	2019-06-12 16:21:43	\N
221	7	459	82500	2019-06-12 16:21:47	2019-06-12 16:21:47	\N
222	7	458	47000	2019-06-12 16:21:48	2019-06-12 16:21:48	\N
223	18	1171	2300	2019-06-12 20:09:26	2019-06-12 20:09:26	\N
224	18	423	5000	2019-06-12 20:09:27	2019-06-12 20:09:27	\N
226	16	1109	1038	2019-06-12 20:31:49	2019-06-12 20:31:49	\N
227	7	1262	2830	2019-06-13 08:37:19	2019-06-13 08:37:19	\N
228	7	460	2830	2019-06-13 08:37:21	2019-06-13 08:37:21	\N
229	7	465	24000	2019-06-13 08:37:31	2019-06-13 08:37:31	\N
230	7	463	2600	2019-06-13 08:37:32	2019-06-13 08:37:32	\N
231	7	464	12000	2019-06-13 08:37:33	2019-06-13 08:37:33	\N
232	7	1213	22000	2019-06-13 08:37:34	2019-06-13 08:37:34	\N
233	7	1258	45000	2019-06-13 08:37:36	2019-06-13 08:37:36	\N
236	9	467	1500	2019-06-13 14:51:56	2019-06-13 14:51:56	\N
237	9	1348	4000	2019-06-13 14:52:01	2019-06-13 14:52:01	\N
238	9	1344	6000	2019-06-13 14:52:02	2019-06-13 14:52:02	\N
235	9	1251	13000	2019-06-13 14:51:54	2019-06-13 14:59:22	\N
150	9	1281	5400	2019-06-01 14:45:39	2019-06-13 14:59:23	\N
239	9	468	28000	2019-06-13 15:09:30	2019-06-13 15:09:30	\N
240	9	1178	58000	2019-06-13 15:09:33	2019-06-13 15:09:33	\N
241	6	1111	3500	2019-06-17 21:25:16	2019-06-17 21:25:16	\N
220	6	457	19800	2019-06-12 16:21:45	2019-06-17 21:25:17	\N
242	6	1047	15000	2019-06-17 21:45:09	2019-06-17 21:45:09	\N
153	4	1284	5000	2019-06-01 14:45:43	2019-06-17 21:59:09	\N
243	4	422	10500	2019-06-24 12:02:10	2019-06-24 12:02:10	\N
244	6	1187	1200	2019-06-24 15:28:53	2019-06-24 15:28:53	\N
245	6	1093	1200	2019-06-24 15:28:57	2019-06-24 15:28:57	\N
246	4	1104	12000	2019-07-01 15:12:30	2019-07-01 15:12:30	\N
247	8	1174	20000	2019-07-24 08:58:07	2019-07-24 08:58:07	\N
185	8	1168	1000	2019-06-02 09:10:10	2019-07-24 09:07:53	\N
45	4	427	2000	2019-05-30 16:36:30	2019-07-24 17:42:27	\N
203	4	1153	2000	2019-06-02 13:22:35	2019-07-24 17:42:28	\N
181	4	1169	2000	2019-06-02 08:58:36	2019-07-24 17:42:28	\N
211	4	1165	2000	2019-06-10 12:51:45	2019-07-24 17:42:29	\N
38	4	1175	2000	2019-05-30 15:25:42	2019-07-24 17:42:29	\N
234	60	1345	8.00	2019-06-13 10:21:54	2019-09-30 14:30:12	\N
248	60	434	80.00	2019-09-30 14:32:08	2019-09-30 14:32:08	\N
166	60	1298	6.5	2019-06-01 15:01:22	2019-09-30 14:42:38	\N
249	60	430	7.7	2019-09-30 14:44:40	2019-09-30 14:44:40	\N
250	6	432	325.75	2019-10-07 09:03:39	2019-10-07 09:03:39	\N
251	4	436	900	2020-05-07 20:51:41	2020-05-07 20:51:41	\N
225	4	435	900	2019-06-12 20:28:01	2020-05-13 21:46:39	\N
165	4	1296	9000	2019-06-01 15:01:19	2020-05-14 10:21:24	\N
176	4	1351	900	2019-06-01 21:31:50	2020-05-14 10:29:19	\N
218	4	1227	11000	2019-06-12 16:21:42	2020-05-16 09:43:37	\N
252	4	439	1000	2020-05-16 10:26:23	2020-05-16 10:26:23	\N
\.


--
-- TOC entry 3672 (class 0 OID 16517)
-- Dependencies: 232
-- Data for Name: m_cities; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.m_cities (id, city, province, created_at, updated_at, deleted_at) FROM stdin;
1	Malang	\N	2019-03-25 20:59:36	2019-03-25 20:59:38	\N
2	Singosari	\N	2019-03-25 20:59:36	2019-03-25 20:59:38	\N
3	Lumajang	\N	2019-03-25 20:59:36	2019-03-25 20:59:38	\N
\.


--
-- TOC entry 3674 (class 0 OID 16522)
-- Dependencies: 234
-- Data for Name: m_doc_types; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.m_doc_types (id, type, code, name, created_at, updated_at, deleted_at, is_mandatory) FROM stdin;
50	doc_kpr	doc_kpr_1	FC KTP PEMOHON SUAMI ISTRI	\N	\N	\N	t
51	doc_kpr	doc_kpr_2	FC SURAT NIKAH / SURAT CERAI	\N	\N	\N	t
52	doc_kpr	doc_kpr_3	FC KARTU KELUARGA	\N	\N	\N	t
53	doc_kpr	doc_kpr_4	FC BUKU TABUNGAN 3 BULAN TERAKHIR	\N	\N	\N	t
54	doc_kpr	doc_kpr_5	FC NPWP	\N	\N	\N	t
55	doc_kpr	doc_kpr_6	SLIP GAJI / SURAT KETERANGN PENGHASILAN	\N	\N	\N	t
56	doc_kpr	doc_kpr_7	FC SK PENGANGKATAN PEGAWAI	\N	\N	\N	t
57	doc_kpr	doc_kpr_8	SPT PAJAK PENGHASILAN 1 TAHUN TERKAHIR	\N	\N	\N	t
58	doc_kpr	doc_kpr_9	FC IJIN PRAKTEK PROFESI	\N	\N	\N	t
59	doc_kpr	doc_kpr_10	FC DOKUMEN KEPEMILIKAN ( SHM / SHGB, PBB dan IMB )	\N	\N	\N	t
60	doc_kpr	doc_kpr_11	LAPORAN KEUANGAN	\N	\N	\N	t
61	doc_ajb	doc_ajb_1	DP LUNAS	\N	\N	\N	t
62	doc_ajb	doc_ajb_2	SP3K BANK	\N	\N	\N	t
63	doc_ajb	doc_ajb_3	VALIDASI PAJAK	\N	\N	\N	t
64	doc_ajb	doc_ajb_4	FC KTP	\N	\N	\N	t
65	doc_ajb	doc_ajb_5	FC KK	\N	\N	\N	t
66	doc_ajb	doc_ajb_6	FC SURAT NIKAH	\N	\N	\N	t
67	doc_ajb	doc_ajb_7	FC NPWP	\N	\N	\N	t
68	doc_ajb	doc_ajb_8	FC PBB	\N	\N	\N	t
69	doc_ajb	doc_ajb_9	FC SERTIFIKAT	\N	\N	\N	t
70	doc_ajb	doc_ajb_10	FC IMB	\N	\N	\N	t
71	doc_ajb	doc_ajb_11	FORMULIR PENDAFTARAN VALIDASI DARI NOTARIS	\N	\N	\N	t
\.


--
-- TOC entry 3676 (class 0 OID 16527)
-- Dependencies: 236
-- Data for Name: m_employees; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.m_employees (id, name, division, role, "position", created_at, updated_at, deleted_at, id_user, position_id, telp, site_id, email, address) FROM stdin;
1	Wijaya Sales	Marketing	Marketing	Sales	\N	\N	\N	1	2	\N	1	\N	\N
2	Supri	Marketing	Marketing Officer	Staff	2019-03-28 05:37:47	2019-03-28 05:37:49	\N	1	2	\N	1	\N	\N
3	Marwoto	Marketing	Marketing Officer	Supervisor	2019-03-28 05:37:47	2019-03-28 05:37:49	\N	1	2	\N	1	\N	\N
4	Anjani	Marketing	Marketing Officer	Staff	2019-03-28 05:37:47	2019-03-28 05:37:49	\N	1	2	\N	1	\N	\N
5	Anjay	Marketing	Marketing Officer	Staff	2019-03-28 05:37:47	2019-03-28 05:37:49	\N	1	2	\N	1	\N	\N
6	jafar	Marketing	Marketing Officer	Sales	2019-08-06 01:32:16	2019-08-06 01:32:43	\N	1	2	08893948598	1	jafaraha18@gmail.com	jember
\.


--
-- TOC entry 3783 (class 0 OID 59158)
-- Dependencies: 343
-- Data for Name: m_item_prices; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.m_item_prices (id, m_item_id, amount, price, created_at, updated_at, deleted_at, site_id, m_unit_id) FROM stdin;
5	439	20	1500	2020-05-16 11:28:03	2020-05-16 11:29:08	\N	1	17
\.


--
-- TOC entry 3678 (class 0 OID 16535)
-- Dependencies: 238
-- Data for Name: m_items; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.m_items (id, name, category, volume, late_time, m_unit_id, created_at, updated_at, deleted_at, type, no, status) FROM stdin;
1250	Kawat-Beton---	MATERIAL	0.0000	3	20	\N	\N	\N	1	0608150000	Active
1023	Bata-Ringan-10x20x60-Fastcon-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301010301	Active
1024	Bata-Ringan-10x20x60-Fastcon-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301010302	Active
1025	Bata-Ringan-10x20x60-priority one-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301010401	Active
1026	Bata-Ringan-10x20x60-priority one-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301010402	Active
1027	Bata-Ringan-10x20x60-VALCON-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301010501	Active
1028	Bata-Ringan-10x20x60-VALCON-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301010502	Active
1029	Bata-Ringan-10x20x50-Fastcon-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301020301	Active
1030	Bata-Ringan-10x20x50-Fastcon-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301020302	Active
1031	Bata-Ringan-10x20x50-priority one-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301020401	Active
1032	Bata-Ringan-10x20x50-priority one-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301020402	Active
1033	Bata-Ringan-10x20x50-VALCON-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301020501	Active
1034	Bata-Ringan-10x20x50-VALCON-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301020502	Active
1035	Bata-Ringan-10x20x40-Fastcon-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301030301	Active
1036	Bata-Ringan-10x20x40-Fastcon-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301030302	Active
1037	Bata-Ringan-10x20x40-priority one-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301030401	Active
1038	Bata-Ringan-10x20x40-priority one-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301030402	Active
1039	Bata-Ringan-10x20x40-VALCON-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301030501	Active
1040	Bata-Ringan-10x20x40-VALCON-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301030502	Active
1041	Bata-Ringan-10x20x30-Fastcon-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301040301	Active
1042	Bata-Ringan-10x20x30-Fastcon-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301040302	Active
1043	Bata-Ringan-10x20x30-priority one-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301040401	Active
1044	Bata-Ringan-10x20x30-priority one-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301040402	Active
1045	Bata-Ringan-10x20x30-VALCON-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301040501	Active
1046	Bata-Ringan-10x20x30-VALCON-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301040502	Active
1047	Bata-Ringan-10x20x25-VALCON-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301050501	Active
1048	Bata-Ringan-10x20x25-VALCON-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301050502	Active
1049	Bata-Ringan-10x20x25-Fastcon-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301050301	Active
1050	Bata-Ringan-10x20x25-Fastcon-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301050302	Active
1051	Bata-Ringan-10x20x25-priority one-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301050401	Active
1052	Bata-Ringan-10x20x25-priority one-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301050402	Active
1053	Bata-Ringan-7,5x20x60-Fastcon-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301060301	Active
1054	Bata-Ringan-7,5x20x60-Fastcon-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301060302	Active
1055	Bata-Ringan-7,5x20x60-priority one-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301060401	Active
1056	Bata-Ringan-7,5x20x60-priority one-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301060402	Active
1057	Bata-Ringan-7,5x20x60-VALCON-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301060501	Active
1058	Bata-Ringan-7,5x20x60-VALCON-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301060502	Active
1059	Bata-Ringan-7,5x20x50-Fastcon-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301070301	Active
1060	Bata-Ringan-7,5x20x50-Fastcon-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301070302	Active
1061	Bata-Ringan-7,5x20x50-priority one-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301070401	Active
1062	Bata-Ringan-7,5x20x50-priority one-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301070402	Active
1063	Bata-Ringan-7,5x20x50-VALCON-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301070501	Active
1064	Bata-Ringan-7,5x20x50-VALCON-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301070502	Active
1065	Bata-Ringan-7,5x20x40-Fastcon-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301080301	Active
1066	Bata-Ringan-7,5x20x40-Fastcon-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301080302	Active
1067	Bata-Ringan-7,5x20x40-priority one-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301080401	Active
1068	Bata-Ringan-7,5x20x40-priority one-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301080402	Active
1069	Bata-Ringan-7,5x20x40-VALCON-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301080501	Active
1070	Bata-Ringan-7,5x20x40-VALCON-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301080502	Active
1071	Bata-Ringan-7,5x20x30-Fastcon-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301090301	Active
1072	Bata-Ringan-7,5x20x30-Fastcon-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301090302	Active
1073	Bata-Ringan-7,5x20x30-priority one-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301090401	Active
1074	Bata-Ringan-7,5x20x30-priority one-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301090402	Active
1075	Bata-Ringan-7,5x20x30-VALCON-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301090501	Active
1076	Bata-Ringan-7,5x20x30-VALCON-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301090502	Active
1077	Bata-Ringan-7,5x20x25-Fastcon-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301100301	Active
1078	Bata-Ringan-7,5x20x25-Fastcon-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301100302	Active
1079	Bata-Ringan-7,5x20x25-priority one-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301100401	Active
1080	Bata-Ringan-7,5x20x25-priority one-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301100402	Active
1081	Bata-Ringan-7,5x20x25-VALCON-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301100501	Active
1082	Bata-Ringan-7,5x20x25-VALCON-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301100502	Active
1083	Bata-Merah-5x10,5x23-Blabak-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0302010101	Active
1084	Bata-Merah-5x10,5x23-Blabak-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0302010102	Active
1085	Bata-Merah-5x10,5x23-Blabak-KW 3	MATERIAL	0.0000	3	17	\N	\N	\N	1	0302010103	Active
1086	Bata-Merah-5x10,5x23-Waru Jayeng-KW 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0302010201	Active
1087	Bata-Merah-5x10,5x23-Waru Jayeng-KW 2	MATERIAL	0.0000	3	17	\N	\N	\N	1	0302010202	Active
1088	Bata-Merah-5x10,5x23-Waru Jayeng-KW 3	MATERIAL	0.0000	3	17	\N	\N	\N	1	0302010203	Active
1089	Semen-PC-Gresik-Curah	MATERIAL	0.0000	3	17	\N	\N	\N	1	0401010101	Active
1090	Semen-PC-Gresik-40 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401010102	Active
1091	Semen-PC-Gresik-50 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401010103	Active
1092	Semen-PC-Holcim-Curah	MATERIAL	0.0000	3	17	\N	\N	\N	1	0401010201	Active
1093	Semen-PC-Holcim-40 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401010202	Active
1094	Semen-PC-Holcim-50 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401010203	Active
1095	Semen-PC-Merah Putih-Curah	MATERIAL	0.0000	3	17	\N	\N	\N	1	0401010301	Active
1096	Semen-PC-Merah Putih-40 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401010302	Active
1097	Semen-PC-Merah Putih-50 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401010303	Active
1098	Semen-PC-CONCH-Curah	MATERIAL	0.0000	3	17	\N	\N	\N	1	0401010401	Active
1099	Semen-PC-CONCH-50 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401010403	Active
1100	Semen-PC-BIMA-Curah	MATERIAL	0.0000	3	17	\N	\N	\N	1	0401010501	Active
1101	Semen-PC-BIMA-40 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401010502	Active
1102	Semen-PC-BIMA-50 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401010503	Active
1103	Semen-Semen putih-Gresik-40 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401020102	Active
1104	Semen-Semen putih-Gresik-50 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401020103	Active
1105	Semen-Semen putih-Holcim-40 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401020202	Active
1106	Semen-Semen putih-Holcim-50 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401020203	Active
1107	Semen-Semen putih-Merah Putih-40 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401020302	Active
1108	Semen-Semen putih-Merah Putih-50 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401020303	Active
1109	Semen-Semen putih-CONCH-40 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401020402	Active
1110	Semen-Semen putih-CONCH-50 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401020403	Active
1111	Semen-Semen putih-BIMA-40 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401020502	Active
1112	Semen-Semen putih-BIMA-50 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0401020503	Active
1113	Mortar-Bata ringan-HOLCIM-40 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0402010102	Active
1114	Mortar-Bata ringan-HOLCIM-5 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0402010104	Active
1115	Mortar-Bata ringan-Blesscon-40 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0402010202	Active
1116	Mortar-Bata ringan-Blesscon-5 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0402010204	Active
1117	Mortar-Bata ringan-MU-40 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0402010302	Active
1118	Mortar-Bata ringan-MU-5 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0402010304	Active
1119	Mortar-Bata ringan-Sika-40 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0402010402	Active
1120	Mortar-Bata ringan-Sika-5 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0402010404	Active
1121	Mortar-Bata ringan-Dry mortar-40 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0402010502	Active
1122	Mortar-Bata ringan-Dry mortar-5 kg	MATERIAL	0.0000	3	20	\N	\N	\N	1	0402010504	Active
1123	Bambu-Ori-baru-panjang	MATERIAL	0.0000	3	14	\N	\N	\N	1	0507010101	Active
1124	Bambu-Umbul2-baru-panjang	MATERIAL	0.0000	3	14	\N	\N	\N	1	0507030101	Active
1125	Bambu-Umbul2-baru-pendek	MATERIAL	0.0000	3	14	\N	\N	\N	1	0507030102	Active
1126	Bambu-Umbul2-bekas-panjang	MATERIAL	0.0000	3	14	\N	\N	\N	1	0507030201	Active
1127	Bambu-Umbul2-bekas-pendek	MATERIAL	0.0000	3	14	\N	\N	\N	1	0507030202	Active
1128	alumunium-baja ringan-canal c-kencana-	MATERIAL	0.0000	3	15	\N	\N	\N	1	0701010100	Active
1129	kayu-multiplex-5/7-1 meter	MATERIAL	0.0000	3	16	\N	\N	\N	1	0506040001	Active
1130	board-gypsum-gyproc-120x240=8mm	MATERIAL	0.0000	3	16	\N	\N	\N	1	0901000603	Active
1131	perekat-mortar-bata ringan	MATERIAL	0.0000	3	20	\N	\N	\N	1	0402010000	Active
1132	besi-betonpolos-besi6--kw 1	MATERIAL	0.0000	3	20	\N	\N	\N	1	0602030003	Active
1133	bata-ringan-7,5x20x60-blescon-kw 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301060201	Active
1134	kayu-kaso-4/6-glugu-4 meter	MATERIAL	0.0000	3	15	\N	\N	\N	1	0502030504	Active
1135	besi-betonpolos-besi 10--Ø 1.5 inch t=1.2 mm	MATERIAL	0.0000	3	20	\N	\N	\N	1	0602030005	Active
1136	plumbing-pipa-pvc c-mpoint-4 inchi	MATERIAL	0.0000	3	15	\N	\N	\N	1	1301030506	Active
1137	plumbing-pipa-pvc d-maspion-2,5 inch inchi	MATERIAL	0.0000	3	15	\N	\N	\N	1	1301020108	Active
1139	plumbing-t joint-pvc aw--2,5 inchi	MATERIAL	0.0000	3	14	\N	\N	\N	1	1303010408	Active
1140	plumbing-knee-pvc aw-rucika-4 inch	MATERIAL	0.0000	3	14	\N	\N	\N	1	1302010405	Active
1141	plumbing-pipa-pvc aw-maspion-3/4 inchi	MATERIAL	0.0000	3	15	\N	\N	\N	1	1301010102	Active
1142	besi-talang-galvalum--L=60, t=0.3mm	MATERIAL	0.0000	3	15	\N	\N	\N	1	0612090012	Active
1143	electrical-pipapvc---5/8	MATERIAL	0.0000	3	15	\N	\N	\N	1	1009000014	Active
1148	electrical-fitting lampu-dexta--	MATERIAL	0.0000	3	14	\N	\N	\N	1	1009001400	Active
1153	alumunium-baja ringan-canal c-garuda-75x70	MATERIAL	0.0000	3	15	\N	\N	\N	1	0701010723	Active
1154	alumunium-baja ringan-canal c-garuda-75x60	MATERIAL	0.0000	3	15	\N	\N	\N	1	0701010724	Active
1155	alumunium-baja ringan-reng-garuda-40x30	MATERIAL	0.0000	3	15	\N	\N	\N	1	0701020726	Active
1156	bata-ringan-7,5x20x30-blescon-kw 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301090202	Active
1157	bata-ringan-7,5x20x40-blescon-kw 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301080202	Active
1158	besi-paku-beton-alexindo	MATERIAL	0.0000	3	14	\N	\N	\N	1	0610150041	Active
1159	perekat-sanding-kalsium biasa-kuda mas--	MATERIAL	0.0000	3	20	\N	\N	\N	1	0403010300	Active
1160	bata-ringan-7,5x20x60-priority one-kw 1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0312060401	Active
1161	alumunium- hollow-ht-4x4	MATERIAL	0.0000	3	15	\N	\N	\N	1	0702000828	Active
1162	alumunium- hollow-ht-4x4	MATERIAL	0.0000	3	15	\N	\N	\N	1	0702000827	Active
1163	alumunium-wall angel-kencana-2x2	MATERIAL	0.0000	3	15	\N	\N	\N	1	0703000029	Active
1165	Alat bantu cat-kuas-Eterna-	MATERIAL	0.0000	3	14	\N	\N	\N	1	0102010100	Active
1166	besi-skrup-drilling--5 inch	MATERIAL	0.0000	3	14	\N	\N	\N	1	0611170032	Active
1150	kabel-nya kuning-extrana-1x1,5	MATERIAL	0.0000	3	15	\N	2019-05-31 21:47:53	\N	1	1001030201	Active
1152	-kabel-nya biru-extrana-1x1,5	MATERIAL	0.0000	3	15	\N	2019-05-31 21:45:26	\N	1	1001020201	Active
1151	kabel-nya hitam-extrana-1x1,5	MATERIAL	0.0000	3	15	\N	2019-05-31 21:47:32	\N	1	1001040201	Active
1164	-box mcb--dexta-2 grup	MATERIAL	0.0000	3	14	\N	2019-05-31 21:46:33	\N	1	1003001408	Active
1144	-mcb--matsuka-10ampere	MATERIAL	0.0000	3	14	\N	2019-05-31 21:46:40	\N	1	1004001207	Active
1146	saklar-seri-broco	MATERIAL	0.0000	3	14	\N	2019-05-31 21:48:49	\N	1	1007070500	Active
1149	kabel-nym-pulung-3x4	MATERIAL	0.0000	3	15	\N	2019-05-31 21:47:28	\N	1	1001051505	Active
1145	saklar-engkel-broco	MATERIAL	0.0000	3	14	\N	2019-05-31 21:48:55	\N	1	1007060500	Active
1147	stop kontak--broco	MATERIAL	0.0000	3	14	\N	2019-05-31 21:49:00	\N	1	1008000500	Active
1167	besi-paku--1,5 inchi	MATERIAL	0.0000	3	20	\N	\N	\N	1	0610000042	Active
1169	Alat bantu cat-sliper-dll	MATERIAL	0.0000	3	14	\N	\N	\N	1	0102070200	Active
1170	plumbing-keni drat kddk-pvcaw-rucika-3/4 - 1/2 "	MATERIAL	0.0000	3	14	\N	\N	\N	1	1305010411	Active
1171	penutup atap-genteng-betongaruda--120x240 t=6mm	MATERIAL	0.0000	3	14	\N	\N	\N	1	0801020002	Active
1172	plumbing-t joint-pvc aw--3/4	MATERIAL	0.0000	3	14	\N	\N	\N	1	1303010011	Active
1173	alumunium- hollow-kencana-4x4	MATERIAL	0.0000	3	19	\N	\N	\N	1	0702000128	Active
1174	alumunium- hollow-kencana-2x4	MATERIAL	0.0000	3	19	\N	\N	\N	1	0702000127	Active
1175	alumunium-wall angel-kencana-2x2	MATERIAL	0.0000	3	19	\N	\N	\N	1	0703000100	Active
1176	besi-skrup-6x2	MATERIAL	0.0000	3	14	\N	\N	\N	1	0611000044	Active
1177	besi-skrup-6x1	MATERIAL	0.0000	3	14	\N	\N	\N	1	0611000043	Active
1178	board-gypsum-knauf	MATERIAL	0.0000	3	22	\N	\N	\N	1	0901000100	Active
1179	perekat-sanding-compount/cornis	MATERIAL	0.0000	3	20	\N	\N	\N	1	0403030000	Active
1180	palatbantu kerja-alat bantu cat-perban gypsum-dll	MATERIAL	0.0000	3	18	\N	\N	\N	1	0102080200	Active
1181	perekat-lem-sealent-grh-sylicon white	MATERIAL	0.0000	3	20	\N	\N	\N	1	0404050304	Active
1182	perekat-sanding-kalsium biasa	MATERIAL	0.0000	3	20	\N	\N	\N	1	0403010000	Active
1183	perekat-sanding-kalsium dsgm-	MATERIAL	0.0000	3	20	\N	\N	\N	1	0403020000	Active
1184	perekat-lem-kayu-rajawali	MATERIAL	0.0000	3	20	\N	\N	\N	1	0404040100	Active
1185	Amplas-Roll 100 m-dll-Gradasi sedang 100	MATERIAL	0.0000	3	15	\N	\N	\N	1	0101010202	Active
1186	Alat bantu cat-kuas-Eterna-4 inch	MATERIAL	0.0000	3	14	\N	\N	\N	1	0102010108	Active
1187	Alat bantu cat-Kapi-Dll-	MATERIAL	0.0000	3	14	\N	\N	\N	1	0102030200	Active
1188	Alat bantu cat-sliper-dll	MATERIAL	0.0000	3	14	\N	\N	\N	1	0102040200	Active
1189	plumbing-pipa-pvc aw-wafin-3/4 inchi	MATERIAL	0.0000	3	19	\N	\N	\N	1	1301010211	Active
1190	plumbing-keni drat kddk-pvcaw-rucika	MATERIAL	0.0000	3	14	\N	\N	\N	1	1305010400	Active
1191	plumbing-t joint-pvc aw-rucika-3/4	MATERIAL	0.0000	3	14	\N	\N	\N	1	1303010402	Active
1192	plumbing-shok joint-pvcaw-rucika-3/4	MATERIAL	0.0000	3	14	\N	\N	\N	1	1304010402	Active
1193	plumbing-t joint-pvc aw--2,5 inchi	MATERIAL	0.0000	3	14	\N	\N	\N	1	1303010004	Active
1194	plumbing-knee-pvc aw--4 inch	MATERIAL	0.0000	3	14	\N	\N	\N	1	1302010006	Active
1195	plumbing-tutup-pvc aw-rucika-3/4 inch	MATERIAL	0.0000	3	14	\N	\N	\N	1	1313010402	Active
1196	sanitary-closed-jongkok-ina-fawi50	MATERIAL	0.0000	3	14	\N	\N	\N	1	1401030204	Active
1197	sanitary-kran air	MATERIAL	0.0000	3	14	\N	\N	\N	1	1403000000	Active
1198	perekat-lem-pvc-isarplas	MATERIAL	0.0000	3	21	\N	\N	\N	1	0404030200	Active
1199	plumbing-TBA	MATERIAL	0.0000	3	14	\N	\N	\N	1	1306000000	Active
1200	Shower-Dinding--ae d5mz	MATERIAL	0.0000	3	14	\N	\N	\N	1	1404100001	Active
1201	Shower---ae fshic	MATERIAL	0.0000	3	14	\N	\N	\N	1	1404000002	Active
1202	Kran Air---ae s5l nz	MATERIAL	0.0000	3	14	\N	\N	\N	1	1403000003	Active
1203	Arde---17mm	MATERIAL	0.0000	3	14	\N	\N	\N	1	1002000015	Active
1204	Bambu-Apus-baru-panjang	MATERIAL	0.0000	3	15	\N	\N	\N	1	0507020101	Active
1205	Bata Ringan-10x20x60-Blesscon-Kw1	MATERIAL	0.0000	3	17	\N	\N	\N	1	0301010201	Active
1206	Batu Hias-Paras-Rata mesin-20x40	MATERIAL	0.0000	3	16	\N	\N	\N	1	0211020402	Active
1207	Batu Hias-Paras-Rata mesin-15x30	MATERIAL	0.0000	3	16	\N	\N	\N	1	0211020401	Active
1208	Jet Shower-Jet Shower-Toto-	MATERIAL	0.0000	3	14	\N	\N	\N	1	1405121100	Active
1209	Begel jadi-begel8x12--KW 2 (0.3-0.5)	MATERIAL	0.0000	3	20	\N	\N	\N	1	0609120004	Active
1210	Begel jadi-begel8x15--KW 2 (0.3-0.5)	MATERIAL	0.0000	3	20	\N	\N	\N	1	0609130004	Active
1211	benang-kenur-	MATERIAL	0.0000	3	18	\N	\N	\N	1	0105010000	Active
1212	Box MCB---2Grup	MATERIAL	0.0000	3	14	\N	\N	\N	1	1003000008	Active
1213	Box MCB---4Grup	MATERIAL	0.0000	3	14	\N	\N	\N	1	1003000016	Active
1214	bracket samping--wina--	MATERIAL	0.0000	3	14	\N	\N	\N	1	1613000400	Active
1215	kunci-pintu pvc-c 603x400	MATERIAL	0.0000	3	24	\N	\N	\N	1	1601040014	Active
1216	kunci-pintu pvc-c 601x300	MATERIAL	0.0000	3	24	\N	\N	\N	1	1601040015	Active
1217	cat-interior-1 kg- nitrolux 31-burnt timber 31 nitrolux	MATERIAL	0.0000	3	21	\N	\N	\N	1	1104040611	Active
1218	cat-exterior-galon- propan-DW 500 OW-3-2 graces smile base A	MATERIAL	0.0000	3	21	\N	\N	\N	1	1105010701	Active
1219	alat bantu kerja-sikat baja	MATERIAL	0.0000	3	14	\N	\N	\N	1	0109000000	Active
1221	cat-exterior-1 kg- propan-DW - 500 S 3010-Y30R base B	MATERIAL	0.0000	3	21	\N	\N	\N	1	1105040702	Active
1222	cat-exterior-pail- propan-Ultraproof 960 Base A	MATERIAL	0.0000	3	21	\N	\N	\N	1	1105020704	Active
1223	Closed-Duduk Monoblok-INA-fawi 50	MATERIAL	0.0000	3	24	\N	\N	\N	1	1401020204	Active
1228	Lampu Led-Downlight outbouw--Putih	MATERIAL	0.0000	3	14	\N	\N	\N	1	1013090010	Active
1229	Lampu Led-Downlight outbouw--Hitam	MATERIAL	0.0000	3	14	\N	\N	\N	1	1013090019	Active
1230	Lampu Led-Downlight inbouw-Philips	MATERIAL	0.0000	3	14	\N	\N	\N	1	1013081600	Active
1231	Sekrup-Drywall-6x1	MATERIAL	0.0000	3	14	\N	\N	\N	1	0611160043	Active
1232	Sekrup-Drywall-6x2	MATERIAL	0.0000	3	14	\N	\N	\N	1	0611160044	Active
1234	Sekrup-Drywall-1 X 1/2	MATERIAL	0.0000	3	14	\N	\N	\N	1	0611160047	Active
1239	Sekrup-Fisher--6mm	MATERIAL	0.0000	3	14	\N	\N	\N	1	0611220038	Active
1242	cat-exterior-pail-vinilex-990 S SB brilliant white	MATERIAL	0.0000	3	21	\N	2019-05-31 19:54:24	\N	1	1105021303	Active
1243	thiner-b	MATERIAL	0.0000	3	21	\N	2019-05-31 19:55:38	\N	1	1106060000	Active
1240	granit-60x60-serenity	MATERIAL	0.0000	3	16	\N	2019-05-31 19:59:19	\N	1	1201010500	Active
1224	clean out-pvc aw-rucika-2,5 inchi	MATERIAL	0.0000	3	14	\N	2019-05-31 20:23:52	\N	1	1307010404	Active
1226	kunci-pintu/jendela kayu-solid-dc 02 60	MATERIAL	0.0000	3	14	\N	2019-05-31 20:28:42	\N	1	1601010101	Active
1236	engsel-pintu/jendela kayu-5 inchi	MATERIAL	0.0000	3	14	\N	2019-05-31 21:27:36	\N	1	1602010007	Active
1241	grendel slot-pintu lipat-4 inchi	MATERIAL	0.0000	3	14	\N	2019-05-31 21:38:09	\N	1	1614030008	Active
1235	tutup kunci-pintu/jendela kayu-E 62 15 CHR+SN	MATERIAL	0.0000	3	14	\N	2019-05-31 21:41:12	\N	1	1615010003	Active
1168	Alat bantu cat-timba-dll-cor	MATERIAL	0.0000	3	14	\N	2019-06-02 09:07:26	\N	1	0102100210	Active
1244	Daun Pintu-HDF-ud budi-210x80	MATERIAL	0.0000	3	14	\N	\N	\N	1	0508020303	Active
1246	Isolasi--Nasional-	MATERIAL	0.0000	3	14	\N	\N	\N	1	1014001300	Active
1247	Shower-Shower--iv 89ts	MATERIAL	0.0000	3	14	\N	\N	\N	1	1404110005	Active
1248	Baja Ringan-Canal C-Kencana-2inchi	MATERIAL	0.0000	3	15	\N	\N	\N	1	0701010146	Active
1251	Baja Ringan-Canal C-Kencana-0,75	MATERIAL	0.0000	3	15	\N	\N	\N	1	0701010148	Active
1256	klem skundex--17mm	MATERIAL	0.0000	3	23	\N	\N	\N	1	1012000015	Active
1258	MCB--Sneider	MATERIAL	0.0000	3	14	\N	\N	\N	1	1004000400	Active
1260	Kaso---2Meter	MATERIAL	0.0000	3	19	\N	\N	\N	1	0502000002	Active
1261	Daun Pintu-double multiplek+Taco Seet-ud budi-	MATERIAL	0.0000	3	14	\N	\N	\N	1	0508040505	Active
1262	Kabel-NYA biru (100m)--1x2,5	MATERIAL	0.0000	3	15	\N	\N	\N	1	1001020002	Active
1263	Kabel-NYA Kuning (100m)--1x2,5	MATERIAL	0.0000	3	15	\N	\N	\N	1	1001030002	Active
1264	Kabel---1x2,5	MATERIAL	0.0000	3	15	\N	\N	\N	1	1001000002	Active
1265	Daun Pintu-Kayu Solid 3 panel + kaca-ud budi-	MATERIAL	0.0000	3	14	\N	\N	\N	1	0508010306	Active
1266	Daun Pintu-Kayu Solid 3 panel-ud budi-	MATERIAL	0.0000	3	14	\N	\N	\N	1	0508050306	Active
1269	Paku---5mm	MATERIAL	0.0000	3	20	\N	\N	\N	1	0610000032	Active
1270	Paku---7mm	MATERIAL	0.0000	3	20	\N	\N	\N	1	0610000031	Active
1271	Paku-Beton--7mm	MATERIAL	0.0000	3	14	\N	\N	\N	1	0610150031	Active
1272	Papan Cor--Randu-	MATERIAL	0.0000	3	15	\N	\N	\N	1	0505000602	Active
1273	Papan Cor--Randu-2meter	MATERIAL	0.0000	3	15	\N	\N	\N	1	0505000600	Active
1274	Pintu-PVC-Ivory-	MATERIAL	0.0000	3	14	\N	\N	\N	1	1408181200	Active
1281	Baja Ringan-Reng-Kencana-40x45	MATERIAL	0.0000	3	15	\N	\N	\N	1	0701020125	Active
1282	Beton Polos-Besi 10--KW 1 (00-0.2)	MATERIAL	0.0000	3	20	\N	\N	\N	1	0602050003	Active
1284	Afoor-Stainless-Sobar-sg f002	MATERIAL	0.0000	3	14	\N	\N	\N	1	1409171308	Active
1285	Sekrup---2inchi	MATERIAL	0.0000	3	14	\N	\N	\N	1	0611000046	Active
1286	Sekrup---1,5inchi	MATERIAL	0.0000	3	14	\N	\N	\N	1	0611000042	Active
1291	Kran Air-Dapur--T 819 NC	MATERIAL	0.0000	3	14	\N	\N	\N	1	1403130009	Active
1293	Multiplex-9mm--120 x 240 cm	MATERIAL	0.0000	3	16	\N	\N	\N	1	0506060001	Active
1297	Kaso-4/6--4Meter	MATERIAL	0.0000	3	15	\N	\N	\N	1	0502030004	Active
1301	Stop Kontak-pieno-sneider-pieno white	MATERIAL	0.0000	3	14	\N	\N	\N	1	1008110422	Active
1307	Kran Air-Dingin--DS TLX 020 C18	MATERIAL	0.0000	3	14	\N	\N	\N	1	1403100007	Active
1309	Lem-Sealant-Rajawali-GRH-acrilyc white	MATERIAL	0.0000	3	14	\N	\N	\N	1	0404050303	Active
1311	Tutup Kaca Mati-	MATERIAL	0.0000	3	15	\N	\N	\N	1	0714000000	Active
1312	Spigot-	MATERIAL	0.0000	3	15	\N	\N	\N	1	0707000000	Active
1313	Jip-	MATERIAL	0.0000	3	15	\N	\N	\N	1	0712000000	Active
1314	Kaca Mati	MATERIAL	0.0000	3	15	\N	\N	\N	1	0705000000	Active
1315	Open Back	MATERIAL	0.0000	3	15	\N	\N	\N	1	0704000000	Active
1317	Hollow-White--1x1/2	MATERIAL	0.0000	3	15	\N	\N	\N	1	0702050047	Active
1318	Tiang Sleding-White--4Inchi	MATERIAL	0.0000	3	15	\N	\N	\N	1	0706050033	Active
1319	Baja Ringan-Doorjam--4inchi	MATERIAL	0.0000	3	15	\N	\N	\N	1	0701030033	Active
1324	plumbing-knee-pvc aw--4 inch	MATERIAL	0.0000	3	14	\N	\N	\N	1	1302010009	Active
1325	plumbing-knee-pvc aw--1,5 inch	MATERIAL	0.0000	3	14	\N	\N	\N	1	1302010003	Active
1326	plumbing-knee-pvc aw--2,5 inch	MATERIAL	0.0000	3	14	\N	\N	\N	1	1302010004	Active
1327	plumbing-t joint-pvc aw--3 inchi	MATERIAL	0.0000	3	14	\N	\N	\N	1	1304010005	Active
1328	plumbing-t joint-pvc aw--4 inchi 	MATERIAL	0.0000	3	14	\N	\N	\N	1	1303010006	Active
1306	-timba-dll-cor	MATERIAL	0.0000	3	14	\N	2019-05-31 17:03:09	\N	1	0102070010	Active
1298	-pensil-makita	MATERIAL	0.0000	3	14	\N	2019-05-31 17:05:31	\N	1	0107000100	Active
1296	-kawat ayakan-1x1	MATERIAL	0.0000	3	16	\N	2019-05-31 17:07:11	\N	1	0108010001	Active
1287	genteng-stop ending-ilufa-15x50	MATERIAL	0.0000	3	14	\N	2019-05-31 19:34:58	\N	1	0801060402	Active
1305	kabel-NYM-eterna-3x4	MATERIAL	0.0000	3	15	\N	2019-05-31 19:36:04	\N	1	1001050105	Active
1304	fitting lampu-kotak-broco-	MATERIAL	0.0000	3	14	\N	2019-05-31 19:41:10	\N	1	1009120500	Active
1259	cst-exterior-5 kg-mowilex-ws-504 akasia	MATERIAL	0.0000	3	21	\N	2019-05-31 19:55:13	\N	1	1105030508	Active
1254	keramik-25x45-kia-white flag	MATERIAL	0.0000	3	16	\N	2019-05-31 20:03:26	\N	1	1202090201	Active
1255	keramik-25x40-kia-luxury white	MATERIAL	0.0000	3	16	\N	2019-05-31 20:04:16	\N	1	1202100202	Active
1322	keramik-20x60-roman--	MATERIAL	0.0000	3	16	\N	2019-05-31 20:04:58	\N	1	1202110100	Active
1283	pipa-pvc aw-3 inchi	MATERIAL	0.0000	3	15	\N	2019-05-31 20:13:32	\N	1	1301010005	Active
1278	pipa-pvc aw-wafin-1,5 inchi	MATERIAL	0.0000	3	15	\N	2019-05-31 20:16:22	\N	1	1301010203	Active
1277	pipa-pvc aw-wafin-4 inchi	MATERIAL	0.0000	3	15	\N	2019-05-31 20:17:02	\N	1	1301010206	Active
1290	knee-pvc aw-3 inchi	MATERIAL	0.0000	3	14	\N	2019-05-31 20:19:51	\N	1	1302010005	Active
1252	knee-pvc aw-rucika-3/4 inchi	MATERIAL	0.0000	3	14	\N	2019-05-31 20:20:27	\N	1	1302010402	Active
1289	T joint-pvc aw-3 inchi	MATERIAL	0.0000	3	14	\N	2019-05-31 20:22:59	\N	1	1303010005	Active
1292	tee move for jet shower-pvc aw-	MATERIAL	0.0000	3	14	\N	2019-05-31 20:24:18	\N	1	1308010000	Active
1308	engsel-pintu/jendela kayu-3 inchi	MATERIAL	0.0000	3	14	\N	2019-05-31 21:28:54	\N	1	1602010009	Active
1320	engdel-pintu/jendela kayu-ek 02	MATERIAL	0.0000	3	14	\N	2019-05-31 21:29:19	\N	1	1602010011	Active
1316	engsel-t engsel-p 6m	MATERIAL	0.0000	3	15	\N	2019-05-31 21:30:32	\N	1	1602050012	Active
1321	hak angin/sikutan-	MATERIAL	0.0000	3	14	\N	2019-05-31 21:31:30	\N	1	1604000000	Active
1268	handle-pintu/jendela kayu-p99 os	MATERIAL	0.0000	3	25	\N	2019-05-31 21:32:03	\N	1	1608010010	Active
1302	saklar-schneider-pieno white	MATERIAL	0.0000	3	14	\N	2019-06-01 15:14:19	2019-06-01 15:14:19	1	1007070422	Active
1279	RAT L-pintu/jendela alumunium	MATERIAL	0.0000	3	14	\N	2019-05-31 21:36:05	\N	1	1610020000	Active
1299	RAP L-pintu lipat-wina	MATERIAL	0.0000	3	14	\N	2019-05-31 21:36:38	\N	1	1611030400	Active
1329	grendel slot-pintu lipat-	MATERIAL	0.0000	3	14	\N	2019-05-31 21:37:40	\N	1	1614030000	Active
1257	lock case-pintu lipat-p 99 os	MATERIAL	0.0000	3	14	\N	2019-05-31 21:38:48	\N	1	1616030010	Active
1310	rambuncis-	MATERIAL	0.0000	3	14	\N	2019-05-31 21:51:23	\N	1	1609000000	Active
1330	Kran Air-Dapur-Silvra-	MATERIAL	0.0000	3	14	\N	\N	\N	1	1403131400	Active
1332	keramik-25x25-asia tile-zeus white	MATERIAL	0.0000	3	16	\N	\N	\N	1	1202120403	Active
1333	keramik-20x60--platinum--	MATERIAL	0.0000	3	16	\N	\N	\N	1	1202110300	Active
1334	keramik-20x60--platinum--	MATERIAL	0.0000	3	16	\N	\N	\N	1	1202130300	Active
1335	Washtafel-Oval-Duty-Ivory	MATERIAL	0.0000	3	14	\N	\N	\N	1	1402201510	Active
1338	Bak cuci piring-Single-Fawi-fawi50	MATERIAL	0.0000	3	14	\N	\N	\N	1	1406141104	Active
1339	Pasir-Pasang-Kelud	MATERIAL	0.0000	3	17	\N	\N	\N	1	0201010100	Active
1340	Tanah-Urug---	MATERIAL	0.0000	3	17	\N	\N	\N	1	0202020000	Active
1341	Sekrup-BSA--12x20	MATERIAL	0.0000	3	14	\N	\N	\N	1	0611170013	Active
1342	Sekrup-BSA--8x13	MATERIAL	0.0000	3	14	\N	\N	\N	1	0611170014	Active
1343	Batu-Pecah/Gebal--	MATERIAL	0.0000	3	17	\N	\N	\N	1	0203010000	Active
1344	penutup atap-atap-bumbungan-ilufa-sudut	MATERIAL	0.0000	3	14	\N	\N	\N	1	0801050403	Active
1347	Begel Jadi-Begel8x10--KW 2 (0.3-0.5)	MATERIAL	0.0000	3	20	\N	\N	\N	1	0609110004	Active
1350	Shower-Shower-Wasser-shs535	MATERIAL	0.0000	3	14	\N	\N	\N	1	1404110611	Active
1352	Baja Ringan-Reng	MATERIAL	0.0000	3	15	\N	\N	\N	1	0701020000	Active
1355	Paku-Beton--2inchi	MATERIAL	0.0000	3	14	\N	\N	\N	1	0610150046	Active
1356	Reng-2/3-Kruing-1meter	MATERIAL	0.0000	3	14	\N	\N	\N	1	0501010301	Active
1357	Fisher-Fisher--S6	MATERIAL	0.0000	3	14	\N	\N	\N	1	0613220020	Active
417	Amplas-Lembar-dll-Gradasi Halus 180		0.0000	2	22	2019-05-30 14:23:40	2019-05-30 14:23:40	\N	1	0101030204	Active
418	Amplas-Roll100M-dll-Gradasi Halus 120		0.0000	2	15	2019-05-30 14:25:43	2019-05-30 14:25:43	\N	1	0101010204	Active
420	Board-Ethernit-Nusa Board-120x240t =3mm		0.0000	3	22	2019-05-30 15:31:21	2019-05-30 15:31:21	\N	1	'0903000701	Active
421	Perekat Sending-Compound-Kornis-Aplus		0.0000	3	20	2019-05-30 15:43:34	2019-05-30 15:43:34	\N	1	0403030200	Active
422	Board-Gypsum-A plus-		0.0000	3	22	2019-05-30 15:49:39	2019-05-30 15:49:39	\N	1	0901000400	Active
423	Penutup Atap-Genteng-Bumbungan-Karya Abadi-Bulat		0.0000	3	14	2019-05-30 15:50:31	2019-05-30 15:50:31	\N	1	0801050204	Active
424	Material Alam-Pasir-Paving-Brantas-		0.0000	3	17	2019-05-30 15:52:06	2019-05-30 15:52:06	\N	1	0201050200	Active
419	Perekat-Semen-PC-Conch-40kg		0.0000	3	20	2019-05-30 15:30:05	2019-05-30 16:10:42	2019-05-30 16:10:42	1	0401010402	Active
1138	perekat-semen-pc-conch-40kg	MATERIAL	0.0000	3	20	\N	2019-05-30 16:11:08	2019-05-30 16:11:08	1	0401010402	Active
425	Semen-PC-Conch-40Kg		0.0000	3	20	2019-05-30 16:08:33	2019-05-30 16:12:22	\N	1	0401010402	Active
426	Skrup---3inchi		0.0000	3	14	2019-05-30 16:14:41	2019-05-30 16:14:41	\N	1	0611000034	Active
427	Alat Bantu Cat-Perban Gypsum-dll-		0.0000	3	18	2019-05-30 16:34:22	2019-05-30 16:34:22	\N	1	0102080200	Active
428	Paving-K250-6x10x20-		0.0000	3	16	2019-05-30 16:48:12	2019-05-30 16:48:12	\N	1	1501010001	Active
429	Pintu-PVC-New Star		0.0000	3	14	2019-05-30 16:54:38	2019-05-30 16:54:38	\N	1	1408181600	Active
1300	kuas-eterna-5 inchi	MATERIAL	0.0000	3	14	\N	2019-05-31 16:48:09	\N	1	0102040112	Active
1249	Kalsiplank--20x300	MATERIAL	0.0000	3	15	\N	2019-05-31 17:04:11	\N	1	0904000006	Active
1369	-kuas-eterna-2,5 inchi	MATERIAL	0.0000	3	14	\N	2019-05-31 16:53:12	\N	1	0102010106	Active
430	-sliper-dll-		0.0000	3	14	2019-05-31 10:20:21	2019-05-31 16:54:22	\N	1	0102040200	Active
431	-timba-dll-cor		0.0000	3	14	2019-05-31 10:20:49	2019-05-31 17:01:00	\N	1	0102070210	Active
1345	-pisaupotong-keramik-	MATERIAL	0.0000	3	14	\N	2019-05-31 17:02:07	\N	1	0106010000	Active
438	-bata-ringan-7,5x20x40-blescon-kw 2		0.0000	3	17	2019-05-31 10:23:39	2019-05-31 17:12:01	\N	1	0301080202	Active
437	-bata-ringan-7,5x20x60-blescon-kw 1		0.0000	3	17	2019-05-31 10:23:21	2019-05-31 17:13:34	\N	1	0301060201	Active
439	-bata-ringan-7,5x20x30-blescon-kw 2		0.0000	3	17	2019-05-31 10:24:01	2019-05-31 17:14:54	\N	1	0301090202	Active
432	-semen-semen putih-gresik-40 kg		0.0000	3	20	2019-05-31 10:21:12	2019-05-31 17:16:29	\N	1	0401020102	Active
440	-mortar-bata ringan-MU-50 kg		0.0000	3	20	2019-05-31 10:24:23	2019-05-31 17:19:06	\N	1	0402010303	Active
435	-lem-kayu-rajawali		0.0000	3	14	2019-05-31 10:22:24	2019-05-31 19:20:42	\N	1	0404040100	Active
434	-lem-pralon/pvc-isarplas		0.0000	3	20	2019-05-31 10:22:04	2019-05-31 19:17:05	2019-05-31 19:17:05	1	0404030200	Active
1351	-besi-beton polos-besi 6-kw 2 (0,3-0,5)	MATERIAL	0.0000	3	20	\N	2019-05-31 19:27:02	\N	1	0602030004	Active
1348	genteng-beton flat-	MATERIAL	0.0000	3	14	\N	2019-05-31 19:30:23	\N	1	0801010000	Active
1267	kabel-NYM-extrana-2 x 2,5	MATERIAL	0.0000	3	15	\N	2019-05-31 19:36:39	\N	1	1001050204	Active
1358	overloop-3 x 4	MATERIAL	0.0000	3	14	\N	2019-05-31 19:37:45	\N	1	1005000005	Active
1227	-lampu led-bulat-iwata-6 watt	MATERIAL	0.0000	3	14	\N	2019-05-31 19:44:31	\N	1	1013130921	Active
1370	cat-genteng-pail-sanlex-912 sanlex	MATERIAL	0.0000	3	20	\N	2019-05-31 19:45:24	\N	1	1101020110	Active
1367	cat-genteng-pail-indianapaint decofresh-axio	MATERIAL	0.0000	3	21	\N	2019-05-31 19:46:43	\N	1	1101020412	Active
1336	cat-plafond-galon-paragon-putih empis	MATERIAL	0.0000	3	20	\N	2019-05-31 19:49:08	\N	1	1102010209	Active
1366	cat-kayu/besi-1 kg-indiana paint decofresh-burn timber 31 nitrolux	MATERIAL	0.0000	3	21	\N	2019-05-31 19:50:11	\N	1	1103040411	Active
1294	cat-interior-galon-vinilex-990 S SB brilliant white	MATERIAL	0.0000	3	20	\N	2019-05-31 19:52:38	\N	1	1104011303	Active
1365	cat-interior-pail-indianapaint decofresh	MATERIAL	0.0000	3	21	\N	2019-05-31 19:53:18	\N	1	1104020400	Active
1368	politur-pail-indianapaint decofresh	MATERIAL	0.0000	3	21	\N	2019-05-31 19:56:26	\N	1	1107020400	Active
1225	coating- 1 liter	MATERIAL	0.0000	3	14	\N	2019-05-31 19:56:54	\N	1	1108070000	Active
1353	granit-25x40-kia-ivi white	MATERIAL	0.0000	3	16	\N	2019-05-31 20:00:31	\N	1	1201100205	Active
1346	keramik-40x40-chelsea begi-green luxor	MATERIAL	0.0000	3	23	\N	2019-05-31 20:01:22	\N	1	1202030804	Active
1364	keramik-20x20	MATERIAL	0.0000	3	16	\N	2019-05-31 20:02:38	\N	1	1202050000	Active
1354	keramik-25x25-asia tile-galaxi brown d 03	MATERIAL	0.0000	3	16	\N	2019-05-31 20:06:01	\N	1	1202120406	Active
441	pipa-pvc c-m point-3 inchi		0.0000	3	15	2019-05-31 10:24:46	2019-05-31 20:19:22	\N	1	1301030505	Active
1362	engsel-pintu jendela/kayu-4 inchi	MATERIAL	0.0000	3	14	\N	2019-05-31 21:25:43	\N	1	1602010008	Active
1359	handle-pintu/jendela kayu-hampton	MATERIAL	0.0000	3	24	\N	2019-05-31 21:33:07	\N	1	1608010300	Active
1337	rel pintu-pintu sliding	MATERIAL	0.0000	3	24	\N	2019-05-31 21:39:14	\N	1	1618060000	Active
433	-kalsium dsgm-		0.0000	3	20	2019-05-31 10:21:42	2019-05-31 21:43:26	\N	1	0403020000	Active
436	-genteng-beton garuda-15x50		0.0000	3	14	2019-05-31 10:22:55	2019-05-31 21:44:48	\N	1	0801020002	Active
1237	mb dos-broco-	MATERIAL	0.0000	3	14	\N	2019-05-31 21:49:26	\N	1	1011000500	Active
1295	woodfiller-1 kg-propan-impra awf-911 jati	MATERIAL	0.0000	3	20	\N	2019-05-31 19:57:52	\N	1	1111040707	Active
1331	cat-waterproof-1 kg-f corca-	MATERIAL	0.0000	3	21	\N	2019-05-31 19:58:35	\N	1	1112041400	Active
1363	keramik-30x30-green luxor	MATERIAL	0.0000	3	16	\N	2019-05-31 20:02:12	\N	1	1202040004	Active
1275	pipa-pvc aw-5/8 inchi	MATERIAL	0.0000	3	15	\N	2019-05-31 20:14:03	\N	1	1301010010	Active
1349	pipa-pvc aw-maspion-3/4-1/2"	MATERIAL	0.0000	3	15	\N	2019-05-31 20:15:15	\N	1	1301010111	Active
1276	pipa-pvc aw-wafin-1"-2"	MATERIAL	0.0000	3	15	\N	2019-05-31 20:17:45	\N	1	1301010209	Active
1253	knee-flexibel-rucika-1"-2"	MATERIAL	0.0000	3	14	\N	2019-05-31 20:22:04	\N	1	1302060409	Active
1233	shock drat luar-pvc aw-rucika-3/4-1/2"	MATERIAL	0.0000	3	14	\N	2019-05-31 20:25:06	\N	1	1314010411	Active
1288	stop kran-pvc aw-rucika-kran 073	MATERIAL	0.0000	3	14	\N	2019-05-31 20:25:39	\N	1	1315010412	Active
1361	kunci-pintu/jendela kayu	MATERIAL	0.0000	3	14	\N	2019-05-31 21:27:54	\N	1	1601010000	Active
1323	engsel-pintu/jendela kayu-dekson- 3inchi	MATERIAL	0.0000	3	14	\N	2019-05-31 21:29:52	\N	1	1602010209	Active
1238	engsel kupu-pintu/jendela alumunium	MATERIAL	0.0000	3	14	\N	2019-05-31 21:30:58	\N	1	1603020000	Active
1245	handle-pintu/jendela alumunium-HR6141	MATERIAL	0.0000	3	24	\N	2019-05-31 21:34:36	\N	1	1608020002	Active
1280	rel atas-pintu/jendela alumunium-2,9m	MATERIAL	0.0000	3	15	\N	2019-05-31 21:37:12	\N	1	1612020004	Active
1360	rel pintu-pintu sliding-j3	MATERIAL	0.0000	3	24	\N	2019-05-31 21:39:53	\N	1	1618060016	Active
1303	stop kontak-engkel-schneider-putih	MATERIAL	0.0000	3	14	\N	2019-05-31 21:50:12	\N	1	1008060410	Active
442	shower-ae-fshic		0.0000	3	14	2019-05-31 22:42:05	2019-05-31 22:42:05	\N	1	1404000002	Active
443	kran air-ae fshic		0.0000	3	14	2019-06-01 01:20:12	2019-06-01 01:20:12	\N	1	1403000002	Active
444	jet shower-fawi		0.0000	3	14	2019-06-01 01:21:10	2019-06-01 01:21:10	\N	1	1405121100	Active
445	mata bor-beton		0.0000	3	14	2019-06-01 01:21:56	2019-06-01 01:21:56	\N	1	0105010000	Active
446	kunci-pintu pvc-c 603x400		0.0000	3	14	2019-06-01 01:23:07	2019-06-01 01:23:07	\N	1	1601040014	Active
447	kunci-pintu pvc-c 603x300		0.0000	3	14	2019-06-01 01:23:48	2019-06-01 01:23:48	\N	1	1601040015	Active
448	closed-duduk monoblok-ina-fawi 50		0.0000	3	14	2019-06-01 01:24:55	2019-06-01 01:24:55	\N	1	1401020204	Active
449	shock drat luar-pvc aw-rucika-3/4-1/2"		0.0000	3	14	2019-06-01 01:26:22	2019-06-01 01:26:22	\N	1	1314010411	Active
450	baja ringan-canal c-kencana-0,3x60		0.0000	3	15	2019-06-01 01:27:31	2019-06-01 01:27:31	\N	1	0701010148	Active
451	knee-flexible-rucika-1"-2"		0.0000	3	14	2019-06-01 13:53:50	2019-06-01 13:53:50	\N	1	1302060409	Active
452	klem skundek-17mm		0.0000	3	14	2019-06-01 13:55:50	2019-06-01 13:55:50	\N	1	1012000015	Active
453	cat-exterior-5 kg-mowilex-ws-504 akasia		0.0000	1	20	2019-06-01 13:57:40	2019-06-01 13:57:40	\N	1	1105030508	Active
454	daun pintu-double multiplex taco seet-280x82		0.0000	3	14	2019-06-01 13:59:28	2019-06-01 13:59:28	\N	1	0508040004	Active
455	kabel-NYM-extrana-3 x 2,5		0.0000	3	15	2019-06-01 14:00:48	2019-06-01 14:00:48	\N	1	1001050220	Active
456	fitting tempel-oval-broco		0.0000	3	14	2019-06-01 15:09:58	2019-06-01 15:09:58	\N	1	1015140500	Active
457	saklar-engkel-schneider-pieno white		0.0000	3	14	2019-06-01 15:12:13	2019-06-01 15:12:13	\N	1	1007060422	Active
458	saklar-seri-schneider-pieno white		0.0000	3	14	2019-06-01 15:13:52	2019-06-01 15:13:52	\N	1	1007070422	Active
459	saklar-triple-schneider-pieno white		0.0000	3	14	2019-06-01 15:16:35	2019-06-01 15:16:35	\N	1	1007150422	Active
460	kabel-NYA merah-extrana-1 x 2,5		0.0000	3	15	2019-06-01 15:19:25	2019-06-01 15:19:25	\N	1	1001160202	Active
461	kabel-NYA kuning-extrana-1 x 2,5		0.0000	3	15	2019-06-01 15:20:37	2019-06-01 15:20:37	\N	1	1001030202	Active
462	kabel-NYA biru-extrana-1 x 2,5		0.0000	3	15	2019-06-01 15:21:42	2019-06-01 15:21:42	\N	1	1001020202	Active
463	mb dos-kotak-schneider-hitam		0.0000	3	15	2019-06-01 15:23:20	2019-06-01 15:23:20	\N	1	1011120419	Active
464	Arde-11 x 1,4 mm		0.0000	3	14	2019-06-01 15:25:11	2019-06-01 15:25:11	\N	1	1002000018	Active
465	kabel-NYY-eterna-3x4		0.0000	3	15	2019-06-01 15:26:57	2019-06-01 15:26:57	\N	1	1001010105	Active
466	Alat Bantu Cat-Perban Gypsum-dll-Cor		0.0000	3	14	2019-06-10 13:15:02	2019-06-10 13:15:02	\N	1	0102080210	Active
467	dynabolt-8 x 65		0.0000	3	14	2019-06-13 12:54:37	2019-06-13 12:54:37	\N	1	0611180050	Active
468	talang-galvalum-0,30x60cm		0.0000	3	15	2019-06-13 12:57:56	2019-06-13 12:57:56	\N	1	0612090048	Active
1220	amplas-rool 100m--gradasi halus 150	MATERIAL	0.0000	3	15	\N	2019-08-15 11:30:26	\N	1	0101010001	Active
469	adsfasdf		0.0000	3	15	2020-05-07 16:40:12	2020-05-07 16:40:26	2020-05-07 16:40:26	1	\N	\N
\.


--
-- TOC entry 3680 (class 0 OID 16541)
-- Dependencies: 240
-- Data for Name: m_kpr_bank_payments; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.m_kpr_bank_payments (id, bank_name, progress_category, payment_percent, created_at, updated_at, deleted_at, bank_code) FROM stdin;
67	MANDIRI SYARIAH (BSM)	Setelah AJB	30.00	\N	\N	\N	001
68	MANDIRI SYARIAH (BSM)	Progress 60% (atap)	30.00	\N	\N	\N	001
69	MANDIRI SYARIAH (BSM)	Progress 100% (BAST)	30.00	\N	\N	\N	001
70	MANDIRI SYARIAH (BSM)	APHT selesai (sertifikat diterima bank)	10.00	\N	\N	\N	001
71	BNI Syariah	Setelah AJB	40.00	\N	\N	\N	002
72	BNI Syariah	Progress 60% (atap)	40.00	\N	\N	\N	002
73	BNI Syariah	Progress 100% (BAST)	10.00	\N	\N	\N	002
74	BNI Syariah	APHT selesai (sertifikat diterima bank)	10.00	\N	\N	\N	002
75	JATIM SYARIAH (BJS)	Setelah AJB	50.00	\N	\N	\N	003
76	JATIM SYARIAH (BJS)	Progress 60% (atap)	30.00	\N	\N	\N	003
77	JATIM SYARIAH (BJS)	Progress 100% (BAST)	20.00	\N	\N	\N	003
78	BRI SYARIAH	Setelah AJB	70.00	\N	\N	\N	004
79	BRI SYARIAH	Progress 60% (atap)	15.00	\N	\N	\N	004
80	BRI SYARIAH	Progress 100% (BAST)	11.25	\N	\N	\N	004
81	BRI SYARIAH	APHT selesai (sertifikat diterima bank)	3.75	\N	\N	\N	004
82	BRI (KONVENSIONAL)	Setelah AJB	40.00	\N	\N	\N	005
83	BRI (KONVENSIONAL)	Progress 60% (atap)	30.00	\N	\N	\N	005
84	BRI (KONVENSIONAL)	Progress 100% (BAST)	20.00	\N	\N	\N	005
85	BRI (KONVENSIONAL)	APHT selesai (sertifikat diterima bank)	10.00	\N	\N	\N	005
86	PANIN	Setelah AJB	40.00	\N	\N	\N	006
87	PANIN	Progress 60% (atap)	40.00	\N	\N	\N	006
88	PANIN	Progress 100% (BAST)	20.00	\N	\N	\N	006
\.


--
-- TOC entry 3769 (class 0 OID 26384)
-- Dependencies: 329
-- Data for Name: m_positions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.m_positions (id, name, created_at, updated_at) FROM stdin;
2	Sales	2020-04-20 10:29:24.167558	2020-04-20 10:29:24.167558
\.


--
-- TOC entry 3682 (class 0 OID 16546)
-- Dependencies: 242
-- Data for Name: m_references; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.m_references (id, code, value, name, created_at, updated_at, deleted_at) FROM stdin;
4	PO	04	PO	\N	\N	\N
5	05	05	Pembatalan PO	\N	\N	\N
7	MREQ	07	Permintaan Material	\N	\N	\N
8	08	08	Pembatalan Permintaan Material	\N	\N	\N
13	13	13	Stok Status	\N	\N	\N
15	15	15	Rekonsiliasi	\N	\N	\N
6	INV_RCV	06	Penerimaan Material	\N	\N	\N
9	INV_OUT	09	Pengeluaran Barang	\N	\N	\N
14	STO	14	Opname	\N	\N	\N
10	TRF_REQ	10	Permintaan Transfer	\N	\N	\N
11	TRF_SEND	11	Pengiriman Transfer	\N	\N	\N
12	TRF_RCV	12	Penerimaan Transfer	\N	\N	\N
16	RAB	RAB	RAB	\N	\N	\N
17	ABK_REQ	16	Permintaan Alat Bantu Kerja	\N	\N	\N
18	17	17	Pembatalan Permintaan Alat Bantu Kerja	\N	\N	\N
19	ABK_OUT	18	Pengeluaran Alat Bantu Kerja	\N	\N	\N
1	NUP	01	NUP	\N	\N	\N
2	SPU	02	SPU	\N	\N	\N
3	PPJB	03	PPJB	\N	\N	\N
20	DCS_REQ	19	Discount Request	\N	\N	\N
21	SPCUP	20	Spec Up	\N	\N	\N
22	BOK	21	Booking	\N	\N	\N
\.


--
-- TOC entry 3685 (class 0 OID 16553)
-- Dependencies: 245
-- Data for Name: m_sequences; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.m_sequences (id, seq_code, period_year, period_month, site_id, seq_length, seq_no, created_at, updated_at, deleted_at) FROM stdin;
31	PO	2019	06	2	3	17	2019-06-01 21:31:38	2019-06-12 20:31:47	\N
23	PO	2019	05	1	3	16	2019-05-30 14:18:43	2019-06-01 01:16:09	\N
22	RAB	2019	05	2	3	1	2019-05-30 09:29:53	2019-05-30 09:29:53	\N
33	TRF_REQ	2019	6	1	3	2	2019-06-13 19:18:40	2019-06-13 19:20:47	\N
24	INV_RCV	2019	5	1	3	5	2019-05-30 14:49:01	2019-05-30 15:25:54	\N
34	TRF_SEND	2019	6	2	3	3	2019-06-13 20:22:10	2019-06-13 20:22:47	\N
25	PO	2019	05	2	3	8	2019-05-30 15:25:40	2019-05-30 16:56:04	\N
26	INV_RCV	2019	5	2	3	8	2019-05-30 15:28:13	2019-05-30 17:08:15	\N
28	TRF_SEND	2019	5	2	3	2	2019-05-30 15:45:15	2019-05-30 17:09:54	\N
29	TRF_RCV	2019	5	1	3	2	2019-05-30 15:46:36	2019-05-30 17:14:08	\N
27	TRF_REQ	2019	5	1	3	3	2019-05-30 15:31:49	2019-05-31 10:19:20	\N
36	SPCUP	2019	6	1	3	3	2019-06-15 23:03:50	2019-07-01 00:18:33	\N
45	DCS_REQ	2019	6	1	3	3	2019-06-22 06:52:04	2019-07-01 00:24:44	\N
43	SPU	2019	6	1	3	11	2019-06-20 00:30:02	2019-07-01 00:30:06	\N
37	PPJB	2019	6	1	3	14	2019-06-16 13:48:36	2019-07-01 00:54:08	\N
57	RAB	2019	07	1	3	3	2019-07-15 12:13:31	2019-07-23 15:52:58	\N
40	INV_OUT	2019	6	1	3	2	2019-06-17 23:11:26	2019-06-23 22:39:58	\N
49	INV_SALE	2019	7	1	3	7	2019-07-01 10:37:35	2019-07-01 10:52:31	\N
58	TRF_REQ	2019	7	1	3	2	2019-07-15 16:12:29	2019-07-15 22:41:57	\N
55	INV_RCV	2019	7	1	3	5	2019-07-01 21:07:04	2019-07-24 09:09:42	\N
59	MREQ	2019	7	1	3	3	2019-07-15 16:15:46	2019-07-16 00:12:25	\N
48	INV_OUT	2019	7	1	3	8	2019-07-01 10:37:34	2019-07-16 00:18:36	\N
60	ABK_OUT	2019	7	1	3	1	2019-07-16 00:19:49	2019-07-16 00:19:49	\N
38	RAB	2019	06	1	3	3	2019-06-17 21:57:00	2019-06-24 15:15:56	\N
30	PO	2019	06	1	3	32	2019-06-01 13:50:19	2019-06-24 15:28:51	\N
32	INV_RCV	2019	6	1	3	40	2019-06-12 11:58:44	2019-06-24 15:31:13	\N
39	MREQ	2019	6	1	3	11	2019-06-17 23:10:46	2019-06-24 15:43:19	\N
46	ABK_OUT	2019	6	1	3	5	2019-06-22 20:32:35	2019-06-24 15:43:59	\N
61	RAB	2019	07	2	3	2	2019-07-24 07:12:58	2019-07-24 17:02:40	\N
47	PO	2019	07	1	3	11	2019-07-01 07:36:02	2019-07-24 17:42:27	\N
35	TRF_REQ	2019	6	2	3	3	2019-06-14 15:16:29	2019-06-24 15:54:32	\N
41	TRF_SEND	2019	6	1	3	2	2019-06-17 23:35:53	2019-06-24 15:55:57	\N
42	TRF_RCV	2019	6	2	3	2	2019-06-17 23:37:07	2019-06-24 15:56:52	\N
69	MREQ	2019	9	1	3	8	2019-09-25 20:57:40	2019-09-26 13:21:14	\N
70	PO	2019	09	1	3	4	2019-09-30 14:30:10	2019-09-30 14:44:39	\N
71	PO	2019	10	1	3	1	2019-10-07 09:03:37	2019-10-07 09:03:37	\N
72	BOK	2019	10	1	3	2	2019-10-07 13:57:22	2019-10-07 14:01:55	\N
73	NUP	2019	10	1	3	2	2019-10-07 13:57:23	2019-10-07 14:01:55	\N
51	SPU	2019	7	1	3	64	2019-07-01 11:49:52	2019-07-29 21:40:13	\N
56	BOK	2019	7	1	3	30	2019-07-05 20:19:02	2019-07-29 21:41:11	\N
52	NUP	2019	7	1	3	67	2019-07-01 14:06:09	2019-07-29 21:41:12	\N
50	PPJB	2019	7	1	3	35	2019-07-01 11:49:35	2019-07-29 21:43:23	\N
53	SPCUP	2019	7	1	3	39	2019-07-01 14:21:12	2019-07-29 21:45:03	\N
54	DCS_REQ	2019	7	1	3	24	2019-07-01 15:00:54	2019-07-29 21:45:35	\N
63	BOK	2019	8	1	3	1	2019-08-30 10:59:53	2019-08-30 10:59:53	\N
64	NUP	2019	8	1	3	1	2019-08-30 10:59:53	2019-08-30 10:59:53	\N
62	SPU	2019	8	1	3	8	2019-08-07 20:03:20	2019-08-30 11:01:05	\N
65	PPJB	2019	8	1	3	1	2019-08-30 11:01:34	2019-08-30 11:01:34	\N
66	BOK	2019	9	1	3	5	2019-09-21 09:02:08	2019-09-21 21:39:08	\N
67	NUP	2019	9	1	3	5	2019-09-21 09:02:09	2019-09-21 21:39:09	\N
68	SPU	2019	9	1	3	1	2019-09-21 21:45:29	2019-09-21 21:45:29	\N
44	NUP	2019	6	1	3	1	2019-06-20 07:15:26	2019-07-01 00:17:13	\N
75	SPU	2020	5	1	3	4	2020-05-07 19:50:39	2020-05-09 11:42:44	\N
74	PO	2020	05	1	3	19	2020-05-07 17:44:59	2020-05-16 11:28:40	\N
76	INV_RCV	2020	5	1	3	22	2020-05-07 20:49:22	2020-05-16 11:29:06	\N
77	ORD	2020	05	1	3	4	2020-05-09 09:38:45	2020-05-16 11:32:01	\N
78	RAB	2020	05	1	3	4	2020-05-09 14:21:27	2020-05-16 11:32:32	\N
79	MREQ	2020	5	1	3	12	2020-05-11 11:43:41	2020-05-16 12:39:06	\N
80	INV_OUT	2020	5	1	3	7	2020-05-13 10:51:42	2020-05-16 12:50:18	\N
\.


--
-- TOC entry 3686 (class 0 OID 16557)
-- Dependencies: 246
-- Data for Name: m_suppliers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.m_suppliers (id, name, address, created_at, updated_at, deleted_at, no, city, phone, notes) FROM stdin;
4	adi putra	Jl. Yos Sudarso No.10, Pakelan, Kec. Kota Kediri, Kota Kediri, Jawa Timur 64129	\N	\N	\N	001	KEDIRI	081217384488	\N
5	Avant grade	JL. Panglima Sudirman 45 D&E,Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung 66219	\N	\N	\N	002	TULUNGAGUNG	(0355) 328638	\N
6	Azzam	jl pamenang ngasem kediri	\N	\N	\N	003	KEDIRI	082210193522	\N
7	bandung elektrik	jln pemuda no 27 kediri	\N	\N	\N	004	KEDIRI	082150366051	\N
8	borobudur	Jl. Perintis Kemerdekaan No.258, Ngronggo, Kec. Kota Kediri, Kediri, Jawa Timur 64129	\N	\N	\N	005	KEDIRI	\N	\N
9	citra jaya	Jl. Pamenang No.52B, Katang, Sukorejo, Ngasem, Kediri, Jawa Timur 64182	\N	\N	\N	006	KEDIRI	081230132540	\N
10	druwo energy	\N	\N	\N	\N	007	\N	082141028511	\N
11	dunia cat	ds.ngasem kec ngasem	\N	\N	\N	008	KEDIRI	\N	\N
12	dwi jaya	Jl. Letjend Sutoyo No.81, Burengan, Pesantren, Kota Kediri, Jawa Timur 64131	\N	\N	\N	009	KEDIRI	081234108888	\N
13	excellen door	Jl. Mayjend Sungkono No.5, Segoromadu, Kebomas, Kabupaten Gresik, Jawa Timur 61124	\N	\N	\N	010	GRESIK	085100844823	\N
14	icc bata ringan	\N	\N	\N	\N	011	\N	085790893456	\N
15	indo bangunan	jln yos sudarso no 89 kediri	\N	\N	\N	012	KEDIRI	085102102888	\N
16	cv.permata anugrah utamabbs surabaya	jln balowerti surabaya	\N	\N	\N	013	SURABAYA	082230786802	\N
17	jaya mandiri	jln tembus kaliombo no 100 kediri	\N	\N	\N	014	KEDIRI	085105100304	\N
18	karya abadi	jln mayjend panjaitan no28 kediri	\N	\N	\N	015	KEDIRI	(0354 - 634664)	\N
19	mitra k45	jln kapten tendean ngronggo	\N	\N	\N	016	KEDIRI	085733313360	\N
20	pahala	jln hm winarto no 89 lirboyo kediri	\N	\N	\N	017	KEDIRI	085649643869	\N
21	pak man 	\N	\N	\N	\N	018	\N	081259794399	\N
22	pak nanang 	\N	\N	\N	\N	019	\N	081234448111	\N
23	pak rif an 	jln soekarno hatta kediri	\N	\N	\N	020	KEDIRI	081335858822	\N
24	putra agung	jln yos sudarso no 92 - 94 kediri	\N	\N	\N	021	KEDIRI	08123233498	\N
25	rejo makmur	jln letjend sutoyo no 104 kediri	\N	\N	\N	022	KEDIRI	082141424193	\N
26	restu maju	jln pamenang sebelah sururi estate	\N	\N	\N	023	KEDIRI	081298895422	\N
27	sinar teknik	\N	\N	\N	\N	024	\N	082231155667	\N
28	top bangunan	jln erlangga no 234 Slg kediri	\N	\N	\N	025	KEDIRI	082335999912	\N
29	usaha jaya	jln erlangga Slg kediri	\N	\N	\N	026	KEDIRI	081230513380	\N
30	cv.alumina	jln kali anaj surabaya	\N	\N	\N	027	SURABAYA	085101785096	\N
31	surya alumunium	jln yos sudarso no 98 kediri	\N	\N	\N	028	KEDIRI	(0354) 687603	\N
32	toko listrik 	jln ngantang malang	\N	\N	\N	029	MALANG	\N	\N
33	toko kali anyar	 jl jagalan surabaya	\N	\N	\N	030	SURABAYA	0811316213	\N
34	watt listrik	jln adi sucipto no 40 kediri	\N	\N	\N	031	KEDIRI	082331407182	\N
35	bu lia	blitar	\N	\N	\N	032	BLITAR	081555701450	\N
36	mujiarto stell	tulung agung	\N	\N	\N	033	TULUNGAGUNG	081357807533	\N
37	kunci & engsel pak ayun lumajang	\N	\N	\N	\N	034	\N	\N	\N
38	karya abadi	WATES	\N	\N	\N	035	KEDIRI	\N	\N
39	FERARI TRANS	\N	\N	\N	\N	036	\N	081234448111	\N
40	PINTU MOJOKERTO BLESSCON	\N	\N	\N	\N	037	\N	\N	\N
41	PAK PUTUT	\N	\N	\N	\N	038	\N	\N	\N
42	SANJAYA	\N	\N	\N	\N	039	\N	081348662444	\N
43	PAK SYAFAAT	\N	\N	\N	\N	040	\N	\N	\N
44	CV ARB LAMONGAN	\N	\N	\N	\N	041	\N	\N	HARUS CASH
45	BU SHE	\N	\N	\N	\N	042	\N	\N	HARUS CASH
59	Megah Bangunan	Jalan Raya Gempol 100 A Tulungagung	\N	\N	\N	\N	Tulungagung	\N	\N
60	INITIAL STOCK	\N	\N	\N	\N	\N	\N	\N	\N
61	jafar	jl kenangan	\N	2019-07-31 14:53:14	2019-07-31 14:53:14	060	jember	099498983498	harus cash
62	jafar	jalan kemuning sari lor	\N	2019-07-31 23:23:25	\N	061	jember	081234825928	harus cash
63	joni	jalan balung wetan	2019-07-31 23:21:19	2019-07-31 23:31:02	\N	062	jember	0909090909	\N
64	minto	patrang	2019-07-31 23:33:20	2019-07-31 23:33:58	\N	\N	banyuwangi	090909009	\N
65	pleki	jl kenangan	2019-08-03 22:00:39	2019-08-03 22:00:39	\N	\N	jember	090980909	aljdh
66	jafar	kediri	2019-08-14 11:11:19	2019-08-14 11:11:19	\N	\N	kediri	0909090909	aksdjhf
\.


--
-- TOC entry 3688 (class 0 OID 16565)
-- Dependencies: 248
-- Data for Name: m_units; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.m_units (id, name, code, created_at, updated_at, deleted_at) FROM stdin;
14	Pcs	pcs	\N	\N	\N
15	M1	m1	\N	\N	\N
16	M2	m2	\N	\N	\N
17	M3	m3	\N	\N	\N
18	Roll	roll	\N	\N	\N
19	Batang	btg	\N	\N	\N
20	Kg	kg	\N	\N	\N
21	Liter	ltr	\N	\N	\N
22	Lembar	lbr	\N	\N	\N
23	Dus	dus	\N	\N	\N
24	set	set	\N	\N	\N
25	Pasang	psg	\N	\N	\N
\.


--
-- TOC entry 3690 (class 0 OID 16570)
-- Dependencies: 250
-- Data for Name: m_warehouses; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.m_warehouses (id, name, code, created_at, updated_at, deleted_at) FROM stdin;
1	Gudang Site A	WH_S1	\N	\N	\N
\.


--
-- TOC entry 3692 (class 0 OID 16575)
-- Dependencies: 252
-- Data for Name: material_prices; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.material_prices (id, m_supplier_id, m_item_id, base_price, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 3695 (class 0 OID 16585)
-- Dependencies: 255
-- Data for Name: menus; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.menus (id, title, url, icon, is_main_menu, is_active, seq_no, created_at, updated_at, is_deleted) FROM stdin;
5	Main Menu	#		0	1	0	\N	\N	f
4	Inventory	#		0	1	1	\N	\N	f
1	Menu	menu	mdi mdi-menu	8	1	0	\N	2019-02-03 14:58:07	f
30	Master Data Asset	master_data_asset	mdi mdi-minus	7	1	0	2019-02-03 16:17:16	2019-02-03 16:17:16	f
31	User	user	mdi mdi-account-multiple	8	1	0	2019-02-03 20:17:05	2019-02-03 20:17:05	f
32	Role	role	mdi mdi-account-settings-variant	8	1	0	2019-02-03 20:20:54	2019-02-03 20:24:52	f
33	RAB & Konstruksi	#	-	0	1	2	2019-02-05 17:47:38	2019-02-05 17:48:23	f
34	Project	#	mdi mdi-hospital-building	33	1	0	2019-02-05 17:52:42	2019-02-05 17:52:42	f
2	Dashboard	home	mdi mdi-view-dashboard	5	1	0	\N	2019-03-14 05:29:16	f
43	Pengajuan Discount	discountrequest	mdi mdi-minus	53	1	12	\N	2019-04-03 22:52:52	f
9	Master Data Material	master_material	mdi mdi-minus	7	1	0	\N	2019-05-26 23:41:30	f
12	Master Data Satuan	master_satuan	mdi mdi-minus	7	1	0	2019-02-03 02:45:55	2019-05-26 23:49:44	f
53	Pesan Unit	#	mdi mdi-account-multiple	38	1	0	2019-04-03 22:11:37	2019-07-08 04:11:26	f
13	Pembelian	#	mdi mdi-note-multiple	4	1	1	2019-02-03 14:14:50	2019-07-08 04:12:50	f
26	Material Request	material_request	mdi mdi-minus	70	1	1	2019-02-03 15:57:07	2019-07-08 04:34:08	f
21	Gudang	#	mdi mdi-store	4	1	2	2019-02-03 14:52:51	2019-07-08 04:15:11	f
7	Master Data	master_data	mdi mdi-database	4	1	4	\N	2019-07-08 04:16:18	f
64	Alat Bantu Kerja Request	alat_kerja_request	mdi mdi-minus	70	1	2	2019-05-27 02:52:26	2019-07-08 04:34:39	f
36	Pembelian	pembelian	mdi mdi-minus	13	1	1	2019-02-14 00:41:42	2019-07-08 04:17:47	f
15	Purchase Order	po_konstruksi	mdi mdi-minus	13	1	2	2019-02-03 14:19:33	2019-07-08 04:18:21	f
11	Penerimaan Material	penerimaan_barang	mdi mdi-minus	21	1	1	2019-02-03 02:06:52	2019-07-08 04:19:15	f
65	Otorisasi Alat Bantu Kerja	auth_alat_kerja	mdi mdi-minus	21	0	0	2019-05-27 02:53:26	2019-05-28 17:13:53	t
57	Site Stock	inventory/stock	mdi mdi-minus	68	1	1	2019-05-04 20:31:48	2019-07-08 04:24:49	f
56	Mutasi Stock	inventory	mdi mdi-minus	68	1	2	2019-04-27 11:12:29	2019-07-08 04:25:11	f
61	Stock Opname	stok_opname	mdi mdi-minus	68	1	3	2019-05-07 01:56:57	2019-07-08 04:26:05	f
69	Print Form Stock Opname	stok_opname/print_stok	mdi mdi-minus	68	1	4	2019-07-08 04:27:15	2019-07-08 04:27:15	f
70	Permintaan Material	#	mdi mdi-clipboard-text	33	1	1	2019-07-08 04:32:32	2019-07-08 04:32:32	f
62	Otorisasi Material Request	auth_pengambilan_barang	mdi mdi-minus	70	1	3	2019-05-15 20:06:44	2019-07-08 04:35:50	f
23	Transfer Stok	#	mdi mdi-minus	21	0	0	2019-02-03 15:24:57	2019-05-05 23:23:22	t
8	Setting	#	-	0	1	4	\N	2019-07-08 04:37:22	f
54	Penerimaan Uang	nuprecord	mdi mdi-minus	53	1	9	\N	2019-07-10 16:52:48	f
42	SPU	spurecord	mdi mdi-minus	53	1	10	\N	2019-07-10 16:53:06	f
55	PPJB	ppjbrecord	mdi mdi-minus	53	1	11	\N	2019-07-10 16:53:17	f
80	AJB	not-found	mdi mdi-minus	53	1	12	2019-07-10 16:53:35	2019-07-10 16:53:35	f
52	Spec Up Request	specuprequest	mdi mdi-minus	53	1	14	\N	2019-07-10 16:55:50	f
58	Permintaan Transfer Stok	transfer_stok	mdi mdi-minus	13	1	4	2019-05-05 19:31:26	2019-07-10 17:00:39	f
60	Penerimaan Transfer Stok	penerimaan_ts	mdi mdi-minus	21	1	3	2019-05-05 23:34:09	2019-07-10 17:02:13	f
81	Pembatalan PO	penerimaan_barang	mdi mdi-minus	13	1	3	2019-07-10 17:01:11	2019-07-10 17:04:10	f
83	Pembatalan Penerimaan Transfer	not-found	mdi mdi-minus	21	1	4	2019-07-10 17:07:24	2019-07-10 17:07:24	f
63	Pengeluaran Material	pengeluaran_barang	mdi mdi-minus	21	1	5	2019-05-19 01:38:41	2019-07-10 17:08:09	f
84	Pembatalan Pengeluaran Material	not-found	mdi mdi-minus	21	1	6	2019-07-10 17:09:20	2019-07-10 17:09:20	f
85	Pembatalan Pengeluaran Alat Bantu Kerja	not-found	mdi mdi-minus	21	1	8	2019-07-10 17:10:07	2019-07-10 17:10:07	f
86	Pembatalan Transfer Material	not-found	mdi mdi-minus	21	1	10	2019-07-10 17:11:24	2019-07-10 17:11:24	f
87	Pembatalan Penjualan Material	not-found	mdi mdi-minus	21	1	12	2019-07-10 17:14:26	2019-07-10 17:14:26	f
66	Pengeluaran Alat Bantu Kerja	pengeluaran_alat_kerja	mdi mdi-minus	21	1	7	2019-05-27 02:53:49	2019-07-10 17:16:43	f
59	Transfer Material	pengiriman_ts	mdi mdi-minus	21	1	9	2019-05-05 23:33:24	2019-07-10 17:17:25	f
67	Penjualan Material	penjualan_keluar	mdi mdi-minus	21	1	11	2019-06-29 10:42:23	2019-07-10 17:18:21	f
89	Follow Up	followup	mdi mdi-clipboard-text	38	1	1	2019-07-14 12:05:22	2019-07-14 12:05:22	f
35	Project RAP	rab	mdi mdi-minus	34	1	0	2019-02-05 17:54:20	2019-02-09 21:14:37	f
90	Theme Of Payment	menu/payment	mdi mdi-bank	8	1	0	2019-07-20 07:30:33	2019-07-20 07:53:50	f
68	Stock	#	mdi mdi-file-multiple	4	1	3	2019-07-08 04:14:57	2019-07-20 08:19:41	f
91	Theme	menu	mdi mdi-bank	38	0	0	2019-07-20 08:16:15	2019-07-20 08:16:15	t
92	Tagihan	inventory/purchase	mdi mdi-tag	4	1	0	2019-07-20 15:01:32	2019-07-20 15:01:32	f
94	Master Kavling	master_kavling	mdi mdi-minus	7	1	0	2019-07-30 23:01:08	2019-07-30 23:08:20	f
95	Master Suplier	master_suplier	mdi mdi-minus	7	1	0	2019-07-31 13:26:22	2019-07-31 13:26:22	f
72	Gallery	menu/gallery	mdi mdi-file-image	71	1	1	2019-07-08 04:39:39	2019-08-03 14:49:36	f
73	Harga	menu/price	mdi mdi-tag-outline	71	1	2	2019-07-08 04:40:06	2019-08-03 14:50:39	f
75	Program - program	dashboard/programList	mdi mdi-note-text	71	1	3	2019-07-08 04:41:05	2019-08-04 23:28:48	f
97	Dashboard	dashboard	mdi mdi-minus	0	1	0	2019-08-04 14:40:55	2019-08-04 14:40:55	f
93	Ini Menu Baru	#	mdi mdi-minus	38	0	0	2019-07-22 15:31:18	2019-08-22 21:27:21	f
74	Simulasi KPR	menu/simulasi_kpr	mdi mdi-home-variant	71	1	3	2019-07-08 04:40:33	2019-08-30 10:31:53	f
77	Key Performance Indikator	not-found	mdi mdi-minus	2	0	0	2019-07-10 16:45:54	2019-08-30 10:57:00	f
78	Notifikasi	not-found	mdi mdi-minus	2	0	1	2019-07-10 16:48:15	2019-08-30 10:57:17	f
79	Running Text Program	dashboard/program	mdi mdi-minus	2	0	2	2019-07-10 16:48:49	2019-08-30 10:57:32	f
98	Pengembalian Sisa Material	material_request/returnlist	mdi mdi-note-text	70	1	0	2019-09-26 10:00:38	2019-09-26 10:05:53	f
100	List Akun	akuntansi	mdi mdi-account-card-details	99	1	0	2020-04-16 10:18:59	2020-04-16 12:41:00	f
71	Information	#	-	0	1	4	2019-07-08 04:37:58	2020-04-20 08:37:54	f
96	Dashboard Sales	customer/dashboard	mdi mdi-minus	38	1	0	2019-08-01 13:59:25	2020-04-21 07:27:25	f
38	CRM	#	fa fa-minus	0	0	1	\N	2020-05-09 15:17:10	f
82	Pembatalan Penerimaan Material	penerimaan_barang/close_purchase	mdi mdi-minus	21	1	2	2019-07-10 17:03:01	2020-05-09 15:32:02	f
99	Akuntansi	#	fa fa-minus	0	1	3	2020-04-16 10:11:03	2020-04-16 10:19:15	f
101	Jurnal Akuntansi	akuntansi/jurnal	mdi mdi-view-list	99	1	1	2020-04-16 12:40:28	2020-04-16 12:40:28	f
102	Laba Rugi	akuntansi/profit-loss	mdi mdi-credit-card-plus	99	1	2	2020-04-16 12:43:50	2020-04-16 12:43:50	f
103	Neraca	akuntansi/neraca	mdi mdi-scale-balance	99	1	3	2020-04-16 12:44:50	2020-04-16 12:44:50	f
104	Tutup Buku	akuntansi/close-book	mdi mdi-book-variant	99	1	4	2020-04-17 09:23:41	2020-04-17 09:23:41	f
106	Pegawai	employee	mdi mdi-account-multiple	105	1	0	2020-04-20 08:36:09	2020-04-20 08:36:09	f
105	HRMS	#	fa fa-minus	0	1	4	2020-04-20 08:34:12	2020-04-20 08:37:27	f
107	Sales	menu/sales	mdi mdi-account-card-details	8	1	4	2020-04-20 09:47:36	2020-04-20 09:47:36	f
108	Jabatan	position	mdi mdi-account-card-details	105	1	1	2020-04-20 09:53:15	2020-04-20 09:53:15	f
109	Absensi	absensi	mdi mdi-calendar-clock	105	1	3	2020-04-20 12:38:38	2020-04-20 12:38:38	f
110	Gaji	salary	mdi mdi-credit-card-plus	105	1	4	2020-04-23 08:46:15	2020-04-23 08:46:15	f
111	Cuti	cuti	mdi mdi-calendar-remove	105	1	3	2020-04-23 10:01:38	2020-04-23 10:01:38	f
112	Master Produk	master_product	mdi mdi-minus	7	1	0	2020-05-08 08:29:18	2020-05-08 08:43:31	f
88	Customer	customer	mdi mdi-account-multiple	33	1	0	2019-07-14 12:04:57	2020-05-08 13:03:53	f
113	Order	order	mdi mdi-basket	33	1	0	2020-05-08 12:42:39	2020-05-10 08:59:00	f
\.


--
-- TOC entry 3697 (class 0 OID 16597)
-- Dependencies: 257
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2019_08_04_043554_create_program_table	1
2	2019_08_04_045622_create_programs_table	2
\.


--
-- TOC entry 3777 (class 0 OID 35763)
-- Dependencies: 337
-- Data for Name: order_ds; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.order_ds (id, order_id, product_id, created_at, updated_at, total, in_rab, deleted_at) FROM stdin;
39	25	2	2020-05-16 11:32:01	2020-05-16 11:32:01	5	1	\N
\.


--
-- TOC entry 3775 (class 0 OID 35755)
-- Dependencies: 335
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.orders (id, customer_id, order_name, order_date, created_at, updated_at, deleted_at, order_no, site_id, is_done) FROM stdin;
25	92	test	2020-05-16	2020-05-16 11:32:01	2020-05-16 11:32:01	\N	110/ORD/05/20/004	1	0
\.


--
-- TOC entry 3698 (class 0 OID 16601)
-- Dependencies: 258
-- Data for Name: password_resets; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_resets (email, token, created_at) FROM stdin;
\.


--
-- TOC entry 3699 (class 0 OID 16607)
-- Dependencies: 259
-- Data for Name: payment_receives; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.payment_receives (id, no, invoice_id, payment_type, bank_account_no, amount, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 3773 (class 0 OID 35625)
-- Dependencies: 333
-- Data for Name: product_subs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.product_subs (id, product_id, price, no, created_at, updated_at, deleted_at, order_d_id) FROM stdin;
119	2	0	110/ORD/05/20/004/2/1	2020-05-16 11:32:01	2020-05-16 11:32:01	\N	39
120	2	0	110/ORD/05/20/004/2/2	2020-05-16 11:32:02	2020-05-16 11:32:02	\N	39
121	2	0	110/ORD/05/20/004/2/3	2020-05-16 11:32:02	2020-05-16 11:32:02	\N	39
122	2	0	110/ORD/05/20/004/2/4	2020-05-16 11:32:02	2020-05-16 11:32:02	\N	39
123	2	0	110/ORD/05/20/004/2/5	2020-05-16 11:32:02	2020-05-16 11:32:02	\N	39
\.


--
-- TOC entry 3771 (class 0 OID 34582)
-- Dependencies: 331
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.products (id, name, description, image, price, m_unit_id, created_at, updated_at, deleted_at, is_active) FROM stdin;
1	1588903553.png	\N	1588903553.png	344444	16	2020-05-08 09:05:54	2020-05-08 09:23:15	2020-05-08 09:23:15	t
2	galangan pintu 2x2	asdkjhasdfjhasdf	1588904681.png	3444	15	2020-05-08 09:24:41	2020-05-08 09:36:15	\N	t
4	asdf	asdf	1589036572.png	33333	15	2020-05-09 22:02:52	2020-05-09 22:03:12	2020-05-09 22:03:12	t
5	Coffe Toffe	asdf	1589036696.png	3333	15	2020-05-09 22:04:56	2020-05-09 22:04:56	\N	t
6	test	asdf	1589037134.png	33	14	2020-05-09 22:12:14	2020-05-09 22:12:14	\N	t
3	Lemari gantung	asdfasdf	1588908905.png	234234	14	2020-05-08 10:03:31	2020-05-10 10:35:53	\N	t
9	galangan pintu 2x2	bahan jati	1588904681.png	3444	15	2020-05-10 10:46:07.819616	2020-05-10 10:46:07.819616	\N	\N
10	Lemari gantung 2 meter	bahan jati pernis	1588908905.png	234234	14	2020-05-10 10:46:07.823579	2020-05-10 10:47:08	\N	t
11	asdf	asdf	1589087249.jpg	33333	15	2020-05-10 12:07:29.275246	2020-05-10 12:07:29.275246	\N	\N
12	Lemari gantung 1 m	asdfasdf	1588908905.png	234234	14	2020-05-10 12:13:58.299474	2020-05-10 12:13:58.299474	\N	\N
13	meja makan 2x3 meter	bahan kayu, kaca	1589092865.jpg	20000	16	2020-05-10 13:41:05	2020-05-10 13:41:05	\N	t
14	Meja Belajar 1x1/2 meter	akdfjh	1589093087.jpg	200000	15	2020-05-10 13:44:47	2020-05-10 13:44:47	\N	t
15	Kaca Kristal	test	1589093323.jpg	60000	16	2020-05-10 13:48:43	2020-05-10 13:48:43	\N	t
16	galangan pintu 2x2 kaca	bahan kaca	1589254471.jpg	3444	15	2020-05-12 10:34:31.934754	2020-05-12 10:34:31.934754	\N	\N
\.


--
-- TOC entry 3701 (class 0 OID 16615)
-- Dependencies: 261
-- Data for Name: programs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.programs (id, name, user_id, status, created_at, updated_at, deleted_at) FROM stdin;
9	program sururi estate	1	Active	2019-08-04 23:36:07	2019-08-04 23:36:07	\N
7	Penyediaan pengembalian investasi yang baik dari bisnis yang dibangkitkan TI	18	Active	2019-08-04 07:00:42	2019-08-04 23:41:36	2019-08-04 23:41:36
8	program pelatihan 45	18	Active	2019-08-04 15:23:24	2019-08-04 23:53:30	\N
10	Penyediaan pengembalian investasi yang baik dari bisnis yang dibangkitkan TIaksdjfh	1	Active	2019-08-05 23:54:02	2019-08-05 23:54:02	\N
\.


--
-- TOC entry 3703 (class 0 OID 16623)
-- Dependencies: 263
-- Data for Name: project_works; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.project_works (id, rab_id, project_id, name, base_price, created_at, updated_at, deleted_at) FROM stdin;
42	36	262	pemotongan bahan	0.00	2020-05-16 11:32:56	2020-05-16 11:32:56	\N
\.


--
-- TOC entry 3705 (class 0 OID 16628)
-- Dependencies: 265
-- Data for Name: project_worksub_ds; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.project_worksub_ds (id, project_worksub_id, m_item_id, amount, m_unit_id, base_price, buy_date, created_at, updated_at, deleted_at) FROM stdin;
83	53	439	10.00	17	0.00	\N	2020-05-16 11:33:44	2020-05-16 12:35:32	\N
\.


--
-- TOC entry 3707 (class 0 OID 16633)
-- Dependencies: 267
-- Data for Name: project_worksubs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.project_worksubs (id, project_work_id, name, base_price, amount, m_unit_id, work_start, work_end, created_at, updated_at, deleted_at) FROM stdin;
53	42	pemotongan kayu	1000.00	5.00	15	2020-05-18	2020-05-19	2020-05-16 11:33:34	2020-05-16 11:33:34	\N
\.


--
-- TOC entry 3709 (class 0 OID 16638)
-- Dependencies: 269
-- Data for Name: projects; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.projects (id, site_id, name, area, base_price, sale_status, customer_id, created_at, updated_at, deleted_at, product_id, order_id) FROM stdin;
262	1	Project galangan pintu 2x2	\N	0.00	Available	92	2020-05-16 11:32:01	2020-05-16 11:32:01	\N	2	25
\.


--
-- TOC entry 3711 (class 0 OID 16646)
-- Dependencies: 271
-- Data for Name: purchase_ds; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.purchase_ds (id, purchase_id, m_item_id, amount, m_unit_id, base_price, created_at, updated_at, deleted_at, buy_date) FROM stdin;
489	148	439	10.00	17	1000.00	2020-05-16 11:27:42	2020-05-16 11:27:42	\N	\N
490	149	439	10.00	17	2000.00	2020-05-16 11:28:40	2020-05-16 11:28:40	\N	\N
\.


--
-- TOC entry 3713 (class 0 OID 16651)
-- Dependencies: 273
-- Data for Name: purchases; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.purchases (id, no, base_price, m_supplier_id, wop, is_closed, is_special, site_id, purchase_date, created_at, updated_at, deleted_at, is_receive, ekspedisi) FROM stdin;
148	110/04/05/20/018	10000.00	4	cash	t	f	1	2020-05-16 00:00:00	2020-05-16 11:27:42	2020-05-16 11:28:03	\N	t	Supriadi
149	110/04/05/20/019	20000.00	5	cash	t	f	1	2020-05-16 00:00:00	2020-05-16 11:28:40	2020-05-16 11:29:07	\N	t	Supriadi
\.


--
-- TOC entry 3716 (class 0 OID 16660)
-- Dependencies: 276
-- Data for Name: rab_request_ds; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.rab_request_ds (id, no, rab_request_id, additional_work, is_approved, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 3717 (class 0 OID 16667)
-- Dependencies: 277
-- Data for Name: rab_requests; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.rab_requests (id, no, sale_trx_id, amount, is_approved, created_at, updated_at, deleted_at, additional_work, project_id, amount_requested, customer_id) FROM stdin;
\.


--
-- TOC entry 3719 (class 0 OID 16675)
-- Dependencies: 279
-- Data for Name: rabs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.rabs (id, project_id, no, base_price, is_final, stats_code, created_at, updated_at, deleted_at, order_d_id) FROM stdin;
36	262	110/RAB/05/20/004	0.00	t		2020-05-16 11:32:33	2020-05-16 11:35:03	\N	39
\.


--
-- TOC entry 3722 (class 0 OID 16683)
-- Dependencies: 282
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.roles (id, role_code, role_name, created_at, updated_at, created_by, updated_by) FROM stdin;
1	SPR_ADM	Super Admin	2019-02-03 21:36:02	2019-02-03 21:36:02	jay	jay
2	DIR_UT	DIREKTUR UTAMA	\N	\N	\N	\N
3	FIN_ACC_MGR	FINANCE & ACCOUNTING MANAGER	\N	\N	\N	\N
4	SIT_PLA_SPV	SITE PLANNING SUPERVISOR	2019-05-29 18:39:18	2019-05-29 18:39:18		
5	INV_MAN	INVENTORY MAN	2019-05-29 18:39:56	2019-05-29 18:39:56		
6	SALESMAN	SALESMAN	2019-06-30 15:56:22	2019-06-30 15:56:22		
7	MKT_ADM	ADMIN MARKETING	2019-08-05 22:58:26	\N	\N	\N
\.


--
-- TOC entry 3723 (class 0 OID 16689)
-- Dependencies: 283
-- Data for Name: sale_trx_docs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sale_trx_docs (id, sale_trx_id, no, name, due_date, is_checked, created_at, updated_at, deleted_at, m_doc_type_id) FROM stdin;
\.


--
-- TOC entry 3725 (class 0 OID 16697)
-- Dependencies: 285
-- Data for Name: sale_trx_ds; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sale_trx_ds (id, sale_trx_id, trx_d_code, seq_no, tenor, due_day, amount, due_date, project_id, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 3727 (class 0 OID 16708)
-- Dependencies: 287
-- Data for Name: sale_trx_kpr_bank_payments; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sale_trx_kpr_bank_payments (id, sale_trx_id, m_kpr_bank_payment_id, plan_at, payment_amount, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 3729 (class 0 OID 16713)
-- Dependencies: 289
-- Data for Name: sale_trxes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sale_trxes (id, no, customer_id, m_employee_id, follow_history_id, trx_type, payment_method, total_amount, base_amount, cash_amount, nup_planned_date, spu_planned_date, project_id, created_at, updated_at, deleted_at, dp_inhouse_amount, dp_kpr_amount, is_printed, is_validated, bank_account, total_discount, additional_amount, ppn_amount, pbhtb_amount, address, sale_trx_id, specup_amount, fasum_fee, notary_fee, booking_amount, owner_name, residence_address, legal_address, deal_type, residence_rt, residence_rw, residence_kelurahan, residence_kecamatan, residence_city, residence_zipcode, legal_rt, legal_rw, legal_kelurahan, legal_kecamatan, legal_city, legal_zipcode) FROM stdin;
\.


--
-- TOC entry 3732 (class 0 OID 16723)
-- Dependencies: 292
-- Data for Name: sites; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sites (id, name, m_city_id, address, created_at, updated_at, deleted_at, code) FROM stdin;
1	SE	1	\N	2019-03-25 21:01:20	2019-03-25 21:01:22	\N	110
2	PHSL	1	\N	2019-03-25 21:01:20	2019-03-25 21:01:22	\N	112
7	HO	1	\N	2019-05-26 15:35:37.212754	2019-05-26 15:35:37.212754	\N	100
8	Konstruksi SE	1	\N	2019-05-26 15:36:04.293492	2019-05-26 15:36:04.293492	\N	810
9	Konstruksi PHSL	1	\N	2019-05-26 15:36:32.134299	2019-05-26 15:36:32.134299	\N	811
\.


--
-- TOC entry 3734 (class 0 OID 16728)
-- Dependencies: 294
-- Data for Name: stock_opname_ds; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.stock_opname_ds (id, stock_opname_id, m_item_id, amount, real_amount, notes, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 3736 (class 0 OID 16736)
-- Dependencies: 296
-- Data for Name: stock_opnames; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.stock_opnames (id, no, site_id, date, created_at, updated_at, deleted_at, is_closed) FROM stdin;
\.


--
-- TOC entry 3757 (class 0 OID 26329)
-- Dependencies: 317
-- Data for Name: tbl_absensi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tbl_absensi (id_absensi, m_employee_id, jam_datang, tanggal, id_shift, durasi_lembur, uang_lembur, keterangan, dtm_crt, dtm_upd, jam_pulang) FROM stdin;
1	1	00:59:00	2020-04-21	\N	\N	\N	\N	\N	2020-04-21 01:47:49	18:00:00
2	2	08:00:00	2020-03-01	\N	\N	\N	\N	\N	\N	00:00:00
3	2	08:00:00	2020-03-02	\N	\N	\N	\N	\N	\N	12:00:00
4	2	08:00:00	2020-03-03	\N	\N	\N	\N	\N	\N	12:00:00
5	2	08:00:00	2020-03-04	\N	\N	\N	\N	\N	\N	12:00:00
6	2	08:00:00	2020-03-05	\N	\N	\N	\N	\N	\N	12:00:00
7	2	08:00:00	2020-03-06	\N	\N	\N	\N	\N	\N	12:00:00
8	2	08:00:00	2020-03-07	\N	\N	\N	\N	\N	\N	12:00:00
9	2	08:00:00	2020-03-08	\N	\N	\N	\N	\N	\N	12:00:00
10	2	08:00:00	2020-03-09	\N	\N	\N	\N	\N	\N	12:00:00
11	2	08:00:00	2020-03-10	\N	\N	\N	\N	\N	\N	12:00:00
12	2	08:00:00	2020-03-11	\N	\N	\N	\N	\N	\N	12:00:00
13	2	08:00:00	2020-03-12	\N	\N	\N	\N	\N	\N	12:00:00
14	2	08:00:00	2020-03-13	\N	\N	\N	\N	\N	\N	12:00:00
15	2	08:00:00	2020-03-14	\N	\N	\N	\N	\N	\N	12:00:00
16	2	08:00:00	2020-03-15	\N	\N	\N	\N	\N	\N	12:00:00
17	2	08:00:00	2020-03-16	\N	\N	\N	\N	\N	\N	12:00:00
18	2	08:00:00	2020-03-17	\N	\N	\N	\N	\N	\N	12:00:00
19	2	08:00:00	2020-03-18	\N	\N	\N	\N	\N	\N	12:00:00
20	2	08:00:00	2020-03-19	\N	\N	\N	\N	\N	\N	12:00:00
21	2	08:00:00	2020-03-20	\N	\N	\N	\N	\N	\N	12:00:00
22	2	08:00:00	2020-03-21	\N	\N	\N	\N	\N	\N	12:00:00
23	2	08:00:00	2020-03-22	\N	\N	\N	\N	\N	\N	12:00:00
24	2	08:00:00	2020-03-23	\N	\N	\N	\N	\N	\N	12:00:00
25	2	08:00:00	2020-03-24	\N	\N	\N	\N	\N	\N	12:00:00
26	2	08:00:00	2020-03-25	\N	\N	\N	\N	\N	\N	12:00:00
27	2	08:00:00	2020-03-26	\N	\N	\N	\N	\N	\N	12:00:00
28	2	08:00:00	2020-03-27	\N	\N	\N	\N	\N	\N	12:00:00
29	2	08:00:00	2020-03-28	\N	\N	\N	\N	\N	\N	12:00:00
30	2	08:00:00	2020-03-29	\N	\N	\N	\N	\N	\N	12:00:00
31	2	08:00:00	2020-03-30	\N	\N	\N	\N	\N	\N	12:00:00
32	2	08:00:00	2020-03-31	\N	\N	\N	\N	\N	\N	12:00:00
33	2	08:00:00	2020-04-23	\N	\N	\N	\N	\N	2020-04-23 02:44:26	15:00:00
34	2	\N	2020-04-24	\N	\N	\N	\N	\N	\N	\N
\.


--
-- TOC entry 3747 (class 0 OID 18063)
-- Dependencies: 307
-- Data for Name: tbl_akun; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tbl_akun (id_akun, no_akun, nama_akun, level, id_main_akun, sifat_debit, sifat_kredit, dtm_crt, dtm_upd) FROM stdin;
1	1	Harta	0	0	1	0	2020-04-16 11:34:25.750294	2020-04-16 11:34:25.750294
2	2	Hutang	0	0	0	1	2020-04-16 11:34:25.750294	2020-04-16 11:34:25.750294
3	3	Modal	0	0	0	1	2020-04-16 11:34:25.750294	2020-04-16 11:34:25.750294
4	4	Pendapatan	0	0	0	1	2020-04-16 11:34:25.750294	2020-04-16 11:34:25.750294
5	5	Beban	0	0	1	0	2020-04-16 11:34:25.750294	2020-04-16 11:34:25.750294
8	1.1.0.0	Kas	1	1	1	0	2020-04-16 12:12:59.821141	2020-04-16 12:12:59.821141
9	1.2.0.0	Piutang	1	1	1	0	2020-04-16 14:17:11.197047	2020-04-16 14:17:11.197047
\.


--
-- TOC entry 3749 (class 0 OID 18071)
-- Dependencies: 309
-- Data for Name: tbl_akun_detail; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tbl_akun_detail (id_akun_d, id_akun, id_parent, turunan1, turunan2, turunan3, dtm_crt, dtm_upd) FROM stdin;
1	8	1	0	0	0	2020-04-16 12:12:59.825502	2020-04-16 12:12:59.825502
2	9	1	0	0	0	2020-04-16 14:17:11.205761	2020-04-16 14:17:11.205761
\.


--
-- TOC entry 3759 (class 0 OID 26340)
-- Dependencies: 319
-- Data for Name: tbl_cuti; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tbl_cuti (id, m_employee_id, tanggal, dtm_crt, dtm_upd) FROM stdin;
1	1	2020-04-25	\N	\N
2	1	2020-04-18	2020-04-23 11:23:25.89271	2020-04-23 11:23:25.89271
\.


--
-- TOC entry 3767 (class 0 OID 26372)
-- Dependencies: 327
-- Data for Name: tbl_other_gaji; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tbl_other_gaji (id, potongan_bpjs, cicilan, kasbon, komisi_langsung, bulan, m_employee_id, dtm_crt, dtm_upd) FROM stdin;
\.


--
-- TOC entry 3761 (class 0 OID 26348)
-- Dependencies: 321
-- Data for Name: tbl_ref_gaji; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tbl_ref_gaji (id_ref_gaji, id_jabatan, gaji_pokok, uang_kehadiran, uang_makan, uang_transport, uang_lembur, dtm_crt, dtm_upd) FROM stdin;
\.


--
-- TOC entry 3751 (class 0 OID 18089)
-- Dependencies: 311
-- Data for Name: tbl_saldo_akun; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tbl_saldo_akun (id_saldo, id_akun, tanggal, jumlah_saldo, is_updated, dtm_crt, dtm_upd, location_id) FROM stdin;
\.


--
-- TOC entry 3763 (class 0 OID 26356)
-- Dependencies: 323
-- Data for Name: tbl_setting_gaji; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tbl_setting_gaji (id_setting_gaji, m_employee_id, gaji_pokok, denda, komisi, dtm_crt, dtm_upd, denda_telat) FROM stdin;
2	2	4000000	80000	0	\N	2020-04-24 02:43:16	100
3	3	1300000	3000	0	2020-04-24 10:02:07.187507	2020-04-24 10:02:07.187507	100
\.


--
-- TOC entry 3765 (class 0 OID 26364)
-- Dependencies: 325
-- Data for Name: tbl_shift; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tbl_shift (id_shift, nama_shift, jam_datang, jam_pulang, site_id, dtm_crt, dtm_upd) FROM stdin;
\.


--
-- TOC entry 3753 (class 0 OID 18102)
-- Dependencies: 313
-- Data for Name: tbl_trx_akuntansi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tbl_trx_akuntansi (id_trx_akun, deskripsi, tanggal, dtm_crt, dtm_upd, location_id) FROM stdin;
1	adsf	2020-04-16	\N	\N	1
\.


--
-- TOC entry 3755 (class 0 OID 18113)
-- Dependencies: 315
-- Data for Name: tbl_trx_akuntansi_detail; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tbl_trx_akuntansi_detail (id_trx_akun_detail, id_trx_akun, id_akun, jumlah, tipe, keterangan, dtm_crt, dtm_upd) FROM stdin;
1	1	8	10000	DEBIT	akun	\N	\N
2	1	9	10000	KREDIT	lawan	\N	\N
\.


--
-- TOC entry 3738 (class 0 OID 16741)
-- Dependencies: 298
-- Data for Name: transfer_stock_ds; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.transfer_stock_ds (id, transfer_stock_id, m_item_id, amount, m_unit_id, notes, created_at, updated_at, deleted_at, actual_amount) FROM stdin;
10	10	1109	800	20	-	2019-05-30 15:31:50	2019-05-30 15:45:17	\N	800
11	11	425	800	20	-	2019-05-30 16:13:13	2019-05-30 17:09:55	\N	800
14	14	425	1000	20	-	2019-06-13 19:20:48	2019-06-13 19:20:48	\N	\N
12	12	425	600	20	-	2019-05-31 10:19:21	2019-06-13 20:22:11	\N	600
13	13	425	800	20	-	2019-06-13 19:18:41	2019-06-13 20:22:29	\N	800
15	15	1217	2	21	-	2019-06-14 15:16:36	2019-06-14 15:16:36	\N	\N
16	16	1186	4	14	BUTUH	2019-06-17 23:34:50	2019-06-17 23:35:54	\N	4
17	17	1339	1	17	BUTUH	2019-06-24 15:54:33	2019-06-24 15:55:58	\N	1
18	18	1091	5000	20	BUTUH	2019-07-15 16:12:30	2019-07-15 16:12:30	\N	\N
19	19	1165	8	14	pinjam stok karna stok habis	2019-07-15 22:41:58	2019-07-15 22:41:58	\N	\N
\.


--
-- TOC entry 3740 (class 0 OID 16749)
-- Dependencies: 300
-- Data for Name: transfer_stocks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.transfer_stocks (id, site_from, site_to, due_date_receive, is_sent, is_receive, shipping, updated_at, deleted_at, no, created_at) FROM stdin;
10	2	1	2019-05-30	t	t	\N	2019-05-30 15:46:36	\N	110/10/05/19/001	2019-05-30 15:31:50
11	2	1	2019-05-30	t	t	\N	2019-05-30 17:14:07	\N	110/10/05/19/002	2019-05-30 16:13:13
12	2	1	2019-05-31	t	\N	\N	2019-06-13 20:22:09	\N	110/10/05/19/003	2019-05-31 10:19:21
13	9	1	2019-11-06	t	\N	\N	2019-06-13 20:22:26	\N	110/10/06/19/001	2019-06-13 19:18:40
14	9	1	2019-12-06	t	\N	\N	2019-06-13 20:22:46	\N	110/10/06/19/002	2019-06-13 19:20:47
15	8	2	2019-06-14	\N	\N	\N	2019-06-14 15:16:29	\N	112/10/06/19/001	2019-06-14 15:16:29
16	1	2	2019-06-18	t	t	\N	2019-06-17 23:37:06	\N	112/10/06/19/002	2019-06-17 23:34:49
17	1	2	2019-06-25	t	t	\N	2019-06-24 15:56:51	\N	112/10/06/19/003	2019-06-24 15:54:32
18	2	1	2019-07-16	\N	\N	\N	2019-07-15 16:12:30	\N	110/10/07/19/001	2019-07-15 16:12:30
19	2	1	2019-07-22	\N	\N	\N	2019-07-15 22:41:57	\N	110/10/07/19/002	2019-07-15 22:41:57
\.


--
-- TOC entry 3743 (class 0 OID 16756)
-- Dependencies: 303
-- Data for Name: user_permission; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.user_permission (id, role_id, menu_id, created_at, updated_at, created_by, updated_by) FROM stdin;
3	1	4	2019-02-04 00:14:37	2019-02-04 00:14:37	\N	\N
4	1	5	2019-02-04 00:14:39	2019-02-04 00:14:39	\N	\N
5	1	7	2019-03-09 00:17:42	2019-03-09 00:17:42	\N	\N
6	1	20	2019-03-09 00:17:44	2019-03-09 00:17:44	\N	\N
7	1	18	2019-03-09 00:17:45	2019-03-09 00:17:45	\N	\N
8	1	30	2019-03-09 00:17:47	2019-03-09 00:17:47	\N	\N
9	1	9	2019-03-09 00:17:49	2019-03-09 00:17:49	\N	\N
10	1	12	2019-03-09 00:17:50	2019-03-09 00:17:50	\N	\N
11	1	1	2019-03-09 00:17:53	2019-03-09 00:17:53	\N	\N
12	1	25	2019-03-09 00:17:57	2019-03-09 00:17:57	\N	\N
13	1	37	2019-03-09 00:18:04	2019-03-09 00:18:04	\N	\N
14	1	36	2019-03-09 00:18:05	2019-03-09 00:18:05	\N	\N
16	1	11	2019-03-09 00:18:08	2019-03-09 00:18:08	\N	\N
17	1	26	2019-03-09 00:18:10	2019-03-09 00:18:10	\N	\N
18	1	29	2019-03-09 00:18:11	2019-03-09 00:18:11	\N	\N
19	1	15	2019-03-09 00:18:13	2019-03-09 00:18:13	\N	\N
20	1	16	2019-03-09 00:18:28	2019-03-09 00:18:28	\N	\N
21	1	17	2019-03-09 00:18:29	2019-03-09 00:18:29	\N	\N
22	1	19	2019-03-09 00:18:31	2019-03-09 00:18:31	\N	\N
23	1	34	2019-03-09 00:18:33	2019-03-09 00:18:33	\N	\N
25	1	13	2019-03-09 00:18:37	2019-03-09 00:18:37	\N	\N
26	1	33	2019-03-09 00:18:39	2019-03-09 00:18:39	\N	\N
27	1	28	2019-03-09 00:18:40	2019-03-09 00:18:40	\N	\N
28	1	22	2019-03-09 00:18:42	2019-03-09 00:18:42	\N	\N
29	1	27	2019-03-09 00:18:44	2019-03-09 00:18:44	\N	\N
30	1	32	2019-03-09 00:18:46	2019-03-09 00:18:46	\N	\N
31	1	8	2019-03-09 00:18:47	2019-03-09 00:18:47	\N	\N
32	1	14	2019-03-09 00:18:48	2019-03-09 00:18:48	\N	\N
33	1	23	2019-03-09 00:18:49	2019-03-09 00:18:49	\N	\N
34	1	24	2019-03-09 00:18:51	2019-03-09 00:18:51	\N	\N
35	1	31	2019-03-09 00:18:52	2019-03-09 00:18:52	\N	\N
36	1	21	2019-03-09 00:18:57	2019-03-09 00:18:57	\N	\N
37	1	35	2019-03-10 04:18:05	2019-03-10 04:18:05	\N	\N
38	1	51	2019-03-31 00:15:55	2019-03-31 00:15:55	\N	\N
39	1	50	2019-03-31 00:15:56	2019-03-31 00:15:56	\N	\N
40	1	48	2019-03-31 00:15:58	2019-03-31 00:15:58	\N	\N
41	1	49	2019-03-31 00:16:00	2019-03-31 00:16:00	\N	\N
42	1	38	2019-03-31 00:16:04	2019-03-31 00:16:04	\N	\N
43	1	40	2019-03-31 00:16:05	2019-03-31 00:16:05	\N	\N
44	1	39	2019-03-31 00:16:07	2019-03-31 00:16:07	\N	\N
45	1	44	2019-03-31 00:16:08	2019-03-31 00:16:08	\N	\N
46	1	41	2019-03-31 00:16:09	2019-03-31 00:16:09	\N	\N
47	1	46	2019-03-31 00:16:20	2019-03-31 00:16:20	\N	\N
48	1	52	2019-03-31 00:16:21	2019-03-31 00:16:21	\N	\N
49	1	45	2019-03-31 00:16:22	2019-03-31 00:16:22	\N	\N
50	1	47	2019-03-31 00:16:24	2019-03-31 00:16:24	\N	\N
51	1	42	2019-03-31 00:16:28	2019-03-31 00:16:28	\N	\N
52	1	43	2019-03-31 00:16:31	2019-03-31 00:16:31	\N	\N
53	1	53	2019-04-03 22:12:14	2019-04-03 22:12:14	\N	\N
54	1	55	2019-02-04 00:14:37	2019-02-04 00:14:37	\N	\N
55	1	54	2019-02-04 00:14:37	2019-02-04 00:14:37	\N	\N
1	1	56	2019-04-27 11:12:49	2019-04-27 11:12:49	\N	\N
56	1	57	2019-05-04 20:34:54	2019-05-04 20:34:54	\N	\N
57	1	58	2019-05-05 19:31:51	2019-05-05 19:31:51	\N	\N
58	1	60	2019-05-05 23:35:23	2019-05-05 23:35:23	\N	\N
59	1	59	2019-05-05 23:35:29	2019-05-05 23:35:29	\N	\N
60	1	61	2019-05-07 01:57:18	2019-05-07 01:57:18	\N	\N
61	1	62	2019-05-15 20:06:55	2019-05-15 20:06:55	\N	\N
62	1	63	2019-05-19 01:38:57	2019-05-19 01:38:57	\N	\N
66	1	64	2019-05-27 02:54:07	2019-05-27 02:54:07	\N	\N
67	1	65	2019-05-27 02:54:13	2019-05-27 02:54:13	\N	\N
68	1	66	2019-05-27 02:54:17	2019-05-27 02:54:17	\N	\N
69	2	64	2019-05-29 18:47:26	2019-05-29 18:47:26	\N	\N
70	2	38	2019-05-29 18:47:27	2019-05-29 18:47:27	\N	\N
71	2	53	2019-05-29 18:47:28	2019-05-29 18:47:28	\N	\N
72	2	2	2019-05-29 18:47:29	2019-05-29 18:47:29	\N	\N
73	2	4	2019-05-29 18:47:30	2019-05-29 18:47:30	\N	\N
74	2	52	2019-05-29 18:47:31	2019-05-29 18:47:31	\N	\N
75	2	15	2019-05-29 18:47:34	2019-05-29 18:47:34	\N	\N
76	2	5	2019-05-29 18:47:35	2019-05-29 18:47:35	\N	\N
77	2	7	2019-05-29 18:47:36	2019-05-29 18:47:36	\N	\N
78	2	30	2019-05-29 18:47:37	2019-05-29 18:47:37	\N	\N
79	2	9	2019-05-29 18:47:54	2019-05-29 18:47:54	\N	\N
80	2	12	2019-05-29 18:47:55	2019-05-29 18:47:55	\N	\N
81	2	26	2019-05-29 18:47:57	2019-05-29 18:47:57	\N	\N
82	2	1	2019-05-29 18:47:58	2019-05-29 18:47:58	\N	\N
83	2	56	2019-05-29 18:47:59	2019-05-29 18:47:59	\N	\N
84	2	65	2019-05-29 18:48:00	2019-05-29 18:48:00	\N	\N
85	2	62	2019-05-29 18:48:01	2019-05-29 18:48:01	\N	\N
86	2	36	2019-05-29 18:48:02	2019-05-29 18:48:02	\N	\N
87	2	54	2019-05-29 18:48:03	2019-05-29 18:48:03	\N	\N
88	2	55	2019-05-29 18:48:05	2019-05-29 18:48:05	\N	\N
89	2	42	2019-05-29 18:48:07	2019-05-29 18:48:07	\N	\N
90	2	60	2019-05-29 18:48:08	2019-05-29 18:48:08	\N	\N
91	2	11	2019-05-29 18:48:09	2019-05-29 18:48:09	\N	\N
92	2	43	2019-05-29 18:48:10	2019-05-29 18:48:10	\N	\N
93	2	66	2019-05-29 18:48:14	2019-05-29 18:48:14	\N	\N
94	2	63	2019-05-29 18:48:15	2019-05-29 18:48:15	\N	\N
95	2	59	2019-05-29 18:48:16	2019-05-29 18:48:16	\N	\N
97	2	34	2019-05-29 18:48:18	2019-05-29 18:48:18	\N	\N
98	2	35	2019-05-29 18:48:21	2019-05-29 18:48:21	\N	\N
99	2	13	2019-05-29 18:48:22	2019-05-29 18:48:22	\N	\N
100	2	33	2019-05-29 18:48:23	2019-05-29 18:48:23	\N	\N
101	2	32	2019-05-29 18:48:24	2019-05-29 18:48:24	\N	\N
102	2	8	2019-05-29 18:48:25	2019-05-29 18:48:25	\N	\N
103	2	57	2019-05-29 18:48:26	2019-05-29 18:48:26	\N	\N
104	2	61	2019-05-29 18:48:28	2019-05-29 18:48:28	\N	\N
105	2	23	2019-05-29 18:48:29	2019-05-29 18:48:29	\N	\N
106	2	31	2019-05-29 18:48:30	2019-05-29 18:48:30	\N	\N
107	2	21	2019-05-29 18:48:31	2019-05-29 18:48:31	\N	\N
108	3	4	2019-05-29 18:50:01	2019-05-29 18:50:01	\N	\N
109	3	21	2019-05-29 18:50:10	2019-05-29 18:50:10	\N	\N
110	3	61	2019-05-29 18:50:32	2019-05-29 18:50:32	\N	\N
111	3	62	2019-05-29 18:51:32	2019-05-29 18:51:32	\N	\N
112	3	5	2019-05-29 18:51:53	2019-05-29 18:51:53	\N	\N
113	3	2	2019-05-29 18:51:58	2019-05-29 18:51:58	\N	\N
114	4	4	2019-05-29 18:53:27	2019-05-29 18:53:27	\N	\N
115	4	5	2019-05-29 18:53:39	2019-05-29 18:53:39	\N	\N
116	4	2	2019-05-29 18:53:44	2019-05-29 18:53:44	\N	\N
117	4	13	2019-05-29 18:53:54	2019-05-29 18:53:54	\N	\N
118	4	15	2019-05-29 18:53:56	2019-05-29 18:53:56	\N	\N
119	4	36	2019-05-29 18:54:01	2019-05-29 18:54:01	\N	\N
120	4	21	2019-05-29 18:54:08	2019-05-29 18:54:08	\N	\N
121	4	64	2019-05-29 18:54:17	2019-05-29 18:54:17	\N	\N
122	4	65	2019-05-29 18:54:18	2019-05-29 18:54:18	\N	\N
123	4	66	2019-05-29 18:54:20	2019-05-29 18:54:20	\N	\N
124	4	26	2019-05-29 18:54:26	2019-05-29 18:54:26	\N	\N
125	4	62	2019-05-29 18:54:28	2019-05-29 18:54:28	\N	\N
126	4	56	2019-05-29 18:54:54	2019-05-29 18:54:54	\N	\N
127	4	11	2019-05-29 18:54:54	2019-05-29 18:54:54	\N	\N
128	4	60	2019-05-29 18:54:55	2019-05-29 18:54:55	\N	\N
129	4	63	2019-05-29 18:55:05	2019-05-29 18:55:05	\N	\N
130	4	57	2019-05-29 18:55:12	2019-05-29 18:55:12	\N	\N
131	4	61	2019-05-29 18:55:20	2019-05-29 18:55:20	\N	\N
132	4	23	2019-05-29 18:55:22	2019-05-29 18:55:22	\N	\N
133	4	58	2019-05-29 18:55:42	2019-05-29 18:55:42	\N	\N
134	4	59	2019-05-29 18:55:49	2019-05-29 18:55:49	\N	\N
135	4	7	2019-05-29 18:56:18	2019-05-29 18:56:18	\N	\N
136	4	9	2019-05-29 18:56:20	2019-05-29 18:56:20	\N	\N
137	4	12	2019-05-29 18:56:22	2019-05-29 18:56:22	\N	\N
138	4	33	2019-05-29 18:56:59	2019-05-29 18:56:59	\N	\N
139	4	35	2019-05-29 18:57:00	2019-05-29 18:57:00	\N	\N
140	4	34	2019-05-29 18:57:06	2019-05-29 18:57:06	\N	\N
141	5	5	2019-05-29 18:58:12	2019-05-29 18:58:12	\N	\N
142	5	2	2019-05-29 18:58:16	2019-05-29 18:58:16	\N	\N
143	5	4	2019-05-29 18:58:23	2019-05-29 18:58:23	\N	\N
144	5	15	2019-05-29 18:58:31	2019-05-29 18:58:31	\N	\N
145	5	13	2019-05-29 18:58:32	2019-05-29 18:58:32	\N	\N
146	5	36	2019-05-29 18:58:37	2019-05-29 18:58:37	\N	\N
147	5	11	2019-05-29 18:58:58	2019-05-29 18:58:58	\N	\N
148	5	26	2019-05-29 18:59:15	2019-05-29 18:59:15	\N	\N
150	5	56	2019-05-29 18:59:21	2019-05-29 18:59:21	\N	\N
151	5	65	2019-05-29 18:59:30	2019-05-29 18:59:30	\N	\N
152	5	66	2019-05-29 18:59:52	2019-05-29 18:59:52	\N	\N
153	5	63	2019-05-29 18:59:53	2019-05-29 18:59:53	\N	\N
154	5	57	2019-05-29 19:00:00	2019-05-29 19:00:00	\N	\N
155	5	23	2019-05-29 19:00:07	2019-05-29 19:00:07	\N	\N
156	5	58	2019-05-29 19:00:13	2019-05-29 19:00:13	\N	\N
157	5	60	2019-05-29 19:00:18	2019-05-29 19:00:18	\N	\N
158	5	59	2019-05-29 19:00:24	2019-05-29 19:00:24	\N	\N
159	5	21	2019-05-29 19:19:41	2019-05-29 19:19:41	\N	\N
160	1	67	2019-06-29 10:42:39	2019-06-29 10:42:39	\N	\N
161	1	68	2019-07-08 04:15:50	2019-07-08 04:15:50	\N	\N
162	1	69	2019-07-08 04:27:35	2019-07-08 04:27:35	\N	\N
163	1	70	2019-07-08 04:33:02	2019-07-08 04:33:02	\N	\N
164	1	71	2019-07-08 04:38:13	2019-07-08 04:38:13	\N	\N
165	1	72	2019-07-08 04:41:19	2019-07-08 04:41:19	\N	\N
166	1	73	2019-07-08 04:41:20	2019-07-08 04:41:20	\N	\N
167	1	75	2019-07-08 04:41:28	2019-07-08 04:41:28	\N	\N
168	1	74	2019-07-08 04:41:31	2019-07-08 04:41:31	\N	\N
169	1	77	2019-07-10 16:49:38	2019-07-10 16:49:38	\N	\N
171	1	79	2019-07-10 16:50:57	2019-07-10 16:50:57	\N	\N
172	1	78	2019-07-10 16:51:01	2019-07-10 16:51:01	\N	\N
173	1	80	2019-07-10 16:53:49	2019-07-10 16:53:49	\N	\N
174	1	82	2019-07-10 17:03:38	2019-07-10 17:03:38	\N	\N
175	1	81	2019-07-10 17:03:39	2019-07-10 17:03:39	\N	\N
176	1	83	2019-07-10 17:13:09	2019-07-10 17:13:09	\N	\N
177	1	85	2019-07-10 17:13:10	2019-07-10 17:13:10	\N	\N
178	1	84	2019-07-10 17:13:11	2019-07-10 17:13:11	\N	\N
179	1	86	2019-07-10 17:13:12	2019-07-10 17:13:12	\N	\N
180	1	87	2019-07-10 17:14:56	2019-07-10 17:14:56	\N	\N
181	1	88	2019-07-14 12:05:33	2019-07-14 12:05:33	\N	\N
182	1	89	2019-07-14 12:05:34	2019-07-14 12:05:34	\N	\N
183	1	90	2019-07-20 08:31:32	2019-07-20 08:31:32		
184	1	92	2019-07-20 15:03:42	2019-07-20 15:03:42		
185	1	93	2019-07-22 15:31:38	2019-07-22 15:31:38	\N	\N
186	1	94	2019-07-30 23:07:19	2019-07-30 23:07:19	\N	\N
187	1	95	2019-07-31 13:29:03	2019-07-31 13:29:03	\N	\N
96	1	58	2019-05-29 18:48:16	2019-05-29 18:48:16	\N	\N
188	2	96	2019-08-01 14:03:50	2019-08-01 14:03:50	\N	\N
190	7	89	2019-08-05 23:03:43	2019-08-05 23:03:43	\N	\N
192	7	5	2019-08-05 23:04:21	2019-08-05 23:04:21	\N	\N
193	7	54	2019-08-05 23:04:40	2019-08-05 23:04:40	\N	\N
194	7	43	2019-08-05 23:04:41	2019-08-05 23:04:41	\N	\N
195	7	53	2019-08-05 23:05:01	2019-08-05 23:05:01	\N	\N
196	7	55	2019-08-05 23:05:01	2019-08-05 23:05:01	\N	\N
197	7	52	2019-08-05 23:05:16	2019-08-05 23:05:16	\N	\N
198	7	42	2019-08-05 23:05:17	2019-08-05 23:05:17	\N	\N
199	7	80	2019-08-05 23:06:14	2019-08-05 23:06:14	\N	\N
200	7	38	2019-08-05 23:06:23	2019-08-05 23:06:23	\N	\N
201	7	88	2019-08-05 23:06:26	2019-08-05 23:06:26	\N	\N
202	7	97	2019-08-05 23:06:32	2019-08-05 23:06:32	\N	\N
203	7	2	2019-08-05 23:09:15	2019-08-05 23:09:15	\N	\N
204	7	96	2019-08-05 23:09:17	2019-08-05 23:09:17	\N	\N
207	1	98	2019-09-26 10:01:18	2019-09-26 10:01:18	\N	\N
208	1	99	2020-04-16 10:11:32	2020-04-16 10:11:32	\N	\N
209	1	100	2020-04-16 10:19:52	2020-04-16 10:19:52	\N	\N
210	1	103	2020-04-16 12:45:37	2020-04-16 12:45:37	\N	\N
211	1	101	2020-04-16 12:45:41	2020-04-16 12:45:41	\N	\N
212	1	102	2020-04-16 12:45:42	2020-04-16 12:45:42	\N	\N
213	1	104	2020-04-17 09:24:07	2020-04-17 09:24:07	\N	\N
214	1	105	2020-04-20 08:36:32	2020-04-20 08:36:32	\N	\N
215	1	106	2020-04-20 08:36:41	2020-04-20 08:36:41	\N	\N
216	1	107	2020-04-20 09:47:54	2020-04-20 09:47:54	\N	\N
217	1	108	2020-04-20 09:53:42	2020-04-20 09:53:42	\N	\N
218	1	109	2020-04-20 12:39:06	2020-04-20 12:39:06	\N	\N
219	1	96	2020-04-21 07:26:27	2020-04-21 07:26:27	\N	\N
220	1	2	2020-04-21 07:26:38	2020-04-21 07:26:38	\N	\N
221	1	110	2020-04-23 08:46:34	2020-04-23 08:46:34	\N	\N
222	1	111	2020-04-23 10:01:59	2020-04-23 10:01:59	\N	\N
223	1	112	2020-05-08 08:29:57	2020-05-08 08:29:57	\N	\N
224	1	113	2020-05-08 13:34:16	2020-05-08 13:34:16	\N	\N
\.


--
-- TOC entry 3745 (class 0 OID 16764)
-- Dependencies: 305
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, is_active, is_deleted, role_id, site_id, m_employee_id) FROM stdin;
8	UBAIDILLAH	obetubaid@phsl.com	\N	$2y$10$Uu6paXM9WHqMqq95iJy3h.z3x1gOP9UR16Xyopom8uD0eNuLqSbzu	MtJlnhacP2WFzSRJyfW8QL5E29FMku5tvnJGfYez5chRHkCo6COydhvyqOw3	\N	\N	1	f	4	2	\N
10	FERI MEI SANDI	poeteloer@se.com	\N	$2y$10$Uu6paXM9WHqMqq95iJy3h.z3x1gOP9UR16Xyopom8uD0eNuLqSbzu	Qe05GX7XThv7mkO6e68AhuQQ1iwXYsr7KQzafZGoCgE5dADDgrnBCMHkZSsk	\N	\N	1	f	5	1	\N
3	HARDIONO	hardiono@ho.com	\N	$2y$10$Uu6paXM9WHqMqq95iJy3h.z3x1gOP9UR16Xyopom8uD0eNuLqSbzu	w3p9qmrJUFex2uTUXuDdwolfCVZL5eQ03FleVN1UQb9yFijykg3nr5nSaGw1	\N	\N	1	f	3	7	\N
5	HARDIONO	hardiono@phsl.com	\N	$2y$10$Uu6paXM9WHqMqq95iJy3h.z3x1gOP9UR16Xyopom8uD0eNuLqSbzu	\N	\N	\N	1	f	3	2	\N
6	UBAIDILLAH	obetubaid@ho.com	\N	$2y$10$Uu6paXM9WHqMqq95iJy3h.z3x1gOP9UR16Xyopom8uD0eNuLqSbzu	\N	\N	\N	1	f	4	7	\N
9	UBAIDILLAH	obetubaid_inventory@ho.com	\N	$2y$10$Uu6paXM9WHqMqq95iJy3h.z3x1gOP9UR16Xyopom8uD0eNuLqSbzu	\N	\N	\N	1	f	5	7	\N
4	HARDIONO	hardiono@se.com	\N	$2y$10$Uu6paXM9WHqMqq95iJy3h.z3x1gOP9UR16Xyopom8uD0eNuLqSbzu	dXsOVU2oNr5zkDEAiUGKj0r4B766XLAp4qTiPDV5vS48eyWJYnzp7paaNaZa	\N	\N	1	f	3	1	\N
7	UBAIDILLAH	obetubaid@se.com	\N	$2y$10$Uu6paXM9WHqMqq95iJy3h.z3x1gOP9UR16Xyopom8uD0eNuLqSbzu	roe7r2w8xnULQMrsUN8SOuCVH2TTP9gEBxGA5qT3YI54zTu1qYwfzqGrH1P3	\N	\N	1	f	4	1	\N
2	ZAINUL HERMAWAN	zainul.hermawan@gmail.com	\N	$2y$10$Uu6paXM9WHqMqq95iJy3h.z3x1gOP9UR16Xyopom8uD0eNuLqSbzu	g7H58xj4B3drZn7cjrblMKVWWAQ2UCKSbiynHvMMmV7lbFGl9CIvU4MtRcZC	\N	\N	1	f	2	7	\N
11	RIAN ASYARI	ryanasyari7@phsl.com	\N	$2y$10$Uu6paXM9WHqMqq95iJy3h.z3x1gOP9UR16Xyopom8uD0eNuLqSbzu	hKZDVpCgaVWFapBTNDIFqSyuLu9NKBzWCaa2SpDxVOPY8pqPwOvaledmKF5R	\N	\N	1	f	5	2	\N
17	Arif	arif.se@ptsp.com	\N	$2y$10$Uu6paXM9WHqMqq95iJy3h.z3x1gOP9UR16Xyopom8uD0eNuLqSbzu	\N	\N	\N	1	f	1	1	\N
19	Sales 1	sales1@ptsp.com	\N	$2y$10$Uu6paXM9WHqMqq95iJy3h.z3x1gOP9UR16Xyopom8uD0eNuLqSbzu	\N	\N	\N	1	\N	6	1	1
20	Sales 2	sales2@ptsp.com	\N	$2y$10$Uu6paXM9WHqMqq95iJy3h.z3x1gOP9UR16Xyopom8uD0eNuLqSbzu	\N	\N	\N	1	\N	6	1	2
21	Sales 3	sales3@ptsp.com	\N	$2y$10$Uu6paXM9WHqMqq95iJy3h.z3x1gOP9UR16Xyopom8uD0eNuLqSbzu	\N	\N	\N	1	\N	6	1	3
22	Sales 4	sales4@ptsp.com	\N	$2y$10$Uu6paXM9WHqMqq95iJy3h.z3x1gOP9UR16Xyopom8uD0eNuLqSbzu	\N	\N	\N	1	\N	6	1	4
23	Sales 5	sales5@ptsp.com	\N	$2y$10$Uu6paXM9WHqMqq95iJy3h.z3x1gOP9UR16Xyopom8uD0eNuLqSbzu	\N	\N	\N	1	\N	6	1	5
18	test	test@gmail.com	\N	$2y$10$.KvxTwFJQSzV.7jvZ8jB3erS5IClZ3p2rn4x5aD5N5SwLNeci4Ad6	WHUMXSPJufROxGwcRq2aMhqSlJA5HUwfpWzLbfnl0hVNMWLfK6T9s6tFEeAS	\N	\N	1	f	1	1	\N
25	poci	poci@gmail.com	\N	$2y$10$2Gl1xWXiIliEW.941I0JB.x7z0E9TYSOa1yMGXmmh59s8Af0tTWma	zCsZaWFGlkvZ4zOld6Bsso1PxRVWkR9YEnICZYT0t2oergEoq0vBANQNe9rj	\N	\N	1	f	1	1	\N
1	Jay	jay@gmail.com	\N	$2y$10$Uu6paXM9WHqMqq95iJy3h.z3x1gOP9UR16Xyopom8uD0eNuLqSbzu	FNapA6e0ZfzBlWrqZEHcAKxaHZPOXYBVnYnXPIu7GC6mItN8cL08ruKWmF4p	2019-01-26 14:01:12	2019-01-26 14:01:12	1	f	1	1	\N
24	jafar	mjafar747@gmail.com	\N	$2y$10$dTflUOAWOtlTJ1bXCyQWOeLqQiOc5A1rFgEBE8OI04UMf8plZFcyC	mZOvP7uokaIlZI1jFVwBibWM0hGHysJXAa5evsxCFy8N7KQhfma7wrCzEj46	\N	\N	1	f	2	1	\N
\.


--
-- TOC entry 3903 (class 0 OID 0)
-- Dependencies: 197
-- Name: customer_financials_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.customer_financials_id_seq', 139, true);


--
-- TOC entry 3904 (class 0 OID 0)
-- Dependencies: 198
-- Name: customers_family_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.customers_family_id_seq', 2, true);


--
-- TOC entry 3905 (class 0 OID 0)
-- Dependencies: 200
-- Name: customers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.customers_id_seq', 92, true);


--
-- TOC entry 3906 (class 0 OID 0)
-- Dependencies: 202
-- Name: discount_requests_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.discount_requests_id_seq', 14, true);


--
-- TOC entry 3907 (class 0 OID 0)
-- Dependencies: 204
-- Name: followup_histories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.followup_histories_id_seq', 47, true);


--
-- TOC entry 3908 (class 0 OID 0)
-- Dependencies: 206
-- Name: gallery_photos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.gallery_photos_id_seq', 15, true);


--
-- TOC entry 3909 (class 0 OID 0)
-- Dependencies: 207
-- Name: general_setting_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.general_setting_id_seq', 8, true);


--
-- TOC entry 3910 (class 0 OID 0)
-- Dependencies: 338
-- Name: inv_orders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.inv_orders_id_seq', 1, false);


--
-- TOC entry 3911 (class 0 OID 0)
-- Dependencies: 210
-- Name: inv_request_ds_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.inv_request_ds_id_seq', 84, true);


--
-- TOC entry 3912 (class 0 OID 0)
-- Dependencies: 212
-- Name: inv_requests_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.inv_requests_id_seq', 75, true);


--
-- TOC entry 3913 (class 0 OID 0)
-- Dependencies: 214
-- Name: inv_return_ds_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.inv_return_ds_id_seq', 7, true);


--
-- TOC entry 3914 (class 0 OID 0)
-- Dependencies: 216
-- Name: inv_returns_id_inv_return_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.inv_returns_id_inv_return_seq', 5, true);


--
-- TOC entry 3915 (class 0 OID 0)
-- Dependencies: 218
-- Name: inv_sale_ds_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.inv_sale_ds_id_seq', 1, true);


--
-- TOC entry 3916 (class 0 OID 0)
-- Dependencies: 220
-- Name: inv_sales_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.inv_sales_id_seq', 4, true);


--
-- TOC entry 3917 (class 0 OID 0)
-- Dependencies: 222
-- Name: inv_trx_ds_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.inv_trx_ds_id_seq', 443, true);


--
-- TOC entry 3918 (class 0 OID 0)
-- Dependencies: 340
-- Name: inv_trx_rest_ds_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.inv_trx_rest_ds_id_seq', 1, false);


--
-- TOC entry 3919 (class 0 OID 0)
-- Dependencies: 224
-- Name: inv_trxes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.inv_trxes_id_seq', 187, true);


--
-- TOC entry 3920 (class 0 OID 0)
-- Dependencies: 226
-- Name: invoices_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.invoices_id_seq', 1, false);


--
-- TOC entry 3921 (class 0 OID 0)
-- Dependencies: 228
-- Name: kpr_simulation_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.kpr_simulation_id_seq', 9, true);


--
-- TOC entry 3922 (class 0 OID 0)
-- Dependencies: 231
-- Name: m_best_price_materials_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.m_best_price_materials_id_seq', 252, true);


--
-- TOC entry 3923 (class 0 OID 0)
-- Dependencies: 233
-- Name: m_cities_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.m_cities_id_seq', 3, true);


--
-- TOC entry 3924 (class 0 OID 0)
-- Dependencies: 235
-- Name: m_doc_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.m_doc_types_id_seq', 71, true);


--
-- TOC entry 3925 (class 0 OID 0)
-- Dependencies: 237
-- Name: m_employees_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.m_employees_id_seq', 9, true);


--
-- TOC entry 3926 (class 0 OID 0)
-- Dependencies: 342
-- Name: m_item_price_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.m_item_price_id_seq', 5, true);


--
-- TOC entry 3927 (class 0 OID 0)
-- Dependencies: 239
-- Name: m_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.m_items_id_seq', 469, true);


--
-- TOC entry 3928 (class 0 OID 0)
-- Dependencies: 241
-- Name: m_kpr_bank_payments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.m_kpr_bank_payments_id_seq', 128, true);


--
-- TOC entry 3929 (class 0 OID 0)
-- Dependencies: 328
-- Name: m_positions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.m_positions_id_seq', 2, true);


--
-- TOC entry 3930 (class 0 OID 0)
-- Dependencies: 243
-- Name: m_references_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.m_references_id_seq', 22, true);


--
-- TOC entry 3931 (class 0 OID 0)
-- Dependencies: 244
-- Name: m_sequences_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.m_sequences_id_seq', 80, true);


--
-- TOC entry 3932 (class 0 OID 0)
-- Dependencies: 247
-- Name: m_suppliers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.m_suppliers_id_seq', 66, true);


--
-- TOC entry 3933 (class 0 OID 0)
-- Dependencies: 249
-- Name: m_units_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.m_units_id_seq', 27, true);


--
-- TOC entry 3934 (class 0 OID 0)
-- Dependencies: 251
-- Name: m_warehouses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.m_warehouses_id_seq', 1, true);


--
-- TOC entry 3935 (class 0 OID 0)
-- Dependencies: 253
-- Name: material_prices_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.material_prices_id_seq', 1, true);


--
-- TOC entry 3936 (class 0 OID 0)
-- Dependencies: 254
-- Name: menus_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.menus_id_seq', 113, true);


--
-- TOC entry 3937 (class 0 OID 0)
-- Dependencies: 256
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 2, true);


--
-- TOC entry 3938 (class 0 OID 0)
-- Dependencies: 336
-- Name: order_ds_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.order_ds_id_seq', 39, true);


--
-- TOC entry 3939 (class 0 OID 0)
-- Dependencies: 334
-- Name: orders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.orders_id_seq', 25, true);


--
-- TOC entry 3940 (class 0 OID 0)
-- Dependencies: 260
-- Name: payment_receives_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.payment_receives_id_seq', 1, false);


--
-- TOC entry 3941 (class 0 OID 0)
-- Dependencies: 332
-- Name: product_subs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.product_subs_id_seq', 123, true);


--
-- TOC entry 3942 (class 0 OID 0)
-- Dependencies: 330
-- Name: products_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.products_id_seq', 16, true);


--
-- TOC entry 3943 (class 0 OID 0)
-- Dependencies: 262
-- Name: programs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.programs_id_seq', 10, true);


--
-- TOC entry 3944 (class 0 OID 0)
-- Dependencies: 264
-- Name: project_works_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.project_works_id_seq', 42, true);


--
-- TOC entry 3945 (class 0 OID 0)
-- Dependencies: 266
-- Name: project_worksub_ds_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.project_worksub_ds_id_seq', 83, true);


--
-- TOC entry 3946 (class 0 OID 0)
-- Dependencies: 268
-- Name: project_worksubs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.project_worksubs_id_seq', 53, true);


--
-- TOC entry 3947 (class 0 OID 0)
-- Dependencies: 270
-- Name: projects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.projects_id_seq', 262, true);


--
-- TOC entry 3948 (class 0 OID 0)
-- Dependencies: 272
-- Name: purchase_ds_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.purchase_ds_id_seq', 490, true);


--
-- TOC entry 3949 (class 0 OID 0)
-- Dependencies: 274
-- Name: purchases_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.purchases_id_seq', 149, true);


--
-- TOC entry 3950 (class 0 OID 0)
-- Dependencies: 275
-- Name: rab_request_ds_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.rab_request_ds_id_seq', 18, true);


--
-- TOC entry 3951 (class 0 OID 0)
-- Dependencies: 278
-- Name: rab_requests_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.rab_requests_id_seq', 10, true);


--
-- TOC entry 3952 (class 0 OID 0)
-- Dependencies: 280
-- Name: rabs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.rabs_id_seq', 36, true);


--
-- TOC entry 3953 (class 0 OID 0)
-- Dependencies: 281
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.roles_id_seq', 7, true);


--
-- TOC entry 3954 (class 0 OID 0)
-- Dependencies: 284
-- Name: sale_trx_docs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sale_trx_docs_id_seq', 428, true);


--
-- TOC entry 3955 (class 0 OID 0)
-- Dependencies: 286
-- Name: sale_trx_ds_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sale_trx_ds_id_seq', 166, true);


--
-- TOC entry 3956 (class 0 OID 0)
-- Dependencies: 288
-- Name: sale_trx_kpr_bank_payments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sale_trx_kpr_bank_payments_id_seq', 26, true);


--
-- TOC entry 3957 (class 0 OID 0)
-- Dependencies: 290
-- Name: sale_trxes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sale_trxes_id_seq', 104, true);


--
-- TOC entry 3958 (class 0 OID 0)
-- Dependencies: 291
-- Name: sequence_id_bank; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sequence_id_bank', 1, false);


--
-- TOC entry 3959 (class 0 OID 0)
-- Dependencies: 293
-- Name: sites_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.sites_id_seq', 9, true);


--
-- TOC entry 3960 (class 0 OID 0)
-- Dependencies: 295
-- Name: stock_opname_ds_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.stock_opname_ds_id_seq', 19, true);


--
-- TOC entry 3961 (class 0 OID 0)
-- Dependencies: 297
-- Name: stock_opnames_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.stock_opnames_id_seq', 18, true);


--
-- TOC entry 3962 (class 0 OID 0)
-- Dependencies: 316
-- Name: tbl_absensi_id_absensi_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tbl_absensi_id_absensi_seq', 34, true);


--
-- TOC entry 3963 (class 0 OID 0)
-- Dependencies: 308
-- Name: tbl_akun_detail_id_akun_d_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tbl_akun_detail_id_akun_d_seq', 2, true);


--
-- TOC entry 3964 (class 0 OID 0)
-- Dependencies: 306
-- Name: tbl_akun_id_akun_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tbl_akun_id_akun_seq', 9, true);


--
-- TOC entry 3965 (class 0 OID 0)
-- Dependencies: 318
-- Name: tbl_cuti_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tbl_cuti_id_seq', 2, true);


--
-- TOC entry 3966 (class 0 OID 0)
-- Dependencies: 326
-- Name: tbl_other_gaji_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tbl_other_gaji_id_seq', 1, false);


--
-- TOC entry 3967 (class 0 OID 0)
-- Dependencies: 320
-- Name: tbl_ref_gaji_id_ref_gaji_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tbl_ref_gaji_id_ref_gaji_seq', 1, false);


--
-- TOC entry 3968 (class 0 OID 0)
-- Dependencies: 310
-- Name: tbl_saldo_akun_id_saldo_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tbl_saldo_akun_id_saldo_seq', 1, false);


--
-- TOC entry 3969 (class 0 OID 0)
-- Dependencies: 322
-- Name: tbl_setting_gaji_id_setting_gaji_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tbl_setting_gaji_id_setting_gaji_seq', 3, true);


--
-- TOC entry 3970 (class 0 OID 0)
-- Dependencies: 324
-- Name: tbl_shift_id_shift_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tbl_shift_id_shift_seq', 1, false);


--
-- TOC entry 3971 (class 0 OID 0)
-- Dependencies: 314
-- Name: tbl_trx_akuntansi_detail_id_trx_akun_detail_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tbl_trx_akuntansi_detail_id_trx_akun_detail_seq', 2, true);


--
-- TOC entry 3972 (class 0 OID 0)
-- Dependencies: 312
-- Name: tbl_trx_akuntansi_id_trx_akun_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tbl_trx_akuntansi_id_trx_akun_seq', 1, true);


--
-- TOC entry 3973 (class 0 OID 0)
-- Dependencies: 299
-- Name: transfer_stock_ds_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.transfer_stock_ds_id_seq', 19, true);


--
-- TOC entry 3974 (class 0 OID 0)
-- Dependencies: 301
-- Name: transfer_stocks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.transfer_stocks_id_seq', 19, true);


--
-- TOC entry 3975 (class 0 OID 0)
-- Dependencies: 302
-- Name: user_permission_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.user_permission_id_seq', 224, true);


--
-- TOC entry 3976 (class 0 OID 0)
-- Dependencies: 304
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 28, true);


--
-- TOC entry 3280 (class 2606 OID 16820)
-- Name: customer_financials customer_financials_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer_financials
    ADD CONSTRAINT customer_financials_pk PRIMARY KEY (id);


--
-- TOC entry 3282 (class 2606 OID 16822)
-- Name: customers customers_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customers
    ADD CONSTRAINT customers_pk PRIMARY KEY (id);


--
-- TOC entry 3284 (class 2606 OID 16824)
-- Name: discount_requests discount_requests_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discount_requests
    ADD CONSTRAINT discount_requests_pk PRIMARY KEY (id);


--
-- TOC entry 3286 (class 2606 OID 16826)
-- Name: followup_histories followup_histories_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.followup_histories
    ADD CONSTRAINT followup_histories_pk PRIMARY KEY (id);


--
-- TOC entry 3288 (class 2606 OID 16828)
-- Name: gallery_photos gallery_photos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.gallery_photos
    ADD CONSTRAINT gallery_photos_pkey PRIMARY KEY (id);


--
-- TOC entry 3290 (class 2606 OID 16830)
-- Name: general_settings general_setting_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.general_settings
    ADD CONSTRAINT general_setting_pk PRIMARY KEY (id);


--
-- TOC entry 3434 (class 2606 OID 51070)
-- Name: inv_orders inv_orders_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_orders
    ADD CONSTRAINT inv_orders_pkey PRIMARY KEY (id);


--
-- TOC entry 3292 (class 2606 OID 16832)
-- Name: inv_request_ds inv_request_ds_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_request_ds
    ADD CONSTRAINT inv_request_ds_pk PRIMARY KEY (id);


--
-- TOC entry 3294 (class 2606 OID 16834)
-- Name: inv_requests inv_requests_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_requests
    ADD CONSTRAINT inv_requests_pk PRIMARY KEY (id);


--
-- TOC entry 3297 (class 2606 OID 16836)
-- Name: inv_return_ds inv_return_ds_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_return_ds
    ADD CONSTRAINT inv_return_ds_pkey PRIMARY KEY (id);


--
-- TOC entry 3299 (class 2606 OID 16838)
-- Name: inv_returns inv_returns_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_returns
    ADD CONSTRAINT inv_returns_pkey PRIMARY KEY (id);


--
-- TOC entry 3301 (class 2606 OID 16840)
-- Name: inv_sale_ds inv_sale_ds_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_sale_ds
    ADD CONSTRAINT inv_sale_ds_pk PRIMARY KEY (id);


--
-- TOC entry 3303 (class 2606 OID 16842)
-- Name: inv_sales inv_sales_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_sales
    ADD CONSTRAINT inv_sales_pk PRIMARY KEY (id);


--
-- TOC entry 3306 (class 2606 OID 16844)
-- Name: inv_trx_ds inv_trx_ds_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_trx_ds
    ADD CONSTRAINT inv_trx_ds_pk PRIMARY KEY (id);


--
-- TOC entry 3436 (class 2606 OID 51424)
-- Name: inv_trx_rest_ds inv_trx_rest_ds_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_trx_rest_ds
    ADD CONSTRAINT inv_trx_rest_ds_pkey PRIMARY KEY (id);


--
-- TOC entry 3309 (class 2606 OID 16846)
-- Name: inv_trxes inv_trxs_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_trxes
    ADD CONSTRAINT inv_trxs_pk PRIMARY KEY (id);


--
-- TOC entry 3311 (class 2606 OID 16848)
-- Name: invoices invoices_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.invoices
    ADD CONSTRAINT invoices_pk PRIMARY KEY (id);


--
-- TOC entry 3313 (class 2606 OID 16850)
-- Name: kpr_simulation kpr_simulation_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kpr_simulation
    ADD CONSTRAINT kpr_simulation_pkey PRIMARY KEY (id);


--
-- TOC entry 3315 (class 2606 OID 16852)
-- Name: list_bank list_bank_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.list_bank
    ADD CONSTRAINT list_bank_pkey PRIMARY KEY (id_bank);


--
-- TOC entry 3317 (class 2606 OID 16854)
-- Name: m_best_prices m_best_prices_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_best_prices
    ADD CONSTRAINT m_best_prices_pk PRIMARY KEY (id);


--
-- TOC entry 3319 (class 2606 OID 16856)
-- Name: m_cities m_cities_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_cities
    ADD CONSTRAINT m_cities_pk PRIMARY KEY (id);


--
-- TOC entry 3321 (class 2606 OID 16858)
-- Name: m_doc_types m_doc_types_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_doc_types
    ADD CONSTRAINT m_doc_types_pk PRIMARY KEY (id);


--
-- TOC entry 3324 (class 2606 OID 16860)
-- Name: m_employees m_employees_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_employees
    ADD CONSTRAINT m_employees_pk PRIMARY KEY (id);


--
-- TOC entry 3438 (class 2606 OID 59163)
-- Name: m_item_prices m_item_price_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_item_prices
    ADD CONSTRAINT m_item_price_pkey PRIMARY KEY (id);


--
-- TOC entry 3326 (class 2606 OID 16862)
-- Name: m_items m_items_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_items
    ADD CONSTRAINT m_items_pk PRIMARY KEY (id);


--
-- TOC entry 3328 (class 2606 OID 16864)
-- Name: m_kpr_bank_payments m_kpr_bank_payment_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_kpr_bank_payments
    ADD CONSTRAINT m_kpr_bank_payment_pk PRIMARY KEY (id);


--
-- TOC entry 3419 (class 2606 OID 26389)
-- Name: m_positions m_positions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_positions
    ADD CONSTRAINT m_positions_pkey PRIMARY KEY (id);


--
-- TOC entry 3330 (class 2606 OID 16866)
-- Name: m_references m_references_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_references
    ADD CONSTRAINT m_references_pk PRIMARY KEY (id);


--
-- TOC entry 3332 (class 2606 OID 16868)
-- Name: m_sequences m_sequences_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_sequences
    ADD CONSTRAINT m_sequences_pk PRIMARY KEY (id);


--
-- TOC entry 3334 (class 2606 OID 16870)
-- Name: m_suppliers m_suppliers_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_suppliers
    ADD CONSTRAINT m_suppliers_pk PRIMARY KEY (id);


--
-- TOC entry 3336 (class 2606 OID 16872)
-- Name: m_units m_units_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_units
    ADD CONSTRAINT m_units_pk PRIMARY KEY (id);


--
-- TOC entry 3338 (class 2606 OID 16874)
-- Name: m_warehouses m_warehouses_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_warehouses
    ADD CONSTRAINT m_warehouses_pk PRIMARY KEY (id);


--
-- TOC entry 3340 (class 2606 OID 16876)
-- Name: material_prices material_prices_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.material_prices
    ADD CONSTRAINT material_prices_pk PRIMARY KEY (id);


--
-- TOC entry 3342 (class 2606 OID 16878)
-- Name: menus menus_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.menus
    ADD CONSTRAINT menus_pkey PRIMARY KEY (id);


--
-- TOC entry 3344 (class 2606 OID 16880)
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- TOC entry 3432 (class 2606 OID 35770)
-- Name: order_ds order_ds_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_ds
    ADD CONSTRAINT order_ds_pkey PRIMARY KEY (id);


--
-- TOC entry 3428 (class 2606 OID 35760)
-- Name: orders orders_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (id);


--
-- TOC entry 3346 (class 2606 OID 16882)
-- Name: payment_receives payment_receive_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payment_receives
    ADD CONSTRAINT payment_receive_pk PRIMARY KEY (id);


--
-- TOC entry 3424 (class 2606 OID 35630)
-- Name: product_subs product_subs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_subs
    ADD CONSTRAINT product_subs_pkey PRIMARY KEY (id);


--
-- TOC entry 3421 (class 2606 OID 34590)
-- Name: products products_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id);


--
-- TOC entry 3349 (class 2606 OID 16884)
-- Name: programs programs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.programs
    ADD CONSTRAINT programs_pkey PRIMARY KEY (id);


--
-- TOC entry 3351 (class 2606 OID 16886)
-- Name: project_works project_works_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_works
    ADD CONSTRAINT project_works_pk PRIMARY KEY (id);


--
-- TOC entry 3353 (class 2606 OID 16888)
-- Name: project_worksub_ds project_worksub_ds_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_worksub_ds
    ADD CONSTRAINT project_worksub_ds_pk PRIMARY KEY (id);


--
-- TOC entry 3355 (class 2606 OID 16890)
-- Name: project_worksubs project_worksubs_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_worksubs
    ADD CONSTRAINT project_worksubs_pk PRIMARY KEY (id);


--
-- TOC entry 3358 (class 2606 OID 16892)
-- Name: projects projects_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT projects_pk PRIMARY KEY (id);


--
-- TOC entry 3360 (class 2606 OID 16894)
-- Name: purchase_ds purchase_ds_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchase_ds
    ADD CONSTRAINT purchase_ds_pk PRIMARY KEY (id);


--
-- TOC entry 3362 (class 2606 OID 16896)
-- Name: purchases purchases_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchases
    ADD CONSTRAINT purchases_pk PRIMARY KEY (id);


--
-- TOC entry 3364 (class 2606 OID 16898)
-- Name: rab_request_ds rab_request_ds_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rab_request_ds
    ADD CONSTRAINT rab_request_ds_pk PRIMARY KEY (id);


--
-- TOC entry 3366 (class 2606 OID 16900)
-- Name: rab_requests rab_requests_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rab_requests
    ADD CONSTRAINT rab_requests_pk PRIMARY KEY (id);


--
-- TOC entry 3368 (class 2606 OID 16902)
-- Name: rabs rabs_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rabs
    ADD CONSTRAINT rabs_pk PRIMARY KEY (id);


--
-- TOC entry 3370 (class 2606 OID 16904)
-- Name: roles roles_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pk PRIMARY KEY (id);


--
-- TOC entry 3372 (class 2606 OID 16906)
-- Name: sale_trx_docs sale_trx_docs_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_trx_docs
    ADD CONSTRAINT sale_trx_docs_pk PRIMARY KEY (id);


--
-- TOC entry 3374 (class 2606 OID 16908)
-- Name: sale_trx_ds sale_trx_ds_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_trx_ds
    ADD CONSTRAINT sale_trx_ds_pk PRIMARY KEY (id);


--
-- TOC entry 3376 (class 2606 OID 16910)
-- Name: sale_trx_kpr_bank_payments sale_trx_kpr_bank_payments_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_trx_kpr_bank_payments
    ADD CONSTRAINT sale_trx_kpr_bank_payments_pk PRIMARY KEY (id);


--
-- TOC entry 3378 (class 2606 OID 16912)
-- Name: sale_trxes sale_trxes_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_trxes
    ADD CONSTRAINT sale_trxes_pk PRIMARY KEY (id);


--
-- TOC entry 3380 (class 2606 OID 16914)
-- Name: sites sites_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sites
    ADD CONSTRAINT sites_pk PRIMARY KEY (id);


--
-- TOC entry 3382 (class 2606 OID 16916)
-- Name: stock_opname_ds stock_opname_ds_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stock_opname_ds
    ADD CONSTRAINT stock_opname_ds_pk PRIMARY KEY (id);


--
-- TOC entry 3384 (class 2606 OID 16918)
-- Name: stock_opnames stock_opnames_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stock_opnames
    ADD CONSTRAINT stock_opnames_pk PRIMARY KEY (id);


--
-- TOC entry 3407 (class 2606 OID 26337)
-- Name: tbl_absensi tbl_absensi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_absensi
    ADD CONSTRAINT tbl_absensi_pkey PRIMARY KEY (id_absensi);


--
-- TOC entry 3399 (class 2606 OID 18076)
-- Name: tbl_akun_detail tbl_akun_detail_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_akun_detail
    ADD CONSTRAINT tbl_akun_detail_pkey PRIMARY KEY (id_akun_d);


--
-- TOC entry 3396 (class 2606 OID 18068)
-- Name: tbl_akun tbl_akun_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_akun
    ADD CONSTRAINT tbl_akun_pkey PRIMARY KEY (id_akun);


--
-- TOC entry 3409 (class 2606 OID 26345)
-- Name: tbl_cuti tbl_cuti_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_cuti
    ADD CONSTRAINT tbl_cuti_pkey PRIMARY KEY (id);


--
-- TOC entry 3417 (class 2606 OID 26380)
-- Name: tbl_other_gaji tbl_other_gaji_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_other_gaji
    ADD CONSTRAINT tbl_other_gaji_pkey PRIMARY KEY (id);


--
-- TOC entry 3411 (class 2606 OID 26353)
-- Name: tbl_ref_gaji tbl_ref_gaji_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_ref_gaji
    ADD CONSTRAINT tbl_ref_gaji_pkey PRIMARY KEY (id_ref_gaji);


--
-- TOC entry 3401 (class 2606 OID 18094)
-- Name: tbl_saldo_akun tbl_saldo_akun_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_saldo_akun
    ADD CONSTRAINT tbl_saldo_akun_pkey PRIMARY KEY (id_saldo);


--
-- TOC entry 3413 (class 2606 OID 26361)
-- Name: tbl_setting_gaji tbl_setting_gaji_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_setting_gaji
    ADD CONSTRAINT tbl_setting_gaji_pkey PRIMARY KEY (id_setting_gaji);


--
-- TOC entry 3415 (class 2606 OID 26369)
-- Name: tbl_shift tbl_shift_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_shift
    ADD CONSTRAINT tbl_shift_pkey PRIMARY KEY (id_shift);


--
-- TOC entry 3405 (class 2606 OID 18121)
-- Name: tbl_trx_akuntansi_detail tbl_trx_akuntansi_detail_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_trx_akuntansi_detail
    ADD CONSTRAINT tbl_trx_akuntansi_detail_pkey PRIMARY KEY (id_trx_akun_detail);


--
-- TOC entry 3403 (class 2606 OID 18110)
-- Name: tbl_trx_akuntansi tbl_trx_akuntansi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_trx_akuntansi
    ADD CONSTRAINT tbl_trx_akuntansi_pkey PRIMARY KEY (id_trx_akun);


--
-- TOC entry 3386 (class 2606 OID 16920)
-- Name: transfer_stock_ds transfer_stock_ds_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transfer_stock_ds
    ADD CONSTRAINT transfer_stock_ds_pk PRIMARY KEY (id);


--
-- TOC entry 3388 (class 2606 OID 16922)
-- Name: transfer_stocks transfer_stocks_pk; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transfer_stocks
    ADD CONSTRAINT transfer_stocks_pk PRIMARY KEY (id);


--
-- TOC entry 3390 (class 2606 OID 16924)
-- Name: user_permission user_permission_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_permission
    ADD CONSTRAINT user_permission_pkey PRIMARY KEY (id);


--
-- TOC entry 3392 (class 2606 OID 16926)
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- TOC entry 3394 (class 2606 OID 16928)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 3425 (class 1259 OID 35792)
-- Name: fki_customer_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_customer_id ON public.orders USING btree (customer_id);


--
-- TOC entry 3397 (class 1259 OID 18086)
-- Name: fki_id_akun; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_id_akun ON public.tbl_akun_detail USING btree (id_akun);


--
-- TOC entry 3322 (class 1259 OID 16929)
-- Name: fki_id_user; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_id_user ON public.m_employees USING btree (id_user);


--
-- TOC entry 3307 (class 1259 OID 51402)
-- Name: fki_inv_requests_inv_trxes_fk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_inv_requests_inv_trxes_fk ON public.inv_trxes USING btree (inv_request_id);


--
-- TOC entry 3295 (class 1259 OID 51430)
-- Name: fki_inv_return_fk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_inv_return_fk ON public.inv_return_ds USING btree (inv_return_id);


--
-- TOC entry 3304 (class 1259 OID 51027)
-- Name: fki_inv_trx_fk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_inv_trx_fk ON public.inv_trx_ds USING btree (inv_trx_id);


--
-- TOC entry 3422 (class 1259 OID 42864)
-- Name: fki_order_d_fk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_order_d_fk ON public.product_subs USING btree (order_d_id);


--
-- TOC entry 3429 (class 1259 OID 35814)
-- Name: fki_order_ds_fk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_order_ds_fk ON public.order_ds USING btree (order_id);


--
-- TOC entry 3356 (class 1259 OID 51241)
-- Name: fki_order_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_order_id ON public.projects USING btree (order_id);


--
-- TOC entry 3430 (class 1259 OID 35808)
-- Name: fki_product_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_product_id ON public.order_ds USING btree (product_id);


--
-- TOC entry 3426 (class 1259 OID 42792)
-- Name: fki_site_orders_fk; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_site_orders_fk ON public.orders USING btree (site_id);


--
-- TOC entry 3347 (class 1259 OID 16930)
-- Name: fki_user_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_user_id ON public.programs USING btree (user_id);


--
-- TOC entry 3512 (class 2606 OID 35798)
-- Name: orders customer_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT customer_fk FOREIGN KEY (customer_id) REFERENCES public.customers(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3439 (class 2606 OID 16931)
-- Name: customer_financials customers_customer_finances_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer_financials
    ADD CONSTRAINT customers_customer_finances_fk FOREIGN KEY (customer_id) REFERENCES public.customers(id);


--
-- TOC entry 3442 (class 2606 OID 16936)
-- Name: followup_histories customers_followup_history_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.followup_histories
    ADD CONSTRAINT customers_followup_history_fk FOREIGN KEY (customer_id) REFERENCES public.customers(id);


--
-- TOC entry 3477 (class 2606 OID 16941)
-- Name: projects customers_projects_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT customers_projects_fk FOREIGN KEY (customer_id) REFERENCES public.customers(id);


--
-- TOC entry 3485 (class 2606 OID 16946)
-- Name: rab_requests customers_rab_requests_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rab_requests
    ADD CONSTRAINT customers_rab_requests_fk FOREIGN KEY (customer_id) REFERENCES public.customers(id);


--
-- TOC entry 3495 (class 2606 OID 16951)
-- Name: sale_trxes customers_sale_trxs_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_trxes
    ADD CONSTRAINT customers_sale_trxs_fk FOREIGN KEY (customer_id) REFERENCES public.customers(id);


--
-- TOC entry 3440 (class 2606 OID 16956)
-- Name: discount_requests discount_requests_projects_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discount_requests
    ADD CONSTRAINT discount_requests_projects_fk FOREIGN KEY (project_id) REFERENCES public.projects(id);


--
-- TOC entry 3496 (class 2606 OID 16961)
-- Name: sale_trxes followup_histories_sale_trxes_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_trxes
    ADD CONSTRAINT followup_histories_sale_trxes_fk FOREIGN KEY (follow_history_id) REFERENCES public.followup_histories(id);


--
-- TOC entry 3509 (class 2606 OID 18095)
-- Name: tbl_saldo_akun id_akun; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_saldo_akun
    ADD CONSTRAINT id_akun FOREIGN KEY (id_akun) REFERENCES public.tbl_akun(id_akun);


--
-- TOC entry 3508 (class 2606 OID 18127)
-- Name: tbl_akun_detail id_akun; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_akun_detail
    ADD CONSTRAINT id_akun FOREIGN KEY (id_akun) REFERENCES public.tbl_akun(id_akun) NOT VALID;


--
-- TOC entry 3510 (class 2606 OID 18122)
-- Name: tbl_trx_akuntansi_detail id_trx_akun; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tbl_trx_akuntansi_detail
    ADD CONSTRAINT id_trx_akun FOREIGN KEY (id_trx_akun) REFERENCES public.tbl_trx_akuntansi(id_trx_akun);


--
-- TOC entry 3464 (class 2606 OID 16966)
-- Name: m_employees id_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_employees
    ADD CONSTRAINT id_user FOREIGN KEY (id_user) REFERENCES public.users(id);


--
-- TOC entry 3444 (class 2606 OID 16971)
-- Name: inv_request_ds inv_requests_inv_request_ds_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_request_ds
    ADD CONSTRAINT inv_requests_inv_request_ds_fk FOREIGN KEY (inv_request_id) REFERENCES public.inv_requests(id);


--
-- TOC entry 3460 (class 2606 OID 51403)
-- Name: inv_trxes inv_requests_inv_trxes_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_trxes
    ADD CONSTRAINT inv_requests_inv_trxes_fk FOREIGN KEY (inv_request_id) REFERENCES public.inv_requests(id);


--
-- TOC entry 3447 (class 2606 OID 16981)
-- Name: inv_requests inv_requests_sites_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_requests
    ADD CONSTRAINT inv_requests_sites_fk FOREIGN KEY (site_id) REFERENCES public.sites(id);


--
-- TOC entry 3450 (class 2606 OID 51425)
-- Name: inv_return_ds inv_return_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_return_ds
    ADD CONSTRAINT inv_return_fk FOREIGN KEY (inv_return_id) REFERENCES public.inv_returns(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3451 (class 2606 OID 16986)
-- Name: inv_sale_ds inv_sale_ds_inv_sales_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_sale_ds
    ADD CONSTRAINT inv_sale_ds_inv_sales_fk FOREIGN KEY (inv_sale_id) REFERENCES public.inv_sales(id);


--
-- TOC entry 3452 (class 2606 OID 16991)
-- Name: inv_sale_ds inv_sale_ds_m_items_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_sale_ds
    ADD CONSTRAINT inv_sale_ds_m_items_fk FOREIGN KEY (m_item_id) REFERENCES public.m_items(id);


--
-- TOC entry 3453 (class 2606 OID 16996)
-- Name: inv_sales inv_sales_sites_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_sales
    ADD CONSTRAINT inv_sales_sites_fk FOREIGN KEY (site_id) REFERENCES public.sites(id);


--
-- TOC entry 3456 (class 2606 OID 51022)
-- Name: inv_trx_ds inv_trx_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_trx_ds
    ADD CONSTRAINT inv_trx_fk FOREIGN KEY (inv_trx_id) REFERENCES public.inv_trxes(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3457 (class 2606 OID 17001)
-- Name: inv_trxes inv_trxes_transfer_stocks_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_trxes
    ADD CONSTRAINT inv_trxes_transfer_stocks_fk FOREIGN KEY (transfer_stock_id) REFERENCES public.transfer_stocks(id);


--
-- TOC entry 3468 (class 2606 OID 17006)
-- Name: payment_receives invoices_payment_receives_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.payment_receives
    ADD CONSTRAINT invoices_payment_receives_fk FOREIGN KEY (invoice_id) REFERENCES public.invoices(id);


--
-- TOC entry 3462 (class 2606 OID 17011)
-- Name: m_best_prices m_best_prices_m_items_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_best_prices
    ADD CONSTRAINT m_best_prices_m_items_fk FOREIGN KEY (m_item_id) REFERENCES public.m_items(id);


--
-- TOC entry 3463 (class 2606 OID 17016)
-- Name: m_best_prices m_best_prices_m_suppliers_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_best_prices
    ADD CONSTRAINT m_best_prices_m_suppliers_fk FOREIGN KEY (m_supplier_id) REFERENCES public.m_suppliers(id);


--
-- TOC entry 3489 (class 2606 OID 17021)
-- Name: sale_trx_docs m_doc_types_sale_trx_docs_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_trx_docs
    ADD CONSTRAINT m_doc_types_sale_trx_docs_fk FOREIGN KEY (m_doc_type_id) REFERENCES public.m_doc_types(id);


--
-- TOC entry 3445 (class 2606 OID 17026)
-- Name: inv_request_ds m_items_inv_request_ds_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_request_ds
    ADD CONSTRAINT m_items_inv_request_ds_fk FOREIGN KEY (m_item_id) REFERENCES public.m_items(id);


--
-- TOC entry 3454 (class 2606 OID 17031)
-- Name: inv_trx_ds m_items_inv_trx_ds_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_trx_ds
    ADD CONSTRAINT m_items_inv_trx_ds_fk FOREIGN KEY (m_item_id) REFERENCES public.m_items(id);


--
-- TOC entry 3472 (class 2606 OID 17036)
-- Name: project_worksub_ds m_items_project_worksub_ds_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_worksub_ds
    ADD CONSTRAINT m_items_project_worksub_ds_fk FOREIGN KEY (m_item_id) REFERENCES public.m_items(id);


--
-- TOC entry 3479 (class 2606 OID 17041)
-- Name: purchase_ds m_items_purchase_ds_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchase_ds
    ADD CONSTRAINT m_items_purchase_ds_fk FOREIGN KEY (m_item_id) REFERENCES public.m_items(id);


--
-- TOC entry 3493 (class 2606 OID 17046)
-- Name: sale_trx_kpr_bank_payments m_kpr_bank_payments_sale_trx_kpr_bank_payments_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_trx_kpr_bank_payments
    ADD CONSTRAINT m_kpr_bank_payments_sale_trx_kpr_bank_payments_fk FOREIGN KEY (m_kpr_bank_payment_id) REFERENCES public.m_kpr_bank_payments(id);


--
-- TOC entry 3465 (class 2606 OID 17051)
-- Name: m_sequences m_sequences_sites_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.m_sequences
    ADD CONSTRAINT m_sequences_sites_fk FOREIGN KEY (site_id) REFERENCES public.sites(id);


--
-- TOC entry 3482 (class 2606 OID 17056)
-- Name: purchases m_suppliers_purchases_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchases
    ADD CONSTRAINT m_suppliers_purchases_fk FOREIGN KEY (m_supplier_id) REFERENCES public.m_suppliers(id);


--
-- TOC entry 3446 (class 2606 OID 17061)
-- Name: inv_request_ds m_units_inv_request_ds_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_request_ds
    ADD CONSTRAINT m_units_inv_request_ds_fk FOREIGN KEY (m_unit_id) REFERENCES public.m_units(id);


--
-- TOC entry 3455 (class 2606 OID 17066)
-- Name: inv_trx_ds m_units_inv_trx_ds_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_trx_ds
    ADD CONSTRAINT m_units_inv_trx_ds_fk FOREIGN KEY (m_unit_id) REFERENCES public.m_units(id);


--
-- TOC entry 3473 (class 2606 OID 17071)
-- Name: project_worksub_ds m_units_project_worksub_ds_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_worksub_ds
    ADD CONSTRAINT m_units_project_worksub_ds_fk FOREIGN KEY (m_unit_id) REFERENCES public.m_units(id);


--
-- TOC entry 3475 (class 2606 OID 17076)
-- Name: project_worksubs m_units_project_worksubs_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_worksubs
    ADD CONSTRAINT m_units_project_worksubs_fk FOREIGN KEY (m_unit_id) REFERENCES public.m_units(id);


--
-- TOC entry 3480 (class 2606 OID 17081)
-- Name: purchase_ds m_units_purchase_ds_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchase_ds
    ADD CONSTRAINT m_units_purchase_ds_fk FOREIGN KEY (m_unit_id) REFERENCES public.m_units(id);


--
-- TOC entry 3448 (class 2606 OID 17086)
-- Name: inv_requests m_warehouses_inv_requests_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_requests
    ADD CONSTRAINT m_warehouses_inv_requests_fk FOREIGN KEY (m_warehouses_id) REFERENCES public.m_warehouses(id);


--
-- TOC entry 3458 (class 2606 OID 17091)
-- Name: inv_trxes m_warehouses_inv_trxs_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_trxes
    ADD CONSTRAINT m_warehouses_inv_trxs_fk FOREIGN KEY (m_warehouse_id) REFERENCES public.m_warehouses(id);


--
-- TOC entry 3466 (class 2606 OID 17096)
-- Name: material_prices material_prices_m_items_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.material_prices
    ADD CONSTRAINT material_prices_m_items_fk FOREIGN KEY (m_item_id) REFERENCES public.m_items(id);


--
-- TOC entry 3467 (class 2606 OID 17101)
-- Name: material_prices material_prices_m_suppliers_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.material_prices
    ADD CONSTRAINT material_prices_m_suppliers_fk FOREIGN KEY (m_supplier_id) REFERENCES public.m_suppliers(id);


--
-- TOC entry 3511 (class 2606 OID 42859)
-- Name: product_subs order_d_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_subs
    ADD CONSTRAINT order_d_fk FOREIGN KEY (order_d_id) REFERENCES public.order_ds(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3514 (class 2606 OID 35809)
-- Name: order_ds order_ds_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_ds
    ADD CONSTRAINT order_ds_fk FOREIGN KEY (order_id) REFERENCES public.orders(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3476 (class 2606 OID 17106)
-- Name: project_worksubs project_works_project_worksubs_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_worksubs
    ADD CONSTRAINT project_works_project_worksubs_fk FOREIGN KEY (project_work_id) REFERENCES public.project_works(id);


--
-- TOC entry 3474 (class 2606 OID 17111)
-- Name: project_worksub_ds project_worksubs_project_worksub_ds_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_worksub_ds
    ADD CONSTRAINT project_worksubs_project_worksub_ds_fk FOREIGN KEY (project_worksub_id) REFERENCES public.project_worksubs(id);


--
-- TOC entry 3449 (class 2606 OID 17116)
-- Name: inv_requests projects_inv_requests_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_requests
    ADD CONSTRAINT projects_inv_requests_fk FOREIGN KEY (rab_id) REFERENCES public.rabs(id);


--
-- TOC entry 3470 (class 2606 OID 17121)
-- Name: project_works projects_project_works_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_works
    ADD CONSTRAINT projects_project_works_fk FOREIGN KEY (project_id) REFERENCES public.projects(id);


--
-- TOC entry 3486 (class 2606 OID 17126)
-- Name: rab_requests projects_rab_requests_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rab_requests
    ADD CONSTRAINT projects_rab_requests_fk FOREIGN KEY (project_id) REFERENCES public.projects(id);


--
-- TOC entry 3488 (class 2606 OID 17131)
-- Name: rabs projects_rabs_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rabs
    ADD CONSTRAINT projects_rabs_fk FOREIGN KEY (project_id) REFERENCES public.projects(id);


--
-- TOC entry 3491 (class 2606 OID 17136)
-- Name: sale_trx_ds projects_sale_trx_ds_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_trx_ds
    ADD CONSTRAINT projects_sale_trx_ds_fk FOREIGN KEY (project_id) REFERENCES public.projects(id);


--
-- TOC entry 3497 (class 2606 OID 17141)
-- Name: sale_trxes projects_sale_trxes_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_trxes
    ADD CONSTRAINT projects_sale_trxes_fk FOREIGN KEY (project_id) REFERENCES public.projects(id);


--
-- TOC entry 3459 (class 2606 OID 17146)
-- Name: inv_trxes purchases_inv_trxs_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inv_trxes
    ADD CONSTRAINT purchases_inv_trxs_fk FOREIGN KEY (purchase_id) REFERENCES public.purchases(id);


--
-- TOC entry 3481 (class 2606 OID 17151)
-- Name: purchase_ds purchases_purchase_ds_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchase_ds
    ADD CONSTRAINT purchases_purchase_ds_fk FOREIGN KEY (purchase_id) REFERENCES public.purchases(id);


--
-- TOC entry 3483 (class 2606 OID 17156)
-- Name: purchases purchases_sites_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.purchases
    ADD CONSTRAINT purchases_sites_fk FOREIGN KEY (site_id) REFERENCES public.sites(id);


--
-- TOC entry 3484 (class 2606 OID 17161)
-- Name: rab_request_ds rab_requests_rab_request_ds_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rab_request_ds
    ADD CONSTRAINT rab_requests_rab_request_ds_fk FOREIGN KEY (rab_request_id) REFERENCES public.rab_requests(id);


--
-- TOC entry 3471 (class 2606 OID 17166)
-- Name: project_works rabs_project_works_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.project_works
    ADD CONSTRAINT rabs_project_works_fk FOREIGN KEY (rab_id) REFERENCES public.rabs(id);


--
-- TOC entry 3441 (class 2606 OID 17171)
-- Name: discount_requests sale_trxes_discount_requests_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discount_requests
    ADD CONSTRAINT sale_trxes_discount_requests_fk FOREIGN KEY (sale_trx_id) REFERENCES public.sale_trxes(id);


--
-- TOC entry 3461 (class 2606 OID 17176)
-- Name: invoices sale_trxes_invoices_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.invoices
    ADD CONSTRAINT sale_trxes_invoices_fk FOREIGN KEY (sale_trx_id) REFERENCES public.sale_trxes(id);


--
-- TOC entry 3487 (class 2606 OID 17181)
-- Name: rab_requests sale_trxes_rab_requests_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rab_requests
    ADD CONSTRAINT sale_trxes_rab_requests_fk FOREIGN KEY (sale_trx_id) REFERENCES public.sale_trxes(id);


--
-- TOC entry 3490 (class 2606 OID 17186)
-- Name: sale_trx_docs sale_trxes_sale_trx_docs_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_trx_docs
    ADD CONSTRAINT sale_trxes_sale_trx_docs_fk FOREIGN KEY (sale_trx_id) REFERENCES public.sale_trxes(id);


--
-- TOC entry 3492 (class 2606 OID 17191)
-- Name: sale_trx_ds sale_trxes_sale_trx_ds_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_trx_ds
    ADD CONSTRAINT sale_trxes_sale_trx_ds_fk FOREIGN KEY (sale_trx_id) REFERENCES public.sale_trxes(id);


--
-- TOC entry 3494 (class 2606 OID 17196)
-- Name: sale_trx_kpr_bank_payments sale_trxes_sale_trx_kpr_bank_payments_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_trx_kpr_bank_payments
    ADD CONSTRAINT sale_trxes_sale_trx_kpr_bank_payments_fk FOREIGN KEY (sale_trx_id) REFERENCES public.sale_trxes(id);


--
-- TOC entry 3443 (class 2606 OID 17201)
-- Name: followup_histories sales_persons_followup_history_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.followup_histories
    ADD CONSTRAINT sales_persons_followup_history_fk FOREIGN KEY (m_employee_id) REFERENCES public.m_employees(id);


--
-- TOC entry 3498 (class 2606 OID 17206)
-- Name: sale_trxes sales_persons_sale_trxs_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sale_trxes
    ADD CONSTRAINT sales_persons_sale_trxs_fk FOREIGN KEY (m_employee_id) REFERENCES public.m_employees(id);


--
-- TOC entry 3513 (class 2606 OID 42787)
-- Name: orders site_orders_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT site_orders_fk FOREIGN KEY (site_id) REFERENCES public.sites(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3478 (class 2606 OID 17211)
-- Name: projects sites_projects_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.projects
    ADD CONSTRAINT sites_projects_fk FOREIGN KEY (site_id) REFERENCES public.sites(id);


--
-- TOC entry 3500 (class 2606 OID 17216)
-- Name: stock_opname_ds stock_opname_ds_m_items_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stock_opname_ds
    ADD CONSTRAINT stock_opname_ds_m_items_fk FOREIGN KEY (m_item_id) REFERENCES public.m_items(id);


--
-- TOC entry 3501 (class 2606 OID 17221)
-- Name: stock_opname_ds stock_opname_ds_stock_opnames_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stock_opname_ds
    ADD CONSTRAINT stock_opname_ds_stock_opnames_fk FOREIGN KEY (stock_opname_id) REFERENCES public.stock_opnames(id);


--
-- TOC entry 3502 (class 2606 OID 17226)
-- Name: stock_opnames stock_opnames_sites_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.stock_opnames
    ADD CONSTRAINT stock_opnames_sites_fk FOREIGN KEY (site_id) REFERENCES public.sites(id);


--
-- TOC entry 3499 (class 2606 OID 17231)
-- Name: sites towns_sites_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sites
    ADD CONSTRAINT towns_sites_fk FOREIGN KEY (m_city_id) REFERENCES public.m_cities(id);


--
-- TOC entry 3503 (class 2606 OID 17236)
-- Name: transfer_stock_ds transfer_stock_ds_m_items_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transfer_stock_ds
    ADD CONSTRAINT transfer_stock_ds_m_items_fk FOREIGN KEY (m_item_id) REFERENCES public.m_items(id);


--
-- TOC entry 3504 (class 2606 OID 17241)
-- Name: transfer_stock_ds transfer_stock_ds_m_units_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transfer_stock_ds
    ADD CONSTRAINT transfer_stock_ds_m_units_fk FOREIGN KEY (m_unit_id) REFERENCES public.m_units(id);


--
-- TOC entry 3505 (class 2606 OID 17246)
-- Name: transfer_stock_ds transfer_stock_ds_transfer_stocks_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transfer_stock_ds
    ADD CONSTRAINT transfer_stock_ds_transfer_stocks_fk FOREIGN KEY (transfer_stock_id) REFERENCES public.transfer_stocks(id);


--
-- TOC entry 3506 (class 2606 OID 17251)
-- Name: transfer_stocks transfer_stocks_sites_site_from_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transfer_stocks
    ADD CONSTRAINT transfer_stocks_sites_site_from_fk FOREIGN KEY (site_from) REFERENCES public.sites(id);


--
-- TOC entry 3507 (class 2606 OID 17256)
-- Name: transfer_stocks transfer_stocks_sites_site_to_fk; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.transfer_stocks
    ADD CONSTRAINT transfer_stocks_sites_site_to_fk FOREIGN KEY (site_to) REFERENCES public.sites(id);


--
-- TOC entry 3469 (class 2606 OID 17261)
-- Name: programs user_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.programs
    ADD CONSTRAINT user_id FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- TOC entry 3793 (class 0 OID 0)
-- Dependencies: 197
-- Name: SEQUENCE customer_financials_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.customer_financials_id_seq TO PUBLIC;


--
-- TOC entry 3794 (class 0 OID 0)
-- Dependencies: 198
-- Name: SEQUENCE customers_family_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.customers_family_id_seq TO PUBLIC;


--
-- TOC entry 3796 (class 0 OID 0)
-- Dependencies: 200
-- Name: SEQUENCE customers_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.customers_id_seq TO PUBLIC;


--
-- TOC entry 3802 (class 0 OID 0)
-- Dependencies: 205
-- Name: TABLE gallery_photos; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.gallery_photos FROM postgres;
GRANT ALL ON TABLE public.gallery_photos TO postgres WITH GRANT OPTION;


--
-- TOC entry 3804 (class 0 OID 0)
-- Dependencies: 339
-- Name: TABLE inv_orders; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.inv_orders FROM postgres;
GRANT ALL ON TABLE public.inv_orders TO postgres WITH GRANT OPTION;


--
-- TOC entry 3809 (class 0 OID 0)
-- Dependencies: 213
-- Name: TABLE inv_return_ds; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.inv_return_ds FROM postgres;
GRANT ALL ON TABLE public.inv_return_ds TO postgres WITH GRANT OPTION;


--
-- TOC entry 3811 (class 0 OID 0)
-- Dependencies: 215
-- Name: TABLE inv_returns; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.inv_returns FROM postgres;
GRANT ALL ON TABLE public.inv_returns TO postgres WITH GRANT OPTION;


--
-- TOC entry 3813 (class 0 OID 0)
-- Dependencies: 217
-- Name: TABLE inv_sale_ds; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.inv_sale_ds TO PUBLIC;


--
-- TOC entry 3815 (class 0 OID 0)
-- Dependencies: 218
-- Name: SEQUENCE inv_sale_ds_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.inv_sale_ds_id_seq TO PUBLIC;


--
-- TOC entry 3816 (class 0 OID 0)
-- Dependencies: 219
-- Name: TABLE inv_sales; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.inv_sales TO PUBLIC;


--
-- TOC entry 3818 (class 0 OID 0)
-- Dependencies: 220
-- Name: SEQUENCE inv_sales_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.inv_sales_id_seq TO PUBLIC;


--
-- TOC entry 3820 (class 0 OID 0)
-- Dependencies: 222
-- Name: SEQUENCE inv_trx_ds_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.inv_trx_ds_id_seq TO PUBLIC;


--
-- TOC entry 3821 (class 0 OID 0)
-- Dependencies: 341
-- Name: TABLE inv_trx_rest_ds; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.inv_trx_rest_ds FROM postgres;
GRANT ALL ON TABLE public.inv_trx_rest_ds TO postgres WITH GRANT OPTION;


--
-- TOC entry 3825 (class 0 OID 0)
-- Dependencies: 227
-- Name: TABLE kpr_simulation; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.kpr_simulation FROM postgres;
GRANT ALL ON TABLE public.kpr_simulation TO postgres WITH GRANT OPTION;


--
-- TOC entry 3827 (class 0 OID 0)
-- Dependencies: 229
-- Name: TABLE list_bank; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.list_bank TO PUBLIC;


--
-- TOC entry 3830 (class 0 OID 0)
-- Dependencies: 234
-- Name: TABLE m_doc_types; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.m_doc_types TO PUBLIC;


--
-- TOC entry 3833 (class 0 OID 0)
-- Dependencies: 343
-- Name: TABLE m_item_prices; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.m_item_prices FROM postgres;
GRANT ALL ON TABLE public.m_item_prices TO postgres WITH GRANT OPTION;


--
-- TOC entry 3837 (class 0 OID 0)
-- Dependencies: 240
-- Name: TABLE m_kpr_bank_payments; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.m_kpr_bank_payments TO PUBLIC;


--
-- TOC entry 3839 (class 0 OID 0)
-- Dependencies: 329
-- Name: TABLE m_positions; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.m_positions FROM postgres;
GRANT ALL ON TABLE public.m_positions TO postgres WITH GRANT OPTION;


--
-- TOC entry 3846 (class 0 OID 0)
-- Dependencies: 337
-- Name: TABLE order_ds; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.order_ds FROM postgres;
GRANT ALL ON TABLE public.order_ds TO postgres WITH GRANT OPTION;


--
-- TOC entry 3848 (class 0 OID 0)
-- Dependencies: 335
-- Name: TABLE orders; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.orders FROM postgres;
GRANT ALL ON TABLE public.orders TO postgres WITH GRANT OPTION;


--
-- TOC entry 3851 (class 0 OID 0)
-- Dependencies: 333
-- Name: TABLE product_subs; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.product_subs FROM postgres;
GRANT ALL ON TABLE public.product_subs TO postgres WITH GRANT OPTION;


--
-- TOC entry 3853 (class 0 OID 0)
-- Dependencies: 331
-- Name: TABLE products; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.products FROM postgres;
GRANT ALL ON TABLE public.products TO postgres WITH GRANT OPTION;


--
-- TOC entry 3855 (class 0 OID 0)
-- Dependencies: 261
-- Name: TABLE programs; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.programs FROM postgres;
GRANT ALL ON TABLE public.programs TO postgres WITH GRANT OPTION;


--
-- TOC entry 3863 (class 0 OID 0)
-- Dependencies: 275
-- Name: SEQUENCE rab_request_ds_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.rab_request_ds_id_seq TO PUBLIC;


--
-- TOC entry 3866 (class 0 OID 0)
-- Dependencies: 283
-- Name: TABLE sale_trx_docs; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.sale_trx_docs TO PUBLIC;


--
-- TOC entry 3871 (class 0 OID 0)
-- Dependencies: 287
-- Name: TABLE sale_trx_kpr_bank_payments; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.sale_trx_kpr_bank_payments TO PUBLIC;


--
-- TOC entry 3879 (class 0 OID 0)
-- Dependencies: 317
-- Name: TABLE tbl_absensi; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.tbl_absensi FROM postgres;
GRANT ALL ON TABLE public.tbl_absensi TO postgres WITH GRANT OPTION;


--
-- TOC entry 3881 (class 0 OID 0)
-- Dependencies: 307
-- Name: TABLE tbl_akun; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.tbl_akun FROM postgres;
GRANT ALL ON TABLE public.tbl_akun TO postgres WITH GRANT OPTION;


--
-- TOC entry 3882 (class 0 OID 0)
-- Dependencies: 309
-- Name: TABLE tbl_akun_detail; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.tbl_akun_detail FROM postgres;
GRANT ALL ON TABLE public.tbl_akun_detail TO postgres WITH GRANT OPTION;


--
-- TOC entry 3885 (class 0 OID 0)
-- Dependencies: 319
-- Name: TABLE tbl_cuti; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.tbl_cuti FROM postgres;
GRANT ALL ON TABLE public.tbl_cuti TO postgres WITH GRANT OPTION;


--
-- TOC entry 3887 (class 0 OID 0)
-- Dependencies: 327
-- Name: TABLE tbl_other_gaji; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.tbl_other_gaji FROM postgres;
GRANT ALL ON TABLE public.tbl_other_gaji TO postgres WITH GRANT OPTION;


--
-- TOC entry 3889 (class 0 OID 0)
-- Dependencies: 321
-- Name: TABLE tbl_ref_gaji; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.tbl_ref_gaji FROM postgres;
GRANT ALL ON TABLE public.tbl_ref_gaji TO postgres WITH GRANT OPTION;


--
-- TOC entry 3891 (class 0 OID 0)
-- Dependencies: 311
-- Name: TABLE tbl_saldo_akun; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.tbl_saldo_akun FROM postgres;
GRANT ALL ON TABLE public.tbl_saldo_akun TO postgres WITH GRANT OPTION;


--
-- TOC entry 3893 (class 0 OID 0)
-- Dependencies: 323
-- Name: TABLE tbl_setting_gaji; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.tbl_setting_gaji FROM postgres;
GRANT ALL ON TABLE public.tbl_setting_gaji TO postgres WITH GRANT OPTION;


--
-- TOC entry 3895 (class 0 OID 0)
-- Dependencies: 325
-- Name: TABLE tbl_shift; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.tbl_shift FROM postgres;
GRANT ALL ON TABLE public.tbl_shift TO postgres WITH GRANT OPTION;


--
-- TOC entry 3897 (class 0 OID 0)
-- Dependencies: 313
-- Name: TABLE tbl_trx_akuntansi; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.tbl_trx_akuntansi FROM postgres;
GRANT ALL ON TABLE public.tbl_trx_akuntansi TO postgres WITH GRANT OPTION;


--
-- TOC entry 3898 (class 0 OID 0)
-- Dependencies: 315
-- Name: TABLE tbl_trx_akuntansi_detail; Type: ACL; Schema: public; Owner: postgres
--

REVOKE ALL ON TABLE public.tbl_trx_akuntansi_detail FROM postgres;
GRANT ALL ON TABLE public.tbl_trx_akuntansi_detail TO postgres WITH GRANT OPTION;


--
-- TOC entry 2147 (class 826 OID 17266)
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: -; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres REVOKE ALL ON TABLES  FROM postgres;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres GRANT ALL ON TABLES  TO postgres WITH GRANT OPTION;


-- Completed on 2020-05-16 13:10:13

--
-- PostgreSQL database dump complete
--

