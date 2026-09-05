import { ref } from 'vue';

type ThemeMode = 'auto' | 'light' | 'dark';

const currentTheme = ref<'light' | 'dark'>('light');
const themeMode = ref<ThemeMode>('auto');

let isInitialized = false;

function applyTheme(theme: 'light' | 'dark') {
  currentTheme.value = theme;
  if (typeof document !== 'undefined') {
    if (theme === 'dark') {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  }
}

export function useTheme() {
  if (typeof window !== 'undefined' && !isInitialized) {
    isInitialized = true;
    const stored = (typeof localStorage !== 'undefined' ? localStorage.getItem('theme') : null) as ThemeMode || 'auto';
    themeMode.value = stored;

    const mediaQuery = typeof window.matchMedia === 'function' ? window.matchMedia('(prefers-color-scheme: dark)') : null;
    
    function update() {
      if (themeMode.value === 'auto') {
        applyTheme(mediaQuery?.matches ? 'dark' : 'light');
      } else {
        applyTheme(themeMode.value === 'dark' ? 'dark' : 'light');
      }
    }

    if (mediaQuery && typeof mediaQuery.addEventListener === 'function') {
      mediaQuery.addEventListener('change', () => {
        if (themeMode.value === 'auto') {
          update();
        }
      });
    }

    update();
  }

  function setTheme(mode: ThemeMode) {
    themeMode.value = mode;
    if (mode === 'auto') {
      if (typeof localStorage !== 'undefined') localStorage.removeItem('theme');
      const mediaQuery = typeof window !== 'undefined' && typeof window.matchMedia === 'function' ? window.matchMedia('(prefers-color-scheme: dark)') : null;
      applyTheme(mediaQuery?.matches ? 'dark' : 'light');
    } else {
      if (typeof localStorage !== 'undefined') localStorage.setItem('theme', mode);
      applyTheme(mode);
    }
  }

  function toggleTheme() {
    if (currentTheme.value === 'dark') {
      setTheme('light');
    } else {
      setTheme('dark');
    }
  }

  return {
    currentTheme,
    themeMode,
    setTheme,
    toggleTheme,
  };
}
