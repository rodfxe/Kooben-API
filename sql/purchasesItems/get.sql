select
	item.id,
	item.purchaseId,
	item.productId,
	item.cant

from shop_purchases_items as item

where
	( :id = -1 or item.id = :id ) and
	( :purchaseId = -1 or item.purchaseId = :purchaseId ) and
	( :productId = -1 or item.productId = :productId );