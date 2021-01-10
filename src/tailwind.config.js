const colors = require('tailwindcss/colors')

module.exports = {
    purge: ['*.vue'],
    darkMode: false, // or 'media' or 'class'
    theme: {
        colors: {
            gray: colors.trueGray,
            red: colors.red,
            yellow: colors.yellow,
            green: colors.lime,
            blue: colors.blue,
            indigo: colors.indigo,
            purple: colors.violet,
            pink: colors.pink
        },
        extend: {
            minHeight: {
                '60': '15rem',
                '64': '16rem'
            },
        },
    },
    variants: {
        extend: {
            backgroundColor: ['active'],
        },
    },
    plugins: [],
}