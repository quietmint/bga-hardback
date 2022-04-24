{OVERALL_GAME_HEADER}

<a id="browser-error" href="https://browsehappy.com/" target="_blank"></a>
<div id="HGame" class="tailwind"></div>

<script type="text/javascript">
const URL = dojoConfig.packages.reduce((r,p) => p.name == "bgagame" ? p.location : r, null);
document.write('<script type="module" src="' + URL + '/modules/index.js"><\/script>');
</script>

{OVERALL_GAME_FOOTER}
