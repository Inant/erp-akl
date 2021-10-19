CREATE TABLE "public"."m_equivalents" (
  "id" serial2,
  "code" varchar(255) NOT NULL,
  "value" numeric NOT NULL,
  "m_unit_id" int4,
  "created_at" timestamp without time zone,
  "updated_at" timestamp without time zone,
  "deleted_at" timestamp without time zone
  PRIMARY KEY ("id"),
  CONSTRAINT "m_equivalents_m_units_fk" FOREIGN KEY ("m_unit_id") REFERENCES "public"."m_units" ("id")
)
;


CREATE TABLE "public"."m_products" (
  "id" serial2,
  "code" varchar(255) NOT NULL,
  "name" varchar(255) NOT NULL,
  "created_at" timestamp,
  "updated_at" timestamp,
  "deleted_at" timestamp,
  PRIMARY KEY ("id")
)
;

CREATE TABLE "public"."m_product_ds" (
  "id" serial2,
  "m_product_id" int4 NOT NULL,
  "m_item_id" int4 NOT NULL,
  "formula" varchar(255) NOT NULL,
  "created_at" timestamp,
  "updated_at" timestamp,
  "deleted_at" timestamp,
  PRIMARY KEY ("id"),
  CONSTRAINT "m_product_ds_m_products_fk" FOREIGN KEY ("m_product_id") REFERENCES "public"."m_products" ("id"),
  CONSTRAINT "m_product_ds_m_items_fk" FOREIGN KEY ("m_item_id") REFERENCES "public"."m_items" ("id")
)
;

ALTER TABLE "public"."products" 
  ADD COLUMN "m_product_id" int4,
  ADD CONSTRAINT "products_m_products_fk" FOREIGN KEY ("m_product_id") REFERENCES "public"."m_products" ("id");