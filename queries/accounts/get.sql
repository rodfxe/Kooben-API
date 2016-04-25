select
	user.IdUsuario as userId,
	user.sIdUsuario as username,
	user.sPassword,
	user.sPassword as password,
	user.sMail as mail,
	user.sNombre as name,
	user.sApellidos as lastName,
	user.sFacebookToken as facebook,
	user.sGoogleToken as google,
	user.sImg as image

from usuarios as user

where
	( :id = -1 or user.IdUsuario = :id ) and
	( :username = -1 or user.sIdUsuario = :username ) and
	( :mail = -1 or user.sMail = :mail );