CREATE TABLE "public"."project_req_development_ds" (
  "id" serial2,
  "project_req_development_id" int4 NOT NULL,
  "m_item_id" int4 NOT NULL,
  "amount" numeric(255) NOT NULL,
  PRIMARY KEY ("id"),
  CONSTRAINT "project_req_developments_project_req_development_ds_fk" FOREIGN KEY ("project_req_development_id") REFERENCES "public"."project_req_developments" ("id"),
  CONSTRAINT "m_items_project_req_development_ds" FOREIGN KEY ("m_item_id") REFERENCES "public"."m_items" ("id")
)
;