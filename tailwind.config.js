/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.ts",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      borderRadius: {
        'astryx-inner': 'var(--astryx-radius-inner)',
        'astryx-element': 'var(--astryx-radius-element)',
        'astryx-container': 'var(--astryx-radius-container)',
        'astryx-card': 'var(--astryx-radius-card)',
        'astryx-modal': 'var(--astryx-radius-modal)',
        'astryx-page': 'var(--astryx-radius-page)',
      },
      boxShadow: {
        'astryx-low': 'var(--astryx-shadow-low)',
        'astryx-med': 'var(--astryx-shadow-med)',
        'astryx-high': 'var(--astryx-shadow-high)',
        'astryx-inset-hover': 'var(--astryx-shadow-inset-hover)',
        'astryx-inset-selected': 'var(--astryx-shadow-inset-selected)',
      },
      colors: {
        astryx: {
          body: 'var(--astryx-color-background-body)',
          surface: 'var(--astryx-color-background-surface)',
          card: 'var(--astryx-color-background-card)',
          'card-hover': 'var(--astryx-color-background-card-hover)',
          popover: 'var(--astryx-color-background-popover)',
          muted: 'var(--astryx-color-background-muted)',
          accent: 'var(--astryx-color-accent)',
          'accent-hover': 'var(--astryx-color-accent-hover)',
          'accent-active': 'var(--astryx-color-accent-active)',
          'accent-contrast': 'var(--astryx-color-accent-contrast)',
          border: 'var(--astryx-border-default)',
          'border-subtle': 'var(--astryx-border-subtle)',
          'border-strong': 'var(--astryx-border-strong)',
          text: {
            primary: 'var(--astryx-color-text-primary)',
            secondary: 'var(--astryx-color-text-secondary)',
            tertiary: 'var(--astryx-color-text-tertiary)',
          },
        },
      },
      transitionTimingFunction: {
        'astryx-standard': 'var(--astryx-ease-standard)',
        'astryx-emphasized': 'var(--astryx-ease-emphasized)',
      },
      transitionDuration: {
        'astryx-fast': 'var(--astryx-duration-fast)',
        'astryx-med': 'var(--astryx-duration-medium)',
      },
    },
  },
  plugins: [],
}

