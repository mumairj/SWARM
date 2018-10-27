CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

CREATE OR REPLACE FUNCTION chunk_update_notify() RETURNS trigger AS $$
DECLARE
  id uuid;
	variant varchar;
	content varchar;
BEGIN
  IF TG_OP = 'INSERT' OR TG_OP = 'UPDATE' THEN
    id = NEW.id;
		variant=NEW.variant;
		content=(select replace(strip_tags(NEW.content),'&nbsp;',''));
  ELSE
    id = OLD.id;
  END IF;
  PERFORM pg_notify('chunk_update', json_build_object('table', TG_TABLE_NAME, 'id', id, 'type', TG_OP,'variant',variant,'content',content)::text);
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS chunk_notify_insert ON chunk;
CREATE TRIGGER chunk_notify_insert AFTER INSERT ON chunk FOR EACH ROW EXECUTE PROCEDURE chunk_update_notify();


CREATE OR REPLACE FUNCTION author_update_notify() RETURNS trigger AS $$
DECLARE
  chunk_id uuid;
	author_id uuid;
	user_display_name varchar;
	content_chunk varchar;
BEGIN
  IF TG_OP = 'INSERT' OR TG_OP = 'UPDATE' THEN
    chunk_id = NEW.chunk_id;
		author_id=NEW.author_id;
		user_display_name = (select display_name from "user" where id=NEW.author_id);
		content_chunk = (select replace(strip_tags(c.content),'&nbsp;','') from chunk c where id=NEW.chunk_id);
  ELSE
    chunk_id = OLD.chunk_id;
		author_id=NEW.author_id;
  END IF;
  PERFORM pg_notify('author_update', json_build_object('table', TG_TABLE_NAME, 'type', TG_OP,'chunk_uuid',chunk_id,'user_display_name',user_display_name,'content',content_chunk)::text);
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS author_update_notify ON chunk_author_relation;
CREATE TRIGGER author_update_notify AFTER INSERT ON chunk_author_relation FOR EACH ROW EXECUTE PROCEDURE author_update_notify();
