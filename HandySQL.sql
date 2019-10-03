-- ## Collection of handy SQL
-- ==========================
-- // Check standard service item quantities for all engines
SELECT boats.name as Boat, CONCAT(engineTemplates.make, " ", engineTemplates.model) as Engine, serviceItemNames.name as Part, siTObeMap.qty as Qty
FROM boats, engines, serviceItems, serviceItemNames, siTObeMap, engineTemplates
WHERE engines.id = siTObeMap.eId
AND boats.id = engines.boatId
AND engineTemplates.id = engines.engineTemplateId
AND serviceItems.id = siTObeMap.siId
AND serviceItemNames.id = serviceItems.siNameId
ORDER BY boats.name

-- // Get details of ALL standard service parts for engine XXX
SELECT sites.name as site, sites.id as siteId, boats.name as boat, boats.id as boatId,  
	engines.type as engineType, engines.serialno as serialno, engines.notes as engineNotes, engines.created as engcreated, engines.updated as engupdated, engines.updatedBy as engupdatedBy, 
	engineTemplates.id as etId, engineTemplates.make as etMake, engineTemplates.model as etModel, engineTemplates.cylinders, engineTemplates.capacity,engineTemplates.fuel, 
	serviceItemNames.name as siName,
	serviceItems.make as siMake,
	serviceItems.partno as siPart,
	serviceItems.price as siPrice,
	serviceItems.notes as siNotes,
	siTObeMap.ismod as siIsmod,
	siTObeMap.qty as siQty
FROM serviceItemNames, serviceItems, siTObeMap, engines, engineTemplates, boats, sites
WHERE engines.id = XXX
AND engineTemplates.id = engines.engineTemplateId
AND engines.boatId = boats.id
AND sites.id = engines.siteId
AND siTObeMap.eId = engines.id
AND serviceItems.id = siTObeMap.siId
AND serviceItemNames.id = serviceItems.siNameId


-- // Get details of ALL NON-standard service parts for engine XXX
SELECT boats.name as boat,
	CONCAT(engineTemplates.make, " ", engineTemplates.model) as engine,
	serviceItemNames.name as siName,
	engineMods.make as siMake,
	engineMods.partno as siPart,
	engineMods.notes as siNotes,
	engineMods.qty as siQty
FROM serviceItemNames, engineMods, engines, engineTemplates, boats
WHERE engines.id = 25
AND engineTemplates.id = engines.engineTemplateId
AND engines.boatId = boats.id
AND engineMods.engineId = engines.id
AND serviceItemNames.id = engineMods.siNameId

-- // Get list of original Engine Mods
SELECT boats.name as boat, engineTemplates.make as engine, serviceItemNames.name, 
	engineMods.make as make, engineMods.partno as PartNo, engineMods.qty as Qty
FROM engineMods, boats, engines, serviceItemNames, engineTemplates
WHERE serviceItemNames.id = engineMods.siNameId
AND engines.id = engineMods.engineId
AND engineTemplates.id = engines.engineTemplateId
AND boats.id = engines.boatId 

-- // Get list of Engines with known duplicate parts
SELECT boats.name, engineTemplates.make 
FROM boats, engines, engineTemplates 
WHERE engines.id IN (SELECT eId FROM siTObeMap WHERE 1 GROUP BY  eId, siId HAVING COUNT(siId)>1)
AND boats.id = engines.boatId
AND engineTemplates.id = engines.engineTemplateId
ORDER BY boats.name

-- // Get list of engines with non-integer quantity
select * from siTObeMap where qty NOT REGEXP '^-?[0-9]+$';

-- // Get list of Boats with engines but no Service Items
SELECT DISTINCT(boats.name)
FROM boats, engines, siTObeMap
WHERE engines.boatId = boats.Id
AND engines.id NOT IN (SELECT eId FROM siTObeMap WHERE 1)



SELECT boats.name, engineTemplates.make 
FROM boats, engines, engineTemplates 
WHERE engines.id IN (SELECT eId FROM siTObeMap WHERE qty NOT REGEXP '^-?[0-9]+$')
AND boats.id = engines.boatId
AND engineTemplates.id = engines.engineTemplateId
ORDER BY boats.name


