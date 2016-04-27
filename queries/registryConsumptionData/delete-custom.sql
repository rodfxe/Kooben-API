delete from cmt_registroconsumodatos
where
	( IdPeriodoConsumo = :periodId ) and
	( IdRegistroConsumo = :consumptionId ) and
	( Activo = 'Si' );