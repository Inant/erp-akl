ALTER TABLE public.purchases
    ADD COLUMN discount double precision;
ALTER TABLE public.purchases
    ADD COLUMN discount_type character varying(20) COLLATE pg_catalog."default";
ALTER TABLE public.purchases
    ADD COLUMN with_ao boolean;
ALTER TABLE public.purchases
    ADD COLUMN acc_ao boolean DEFAULT false;
ALTER TABLE public.purchases
    ADD COLUMN status_payment boolean;
ALTER TABLE public.purchases
    ADD COLUMN signature_holding text COLLATE pg_catalog."default";
ALTER TABLE public.purchases
    ADD COLUMN signature_supplier text COLLATE pg_catalog."default";

ALTER TABLE public.purchase_assets
    ADD COLUMN discount double precision;
ALTER TABLE public.purchase_assets
    ADD COLUMN discount_type character varying(20) COLLATE pg_catalog."default";
ALTER TABLE public.purchase_assets
    ADD COLUMN with_ao boolean;
ALTER TABLE public.purchase_assets
    ADD COLUMN acc_ao boolean DEFAULT false;
ALTER TABLE public.purchase_assets
    ADD COLUMN status_payment boolean;
ALTER TABLE public.purchase_assets
    ADD COLUMN signature_holding text COLLATE pg_catalog."default";
ALTER TABLE public.purchase_assets
    ADD COLUMN signature_supplier text COLLATE pg_catalog."default";