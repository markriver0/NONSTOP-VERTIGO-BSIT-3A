
  const form = document.querySelector(".signup-form");
  const password = document.getElementById("password");
  const confirmPassword = document.getElementById("confirm-password");

  form.addEventListener("submit", function (e) {
    if (password.value !== confirmPassword.value) {
      e.preventDefault(); // stop form submission
      alert("Passwords do not match!");
    }
  });

