import theme from "../../import_tailwind.js";

// Button Element
export const buttonElement = {
	"button": {
		":active": {
			"color": {
				"background": theme.colors.accent[200],
				"text": theme.colors.contrast[100]
			}
		},
		":focus": {
			"color": {
				"background": theme.colors.accent[100],
				"text": theme.colors.contrast[100]
			},
			"outline": {
				"color": theme.colors.contrast[100],
				"offset": theme.spacing[1]
			},
			"border": {
				"color":  theme.colors.accent[200]
			}
		},
		":hover": {
			"color": {
				"background": theme.colors.accent[100],
				"text": theme.colors.contrast[100]
			},
			"border": {
				"color": theme.colors.accent[400]
			},
		},
		"border": {
			"radius": theme.borderRadius.md,
			"color": theme.colors.contrast[100]
		},
		"color": {
			"background": theme.colors.accent[400],
			"text": theme.colors.base[100]
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
				}
			}
		},
	},
	"core/buttons": {
		"spacing": {
			"blockGap": theme.spacing[12]
		},
	},
}
