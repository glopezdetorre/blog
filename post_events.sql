-- DROP TABLE post_events;
-- DROP SEQUENCE post_events_id_seq CASCADE;

CREATE SEQUENCE post_events_id_seq INCREMENT BY 1 MINVALUE 1;

CREATE TABLE post_events
(
  id integer NOT NULL DEFAULT nextval('post_events_id_seq'::regclass),
  data jsonb,
  CONSTRAINT post_pkey PRIMARY KEY (id)
);
CREATE INDEX post_aggregate_id_idx ON post_events ((data>'message'->'payload'->>'id'));