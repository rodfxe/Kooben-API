select
    session.id,
    session.id as realId,
    session.userId,
    session.hash,
    session.username
from nuc_sessions as session

where
    ( :id = -1 or ( :id <> -1 and session.id = :id ) ) and
    ( session.hash = :hash ) and
    session.active = 'true';
