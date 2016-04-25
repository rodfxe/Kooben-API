update shop_purchases
	set
		deliveredDate = now(),
		delivered = 1

	where
		id = :id;