select
	supply.IdInsumo as id,
	supply.NombreInsumo as name

from cmt_insumo as supply

where
	supply.Activo = 'Si'

order by
	supply.IdInsumo;