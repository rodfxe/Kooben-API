/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  adal2404
 * Created: 16/01/2016
 */

/*(Lat: 4.6665578 | Long: -74.0524521)*/
SELECT 
    nuc_empresas.iIdEmpresa, 
    nuc_empresas.sNombreCorto,  
    nuc_empresas.sRazonSocial,
    nuc_empresas.sDomicilio,
    nuc_empresas.sCiudad,
    nuc_empresas.sCp, 
    nuc_empresas.lat,
    nuc_empresas.lng,
    nuc_empresas.IdLogo,
    nuc_logos_empresas.Nombre_Archivo,
    (6371 * ACOS( 
                                SIN(RADIANS(nuc_empresas.lat)) * SIN(RADIANS(:lat)) 
                                + COS(RADIANS(nuc_empresas.lng - :lng)) * COS(RADIANS(nuc_empresas.lat)) 
                                * COS(RADIANS(:lat))
                                )
    ) AS distance
FROM nuc_empresas
     LEFT JOIN nuc_logos_empresas on
        nuc_empresas.IdLogo = nuc_logos_empresas.IdLogo
HAVING distance < :kilometers /* X KM  a la redonda */
ORDER BY distance ASC