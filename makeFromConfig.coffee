config = require './config.json'
config.definitions = []
fileSystem = require 'fs'
perfil = config.config.mysql.profile
enProduccion = perfil is 'production'

for modelo, propiedades of config.models
	path = "definitions/#{modelo}.json"

	console.log "Creando: #{path}"
	if enProduccion
		fileSystem.writeFileSync( path, JSON.stringify( propiedades ), 'utf8' )
	else
		fileSystem.writeFileSync( path, JSON.stringify( propiedades, null, 4 ), 'utf8' )

	config.definitions.push modelo

console.log 'Guardando configuración'
fileSystem.writeFileSync( './config.json', JSON.stringify( config, null, 4 ), 'utf8' );
fileSystem.writeFileSync( './config.example.json', JSON.stringify( config, null, 4 ), 'utf8' );