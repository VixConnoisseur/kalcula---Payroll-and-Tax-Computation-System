// Add this line at the top to find the main search bar in the header
const mainSearchBox = document.querySelector("header input[type='text']");

// Change the 'searchBox.addEventListener' to 'mainSearchBox.addEventListener'
mainSearchBox.addEventListener("keyup", (e) => {
  // Only search if the employees tab is active
  const employeesTab = document.querySelector(
    "button[data-page='dashboard/employees.php']"
  );
  if (employeesTab && employeesTab.classList.contains("active")) {
    fetchEmployees(e.target.value);
  }
});
