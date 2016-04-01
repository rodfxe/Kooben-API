select
	user.IdUsuario as userId

from usuarios as user

where
	( user.sIdUsuario = :username ) or
	( user.sMail = :mail );