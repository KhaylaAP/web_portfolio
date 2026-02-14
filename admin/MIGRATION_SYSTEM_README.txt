
[2026-02-14 13:04:58] ❌ Migrasi gagal: 2026-02-13_initial_setup.sql - SQLSTATE[HY000]: General error: 3730 Cannot drop table 'users' referenced by a foreign key constraint 'fk_user_page_user' on table 'user_page_access'.
[2026-02-14 13:04:58] ❌ Migrasi gagal: 2026-02-14_add_sample_projects.sql - SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near '[admin/migrate/2026-02-14_add_sample_projects.sql]
-- Migration: Add Sample Proj' at line 1
[2026-02-14 13:04:58] ❌ Migrasi gagal: 2026-02-14_default_data.sql - SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'projects' for key 'pages.page_code'
[2026-02-14 13:14:49] ✅ Migrasi berhasil: 2026-02-14_fix_001_disable_foreign_keys.sql
[2026-02-14 13:14:49] ❌ Migrasi gagal: 2026-02-14_fix_002_create_tables.sql - SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near '[admin/migrate/2026-02-14_fix_002_create_tables.sql]
-- Migration: Create all ta' at line 1
[2026-02-14 13:14:49] ✅ Migrasi berhasil: 2026-02-14_fix_003_insert_data.sql
[2026-02-14 13:14:49] ✅ Migrasi berhasil: 2026-02-14_fix_005_enable_foreign_keys.sql
[2026-02-14 13:25:22] ✅ Migrasi berhasil: 2026-02-14_fix_004_grant_admin_permissions.sql
[2026-02-14 13:39:25] 📝 File migrasi dibuat: 2026-02-14_1771076365_testing_file.sql - testing_file
[2026-02-14 13:39:33] ✅ Migrasi berhasil: 2026-02-14_1771076365_testing_file.sql