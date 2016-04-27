update shop_user_addresses
	set
		countryId = :countryId,
		fullname = :fullname,
		street = :street,
		number = :number,
		state = :state,
		city = :city,
		code = :code,
		notes = :notes

	where
		id = :id;