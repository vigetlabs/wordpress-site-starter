import theme from "../tailwind.js";

export default {
	"fluid": true,
	"letterSpacing": false,
	"lineHeight": false,
	"writingMode": false,
	"fontFamilies": [
		{
			"fontFamily": theme.fontFamily.body,
			"name": "Body " + theme.fontFamily.body.split(',')[0],
			"slug": "body"
		},
		{
			"fontFamily": theme.fontFamily.heading,
			"name": "Heading " +theme.fontFamily.heading.split(',')[0],
			"slug": "heading"
		},
		{
			"fontFamily": "-apple-system, BlinkMacSystemFont, avenir next, avenir, segoe ui, helvetica neue, helvetica, Cantarell, Ubuntu, roboto, noto, arial, sans-serif",
			"name": "System Sans-serif",
			"slug": "system-sans-serif"
		},
		{
			"fontFamily": "Iowan Old Style, Apple Garamond, Baskerville, Times New Roman, Droid Serif, Times, Source Serif Pro, serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol",
			"name": "System Serif",
			"slug": "system-serif"
		}
	],
	"fontSizes": [
		{
			"fluid": false,
			"name": "Extra Small",
			"size": theme.fontSize['xs'][0][1],
			"slug": "x-small"
		},
		{
			"fluid": false,
			"name": "Small",
			"size": theme.fontSize['sm'][0][1],
			"slug": "small"
		},
		{
			"fluid": false,
			"name": "Medium",
			"size": theme.fontSize['base'][0][1],
			"slug": "medium"
		},
		{
			"name": "Large",
			"size": theme.fontSize['lg'][0][1],
			"slug": "large"
		},
		{
			"name": "Extra Large",
			"size": theme.fontSize['xl'][0][1],
			"slug": "x-large"
		},
		{
			"name": "Extra Extra Large",
			"size": theme.fontSize['2xl'][0][1],
			"slug": "xx-large"
		}
	],
	"writingMode": true
}
