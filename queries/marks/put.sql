update cmt_marca
set
	CodigoMarca = :CodigoMarca,
	TituloMarca = :TituloMarca
where
	IdMarca = :IdMarca;