select
    menu.IdMenu as id,
    menu.CodigoMenu as code,
    menu.Comentarios as comments,
    menu.Desde as 'from',
    menu.Hasta as 'to'

from cmt_menu as menu

inner join nuc_sessions as session
    on( session.id = menu.IdSession )

where
    ( :IdMenu = -1 or menu.IdMenu = :IdMenu ) and
    ( :IdSession = -1 or ( menu.IdSession = (
        select
            session.id
        from nuc_sessions as session
        where
            session.hash = :IdSession
    ) ) );