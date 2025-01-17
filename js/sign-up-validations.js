let duplicateChecks = {
    username: false,
    email: false,
    phone: false
};

function checkDuplicate(field) {
    const value = document.forms["sign-up"][field].value;
    const errorSpan = document.getElementById(`${field}-error`);
    
    if (!value) return;

    fetch('check_duplicate.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `field=${field}&value=${value}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.duplicate) {
            errorSpan.textContent = `This ${field} is already registered`;
            errorSpan.style.display = 'block';
            duplicateChecks[field] = true;
        } else {
            errorSpan.textContent = '';
            errorSpan.style.display = 'none';
            duplicateChecks[field] = false;
        }
    });
}

function validateForm() {
    // Name validation - no numbers allowed
    const name = document.forms["sign-up"]["name"].value;
    if (/\d/.test(name)) {
        alert("Name cannot contain numbers");
        return false;
    }

    // Phone validation - must be 11 digits
    const phone = document.forms["sign-up"]["phone"].value;
    if (!/^\d{11}$/.test(phone)) {
        alert("Phone number must be exactly 11 digits");
        return false;
    }

    // Email validation
    const email = document.forms["sign-up"]["email"].value;
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        alert("Please enter a valid email address");
        return false;
    }

    // Date of birth validation
    const dob = new Date(document.forms["sign-up"]["date-of-birth"].value);
    const today = new Date();
    if (dob > today) {
        alert("Date of birth cannot be in the future");
        return false;
    }

    // Password validation
    const password = document.forms["sign-up"]["password"].value;
    if (password.length < 6) {
        alert("Password must be at least 6 characters long");
        return false;
    }

    // Zip code validation
    const zipCode = document.forms["sign-up"]["zip-code"].value;
    if (!/^\d{4}$/.test(zipCode)) {
        alert("Zip code must be exactly 4 digits");
        return false;
    }

    // Check for duplicates
    if (duplicateChecks.username || duplicateChecks.email || duplicateChecks.phone) {
        alert("Please fix the entry errors");
        return false;
    }

    return true;
}