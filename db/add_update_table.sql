ALTER TABLE public.purchase_assets
    ADD COLUMN with_ppn boolean;
ALTER TABLE public.purchase_services
    ADD COLUMN with_ppn boolean;
ALTER TABLE public.purchases
    ADD COLUMN with_ppn boolean;
ALTER TABLE public.tbl_trx_akuntansi_detail
    ADD COLUMN no character varying(60) COLLATE pg_catalog."default";

    
CREATE SEQUENCE public.dev_projects_frame_worksubs_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.dev_projects_frame_worksubs_id_seq
    OWNER TO tanting;

CREATE SEQUENCE public.install_worksubs_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.install_worksubs_id_seq
    OWNER TO tanting;

CREATE SEQUENCE public.temp_worksubs_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.temp_worksubs_id_seq
    OWNER TO tanting;

CREATE TABLE public.dev_project_frame_worksubs
(
    id integer NOT NULL DEFAULT nextval('dev_projects_frame_worksubs_id_seq'::regclass),
    product_id integer,
    product_sub_id integer,
    worksub_id integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    dev_project_frame_id integer,
    CONSTRAINT dev_projects_frame_worksubs_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.dev_project_frame_worksubs
    OWNER to tanting;

CREATE TABLE public.install_worksubs
(
    id integer NOT NULL DEFAULT nextval('install_worksubs_id_seq'::regclass),
    install_order_id integer,
    install_order_d_id integer,
    worksub_id integer,
    price_work double precision,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    order_d_id integer,
    product_id integer,
    CONSTRAINT install_worksubs_pkey PRIMARY KEY (id),
    CONSTRAINT install_order_d_id FOREIGN KEY (install_order_d_id)
        REFERENCES public.install_order_ds (id) MATCH SIMPLE
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT VALID
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.install_worksubs
    OWNER to tanting;

-- Index: fki_install_order_d_id

-- DROP INDEX public.fki_install_order_d_id;

CREATE INDEX fki_install_order_d_id
    ON public.install_worksubs USING btree
    (install_order_d_id)
    TABLESPACE pg_default;

CREATE TABLE public.temp_worksubs
(
    id integer NOT NULL DEFAULT nextval('temp_worksubs_id_seq'::regclass),
    order_d_id integer,
    worksub_id integer,
    price double precision,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    CONSTRAINT temp_worksubs_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.temp_worksubs
    OWNER to tanting;

ALTER TABLE public.purchase_assets
    ADD COLUMN delivery_date date,
    ADD COLUMN delivery_fee double precision;

ALTER TABLE public.purchases
    ADD COLUMN delivery_date date,
    ADD COLUMN delivery_fee double precision;

ALTER TABLE public.purchases
    ADD COLUMN acc_director_date date,
    ADD COLUMN acc_manager_date date;
ALTER TABLE public.purchase_assets
    ADD COLUMN acc_director_date date,
    ADD COLUMN acc_manager_date date;
ALTER TABLE public.payment_suppliers
    ADD COLUMN delivery_fee double precision;
ALTER TABLE public.payment_supplier_ds
    ADD COLUMN delivery_fee double precision DEFAULT 0;

ALTER TABLE public.project_worksubs
    ADD COLUMN luas_1_a double precision,
    ADD COLUMN luas_1_b double precision,
    ADD COLUMN luas_2_a double precision,
    ADD COLUMN luas_2_b double precision,
    ADD COLUMN luas_3_a double precision,
    ADD COLUMN luas_3_b double precision;

ALTER TABLE public.tbl_setting_gaji
    ADD COLUMN uang_makan double precision,
    ADD COLUMN uang_transport double precision;

ALTER TABLE public.purchase_services
    ADD COLUMN delivery_date date,
    ADD COLUMN delivery_fee double precision,
    ADD COLUMN acc_director_date date,
    ADD COLUMN acc_manager_date date;
ALTER TABLE public.payment_suppliers
    ADD COLUMN purchase_service_id integer;

ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN purchase_service_id integer,
    ADD COLUMN inv_trx_service_id integer;

ALTER TABLE public.payment_suppliers
    ADD COLUMN inv_trx_service_id integer;

CREATE SEQUENCE public.inv_trx_services_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.inv_trx_services_id_seq
    OWNER TO postgres;

CREATE SEQUENCE public.inv_trx_service_ds_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.inv_trx_service_ds_id_seq
    OWNER TO postgres;

CREATE TABLE public.inv_trx_services
(
    id integer NOT NULL DEFAULT nextval('inv_trx_services_id_seq'::regclass),
    purchase_service_id integer,
    no character varying(50) COLLATE pg_catalog."default",
    inv_trx_date date,
    site_id integer,
    is_entry boolean,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    CONSTRAINT inv_trx_services_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.inv_trx_services
    OWNER to postgres;

CREATE TABLE public.inv_trx_service_ds
(
    id integer NOT NULL DEFAULT nextval('inv_trx_service_ds_id_seq'::regclass),
    inv_trx_service_id integer,
    amount double precision,
    m_unit_id integer,
    notes text COLLATE pg_catalog."default",
    value double precision,
    m_warehouse_id integer,
    base_price double precision,
    condition integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    service_name character varying(100) COLLATE pg_catalog."default",
    CONSTRAINT inv_trx_service_ds_pkey PRIMARY KEY (id),
    CONSTRAINT inv_trx_service_id FOREIGN KEY (id)
        REFERENCES public.inv_trx_services (id) MATCH SIMPLE
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT VALID
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.inv_trx_service_ds
    OWNER to postgres;

ALTER TABLE public.project_worksubs
    ADD COLUMN quantity double precision;

ALTER TABLE public.project_worksub_ds
    ADD COLUMN tipe_material character varying(10) COLLATE pg_catalog."default";

ALTER TABLE public.inv_order_ds
    ADD COLUMN dev_project_label_id integer;
ALTER TABLE public.dev_projects
    ADD COLUMN work_header character varying(30) COLLATE pg_catalog."default";
ALTER TABLE public.dev_project_ds
    ADD COLUMN work_detail character varying(100) COLLATE pg_catalog."default";

CREATE SEQUENCE public.giros_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.giros_id_seq
    OWNER TO postgres;

CREATE TABLE public.giros
(
    id integer NOT NULL DEFAULT nextval('giros_id_seq'::regclass),
    customer_bill_id integer,
    order_id integer,
    amount double precision,
    is_divided boolean DEFAULT false,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    customer_bill_d_id integer,
    pay_date date,
    no character varying(60) COLLATE pg_catalog."default",
    site_id integer,
    install_order_id integer,
    CONSTRAINT giros_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.giros
    OWNER to postgres;

ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN giro_id integer;

CREATE SEQUENCE public.cashes_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.cashes_id_seq
    OWNER TO postgres;
CREATE SEQUENCE public.giro_ds_id_seq
    INCREMENT 1
    START 4
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.giro_ds_id_seq
    OWNER TO postgres;

CREATE TABLE public.cashes
(
    id integer NOT NULL DEFAULT nextval('cashes_id_seq'::regclass),
    amount double precision,
    site_id integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    amount_in double precision,
    amount_out double precision,
    CONSTRAINT cashes_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.cashes
    OWNER to postgres;

CREATE TABLE public.giro_ds
(
    id integer NOT NULL DEFAULT nextval('giro_ds_id_seq'::regclass),
    giro_id integer,
    akun_id integer,
    amount double precision,
    notes text COLLATE pg_catalog."default",
    tipe character varying(20) COLLATE pg_catalog."default",
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    CONSTRAINT giro_ds_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.giro_ds
    OWNER to postgres;
ALTER TABLE public.calculate_stocks
    ADD COLUMN price double precision DEFAULT 0;

CREATE SEQUENCE public.tbl_saldo_months_id_seq
    INCREMENT 1
    START 744
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.tbl_saldo_months_id_seq
    OWNER TO postgres;

CREATE TABLE public.tbl_saldo_months
(
    id integer NOT NULL DEFAULT nextval('tbl_saldo_months_id_seq'::regclass),
    id_akun integer,
    bulan character varying(10) COLLATE pg_catalog."default",
    total double precision,
    dtm_crt timestamp without time zone DEFAULT now(),
    dtm_upd timestamp without time zone DEFAULT now(),
    location_id integer,
    total_debit double precision,
    total_kredit double precision,
    CONSTRAINT tbl_saldo_months_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.tbl_saldo_months
    OWNER to postgres;

ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN m_supplier_id integer;

CREATE SEQUENCE public.paid_customer_ds_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.paid_customer_ds_id_seq
    OWNER TO postgres;

CREATE SEQUENCE public.paid_customers_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.paid_customers_id_seq
    OWNER TO postgres;

CREATE SEQUENCE public.paid_supplier_ds_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.paid_supplier_ds_id_seq
    OWNER TO postgres;

CREATE SEQUENCE public.paid_suppliers_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.paid_suppliers_id_seq
    OWNER TO postgres;


CREATE TABLE public.paid_customers
(
    id integer NOT NULL DEFAULT nextval('paid_customers_id_seq'::regclass),
    paid_date date,
    site_id integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    amount double precision,
    notes text COLLATE pg_catalog."default",
    wop character varying(10) COLLATE pg_catalog."default",
    ref_code character varying(100) COLLATE pg_catalog."default",
    bank_number character varying(100) COLLATE pg_catalog."default",
    id_bank integer,
    no character varying(50) COLLATE pg_catalog."default",
    customer_id integer,
    CONSTRAINT paid_customers_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.paid_customers
    OWNER to postgres;

CREATE TABLE public.paid_customer_ds
(
    id integer NOT NULL DEFAULT nextval('paid_customer_ds_id_seq'::regclass),
    paid_customer_id integer,
    customer_bill_id integer,
    customer_bill_d_id integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    amount double precision,
    CONSTRAINT paid_customer_ds_pkey PRIMARY KEY (id),
    CONSTRAINT paid_customer_id FOREIGN KEY (paid_customer_id)
        REFERENCES public.paid_customers (id) MATCH SIMPLE
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT VALID
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.paid_customer_ds
    OWNER to postgres;

-- Index: fki_paid_customer_id

-- DROP INDEX public.fki_paid_customer_id;

CREATE INDEX fki_paid_customer_id
    ON public.paid_customer_ds USING btree
    (paid_customer_id)
    TABLESPACE pg_default;

CREATE TABLE public.paid_suppliers
(
    id integer NOT NULL DEFAULT nextval('paid_suppliers_id_seq'::regclass),
    amount double precision,
    paid_date date,
    m_supplier_id integer,
    no character varying(100) COLLATE pg_catalog."default",
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    bank_number character varying(100) COLLATE pg_catalog."default",
    ref_code character varying(100) COLLATE pg_catalog."default",
    id_bank integer,
    wop character varying(10) COLLATE pg_catalog."default",
    notes text COLLATE pg_catalog."default",
    site_id integer,
    CONSTRAINT paid_suppliers_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.paid_suppliers
    OWNER to postgres;


CREATE TABLE public.paid_supplier_ds
(
    id integer NOT NULL DEFAULT nextval('paid_supplier_ds_id_seq'::regclass),
    paid_supplier_id integer,
    payment_supplier_id integer,
    payment_supplier_d_id integer,
    amount double precision,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    CONSTRAINT paid_supplier_ds_pkey PRIMARY KEY (id),
    CONSTRAINT paid_supplier_id FOREIGN KEY (paid_supplier_id)
        REFERENCES public.paid_suppliers (id) MATCH SIMPLE
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT VALID
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.paid_supplier_ds
    OWNER to postgres;

-- Index: fki_paid_supplier_id

-- DROP INDEX public.fki_paid_supplier_id;

CREATE INDEX fki_paid_supplier_id
    ON public.paid_supplier_ds USING btree
    (paid_supplier_id)
    TABLESPACE pg_default;

ALTER TABLE public.payment_suppliers
    ADD COLUMN paid_no character varying(100) COLLATE pg_catalog."default";

ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN in_cash_journal boolean DEFAULT false,
    ADD COLUMN paid_customer_id integer,
    ADD COLUMN customer_id integer,
    ADD COLUMN paid_supplier_id integer;

CREATE SEQUENCE public.debt_ds_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.debt_ds_id_seq
    OWNER TO tanting;

CREATE TABLE public.debt_ds
(
    id integer NOT NULL DEFAULT nextval('debt_ds_id_seq'::regclass),
    debt_id integer,
    amount double precision,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_ata timestamp without time zone,
    akun_id integer,
    wop character varying(10) COLLATE pg_catalog."default",
    bank_number character varying(100) COLLATE pg_catalog."default",
    atas_nama character varying(100) COLLATE pg_catalog."default",
    id_bank integer,
    pay_date date,
    notes text COLLATE pg_catalog."default",
    CONSTRAINT debt_ds_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.debt_ds
    OWNER to tanting;

ALTER TABLE public.giros
    ADD COLUMN paid_customer_id integer,
    ADD COLUMN paid_supplier_id integer,
    ADD COLUMN is_fill integer DEFAULT 0;

COMMENT ON COLUMN public.giros.is_fill
    IS '0=pencairan, 1=pengisian';

ALTER TABLE public.purchase_assets
    ADD COLUMN is_without_ppn boolean DEFAULT false;
ALTER TABLE public.purchase_services
    ADD COLUMN is_without_ppn boolean DEFAULT false;
ALTER TABLE public.purchases
    ADD COLUMN is_without_ppn boolean DEFAULT false;

ALTER TABLE public.debts
    ADD COLUMN is_paid boolean DEFAULT false;

ALTER TABLE public.orders
    ADD COLUMN spk_number character varying(100) COLLATE pg_catalog."default";
ALTER TABLE public.purchase_assets
    ADD COLUMN spk_number character varying(100) COLLATE pg_catalog."default";
ALTER TABLE public.purchase_services
    ADD COLUMN spk_number character varying(100) COLLATE pg_catalog."default";
ALTER TABLE public.purchases
    ADD COLUMN spk_number character varying(100) COLLATE pg_catalog."default";
ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN customer_bill_id integer;

ALTER TABLE public.inv_request_prod_ds
    ADD COLUMN label character varying(100) COLLATE pg_catalog."default";
ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN inv_sale_id integer;

CREATE SEQUENCE public.paid_debts_id_seq
    INCREMENT 1
    START 4
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.paid_debts_id_seq
    OWNER TO postgres;
CREATE SEQUENCE public.paid_debt_ds_id_seq
    INCREMENT 1
    START 4
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.paid_debt_ds_id_seq
    OWNER TO postgres;

CREATE TABLE public.paid_debts
(
    id integer NOT NULL DEFAULT nextval('paid_debts_id_seq'::regclass),
    amount double precision,
    paid_date date,
    bank_number character varying(50) COLLATE pg_catalog."default",
    ref_code character varying(50) COLLATE pg_catalog."default",
    id_bank integer,
    "atas nama" character varying(100) COLLATE pg_catalog."default",
    wop character varying(10) COLLATE pg_catalog."default",
    notes text COLLATE pg_catalog."default",
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    m_supplier_id integer,
    no character varying(50) COLLATE pg_catalog."default",
    site_id integer,
    CONSTRAINT paid_debts_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.paid_debts
    OWNER to postgres;

CREATE TABLE public.paid_debt_ds
(
    id integer NOT NULL DEFAULT nextval('paid_debt_ds_id_seq'::regclass),
    paid_debt_id integer,
    debt_id integer,
    debt_d_id integer,
    amount double precision,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    CONSTRAINT paid_debt_ds_pkey PRIMARY KEY (id),
    CONSTRAINT paid_debt_id FOREIGN KEY (paid_debt_id)
        REFERENCES public.paid_debts (id) MATCH SIMPLE
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT VALID
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.paid_debt_ds
    OWNER to postgres;

-- Index: fki_paid_debt_id

-- DROP INDEX public.fki_paid_debt_id;

CREATE INDEX fki_paid_debt_id
    ON public.paid_debt_ds USING btree
    (paid_debt_id)
    TABLESPACE pg_default;

ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN paid_debt_id integer;

ALTER TABLE public.customer_bills
    ADD COLUMN create_date date;

CREATE SEQUENCE public.calculate_prices_id_seq
    INCREMENT 1
    START 11049
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.calculate_prices_id_seq
    OWNER TO postgres;

GRANT ALL ON SEQUENCE public.calculate_prices_id_seq TO PUBLIC;

GRANT ALL ON SEQUENCE public.calculate_prices_id_seq TO postgres;

CREATE TABLE public.calculate_prices
(
    id integer NOT NULL DEFAULT nextval('calculate_prices_id_seq'::regclass),
    site_id integer,
    type character varying(20) COLLATE pg_catalog."default",
    m_item_id integer,
    m_unit_id integer,
    amount double precision,
    amount_in double precision,
    amount_out double precision,
    last_month character varying(10) COLLATE pg_catalog."default",
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    price double precision DEFAULT 0,
    CONSTRAINT calculate_prices_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.calculate_prices
    OWNER to postgres;

GRANT ALL ON TABLE public.calculate_prices TO postgres;

GRANT ALL ON TABLE public.calculate_prices TO PUBLIC;

ALTER TABLE public.customer_bills
    ADD COLUMN with_pph boolean DEFAULT false;

ALTER TABLE public.payment_suppliers
    ADD COLUMN create_date date;
ALTER TABLE public.m_suppliers
    ADD COLUMN rekening_number character varying(100) COLLATE pg_catalog."default";

ALTER TABLE public.tbl_trx_akuntansi_detail
    ADD COLUMN m_item_id integer;

ALTER TABLE public.payment_suppliers
    ADD COLUMN no_surat_jalan character varying(100) COLLATE pg_catalog."default",
    ADD COLUMN no_surat_jalan_jasa character varying(100) COLLATE pg_catalog."default";

CREATE SEQUENCE public.payment_supplier_details_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.payment_supplier_details_id_seq
    OWNER TO postgres;

CREATE TABLE public.payment_supplier_details
(
    id integer NOT NULL DEFAULT nextval('payment_supplier_details_id_seq'::regclass),
    purchase_id integer,
    purchase_asset_id integer,
    inv_trx_id integer,
    purchase_service_id integer,
    inv_trx_service_id integer,
    total double precision,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    payment_supplier_id integer,
    CONSTRAINT payment_supplier_details_pkey PRIMARY KEY (id),
    CONSTRAINT payment_supplier_id FOREIGN KEY (payment_supplier_id)
        REFERENCES public.payment_suppliers (id) MATCH SIMPLE
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT VALID
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.payment_supplier_details
    OWNER to postgres;

-- Index: fki_payment_supplier_id

-- DROP INDEX public.fki_payment_supplier_id;

CREATE INDEX fki_payment_supplier_id
    ON public.payment_supplier_details USING btree
    (payment_supplier_id)
    TABLESPACE pg_default;

ALTER TABLE public.paid_suppliers
    ADD COLUMN amount_ppn double precision;

CREATE SEQUENCE public.customer_bill_histories_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.customer_bill_histories_id_seq
    OWNER TO postgres;

CREATE TABLE public.customer_bill_histories
(
    id integer NOT NULL DEFAULT nextval('customer_bill_histories_id_seq'::regclass),
    customer_bill_id integer,
    user_id integer,
    date_bill timestamp without time zone,
    status_bill integer,
    reason_of_bill text COLLATE pg_catalog."default",
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    CONSTRAINT customer_bill_histories_pkey PRIMARY KEY (id),
    CONSTRAINT customer_bill_id FOREIGN KEY (customer_bill_id)
        REFERENCES public.customer_bills (id) MATCH SIMPLE
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT VALID
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.customer_bill_histories
    OWNER to postgres;

ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN payment_id integer,
    ADD COLUMN payment_per_week_id integer,
    ADD COLUMN payment_per_week_d_id integer,
    ADD COLUMN payment_cost_other_id integer;

ALTER TABLE public.cashes
    ADD COLUMN m_warehouse_id integer;

ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN m_warehouse_id integer;

ALTER TABLE public.dev_project_workers
    ADD COLUMN notes text COLLATE pg_catalog."default";

ALTER TABLE public.customer_bills
    ADD COLUMN invoice_no character varying(100) COLLATE pg_catalog."default";
ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN payment_order_install_id integer;

ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN bill_vendor_id integer;
CREATE SEQUENCE public.bill_vendors_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.bill_vendors_id_seq
    OWNER TO postgres;

CREATE TABLE public.bill_vendors
(
    id integer NOT NULL DEFAULT nextval('bill_vendors_id_seq'::regclass),
    order_id integer,
    install_order_id integer,
    m_supplier_id integer,
    no character varying(100) COLLATE pg_catalog."default",
    bill_no character varying(100) COLLATE pg_catalog."default",
    notes text COLLATE pg_catalog."default",
    amount double precision,
    create_date date,
    due_date date,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    with_pph boolean DEFAULT false,
    is_paid boolean DEFAULT false,
    CONSTRAINT bill_vendors_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.bill_vendors
    OWNER to postgres;

ALTER TABLE public.payment_suppliers
    ADD COLUMN bill_no character varying(100) COLLATE pg_catalog."default";

-- Table: public.paid_bill_vendors

-- DROP TABLE public.paid_bill_vendors;

CREATE TABLE public.paid_bill_vendors
(
    id integer NOT NULL DEFAULT nextval('paid_bill_vendors_id_seq'::regclass),
    m_supplier_id integer,
    no character varying(100) COLLATE pg_catalog."default",
    amount double precision,
    wop character varying(20) COLLATE pg_catalog."default",
    bank_number character varying(100) COLLATE pg_catalog."default",
    ref_code character varying(100) COLLATE pg_catalog."default",
    id_bank integer,
    notes text COLLATE pg_catalog."default",
    paid_date date,
    site_id integer,
    atas_nama character varying(100) COLLATE pg_catalog."default",
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    CONSTRAINT paid_bill_vendors_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.paid_bill_vendors
    OWNER to postgres;

-- Table: public.paid_bill_vendor_ds

-- DROP TABLE public.paid_bill_vendor_ds;

CREATE TABLE public.paid_bill_vendor_ds
(
    id integer NOT NULL DEFAULT nextval('paid_bill_vendor_ds_id_seq'::regclass),
    paid_bill_vendor_id integer,
    bill_vendor_id integer,
    amount double precision,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    CONSTRAINT paid_bill_vendor_ds_pkey PRIMARY KEY (id),
    CONSTRAINT paid_bill_vendor_id FOREIGN KEY (paid_bill_vendor_id)
        REFERENCES public.paid_bill_vendors (id) MATCH SIMPLE
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT VALID
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.paid_bill_vendor_ds
    OWNER to postgres;

-- SEQUENCE: public.paid_bill_vendor_ds_id_seq

-- DROP SEQUENCE public.paid_bill_vendor_ds_id_seq;

CREATE SEQUENCE public.paid_bill_vendor_ds_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.paid_bill_vendor_ds_id_seq
    OWNER TO postgres;

-- SEQUENCE: public.paid_bill_vendors_id_seq

-- DROP SEQUENCE public.paid_bill_vendors_id_seq;

CREATE SEQUENCE public.paid_bill_vendors_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.paid_bill_vendors_id_seq
    OWNER TO postgres;

CREATE SEQUENCE public.account_hpps_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.account_hpps_id_seq
    OWNER TO postgres;

CREATE TABLE public.account_hpps
(
    id integer NOT NULL DEFAULT nextval('account_hpps_id_seq'::regclass),
    m_item_id integer,
    hpp_id integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    CONSTRAINT account_hpps_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.account_hpps
    OWNER to postgres;

ALTER TABLE public.inv_sales
    ADD COLUMN create_date date;

ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN paid_sell_item_id integer;

CREATE SEQUENCE public.paid_sell_items_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.paid_sell_items_id_seq
    OWNER TO postgres;

CREATE SEQUENCE public.paid_sell_item_ds_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.paid_sell_item_ds_id_seq
    OWNER TO postgres;

CREATE TABLE public.paid_sell_items
(
    id integer NOT NULL DEFAULT nextval('paid_sell_items_id_seq'::regclass),
    paid_date date,
    site_id integer,
    amount double precision,
    notes text COLLATE pg_catalog."default",
    wop character varying(20) COLLATE pg_catalog."default",
    ref_code bit varying(100),
    bank_number character varying(100) COLLATE pg_catalog."default",
    id_bank integer,
    atas_nama character varying(20) COLLATE pg_catalog."default",
    no character varying(50) COLLATE pg_catalog."default",
    customer_id integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    CONSTRAINT paid_sell_items_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.paid_sell_items
    OWNER to postgres;

CREATE TABLE public.paid_sell_item_ds
(
    id integer NOT NULL DEFAULT nextval('paid_sell_item_ds_id_seq'::regclass),
    paid_sell_item_id integer,
    inv_sale_id integer,
    amount double precision,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    CONSTRAINT paid_sell_item_ds_pkey PRIMARY KEY (id),
    CONSTRAINT paid_sell_item_id FOREIGN KEY (paid_sell_item_id)
        REFERENCES public.paid_sell_items (id) MATCH SIMPLE
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT VALID
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.paid_sell_item_ds
    OWNER to postgres;

-- Index: fki_paid_sell_item_id

-- DROP INDEX public.fki_paid_sell_item_id;

CREATE INDEX fki_paid_sell_item_id
    ON public.paid_sell_item_ds USING btree
    (paid_sell_item_id)
    TABLESPACE pg_default;

ALTER TABLE public.tbl_trx_akuntansi
    ADD COLUMN stock_adjustment_id integer;

ALTER TABLE public.inv_trxes
    ADD COLUMN stock_adjustment_id integer;

CREATE SEQUENCE public.stock_adjusment_ds_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.stock_adjusment_ds_id_seq
    OWNER TO postgres;

CREATE SEQUENCE public.stock_adjusments_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 2147483647
    CACHE 1;

ALTER SEQUENCE public.stock_adjusments_id_seq
    OWNER TO postgres;

CREATE TABLE public.stock_adjustments
(
    id integer NOT NULL DEFAULT nextval('stock_adjusments_id_seq'::regclass),
    site_id integer,
    create_date date,
    m_warehouse_id integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    notes text COLLATE pg_catalog."default",
    no character varying(60) COLLATE pg_catalog."default",
    CONSTRAINT stock_adjusments_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.stock_adjustments
    OWNER to postgres;

CREATE TABLE public.stock_adjustment_ds
(
    id integer NOT NULL DEFAULT nextval('stock_adjusment_ds_id_seq'::regclass),
    stock_adjustment_id integer,
    m_item_id integer,
    m_unit_id integer,
    base_price double precision,
    amount double precision,
    m_warehouse_id integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    CONSTRAINT stock_adjusment_ds_pkey PRIMARY KEY (id),
    CONSTRAINT stock_adjusment_id FOREIGN KEY (stock_adjustment_id)
        REFERENCES public.stock_adjustments (id) MATCH SIMPLE
        ON UPDATE CASCADE
        ON DELETE CASCADE
        NOT VALID
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.stock_adjustment_ds
    OWNER to postgres;

-- Index: fki_stock_adjusment_id

-- DROP INDEX public.fki_stock_adjusment_id;

CREATE INDEX fki_stock_adjusment_id
    ON public.stock_adjustment_ds USING btree
    (stock_adjustment_id)
    TABLESPACE pg_default;

ALTER TABLE public.purchases
    ADD COLUMN credit_age integer DEFAULT 0;

ALTER TABLE public.purchase_assets
    ADD COLUMN credit_age integer DEFAULT 0;