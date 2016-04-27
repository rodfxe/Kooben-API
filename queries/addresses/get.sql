select
	address.id,
	address.userId,
	address.countryId,
	address.fullname,
	address.street,
	address.number,
	address.state,
	address.city,
	address.code,
	address.notes

from shop_user_addresses as address

where
	( :id = -1 or address.id = :id ) and
	( :userId = -1 or address.userId = :userId ) and
	( address.active = 1 );