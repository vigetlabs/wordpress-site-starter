// Flags [data-motion] elements as they scroll into view; motion.css does the animating.
import './motion.css';

const root = document.documentElement;
const { threshold = 0.15, rootMargin = '0px', once = true } = window.motionConfig ?? {};

// An element is revealed by its nearest parent scope, or by itself when it has none.
const triggerFor = (el) => el.parentElement?.closest('[data-motion-scope]') ?? el;

const membersOf = (trigger) => {
	const members = [...trigger.querySelectorAll('[data-motion]')].filter(
		(el) => triggerFor(el) === trigger,
	);
	return trigger.matches('[data-motion]') && triggerFor(trigger) === trigger
		? [trigger, ...members]
		: members;
};

const observer = new IntersectionObserver(
	(entries) => {
		entries.forEach(({ target, isIntersecting }) => {
			if (isIntersecting) {
				membersOf(target).forEach((el) => el.setAttribute('data-motion-in', ''));
				if (once) observer.unobserve(target);
			} else if (!once) {
				membersOf(target).forEach((el) => el.removeAttribute('data-motion-in'));
			}
		});
	},
	{ threshold, rootMargin },
);

const scan = (node) => {
	if (!(node instanceof Element)) return;
	const els = node.matches('[data-motion]') ? [node] : [];
	els.push(...node.querySelectorAll('[data-motion]'));
	new Set(els.map(triggerFor)).forEach((trigger) => observer.observe(trigger));
};

export default function initMotion() {
	if (!root.classList.contains('has-motion')) return;
	window.motionReady = true;

	scan(document.body);

	// AJAX pagination, FacetWP, etc. inject blocks after load.
	new MutationObserver((mutations) =>
		mutations.forEach(({ addedNodes }) => addedNodes.forEach(scan)),
	).observe(document.body, { childList: true, subtree: true });
}
