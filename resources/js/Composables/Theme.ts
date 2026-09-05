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
      const meta = document.querySelector('meta[name="theme-color"]');
      if (meta) meta.setAttribute('content', '#090a0f');
    } else {
      document.documentElement.classList.remove('dark');
      const meta = document.querySelector('meta[name="theme-color"]');
      if (meta) meta.setAttribute('content', '#f8fafc');
    }
  }
}

export function useTheme() {
  if (typeof window !== 'undefined' && !isInitialized) {
    isInitialized = true;
    const stored = (typeof localStorage !== 'undefined' ? localStorage.getItem('theme') : null) as ThemeMode | null;
    
    // Padrão do site é 'light' (claro e limpo) a menos que o usuário tenha escolhido 'dark'
    if (stored === 'dark') {
      themeMode.value = 'dark';
      applyTheme('dark');
    } else {
      themeMode.value = 'light';
      applyTheme('light');
    }
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
