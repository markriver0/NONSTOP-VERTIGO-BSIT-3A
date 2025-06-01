// Dark Mode Toggle
const darkModeToggle = document.getElementById('dark-mode-toggle');
const body = document.body;

// Check for saved theme in localStorage
const savedTheme = localStorage.getItem('theme');
if (savedTheme) {
  body.setAttribute('data-theme', savedTheme);
  if (savedTheme === 'dark') {
    darkModeToggle.innerHTML = '☀️'; // Sun icon for light mode
  }
}

// Toggle Dark Mode
darkModeToggle.addEventListener('click', () => {
  if (body.getAttribute('data-theme') === 'dark') {
    body.setAttribute('data-theme', 'light');
    darkModeToggle.innerHTML = '🌙'; // Moon icon for dark mode
    localStorage.setItem('theme', 'light');
  } else {
    body.setAttribute('data-theme', 'dark');
    darkModeToggle.innerHTML = '☀️'; // Sun icon for light mode
    localStorage.setItem('theme', 'dark');
  }
});