// payroll.js - Revised for simplicity and better flow
document.addEventListener("DOMContentLoaded", () => {
  console.log("Payroll script loaded!");

  // Get elements
  const employeeIdInput = document.getElementById("employeeId");
  const fetchBtn = document.getElementById("fetchEmployeeBtn");
  const employeeDetails = document.getElementById("employeeDetails");
  const employeeName = document.getElementById("employeeName");
  const employeePosition = document.getElementById("employeePosition");
  const dailyRateDisplay = document.getElementById("dailyRateDisplay");
  const daysWorkedInput = document.getElementById("daysWorked");
  const daysAbsentInput = document.getElementById("daysAbsent");
  const computeBtn = document.getElementById("computePayrollBtn");
  const payrollSummary = document.getElementById("payrollSummary");
  const grossPay = document.getElementById("grossPay");
  const sssEl = document.getElementById("sss");
  const philhealthEl = document.getElementById("philhealth");
  const pagibigEl = document.getElementById("pagibig");
  const taxEl = document.getElementById("tax");
  const netPay = document.getElementById("netPay");
  const printBtn = document.getElementById("printPayslipBtn");

  let employeeData = null;
  let dailyRate = 0;

  // Helper to format money
  function formatMoney(amount) {
    return `₱${parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`;
  }

  // Helper to calculate TRAIN Law tax (simplified monthly brackets)
  function calculateTax(taxableIncome) {
    if (taxableIncome <= 20833.33) return 0;
    if (taxableIncome <= 33333.33) return (taxableIncome - 20833.33) * 0.20;
    if (taxableIncome <= 66666.67) return 2500 + (taxableIncome - 33333.33) * 0.25;
    if (taxableIncome <= 166666.67) return 10416.67 + (taxableIncome - 66666.67) * 0.30;
    return 40833.33 + (taxableIncome - 166666.67) * 0.32;
  }

  // Fetch employee
  fetchBtn.addEventListener("click", async () => {
    const id = employeeIdInput.value.trim();
    if (!id) {
      alert("Please enter Employee ID!");
      return;
    }

    try {
      const response = await fetch(`../../backend/api/payroll_api.php?id=${id}`);
      if (!response.ok) {
        throw new Error(`Error: ${response.status}`);
      }
      const data = await response.json();

      if (data.error) {
        alert(data.error);
        return;
      }

      employeeData = data;
      employeeName.textContent = employeeData.full_name || `${data.first_name} ${data.last_name}`;
      employeePosition.textContent = data.position;
      dailyRate = (data.salary / 22).toFixed(2); // Assume 22 working days
      dailyRateDisplay.textContent = formatMoney(dailyRate);

      // Show details and reset inputs
      employeeDetails.classList.remove("hidden");
      daysWorkedInput.value = 0;
      daysAbsentInput.value = 0;
      computeBtn.disabled = false;
      payrollSummary.classList.add("hidden");
      printBtn.disabled = true;

      console.log("Employee fetched:", data);
    } catch (error) {
      console.error("Fetch error:", error);
      alert("Failed to fetch employee. Check ID and try again.");
    }
  });

  // Compute payroll
  computeBtn.addEventListener("click", () => {
    if (!employeeData) {
      alert("Fetch employee first!");
      return;
    }

    const worked = parseInt(daysWorkedInput.value) || 0;
    const absent = parseInt(daysAbsentInput.value) || 0;

    if (worked < 0 || absent < 0) {
      alert("Days must be 0 or more!");
      return;
    }

    // Calculations (simplified)
    const grossPayAmount = dailyRate * worked;
    const monthlySalary = dailyRate * 22; // Approximate monthly

    // Simplified contributions (use actual tables in real app)
    const sss = Math.min(monthlySalary * 0.0455, 1350); // Employee share approx
    const philhealth = Math.min(monthlySalary * 0.025, 2000); // 2.5% shared
    const pagibig = Math.min(monthlySalary * 0.02, 100); // 2% shared, cap 100

    const totalDeductions = sss + philhealth + pagibig;
    const taxable = grossPayAmount - totalDeductions;
    const tax = Math.max(0, calculateTax(taxable));
    const net = grossPayAmount - totalDeductions - tax;

    // Update display
    grossPay.textContent = formatMoney(grossPayAmount);
    sssEl.textContent = formatMoney(sss);
    philhealthEl.textContent = formatMoney(philhealth);
    pagibigEl.textContent = formatMoney(pagibig);
    taxEl.textContent = formatMoney(tax);
    netPay.textContent = formatMoney(net);

    payrollSummary.classList.remove("hidden");
    printBtn.disabled = false;

    console.log("Payroll computed!");
  });

  // Print payslip
  printBtn.addEventListener("click", () => {
    if (!employeeData || payrollSummary.classList.contains("hidden")) {
      alert("Compute payroll first!");
      return;
    }

    const printContent = `
      <div style="font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: auto; border: 1px solid #ccc;">
        <h2 style="text-align: center; color: #333;">Payslip - ${employeeName.textContent}</h2>
        <p><strong>Position:</strong> ${employeePosition.textContent}</p>
        <p><strong>Days Worked:</strong> ${daysWorkedInput.value} | <strong>Days Absent:</strong> ${daysAbsentInput.value}</p>
        <hr>
        <h3>Earnings</h3>
        <p><strong>Gross Pay:</strong> ${grossPay.textContent}</p>
        <h3>Deductions</h3>
        <p>SSS: ${sssEl.textContent}</p>
        <p>PhilHealth: ${philhealthEl.textContent}</p>
        <p>Pag-IBIG: ${pagibigEl.textContent}</p>
        <p>Withholding Tax (TRAIN): ${taxEl.textContent}</p>
        <hr>
        <h3 style="text-align: right; font-size: 1.5em; color: green;"><strong>Net Pay: ${netPay.textContent}</strong></h3>
      </div>
    `;

    const printWin = window.open("", "_blank");
    printWin.document.write(`
      <html><head><title>Payslip</title>
      <style>body { margin: 0; padding: 0; }</style></head>
      <body>${printContent}</body></html>
    `);
    printWin.document.close();
    printWin.print();
  });

  // Allow Enter key in ID input to fetch
  employeeIdInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") fetchBtn.click();
  });
});
