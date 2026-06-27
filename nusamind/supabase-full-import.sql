-- Nusamind AI - Supabase Full Import
-- Run this to create the complete schema.
-- User data will be created through Supabase Auth on first login.
-- After users sign up via Supabase Auth, the app will insert
-- corresponding rows into public.users via a database trigger or
-- the application layer.

-- Execute the schema file:
-- \i supabase-schema.sql

-- ============================================================
-- SCHEMA ONLY — see supabase-schema.sql for table definitions.
-- ============================================================
-- This file exists as a single-step import reference.
-- The schema in supabase-schema.sql is the source of truth.

-- To import:
--   1. Run supabase-schema.sql in the Supabase SQL Editor
--   2. Configure Supabase Auth providers (email, Google, etc.)
--   3. When users sign up, a trigger or the app will insert into
--      public.users with the UUID from auth.users
