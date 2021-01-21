{OVERALL_GAME_HEADER}

<div id="happ" class="tailwind"></div>

<script type="text/javascript">
const rand = Date.now();
const URL = dojoConfig.packages.reduce((r,p) => p.name == "bgagame" ? p.location : r, null);
document.write('<link rel="stylesheet" href="' + URL + '/modules/vue/HardbackVue.css?' + rand + '">');
document.write('<script src="' + URL + '/modules/vue/HardbackVue.js?' + rand + '" type="module"><\/script>');
</script>

{OVERALL_GAME_FOOTER}
