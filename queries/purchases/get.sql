select
	purchase.id,
	purchase.delivererId,
	purchase.addressId,
	purchase.beginDate,
	purchase.deliveredDate,
	purchase.delivered,

	address.fullname,
	address.street,
	address.number,
	address.state,
	address.city,
	address.code,
	country.titulopais as countryName

from shop_purchases as purchase

inner join shop_user_addresses as address
	on( address.id = purchase.addressId )

inner join nuc_pais as country
	on( country.idpais = address.countryId )

where
	( :id = -1 or purchase.id = :id ) and
	( :delivererId = -1 or purchase.delivererId = :delivererId ) and
	( :addressId = -1 or purchase.addressId = :addressId ) and
	( :beginDate = -1 or ( purchase.beginDate = :beginDate ) ) and
	( :deliveredDate = -1 or purchase.deliveredDate = :deliveredDate ) and
	( :delivered = -1 or purchase.delivered = :delivered );