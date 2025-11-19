const apiUrl = "https://apploqic.my/api/v1/analytics";

async function fetchAnalytics() {
  try {
    const response = await fetch(apiUrl);
    const result = await response.json();

    if(result.status === "success") {
      const data = result.data;
      document.getElementById("total_businesses").textContent = data.total_businesses;
      document.getElementById("active_businesses").textContent = data.active_businesses;
      document.getElementById("inactive_businesses").textContent = data.inactive_businesses;
      document.getElementById("featured_businesses").textContent = data.featured_businesses;
      document.getElementById("activation_rate").textContent = data.activation_rate + "%";
      document.getElementById("featured_rate").textContent = data.featured_rate + "%";
    } else {
      console.error("Analytics API returned error:", result);
    }
  } catch (err) {
    console.error("Failed to fetch analytics:", err);
  }
}

// Initial fetch
fetchAnalytics();

// Refresh every 30 seconds
setInterval(fetchAnalytics, 30000);
