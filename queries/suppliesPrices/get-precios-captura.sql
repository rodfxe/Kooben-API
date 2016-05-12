select
  precio.IdInsumoPrecio as id,
  precio.IdInsumo as productoId,
  precio.iIdEmpresa as proveedorId,
  precio.IdMarca as marcaId,
  precio.IdPresentacion as presentacionId,
  precio.PrecioCompra as precioCompra,
  precio.PrecioVenta as precioVenta,
  precio.Aplicacion as aplicacion,

  proveedor.sNombreCorto as proveedorNombre,
  presentacion.TituloPresentacion as presentacionNombre

from cmt_insumoprecio as precio

inner join cmt_insumo as producto
  on ( producto.IdInsumo = precio.IdInsumo and
       producto.Activo = 'Si'
  )

inner join nuc_empresas as proveedor
  on ( proveedor.iIdEmpresa = precio.iIdEmpresa and
       proveedor.Activo = 'Si'
  )

inner join cmt_marca as marca
  on ( marca.IdMarca = precio.IdMarca and
       marca.Activo = 'Si'
  )

inner join cmt_presentacion as presentacion
  on ( presentacion.IdPresentacion = precio.IdPresentacion and
       presentacion.Activo = 'Si'
  )

where
  ( :id = -1 or precio.IdInsumoPrecio = :id ) and
  ( :producto = -1 or precio.IdInsumo = :producto ) and
  ( :marca = -1 or precio.IdMarca = :marca ) and
  ( precio.Activo = 'Si' )

order by
  precio.IdInsumo,
  precio.iIdEmpresa,
  precio.IdMarca,
  precio.IdPresentacion,
  precio.Aplicacion desc;