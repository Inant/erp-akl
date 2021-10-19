ALTER TABLE "public"."project_worksubs" 
  ADD COLUMN "estimation_in_minute" int4;

ALTER TABLE "public"."inv_request_ds" 
  ADD COLUMN "buy_date" timestamp;

ALTER TABLE "public"."project_worksub_ds" 
  ADD COLUMN "amount_unit_child" numeric(255),
  ADD COLUMN "qty_item" int4,
  ADD COLUMN "notes" varchar(255);


ALTER TABLE public.inv_requests
    ADD COLUMN project_req_development_id integer DEFAULT 0;

ALTER TABLE public.inv_orders
    ADD COLUMN project_req_development_id integer;

ALTER TABLE public.inv_orders
    ADD COLUMN type character varying(20) COLLATE pg_catalog."default";

ALTER TABLE public.inv_orders
    ADD COLUMN amount double precision DEFAULT 0;

ALTER TABLE public.inv_order_ds
    ADD COLUMN condition character varying COLLATE pg_catalog."default";
ALTER TABLE public.inv_order_ds
    ADD COLUMN is_entry integer DEFAULT 0;

ALTER TABLE public.dev_projects
    ADD COLUMN project_req_development_id integer;


CREATE TABLE public.payments
(
    id integer NOT NULL DEFAULT nextval('payments_id_seq'::regclass),
    inv_order_id integer,
    wop character varying(20) COLLATE pg_catalog."default",
    amount double precision,
    type character varying(20) COLLATE pg_catalog."default",
    name character varying(200) COLLATE pg_catalog."default",
    note text COLLATE pg_catalog."default",
    created_at timestamp without time zone DEFAULT now(),
    updated_at timestamp without time zone DEFAULT now(),
    deleted_at timestamp without time zone,
    CONSTRAINT payments_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.payments
    OWNER to postgres;

GRANT ALL ON TABLE public.payments TO postgres WITH GRANT OPTION;


CREATE TABLE public.project_req_developments
(
    order_id integer,
    rab_id integer,
    total double precision,
    request_date timestamp without time zone,
    finish_date timestamp without time zone,
    note text COLLATE pg_catalog."default",
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    id integer NOT NULL DEFAULT nextval('project_req_developments_id_seq'::regclass),
    no character varying(100) COLLATE pg_catalog."default",
    status boolean DEFAULT false,
    work_start timestamp without time zone,
    work_end timestamp without time zone,
    CONSTRAINT project_req_developments_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.project_req_developments
    OWNER to postgres;

GRANT ALL ON TABLE public.project_req_developments TO postgres WITH GRANT OPTION;


ALTER TABLE public.inv_orders
    ADD COLUMN due_date date;

COMMENT ON COLUMN public.inv_orders.due_date
    IS 'payment';

ALTER TABLE public.inv_orders
    ADD COLUMN no_surat_jalan character varying(100) COLLATE pg_catalog."default";

ALTER TABLE public.inv_orders
    ADD COLUMN payment_status boolean DEFAULT false;

ALTER TABLE public.products
    ADD COLUMN customer_id integer DEFAULT 0;

ALTER TABLE public.inv_trx_ds
    ADD COLUMN m_warehouse_id integer;

ALTER TABLE public.inv_request_ds
    ADD COLUMN m_warehouse_id integer;

ALTER TABLE public.m_warehouses
    ADD COLUMN site_id integer;

ALTER TABLE public.users
    ADD COLUMN m_warehouse_id integer;

ALTER TABLE public.inv_trxes
    ADD COLUMN ts_warehouse_id integer;

ALTER TABLE public.inv_request_rest_ds
    ADD COLUMN m_warehouse_id integer;

ALTER TABLE public.inv_trx_rest_ds
    ADD COLUMN m_warehouse_id integer;

ALTER TABLE public.transfer_stock_ds
    ADD COLUMN m_warehouse_id integer;

ALTER TABLE public.orders
    ADD COLUMN paid_off_date timestamp without time zone;

ALTER TABLE public.purchases
    ADD COLUMN discount double precision;

ALTER TABLE public.purchases
    ADD COLUMN discount_type character varying(20) COLLATE pg_catalog."default";

ALTER TABLE public.products
    ADD COLUMN installation_fee double precision DEFAULT 0;

ALTER TABLE public.inv_trxes
    ADD COLUMN purchase_asset_id integer;

ALTER TABLE public.inv_order_ds
    ADD COLUMN no character varying(50) COLLATE pg_catalog."default";

ALTER TABLE public.inv_order_ds
    ADD COLUMN inv_request_prod_id integer;

ALTER TABLE public.purchase_ds
    ADD COLUMN price_before_discount double precision;

ALTER TABLE public.payments
    ADD COLUMN order_id integer;
ALTER TABLE public.payments
    ADD COLUMN project_req_development_id integer;
ALTER TABLE public.payments
    ADD COLUMN description character varying(100) COLLATE pg_catalog."default";
ALTER TABLE public.payments
    ADD COLUMN no character varying(30) COLLATE pg_catalog."default";
ALTER TABLE public.payments
    ADD COLUMN pay_date date;
ALTER TABLE public.payments
    ADD COLUMN ref_code character varying(100) COLLATE pg_catalog."default";
ALTER TABLE public.payments
    ADD COLUMN bank_number character varying(60) COLLATE pg_catalog."default";
ALTER TABLE public.payments
    ADD COLUMN atas_nama character varying(100) COLLATE pg_catalog."default";
ALTER TABLE public.payments
    ADD COLUMN id_bank integer;
ALTER TABLE public.payments
    ADD COLUMN is_out_source boolean;
ALTER TABLE public.payments
    ADD COLUMN is_production boolean;

ALTER TABLE public.payment_cost_others
    ADD COLUMN site_id integer;
ALTER TABLE public.payment_per_weeks
    ADD COLUMN site_id integer;
ALTER TABLE public.payments
    ADD COLUMN site_id integer;
ALTER TABLE public.project_req_developments
    ADD COLUMN site_id integer;

ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN notes character varying(100) COLLATE pg_catalog."default";

ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN purchase_id integer;
ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN purchase_asset_id integer;
ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN user_id integer;
ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN inv_trx_id integer;
ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN order_id integer;
ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN project_req_development_id integer;
ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN inv_request_id integer;

ALTER TABLE public.dev_project_frames
    ADD COLUMN inv_request_id integer;
ALTER TABLE public.dev_project_frames
    ADD COLUMN inv_request_id integer;
ALTER TABLE public.products
    ADD COLUMN amount_set double precision;

ALTER TABLE public.customer_bills
    ADD COLUMN bill_no character varying(100) COLLATE pg_catalog."default";
ALTER TABLE public.customer_bills
    ADD COLUMN bill_address text COLLATE pg_catalog."default";

CREATE SEQUENCE public.customer_projects_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.customer_projects_id_seq
    OWNER TO tanting;
CREATE TABLE public.customer_projects
(
    id integer NOT NULL DEFAULT nextval('customer_projects_id_seq'::regclass),
    customer_id integer,
    name character varying(200) COLLATE pg_catalog."default",
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    site_id integer,
    CONSTRAINT customer_projects_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.customer_projects
    OWNER to tanting;

ALTER TABLE public.orders
    ADD COLUMN customer_project_id integer;
ALTER TABLE public.purchase_assets
    ADD COLUMN notes text COLLATE pg_catalog."default";
ALTER TABLE public.purchase_assets
    ADD COLUMN signature_request character varying(100) COLLATE pg_catalog."default";

ALTER TABLE public.purchase_services
    ADD COLUMN notes text COLLATE pg_catalog."default";
ALTER TABLE public.purchase_services
    ADD COLUMN signature_request character varying(100) COLLATE pg_catalog."default";

ALTER TABLE public.purchases
    ADD COLUMN notes text COLLATE pg_catalog."default";
ALTER TABLE public.purchases
    ADD COLUMN signature_request character varying(100) COLLATE pg_catalog."default";

ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN debt_id integer;

ALTER TABLE public.customers
    ADD COLUMN npwp_address text COLLATE pg_catalog."default";
ALTER TABLE public.customers
    ADD COLUMN phone_no2 character varying(50) COLLATE pg_catalog."default";