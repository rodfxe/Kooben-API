insert into nuc_kardex
(
	rowId,
	sessionId,
	tableName,
	operation,
	affectedAt
)
values
(
	:rowId,
	:sessionId,
	:tableName,
	:operation,
	now()
);