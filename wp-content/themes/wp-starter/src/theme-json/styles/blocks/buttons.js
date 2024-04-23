import theme from "../../import_tailwind.js";

// Button Element
export const buttonElement = {
	"button": {
		"border": {
			"radius":"0",
			"color": theme.colors.sky[600]
		},
		"color": {
			"background": theme.colors.sky[600],
			"text": theme.colors.white[100]
		},
		"spacing": {
			"padding": {
				"bottom": theme.spacing[8],
				"left": theme.spacing[16],
				"right": theme.spacing[16],
				"top": theme.spacing[8]
			}
		},
		"typography": {
			"fontFamily": "var(--wp--preset--font-family--body)",
			"fontSize": "var(--wp--preset--font-size--small)",
			"fontStyle": "normal",
			"fontWeight": "500"
		},
	},
}

// Variations and grouped buttons
export const coreButtons = {
	"core/button": {
		"variations": {
			"outline": {
				"spacing": {
					"padding": {
						"bottom": theme.spacing[8],
						"left": theme.spacing[16],
						"right": theme.spacing[16],
						"top": theme.spacing[8]
					}
				},
				"border": {
					"width": theme.spacing[1]
				},
				"color": {
					"background": theme.colors.transparent,
					"text": theme.colors.sky[600]
				},
			}
		},
	},
	"core/buttons": {
		"spacing": {
			"blockGap": theme.spacing[12]
		},
	},
}
