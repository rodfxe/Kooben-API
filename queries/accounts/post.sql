insert into usuarios
(
	sIdUsuario,
	sPassword,
	sNombre,
	sApellidos,
	sMail,
	sFacebookToken,
	sGoogleToken,
	sTipo,
	sImg
)
values
(
	:sIdUsuario,
	:sPassword,
	:sNombre,
	:sApellidos,
	:sMail,
	:sFacebookToken,
	:sGoogleToken,
	:sTipo,
	:sImg
);