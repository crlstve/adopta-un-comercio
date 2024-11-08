/** @type {import('tailwindcss').Config} */
module.exports = {
  mode: 'jit',
  purge: {
    content: [
      './assets/js/*.js',
      './templates/*.php',
      './partials/*.php',
      './inc/*.php',
      './*.php',
    ],
  },
  darkMode: 'selector',
  theme: {
    extend: {},
    fontFamily: {
      'sans': ['Jeko', 'sans-serif', 'Poppins'],
      'title': ['Jeko Bold', 'Poppins', 'sans-serif'],
    },
    container: {
      center: true,
      padding: {
        DEFAULT: '1rem',
        sm: '2rem',
        lg: '4rem',
        xl: '5rem',
        '2xl': '6rem',
      },
      screens: {
        sm: '600px',
        md: '728px',
        lg: '984px',
        xl: '1120px',
        '2xl': '1280px',
      },
      colors: {
        'dark': '#232323',
        'pink': '#ee69f9',
        'orange': '#e7a300',
      },
    },
  },
  variants: {},
  plugins: [],
}