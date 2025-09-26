// dashboard.js

// Function to fetch data from the API and update the dashboard cards
function fetchDashboardData() {
  // Correct relative path to the API endpoint
  // It should go up two directories to the root of Kalcula, then down to backend/api/
  fetch("../../backend/api/dashboard_api.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      if (data.error) {
        console.error("API Error:", data.error);
        return;
      }

      // Update the HTML elements with data from the API
      document.getElementById("totalEmployees").innerText = data.employees;
      document.getElementById("totalPayrolls").innerText = data.payrolls;
      document.getElementById("totalNetPay").innerText =
        "₱" + data.total_net_pay;
    })
    .catch((error) => {
      console.error("Fetch Error:", error);
      document.getElementById(
        "content"
      ).innerHTML = `<p class="text-red-600">Failed to load dashboard data. Please check your API.</p>`;
    });
}

// Function to handle tab switching
function setupTabSwitching() {
  document.querySelectorAll(".tab-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      // Remove 'active' class from all buttons
      document
        .querySelectorAll(".tab-btn")
        .forEach((b) => b.classList.remove("active"));
      // Add 'active' class to the clicked button
      btn.classList.add("active");

      const page = btn.getAttribute("data-page");

      // Fetch the content for the selected page
      fetch(page)
        .then((res) => {
          if (!res.ok) {
            throw new Error(`HTTP error! Status: ${res.status}`);
          }
          return res.text();
        })
        .then((html) => {
          document.getElementById("content").innerHTML = html;

          // If the dashboard tab is clicked, fetch the API data
          if (page === "kalcula_dashboard_content.php") {
            fetchDashboardData();
          }
        })
        .catch((error) => {
          console.error("Fetch Error:", error);
          document.getElementById(
            "content"
          ).innerHTML = `<p class="text-red-600">Error loading ${page}. Please check the file path.</p>`;
        });
    });
  });
}

// Function to toggle profile dropdown
function setupProfileDropdown() {
  document.getElementById("profileBtn").addEventListener("click", () => {
    document.getElementById("profileMenu").classList.toggle("hidden");
  });
}

// Initialize all functionality when the page loads
document.addEventListener("DOMContentLoaded", () => {
  setupTabSwitching();
  setupProfileDropdown();

  // Load dashboard content and data on the initial page load
  fetch("kalcula_dashboard_content.php")
    .then((response) => response.text())
    .then((html) => {
      document.getElementById("content").innerHTML = html;
      fetchDashboardData(); // Call the API fetch function
    })
    .catch((error) => {
      console.error("Initial Load Error:", error);
    });
});
