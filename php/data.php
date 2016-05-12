<?php
	$jsonConfig = 
	'{
		"initialFinances": 2000,
		"roundDuration": 300,
		"roundsNb" : 10,
		"maximunAmounts": {
			"qualityBudget": 1500,
			"productPrice": 300,
			"marketingBudget": 1500,
			"overdraft": 300
		},
		"customers": {
			"A": {
				"name": "A",
				"priceSensitivity": 2,
				"qualitySensitivity": 1,
				"marketingSensitivity": 1,
				"maxPrice": 150,
				"minQuality":  150,
				"minMaketing": 150
			},
			"B": {
				"name": "B",
				"priceSensitivity": 2,
				"qualitySensitivity": 1,
				"marketingSensitivity": 1,
				"maxPrice": 150,
				"minQuality":  150,
				"minMaketing": 150
			},			
			"C": {
				"name": "C",
				"priceSensitivity": 2,
				"qualitySensitivity": 1,
				"marketingSensitivity": 1,
				"maxPrice": 150,
				"minQuality":  150,
				"minMaketing": 150
			}
		},
		"mapDistricts": [
			{ 
				"name": "A",
				"stallPrice": 300,
				"maxStallNb": 15,
				"totalPopulation" : 9001,
				"population": [
					{
						"typeName": "A",
						"quantity": 0.5
					},
					{
						"typeName": "B",
						"quantity": 0.5
					}
				]
			},
			{ 
				"name": "B",
				"stallPrice": 500,
				"maxStallNb": 10,
				"totalPopulation" : 9001,
				"population": [
					{
						"typeName": "A",
						"quantity": 0.5
					},
					{
						"typeName": "B",
						"quantity": 0.5
					}
				]
			},
			{ 
				"name": "C",
				"stallPrice": 500,
				"maxStallNb": 10,
				"totalPopulation" : 9001,
				"population": [
					{
						"typeName": "A",
						"quantity": 0.5
					},
					{
						"typeName": "B",
						"quantity": 0.5
					}
				]
			}
		]
	}';

	$gameData = json_decode($jsonConfig, true);
?>