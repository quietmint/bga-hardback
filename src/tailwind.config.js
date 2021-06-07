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
        12: "12px",
        13: "13px",
        14: "14px",
        15: "15px",
        16: "16px",
        17: "17px",
        18: "18px",
        19: "19px",
        20: "20px",
        24: "24px",
        32: "32px",
        90: "90%",
        105: "105%",
        110: "110%",
        115: "115%",
        120: "120%",
        125: "125%",
      },
      height: {
        13: "3.25rem",
        15: "3.75rem",
        25: "6.25rem",
      },
      lineHeight: {
        14: "3.5rem",
        17: "17px",
        100: "100%",
        110: "110%",
        115: "115%",
        120: "120%",
      },
      minHeight: {
        60: "15rem",
        64: "16rem",
        66: "16.5rem",
      },
      opacity: {
        15: "0.15",
      },
      ringWidth: {
        DEFAULT: "6px",
      },
      width: {
        18: "4.5rem",
      },
      zIndex: {
        top: "2000",
      },
    },
  },
  plugins: [],
};
