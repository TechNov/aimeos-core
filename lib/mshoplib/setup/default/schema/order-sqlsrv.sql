--
-- SQL Server specific database definitions
--

CREATE INDEX "idx_msordbaprat_si_cd_va" ON "mshop_order_base_product_attr" ("siteid", "code", "value");

CREATE INDEX "idx_msordbaseat_si_cd_va" ON "mshop_order_base_service_attr" ("siteid", "code", "value");
