document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("signupForm").addEventListener("submit", function(event) {
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirm_password").value;

        if (password !== confirmPassword) {
            alert("Passwords do not match");
            //document.getElementById('passwordError').innerText = "Passwords do not match"
            event.preventDefault(); 
        }
    });
});
