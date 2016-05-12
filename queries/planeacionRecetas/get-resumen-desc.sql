select
  planeacion.id as planeacionId,
  suministro.IdInsumo as suministroId,
  dia.id as diaId,
  @precioId := ifnull( (
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
  dia.personas as diaPersonas,
  ingrediente.Cantidad as cantidadIngrediente,

  count( suministro.IdInsumo ) as countInsumo,

  @cantidadRecetas := (
    select
      sum( receta2.personas )
    from cmt_planeacionrecetas as receta2
    where receta2.diaId = dia.id
  ) as cantidadRecetas,

  ( if( @cantidadRecetas = 0,
    count( suministro.IdInsumo ) * ingrediente.cantidad * if( dia.personas = 0, 1, dia.personas ),
    count( suministro.IdInsumo ) * ingrediente.cantidad * @cantidadRecetas
  ) ) as cantidadSuministro,

  if( @precioId > -1 ,(
    select
      precio.PrecioVenta
    from cmt_insumoprecio as precio
    where precio.IdInsumoPrecio = @precioId
  ), 0 ) as precioValor/*,

  ( ( if( @cantidadRecetas = 0,
    count( suministro.IdInsumo ) * ingrediente.cantidad * if( dia.personas = 0, 1, dia.personas ),
    count( suministro.IdInsumo ) * ingrediente.cantidad * @cantidadRecetas
  ) ) * ( if( @precioId > -1 ,(
    select
      precio.PrecioVenta
    from cmt_insumoprecio as precio
    where precio.IdInsumoPrecio = @precioId
  ), 0 ) ) ) as importe*/

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