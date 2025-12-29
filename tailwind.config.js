/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/Views/**/*.php",
    "./public/**/*.html",
  ],
  theme: {
    extend: {
      colors: {
        // Nord Theme Color Palette
        nord: {
          // Polar Night (Dark Backgrounds)
          0: '#2e3440',
          1: '#3b4252',
          2: '#434c5e',
          3: '#4c566a',
          // Snow Storm (Light Text/Backgrounds)
          4: '#d8dee9',
          5: '#e5e9f0',
          6: '#eceff4',
          // Frost (Accents & UI Components)
          7: '#8fbcbb',
          8: '#88c0d0',
          9: '#81a1c1',
          10: '#5e81ac',
          // Aurora (Status & Notifications)
          11: '#bf616a',
          12: '#d08770',
          13: '#ebcb8b',
          14: '#a3be8c',
          15: '#b48ead',
        },
      },
    },
  },
  plugins: [require("daisyui")],
  daisyui: {
    themes: [
      {
        nord: {
          'primary': '#88c0d0',        // nord8 - Primary accent
          'primary-content': '#2e3440', // nord0 - Text on primary
          'secondary': '#81a1c1',      // nord9 - Secondary accent
          'secondary-content': '#2e3440',
          'accent': '#8fbcbb',         // nord7 - Tertiary accent
          'accent-content': '#2e3440',
          'neutral': '#4c566a',        // nord3 - Neutral gray
          'neutral-content': '#eceff4', // nord6
          'base-100': '#2e3440',       // nord0 - Base background
          'base-200': '#3b4252',       // nord1 - Elevated surface
          'base-300': '#434c5e',       // nord2 - More elevated
          'base-content': '#eceff4',   // nord6 - Base text
          'info': '#5e81ac',           // nord10 - Info blue
          'info-content': '#eceff4',
          'success': '#a3be8c',        // nord14 - Success green
          'success-content': '#2e3440',
          'warning': '#ebcb8b',        // nord13 - Warning yellow
          'warning-content': '#2e3440',
          'error': '#bf616a',          // nord11 - Error red
          'error-content': '#eceff4',
        },
      },
    ],
  },
}