SELECT
  u.iIdUnidad as id,
  u.sCodigo as code,
  u.sNombre as name,
  u.sSigla
FROM
  nuc_unidades u
WHERE
  ( ( :Activo = -1 and u.Activo = "Si" ) or u.Activo = :Activo ) and 
  ( :iIdUnidad = -1 or u.iIdUnidad = :iIdUnidad ) and 
  ( :sCodigo = -1 or u.sCodigo LIKE :sCodigo ) and 
  ( :sNombre = -1 or u.sNombre LIKE :sNombre ) and 
  ( :sSigla = -1 or u.sSigla = :sSigla )
ORDER BY
  u.sCodigo