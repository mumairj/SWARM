drop view if exists view_tags;
drop view if exists view_comments;
drop view if exists view_prob_hyp_comm_desc_team;
drop view if exists view_comments;
drop function if exists strip_tags;

drop function if exists strip_tags;
create function strip_tags(text)
  returns text
language sql
as $$
SELECT regexp_replace(
        regexp_replace($1, E'(?x)<[^>]*?(\s alt \s* = \s* ([\'"]) ([^>]*?) \2) [^>]*? >', E'\3'), 
       E'(?x)(< [^>]*? >)', '', 'g')
$$;

alter function strip_tags(text)
  owner to postgres;



-- auto-generated definition
drop table if exists swarm_scores;
create table swarm_scores
(
  team_type varchar(3),
  team      varchar,
  problem   varchar,
  code      varchar(4),
  score     double precision
);

alter table swarm_scores
  owner to postgres;

drop table if exists chunk_sentiment;
create table chunk_sentiment
(
  chunk_id  uuid,
  sentiment varchar(10) default 'neutral' :: character varying
);

alter table chunk_sentiment
  owner to postgres;

 drop table if exists swarm_user_tag_chunk;
  -- auto-generated definition
create table swarm_user_tag_chunk
(
  user_display_name        varchar,
  tagged_user_display_name varchar,
  chunk_uuid               uuid
);

alter table swarm_user_tag_chunk
  owner to postgres;

 drop view if exists team_members;
 create view team_members as
  SELECT pug.id AS team_id, pugm.user_id, pug.title, u.display_name
  FROM ((perm_user_group pug
      JOIN perm_user_group_member pugm ON ((pug.id = pugm.user_group_id)))
      JOIN "user" u ON ((pugm.user_id = u.id)))
  GROUP BY pug.id, pugm.user_id, pug.title, u.display_name;

alter table team_members
  owner to postgres;


drop view if exists chunk_swarm;
create view chunk_swarm as
  SELECT c.id,
         c.public_visibility,
         c.created_at,
         c.last_edited,
         c.title,
         c.summary,
         c.content,
         c.content_type,
         c.description,
         c.parent_id,
         c.parent_relation,
         c.ext,
         c.state,
         c.due_date,
         c.variant,
         c.level,
         c.deleted,
         c.delete_at,
         c.published_date,
         c.start_date,
         u.display_name
  FROM ((chunk c
      JOIN chunk_author_relation car ON ((c.id = car.chunk_id)))
      JOIN "user" u ON ((car.author_id = u.id)));

alter table chunk_swarm
  owner to postgres;

drop view if exists view_quality_prob_hyp_team_avg;
create view view_quality_prob_hyp_team_avg as
  SELECT c.id                        AS problem_id,
         cpr.child_id                AS hypothesis_id,
         t.type,
         c.title                     AS problem_title,
         pug.title                   AS team,
         round(avg(mv.mid_value), 2) AS rating
  FROM (((((((chunk c
      JOIN chunk_parent_relation cpr ON ((cpr.parent_id = c.id)))
      LEFT JOIN chunk_type_relation r ON ((cpr.child_id = r.chunk_id)))
      LEFT JOIN type t ON ((r.type_id = t.id)))
      LEFT JOIN chunk_user_group_relation cugr ON ((cugr.chunk_id = c.id)))
      LEFT JOIN perm_user_group pug ON ((pug.id = cugr.user_group_id)))
      JOIN metric_value mv ON ((mv.chunk_id = cpr.child_id)))
      JOIN metric m ON ((m.id = mv.metric_id)))
  WHERE (((t.type) :: text = 'claim' :: text) AND (m.id = '7bc0979f-030f-426d-ac9f-daf85eb15dd9' :: uuid) AND
         (mv.mid_value IS NOT NULL))
  GROUP BY c.id, cpr.child_id, t.type, c.title, pug.title;

alter table view_quality_prob_hyp_team_avg
  owner to postgres;


  
drop view if exists view_prob_hyp_comm_desc_team;
create view view_prob_hyp_comm_desc_team as
  SELECT c.id,
         c.public_visibility,
         c.created_at,
         c.last_edited,
         c.title,
         c.summary,
         c.content,
         c.content_type,
         c.description,
         c.parent_id,
         c.parent_relation,
         c.ext,
         c.state,
         c.due_date,
         c.variant,
         c.level,
         c.deleted,
         c.delete_at,
         c.published_date,
         c.start_date,
         c.id                     AS problem_id,
         cpr.child_id             AS hypothesis_id,
         cpr2.child_id            AS hypothesis_child_id,
         cpr2.chunk_relation      AS hypothesis_child_relation,
         pug.id                   AS team_id,
         pug.title                AS team,
         t.type                   AS hypothesis_type,
         u_prb.display_name       AS problem_author,
         u_hyp.display_name       AS hyp_author,
         u_hyp_child.display_name AS hyp_child_author
  FROM ((((((((((((chunk c
      JOIN chunk_parent_relation cpr ON ((cpr.parent_id = c.id)))
      LEFT JOIN chunk_user_group_relation cugr ON ((c.id = cugr.chunk_id)))
      LEFT JOIN perm_user_group pug ON ((pug.id = cugr.user_group_id)))
      LEFT JOIN chunk_type_relation ctr ON ((ctr.chunk_id = cpr.child_id)))
      LEFT JOIN chunk_parent_relation cpr2 ON ((cpr2.parent_id = cpr.child_id)))
      JOIN type t ON ((ctr.type_id = t.id)))
      LEFT JOIN chunk_author_relation car ON ((car.chunk_id = c.id)))
      LEFT JOIN "user" u_prb ON ((u_prb.id = car.author_id)))
      LEFT JOIN chunk_author_relation car_hyp ON ((car_hyp.chunk_id = cpr.child_id)))
      LEFT JOIN "user" u_hyp ON ((u_hyp.id = car_hyp.author_id)))
      LEFT JOIN chunk_author_relation car_hyp_child ON ((car_hyp_child.chunk_id = cpr2.child_id)))
      LEFT JOIN "user" u_hyp_child ON ((u_hyp_child.id = car_hyp_child.author_id)))
  WHERE (c.parent_id IS NULL);

alter table view_prob_hyp_comm_desc_team
  owner to postgres;



 drop view if exists view_comments;
 create view view_comments as
  SELECT problem.parent_id                                                      AS problem_id,
         cpr.parent_id                                                          AS hypothesis_id,
         c.id                                                                   AS comment_id,
         pug.title                                                              AS team,
         c.display_name                                                         AS comment_author,
         problem_chunk.title                                                    AS problem_title,
         cs.sentiment,
         replace(strip_tags((c.content) :: text), '&nbsp;' :: text, '' :: text) AS cleaned_content,
         to_timestamp(((c.due_date / 1000)) :: double precision)                AS comment_due_date,
         to_timestamp(((problem_chunk.due_date / 1000)) :: double precision)    AS prob_due_date,
         to_timestamp(((hypothesis_chunk.due_date / 1000)) :: double precision) AS hyp_due_date
  FROM (((((((chunk_swarm c
      JOIN chunk_sentiment cs ON ((cs.chunk_id = c.id)))
      JOIN chunk_parent_relation cpr ON ((cpr.child_id = c.id)))
      JOIN chunk_parent_relation problem ON ((problem.child_id = cpr.parent_id)))
      JOIN chunk_user_group_relation cugr ON ((cugr.chunk_id = problem.parent_id)))
      JOIN chunk problem_chunk ON ((problem_chunk.id = problem.parent_id)))
      JOIN chunk hypothesis_chunk ON ((hypothesis_chunk.id = cpr.parent_id)))
      JOIN perm_user_group pug ON ((pug.id = cugr.user_group_id)))
  WHERE ((c.variant) :: text = 'comment' :: text);

alter table view_comments
  owner to postgres;
  
 drop view if exists view_tags;
 create view view_tags as
  SELECT p.id,
         p.title,
         p.team,
         t.user_display_name               AS src,
         t.tagged_user_display_name        AS trgt,
         count(t.tagged_user_display_name) AS src_trgt_tagged_ct
  FROM (view_prob_hyp_comm_desc_team p
      JOIN swarm_user_tag_chunk t ON ((t.chunk_uuid = p.hypothesis_child_id)))
  GROUP BY p.id, p.title, p.team, t.user_display_name, t.tagged_user_display_name;

alter table view_tags
  owner to postgres;

