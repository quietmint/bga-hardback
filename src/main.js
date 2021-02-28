import { createApp } from "vue";
import mitt from "mitt";
import HConstants from "./HConstants.js";
import HGame from "./HGame.vue";
import "./index.css";

window.Vue = { createApp };
window.mitt = mitt;
window.HConstants = HConstants;
window.HGame = HGame;
