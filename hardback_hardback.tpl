{OVERALL_GAME_HEADER}

<div id="HGame" class="tailwind"></div>

<script type="text/javascript">
const rand = Date.now();
const URL = dojoConfig.packages.reduce((r,p) => p.name == "bgagame" ? p.location : r, null);
document.write('<script src="' + URL + '/modules/HardbackVue.js?' + rand + '" type="module"><\/script>');
</script>

{OVERALL_GAME_FOOTER}
