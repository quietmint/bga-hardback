const colors = require("tailwindcss/colors");

module.exports = {
  purge: ["*.vue"],
  darkMode: "media",
  theme: {
    colors: {
      transparent: "transparent",
      white: "white",
      black: "black",
      gray: colors.trueGray,
      red: colors.red,
      yellow: colors.yellow,
      green: colors.lime,
      blue: colors.blue,
      indigo: colors.indigo,
      purple: colors.violet,
      pink: colors.pink,
    },
    boxShadow: {
      DEFAULT: "2px 2px 4px rgba(0, 0, 0, 0.5)",
    },
    extend: {
      borderWidth: {
        3: "3px",
      },
      cursor: {
        "ew-resize": "ew-resize",
      },
      ringWidth: {
        DEFAULT: "6px",
      },
      minHeight: {
        60: "15rem",
        64: "16rem",
        66: "16.5rem",
      },
      fontSize: {
        12: "12px",
        13: "13px",
        14: "14px",
        15: "15px",
        16: "16px",
        17: "17px",
        18: "18px",
        20: "20px",
        24: "24px",
        32: "32px",
      },
      lineHeight: {
        14: "3.5rem",
        17: "17px",
      },
    },
  },
  plugins: [],
};
