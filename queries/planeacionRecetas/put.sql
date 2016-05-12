update cmt_planeacionrecetas
set
  diaId = :diaId,
  periodo = :periodo,
  personas = :personas

where
  id = :id;