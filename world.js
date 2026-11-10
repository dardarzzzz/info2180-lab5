document.addEventListener('DOMContentLoaded', () => {
  const lookup = document.getElementById('lookup');
  const country = document.getElementById('country');
  const result = document.getElementById('result');
  result.setAttribute('aria-live', 'polite');

  async function lookupCountry() {
    lookup.disabled = true;
    result.textContent = 'Loading countries...';

    try {
      const params = new URLSearchParams({ country: country.value.trim() });
      const response = await fetch(`world.php?${params.toString()}`);
      if (!response.ok) {
        throw new Error('Lookup failed');
      }
      result.innerHTML = await response.text();
    } catch (error) {
      result.textContent = 'Unable to load countries. Please try again.';
    } finally {
      lookup.disabled = false;
    }
  }

  lookup.addEventListener('click', lookupCountry);
  country.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && !lookup.disabled) {
      event.preventDefault();
      lookupCountry();
    }
  });
});
