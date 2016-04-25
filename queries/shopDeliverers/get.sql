select
	deliverer.id,
	deliverer.id as identifier,
	deliverer.firstname,
	deliverer.lastname,
	deliverer.email,
	deliverer.password,
	deliverer.free

from shop_deliverers as deliverer

where
	( :id = -1 or deliverer.id = :id ) and
	( :email = -1 or deliverer.email = :email ) and
	( :free = -1 or deliverer.free = :free );