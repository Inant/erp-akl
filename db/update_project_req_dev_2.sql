ALTER TABLE "public"."project_req_developments" 
  DROP COLUMN "rab_id",
  DROP COLUMN "total",
  ADD COLUMN "rab_id" int4,
  ADD COLUMN "total" numeric(255);