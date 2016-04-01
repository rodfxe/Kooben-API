insert into cmt_menu
(
	IdSession,
	CodigoMenu,
	Comentarios,
	Desde,
	Hasta
)
values
(
	:IdSession,
	:CodigoMenu,
	:Comentarios,
	adddate( adddate( date( :Desde ), interval 1 - dayofweek( date( :Desde ) ) day ), interval 1 day ),
	adddate( adddate( date( :Desde ), interval 7 - dayofweek( date( :Desde ) ) day ), interval 1 day )
);