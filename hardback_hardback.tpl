{OVERALL_GAME_HEADER}
<script src="https://code.iconify.design/1/1.0.7/iconify.min.js"></script>
<script type="text/javascript">

var jstpl_player_panel = `<span>
    • <span class="panel-stat" title="Coins"><span id="player_coins_\${id}">\${coins}</span> ¢</span>
    • <span class="panel-stat" title="Ink"><span id="player_ink_\${id}">\${ink}</span><span class="iconify icon-ink" data-icon="mdi:flask-empty-plus" data-inline="false"></span></span>
    • <span class="panel-stat" title="Remover"><span id="player_remover_\${id}">\${remover}</span><span class="iconify icon-remover" data-icon="mdi:flask-empty-remove-outline" data-inline="false"></span></span>
    • <span class="panel-stat" title="Total Card Count"><span id="player_total_\${id}">\${totalCount}</span><span class="iconify icon-total" data-icon="mdi:cards" data-inline="false"></span></span>
</span>`;

var jstpl_player = `<div id="player_\${id}" class="player">
    <div class="player-name" style="color: #$\{color}">\${name}</div>
    <div id="cards_\${id}" class="cards"></div>
</div>`;

var jstpl_tableau= `<div id="tableau" class="tableau">
    <div class="player-name">TABLAU</div>
    <div id="cards_tableau" class="cards"></div>
</div>`;

var jstpl_card = `<div class="letter">\${letter}</div>`;

var jstpl_card_actions = `<div class="card-actions">
    <div class="action" title="Flip"><span class="iconify" data-icon="eva:flip-2-fill" data-inline="false"></span></div>
    <div class="action" title="Remove"><span class="iconify" data-icon="eva:close-fill" data-inline="false"></span></div>
</div>`;

</script>
{OVERALL_GAME_FOOTER}
