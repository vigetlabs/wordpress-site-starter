import theme from "../import_tailwind.js";
import { buttonElement } from "./blocks/buttons.js"


export default {
	...buttonElement,
	"caption": {
		"color": {
			"text": "var(--wp--preset--color--contrast-2)"
		},
		"typography": {
			"fontFamily": "var(--wp--preset--font-family--body)",
			"fontSize": "var(--wp--preset--font-size--x-small)"
		}
	},
	"h1": {
		"typography": {
			"fontSize": "var(--wp--preset--font-size--xx-large)",
			"lineHeight": "1.15"
		}
	},
	"h2": {
		"typography": {
			"fontSize": "var(--wp--preset--font-size--x-large)"
		}
	},
	"h3": {
		"typography": {
			"fontSize": "var(--wp--preset--font-size--large)"
		}
	},
	"h4": {
		"typography": {
			"fontSize": "var(--wp--preset--font-size--medium)"
		}
	},
	"h5": {
		"typography": {
			"fontSize": "var(--wp--preset--font-size--small)"
		}
	},
	"h6": {
		"typography": {
			"fontSize": "var(--wp--preset--font-size--x-small)"
		}
	},
	"heading": {
		"color": {
			"text": "var(--wp--preset--color--contrast)"
		},
		"typography": {
			"fontFamily": "var(--wp--preset--font-family--heading)",
			"fontWeight": "400",
			"lineHeight": "1.2"
		}
	},
	"link": {
		":hover": {
			"typography": {
				"textDecoration": "none"
			}
		},
		"color": {
			"text": "var(--wp--preset--color--contrast)"
		}
	}
}
