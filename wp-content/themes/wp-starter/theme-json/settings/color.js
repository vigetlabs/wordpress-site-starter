import resolveConfig from 'tailwindcss/resolveConfig'
import tailwindConfig from './tailwind.config.js'

const { theme } = resolveConfig(tailwindConfig)


export default {
	"defaultDuotone": false,
	"defaultPalette": false,
	"defaultGradients": false,
	"palette": [
		{
			"color": theme.colors.base,
			"name": "Base",
			"slug": "base"
		},
		{
			"color": "#ffffff",
			"name": "Base / Two",
			"slug": "base-2"
		},
		{
			"color": "#111111",
			"name": "Contrast",
			"slug": "contrast"
		},
		{
			"color": "#636363",
			"name": "Contrast / Two",
			"slug": "contrast-2"
		},
		{
			"color": "#A4A4A4",
			"name": "Contrast / Three",
			"slug": "contrast-3"
		},
		{
			"color": "#cfcabe",
			"name": "Accent",
			"slug": "accent"
		},
		{
			"color": "#c2a990",
			"name": "Accent / Two",
			"slug": "accent-2"
		},
		{
			"color": "#d8613c",
			"name": "Accent / Three",
			"slug": "accent-3"
		},
		{
			"color": "#b1c5a4",
			"name": "Accent / Four",
			"slug": "accent-4"
		},
		{
			"color": "#b5bdbc",
			"name": "Accent / Five",
			"slug": "accent-5"
		}
	]
}
