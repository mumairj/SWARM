select *  from chunk where variant='question';
select distinct variant from chunk where variant!='question';
--order by parent_id asc;
select *  from chunk where parent_id='1b225df6-d1ac-48c9-ac10-721fb32567df';
select count(*)  from chunk;


select parent_id, variant, count(*) from chunk
group by  parent_id, variant
order by parent_id;

select parent_id,variant,count(*) from chunk where parent_id in (select id  from chunk where variant='question')
group by parent_id,variant;

select car.user_id, count(*) from chunk c
inner join chunk_author_relation car on car.chunk_id=c.id
group by car.user_id
order by count(*) desc;

select id from "user";

select * from chunk where id in (select distinct parent_id from chunk_parent_relation);

select distinct parent_relation from chunk;
select * from chunk where parent_relation='comment';
select * from chunk where id='b88d14da-209a-4f97-a916-30d197aa7c6d'
--P:a15bb2da-440e-48a3-9398-d368ff9d04b5 | I:b88d14da-209a-4f97-a916-30d197aa7c6d
--BP:28fe1b8a-7ce4-44c2-95fc-adf34a2cede9

select distinct variant from chunk where id in
                                         (select distinct base_parent_id
                                          from (select base_parent_id, variant, count(*)
                                                from chunk
                                                group by base_parent_id, variant
                                                order by base_parent_id)X);

select base_parent_id, variant, count(*)
from chunk
group by base_parent_id, variant
order by count(*) desc,base_parent_id;