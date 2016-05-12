select
    receta.id,
    receta.diaId,
    receta.recetaId,
    receta.periodo,
    receta.personas

from cmt_planeacionrecetas as receta

inner join cmt_planeaciondias as dia
    on ( dia.id = receta.diaId )

inner join cmt_receta as recetas
    on ( recetas.IdReceta = receta.recetaId )


where
    ( :id = -1 or receta.id = :id ) and
    ( :dia = -1 or receta.diaId = :dia ) and
    ( :receta = -1 or receta.recetaId = :receta ) and
    ( :periodo = -1 or receta.periodo = :periodo )

order by
    receta.diaId,
    receta.periodo;
