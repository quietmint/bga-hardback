const colors = require('tailwindcss/colors')

module.exports = {
    purge: ['*.vue'],
    darkMode: false, // or 'media' or 'class'
    theme: {
        colors: {
            transparent: 'transparent',
            white: 'white',
            black: 'black',
            gray: colors.trueGray,
            red: colors.red,
            yellow: colors.yellow,
            green: colors.lime,
            blue: colors.blue,
            indigo: colors.indigo,
            purple: colors.violet,
            pink: colors.pink
        },
        boxShadow: {
            DEFAULT: '2px 2px 4px rgba(0, 0, 0, 0.5)',
        },
        extend: {
            minHeight: {
                '60': '15rem',
                '64': '16rem',
                '66': '16.5rem',
            },
        },
    },
    variants: {
        extend: {
            backgroundColor: ['active'],
            padding: ['hover', 'focus'],
        },
    },
    plugins: [],
}