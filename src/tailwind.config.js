const colors = require("tailwindcss/colors");
const plugin = require("tailwindcss/plugin");
const flattenColorPalette = require("tailwindcss/lib/util/flattenColorPalette").default;
const { parseColor } = require("tailwindcss/lib/util/color");

module.exports = {
  content: ["*.js", "*.vue"],
  darkMode: "class",
  theme: {
    colors: {
      transparent: "transparent",
      current: "currentColor",
      inherit: "inherit",
      black: colors.black,
      white: colors.white,
      gray: colors.neutral,
      red: colors.red,
      yellow: colors.yellow,
      green: colors.lime,
      blue: colors.blue,
      purple: colors.fuchsia,
    },
    boxShadow: {
      DEFAULT: "2px 2px 4px rgb(0 0 0 / 50%)",
    },
    extend: {
      borderWidth: {
        3: "3px",
      },
      flex: {
        2: "0 1 calc(50% - 0.25rem)",
        3: "0 1 calc(33% - 0.25rem)",
      },
      fontSize: {
        13: "13px",
        14: "14px",
        15: "15px",
        16: "16px",
        17: "17px",
        18: "18px",
        19: "19px",
        20: "20px",
        22: "22px",
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
      opacity: {
        33: ".33",
        66: ".66",
      },
      padding: {
        "1/3": "33.333333%",
        "2/3": "66.666667%",
      },
      ringWidth: {
        DEFAULT: "6px",
      },
      width: {
        120: "30rem",
      },
      zIndex: {
        top: "2000",
      },
    },
  },
  plugins: [
    plugin(({ matchUtilities, theme }) => {
      matchUtilities(
        {
          hatch: (value) => {
            const { color, alpha } = parseColor(value);
            return {
              background: `repeating-linear-gradient(45deg, rgb(0 0 0 / var(--hatch-opacity)), rgb(0 0 0 / var(--hatch-opacity)) 1px, transparent 1px, transparent var(--hatch-size)), rgb(${color[0]} ${color[1]} ${color[2]} / ${alpha})`,
            };
          },
        },
        { values: flattenColorPalette(theme("colors")), type: "color" }
      );
    }),
  ],
};
