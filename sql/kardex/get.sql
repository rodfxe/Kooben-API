select
	kardex.id,
	kardex.rowId,
	kardex.sessionId,
	kardex.tableName,
	kardex.operation
from nuc_kardex as kardex
where
	kardex.id = :id;