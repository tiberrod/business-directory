console.log('Admin API Debug Script Loaded Successfully');
console.log('Current URL:', window.location.href);
console.log('Hostname:', window.location.hostname);
console.log('Port:', window.location.port);
console.log('Pathname:', window.location.pathname);

// Test if we can access the API file
fetch('js/admin-api.js')
  .then(response => {
    console.log('API script fetch status:', response.status, response.statusText);
    if (response.ok) {
      console.log('✅ API script is accessible');
    } else {
      console.log('❌ API script not accessible');
    }
  })
  .catch(error => {
    console.log('❌ Error fetching API script:', error);
  });