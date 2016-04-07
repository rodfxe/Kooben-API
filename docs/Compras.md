Ruteo para ordenes de compra
============================


Las rutas utilizadas son las siguientes

Para obtener la lista de productos con base a ubicación geografica.
El rango de busqueda tiene que proporcionarse en kilometros.

	type -> GET
	url -> /shop/product-list/:latitude/:longitude/:range/K




Para obtener las marcas de un producto especifico.

	type -> GET
	url -> supplies/:productId/marks




Para obtener las presentaciones de un producto especifico.
Está obtiene todas las marcas, si se desea solo las presentaciones
de una marca, depende del programador, hacer un filtrado interno.
**No existe ruta para hacer un filtrado por producto y marca.**

	type -> GET
	url -> supplies/:supplyid/presentations




Para Realizar la cotizacion de un conjunto de productos.

	type -> POST
	url -> /shop/estimate
	body -> { addressId: -1, items: [] }

Ejemplo

```javascript
request.data = {
	items: [
		{ supplyId: -1, markId: -1, presentationId: -1 }
		{ supplyId: -2, markId: -2, presentationId: -2 }
		{ supplyId: -3, markId: -3, presentationId: -3 }
	]
};
```




Para obtener la lista de direcciones del usuario.

	type -> GET
	url -> me/addresses





Para crear la orden de compra

	type -> POST
	url -> /purchases
	body -> { addressId: -1, items: [] }

Ejemplo

```javascript
var address = { ... }
...
request.data = {
	addressId: address.id,
	items: [
		{ priceId: -1, cant: 1 }
		{ priceId: -2, cant: 3 }
		{ priceId: -3, cant: 2 }
	]
};
```




Para obtener las compras realizadas

	type -> GET
	url -> /purchases