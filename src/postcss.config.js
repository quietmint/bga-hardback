module.exports = {
  plugins: {
    "tailwindcss/nesting": {},
    tailwindcss: {},
    autoprefixer: {},
    "postcss-prefix-selector": {
      includeFiles: "main.css",
      prefix: ".hb-tailwind ",
      transform: function (prefix, selector, prefixedSelector, filePath, rule) {
        if (selector.startsWith(".hb-tailwind")) {
          return selector;
        } else {
          return prefixedSelector;
        }
      },
    },
  },
};
