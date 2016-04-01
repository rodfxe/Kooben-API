select
	item.IdMarcaxInsumo as id,
	item.IdInsumo as supplyId,
	item.IdMarca as markId,

	mark.TituloMarca as name

from cmt_marcaxinsumo as item

inner join cmt_marca as mark
	on ( mark.IdMarca = item.IdMarca )

inner join cmt_insumo as supply
	on ( supply.IdInsumo = item.IdInsumo )

where
	( :id = -1 or item.IdMarcaxInsumo = :id ) and
	( :supplyId = -1 or item.IdInsumo = :supplyId ) and
	( :markId = -1 or item.IdMarca = :markId ) and
	( mark.Activo = 'Si' );