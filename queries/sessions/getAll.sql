select
    session.id,
    session.id as realId,
    session.hash,
    session.username,
    session.latitude,
    session.longitude,
    (6371 * ACOS( 
        SIN(RADIANS(session.latitude)) * SIN(RADIANS(:lat)) 
        + COS(RADIANS(session.longitude - :lng)) * COS(RADIANS(session.latitude)) 
        * COS(RADIANS(:lat))
        )
    ) AS distance
from nuc_sessions as session
where
    session.active = 'true'
    and  DATE(session.updatedAt) = CURRENT_DATE()
    and session.latitude is not null
    and session.longitude is not null;
