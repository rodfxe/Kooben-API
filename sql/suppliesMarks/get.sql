select
	item.IdMarcaxInsumo as id,
	item.IdInsumo as supplyId,
	item.IdMarca as markId

from cmt_marcaxinsumo as item

where
	( :id = -1 or item.IdMarcaxInsumo = :id ) and
	( :supplyid = -1 or item.IdInsumo = :supplyid ) and
	( :markId = -1 or item.IdMarca = :markId );