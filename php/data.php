<?php
	$jsonConfig = '{
		"initialFinances": 10000,
		"roundDuration": 300,
		"roundsNb": 10,
		"maximunAmounts": {
			"qualityBudget": 10000,
			"productPrice": 10,
			"marketingBudget": 10000,
			"overdraft": 1000
		},
		"customers": {
			"A": {
				"name": "A",
				"priceSensitivity": 100,
				"qualitySensitivity": 15,
				"marketingSensitivity": 35,
				"maxPrice": 3,
				"minQuality": 1523,
				"minMaketing": 3574
			},
			"B": {
				"name": "B",
				"priceSensitivity": 51,
				"qualitySensitivity": 53,
				"marketingSensitivity": 52,
				"maxPrice": 5.2,
				"minQuality": 5086,
				"minMaketing": 4999
			},
			"C": {
				"name": "C",
				"priceSensitivity": 7,
				"qualitySensitivity": 100,
				"marketingSensitivity": 69,
				"maxPrice": 10,
				"minQuality": 8045,
				"minMaketing": 1200
			}
		},
		"mapDistricts": [{
			"name": "Bronx",
			"stallPrice": 100,
			"maxStallNb": 20,
			"totalPopulation": 9100,
			"population": [{
				"typeName": "A",
				"quantity": 0.79
			}, {
				"typeName": "B",
				"quantity": 0.18
			}, {
				"typeName": "C",
				"quantity": 0.03
			}]
		}, {
			"name": "Brooklyn",
			"stallPrice": 300,
			"maxStallNb": 20,
			"totalPopulation": 6290,
			"population": [{
				"typeName": "A",
				"quantity": 0.15
			}, {
				"typeName": "B",
				"quantity": 0.60
			}, {
				"typeName": "C",
				"quantity": 0.25
			}]
		}, {
			"name": "Wall_street",
			"stallPrice": 800,
			"maxStallNb": 20,
			"totalPopulation": 4680,
			"population": [{
				"typeName": "A",
				"quantity": 0.05
			}, {
				"typeName": "B",
				"quantity": 0.20
			}, {
				"typeName": "C",
				"quantity": 0.75
			}]
		}]
	}';

	$gameData = json_decode($jsonConfig, true);
?>