select
  planeacion.id as planeacionId,
  suministro.IdInsumo as suministroId,
  dia.id as diaId,
  ifnull( (
    select precio.IdInsumoPrecio
    from cmt_insumoprecio as precio
    where
        ( precio.IdInsumo = suministro.IdInsumo ) and
        ( precio.Aplicacion <= dia.fecha ) and
        ( :filtrarPorCoordenadas = -1 or precio.iIdEmpresa in ( :proveedores ) )
    order by
        precio.Aplicacion desc,
        precio.PrecioVenta desc
    limit 1
  ), -1 ) as precioId,

  suministro.NombreInsumo as nombreSuministro,
  unidad.sNombre as nombreUnidad,
  ( count( suministro.IdInsumo ) * ingrediente.cantidad * if( dia.personas = 0, 1, dia.personas ) ) as cantidadSuministro,
  ifnull( (
    select precio.PrecioVenta
    from cmt_insumoprecio as precio
    where
        ( precio.IdInsumo = suministro.IdInsumo ) and
        ( precio.Aplicacion <= dia.fecha ) and
        ( :filtrarPorCoordenadas = -1 or precio.iIdEmpresa in ( :proveedores ) )
    order by
        precio.Aplicacion desc,
        precio.PrecioVenta desc
    limit 1
  ), 0 ) *  ( count( suministro.IdInsumo ) * ingrediente.cantidad * if( dia.personas = 0, 1, dia.personas ) ) as importe

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
  ( :receta = -1 or recetas.recetaId = :receta ) and
  ( :suministro = -1 or suministro.IdInsumo = :suministro )

group by
  planeacion.id,
  suministro.IdInsumo,
  dia.id

order by
  planeacion.id,
  suministro.IdInsumo,
  dia.id;