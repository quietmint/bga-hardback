{OVERALL_GAME_HEADER}

<a id="browser-error" href="https://browsehappy.com/" target="_blank"></a>
<div id="HGame" class="tailwind"></div>

<script type="text/javascript">
const fontEl = document.createElement("link");
fontEl.rel = "stylesheet";
fontEl.href = "https://fonts.googleapis.com/css2?family=Big+Shoulders+Inline+Text:wght@900&family=Bodoni+Moda:wght@700&family=Dancing+Script&family=Finger+Paint&family=Love+Light&family=New+Rocker&family=Playwrite+DE+Grund&family=Playwrite+HU&family=Staatliches&display=swap";
document.head.appendChild(fontEl);

const baseUrl = dojoConfig.packages.reduce((r, p) => p.name == "bgagame" ? p.location : r, null);
document.write('<script type="module" src="' + baseUrl + '/modules/index.js"><\/script>');
</script>

{OVERALL_GAME_FOOTER}
