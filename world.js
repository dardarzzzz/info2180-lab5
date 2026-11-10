document.addEventListener('DOMContentLoaded', () => {
  const lookup = document.getElementById('lookup');
  const lookupCities = document.getElementById('lookup-cities');
  const country = document.getElementById('country');
  const result = document.getElementById('result');
  result.setAttribute('aria-live', 'polite');

  async function lookupResults(type) {
    lookup.disabled = true;
    lookupCities.disabled = true;
    result.setAttribute('aria-busy', 'true');
    result.textContent = `Loading ${type}...`;
    try {
      const params = new URLSearchParams({ country: country.value.trim() });
      if (type === 'cities') params.set('lookup', 'cities');
      const response = await fetch(`world.php?${params.toString()}`);
      if (!response.ok) throw new Error('Lookup failed');
      result.innerHTML = await response.text();
    } catch (error) {
      result.textContent = 'Unable to load results. Please try again.';
    } finally {
      lookup.disabled = false;
      lookupCities.disabled = false;
      result.setAttribute('aria-busy', 'false');
    }
  }

  lookup.addEventListener('click', () => lookupResults('countries'));
  lookupCities.addEventListener('click', () => lookupResults('cities'));
  country.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && !lookup.disabled) {
      event.preventDefault();
      lookupResults('countries');
    }
  });
});
