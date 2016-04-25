update cmt_presentacion
set
	CodigoPresentacion = :CodigoPresentacion,
	TituloPresentacion = :TituloPresentacion,
	iIdUnidad = :iIdUnidad,
	Cantidad = :Cantidad
where
	IdPresentacion = :IdPresentacion;