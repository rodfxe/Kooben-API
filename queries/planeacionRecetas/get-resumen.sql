select
  planeacion.id as planeacionId,
  suministro.IdInsumo as suministroId,
  dia.id as diaId,

  suministro.NombreInsumo as nombreSuministro,
  ( ingrediente.cantidad * count( suministro.IdInsumo ) ) as cantidadSuministro,
  unidad.sNombre as nombreUnidad

from cmt_insumo as suministro

inner join cmt_recetapartida as ingrediente
  on ( ingrediente.IdInsumo = suministro.IdInsumo )

inner join nuc_unidades as unidad
  on ( unidad.iIdUnidad = ingrediente.iIdUnidad )

inner join cmt_planeacionrecetas as recetas
  on( recetas.recetaId = ingrediente.IdReceta )

inner join cmt_planeaciondias as dia
  on ( dia.id = recetas.diaId )

inner join cmt_planeacion as planeacion
  on ( planeacion.id = dia.planeacionId )

where
  ( :planeacion = -1 or planeacion.id = :planeacion ) and
  ( :dia = -1 or dia.id = :dia ) and
  ( :receta = -1 or recetas.recetaId = :receta )

group by
  planeacion.id,
  suministro.IdInsumo,
  dia.id

order by
  planeacion.id,
  suministro.IdInsumo,
  dia.id