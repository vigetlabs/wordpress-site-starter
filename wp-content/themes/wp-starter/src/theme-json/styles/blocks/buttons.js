import theme from "../../import_tailwind.js";

// Button Element
export const buttonElement = {
	"button": {
		"border": {
			"radius": "0",
		}
	},
}

// Variations and grouped buttons
export const coreButtons = {
	"core/button": {
		"variations": {
			"outline": {

			}
		},
	},
	"core/buttons": {
		"spacing": {
			"blockGap": theme.spacing[12]
		},
	},
}
