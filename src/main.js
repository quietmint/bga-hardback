import { createApp } from "vue";
import mitt from "mitt";
import HConstants from "./HConstants.js";
import HGame from "./HGame.vue";

window.Vue = { createApp };
window.mitt = mitt;
window.HConstants = HConstants;
window.HGame = HGame;
