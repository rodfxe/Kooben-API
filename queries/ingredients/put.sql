update cmt_recetapartida
set
	IdInsumo = :IdInsumo,
	iIdUnidad = :iIdUnidad,
	Cantidad = :iIdUnidad

where
	IdRecetaPartida = :IdRecetaPartida;