const colors = require("tailwindcss/colors");

module.exports = {
  purge: ["*.js", "*.vue"],
  darkMode: "media",
  theme: {
    colors: {
      transparent: "transparent",
      current: "currentColor",
      black: colors.black,
      white: colors.white,
      gray: colors.trueGray,
      red: colors.red,
      yellow: colors.yellow,
      green: colors.lime,
      blue: colors.blue,
      purple: colors.fuchsia,
    },
    boxShadow: {
      DEFAULT: "2px 2px 4px rgba(0, 0, 0, 0.5)",
    },
    extend: {
      borderWidth: {
        3: "3px",
      },
      fontSize: {
        14: "14px",
        15: "15px",
        16: "16px",
        17: "17px",
        18: "18px",
        20: "20px",
        24: "24px",
        90: "90%",
        105: "105%",
        110: "110%",
        115: "115%",
        125: "125%",
      },
      lineHeight: {
        14: "3.5rem",
        120: "120%",
      },
      margin: {
        18: "4.5rem",
      },
      ringWidth: {
        DEFAULT: "6px",
      },
      zIndex: {
        top: "2000",
      },
    },
  },
  plugins: [],
};
