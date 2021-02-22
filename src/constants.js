const starter = {
  icon: "starter",
  bg: "bg-gray-600",
  text: "text-gray-900",
  textLight: "text-gray-100",
};

const adventure = {
  icon: "adventure",
  bg: "bg-yellow-500",
  text: "text-yellow-900",
  textLight: "text-yellow-900",
};

const horror = {
  icon: "horror",
  bg: "bg-green-700",
  text: "text-green-700",
  textLight: "text-green-100",
};

const mystery = {
  icon: "mystery",
  bg: "bg-blue-700",
  text: "text-blue-700",
  textLight: "text-blue-100",
};

const romance = {
  icon: "romance",
  bg: "bg-red-700",
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
    STARTER: starter,
    0: starter,
    ADVENTURE: adventure,
    1: adventure,
    HORROR: horror,
    2: horror,
    MYSTERY: mystery,
    3: mystery,
    ROMANCE: romance,
    4: romance,
  },

  // Benefits
  EITHER_BASIC: 3,
  EITHER_GENRE: 4,
  EITHER_INK: 5,
});
