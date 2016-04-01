select
  period.IdPeriodoConsumo as id,
  period.TituloPeriodoConsumo as title,
  period.Desde as 'from',
  period.Hasta as 'to'

from
  cmt_periodoconsumo as period

where
  ( :IdPeriodoConsumo = -1 or period.IdPeriodoConsumo = :IdPeriodoConsumo ) and 
  ( :TituloPeriodoConsumo = -1 or period.TituloPeriodoConsumo LIKE :TituloperiodoConsumo ) and
  ( period.Activo = 'Si' )

order by
  period.Desde