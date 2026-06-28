const starter = {
  icon: "starter",
  bg: "hb-bg-gray-600",
  bg25: "hb-bg-gray-600/25",
  bg80: "hb-bg-gray-600/80",
  border: "hb-border-gray-900",
  text: "hb-text-gray-900",
  textLight: "hb-text-gray-100",
};

const adventure = {
  icon: "adventure",
  bg: "hb-bg-yellow-500",
  bg25: "hb-bg-yellow-500/25",
  bg80: "hb-bg-yellow-500/80",
  border: "hb-border-yellow-900",
  hatch: "hb-hatch-yellow-500/25",
  text: "hb-text-yellow-900",
  textLight: "hb-text-yellow-900",
};

const horror = {
  icon: "horror",
  bg: "hb-bg-green-700",
  bg25: "hb-bg-green-700/25",
  bg80: "hb-bg-green-700/80",
  border: "hb-border-green-700",
  hatch: "hb-hatch-green-700/25",
  text: "hb-text-green-700",
  textLight: "hb-text-green-100",
};

const mystery = {
  icon: "mystery",
  bg: "hb-bg-blue-700",
  bg25: "hb-bg-blue-700/25",
  bg80: "hb-bg-blue-700/80",
  border: "hb-border-blue-700",
  hatch: "hb-hatch-blue-700/25",
  text: "hb-text-blue-700",
  textLight: "hb-text-blue-100",
};

const romance = {
  icon: "romance",
  bg: "hb-bg-red-700",
  bg25: "hb-bg-red-700/25",
  bg80: "hb-bg-red-700/80",
  border: "hb-border-red-700",
  hatch: "hb-hatch-red-700/25",
  text: "hb-text-red-700",
  textLight: "hb-text-red-100",
};

export default Object.freeze({
  // Genres
  STARTER: 0,
  ADVENTURE: 1,
  HORROR: 2,
  MYSTERY: 3,
  ROMANCE: 4,
  GENRES: {
    starter,
    STARTER: starter,
    0: starter,
    adventure,
    ADVENTURE: adventure,
    1: adventure,
    horror,
    HORROR: horror,
    2: horror,
    mystery,
    MYSTERY: mystery,
    3: mystery,
    romance,
    ROMANCE: romance,
    4: romance,
  },

  // Benefits
  EITHER_BASIC: 3,
  EITHER_GENRE: 4,
  EITHER_INK: 5,

  // Languages
  LANG_EN: 1,
  LANG_DE: 2,
  LANG_FR: 3,

  // Game preferences
  PREF_DARK_MODE: 101,
  AUTOMATIC: 0,
  LIGHT: 1,
  DARK: 2,
  PREF_DRAG: 100,
  DRAG_ENABLED: 0,
  PREF_ANIMATION: 150,
  ANIMATION_ENABLED: 0,
  PREF_CARD_SIZE: 151,
  PREF_TOOLTIP: 200,
  TOOLTIP_ENABLED: 0,
  TOOLTIP_TIMEOUT: 500, // ms
});
