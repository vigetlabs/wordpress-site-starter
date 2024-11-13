document.addEventListener('alpine:init', () => {
	YouTubePlayer();
})

function YouTubePlayer() {
	//Get the video cover and play button
	const videoCover = document.querySelectorAll(".acf-block-video-player .wp-block-cover");
	const playButton = document.querySelectorAll(".acf-block-video-player .is-style-icon-only > a");
	const iframe = document.querySelectorAll('.acf-block-video-player iframe');

	//Add Alpine to the HTML
	videoCover.forEach(element => {
		element.setAttribute('x-show', '!playvideo');
	});

	//Set the video ref
	iframe.forEach(element => {
		element.setAttribute('x-ref', 'video');
	});

	//Set the event listener for the play
	playButton.forEach(element => {
		element.setAttribute('x-on:click', 'play()');
		element.setAttribute('href', '#playvideo');
	});
}

export default () => ({
	playvideo: false,
	play() {
		this.playvideo = true;
		this.$refs.video.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
	}
});
