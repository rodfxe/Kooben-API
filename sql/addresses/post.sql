insert into shop_user_addresses (
	userId,
	countryId,
	fullname,
	street,
	number,
	state,
	city,
	code,
	notes
) values (
	:userId,
	:countryId,
	:fullname,
	:street,
	:number,
	:state,
	:city,
	:code,
	:notes
);