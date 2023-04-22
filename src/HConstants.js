const starter = {
  icon: "starter",
  bg: "bg-gray-600",
  bg25: "bg-gray-600/25",
  bg80: "bg-gray-600/80",
  border: "border-gray-900",
  text: "text-gray-900",
  textLight: "text-gray-100",
};

const adventure = {
  icon: "adventure",
  bg: "bg-yellow-500",
  bg25: "bg-yellow-500/25",
  bg80: "bg-yellow-500/80",
  border: "border-yellow-900",
  hatch: "hatch-yellow-500/25",
  text: "text-yellow-900",
  textLight: "text-yellow-900",
};

const horror = {
  icon: "horror",
  bg: "bg-green-700",
  bg25: "bg-green-700/25",
  bg80: "bg-green-700/80",
  border: "border-green-700",
  hatch: "hatch-green-700/25",
  text: "text-green-700",
  textLight: "text-green-100",
};

const mystery = {
  icon: "mystery",
  bg: "bg-blue-700",
  bg25: "bg-blue-700/25",
  bg80: "bg-blue-700/80",
  border: "border-blue-700",
  hatch: "hatch-blue-700/25",
  text: "text-blue-700",
  textLight: "text-blue-100",
};

const romance = {
  icon: "romance",
  bg: "bg-red-700",
  bg25: "bg-red-700/25",
  bg80: "bg-red-700/80",
  border: "border-red-700",
  hatch: "hatch-red-700/25",
  text: "text-red-700",
  textLight: "text-red-100",
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
