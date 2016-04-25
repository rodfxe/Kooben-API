insert into nuc_sessions
(
    hash,
    username,
    createdAt,
    application,
    type,
    userId
)
values
(
    :hash,
    :username,
    now(),
    :application,
    :type,
    if(:userId = -1, null, :userId )
);
