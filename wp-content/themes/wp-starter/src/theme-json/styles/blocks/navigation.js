import theme from "../../import_tailwind.js";

export default {
	"core/navigation": {
		"elements": {
			"link": {
				":hover": {
					"typography": {
						"textDecoration": "underline"
					}
				},
			}
		},
		"color": {
			"text": theme.colors.accent[400]
		},
		"typography": {
			"fontWeight": "500"
		}
	}
}
