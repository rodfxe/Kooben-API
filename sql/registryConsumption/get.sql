select
	consumption.IdRegistroConsumo as id,
	consumption.IdMenu as menuId,
	consumption.Fecha as day,
	consumption.Personas as persons

from cmt_registroconsumo as consumption

inner join cmt_menu as menu
	on( menu.IdMenu = consumption.IdMenu )

where
	( :IdRegistroConsumo = -1 or consumption.IdRegistroConsumo = :IdRegistroConsumo ) and
	( :IdMenu = -1 or menu.IdMenu = :IdMenu )

order by
	consumption.IdRegistroConsumo;