function validateForm() {
    const name = document.getElementsByName('name')[0].value.trim();
    const phone = document.getElementsByName('phone')[0].value.trim();
    const zipCode = document.getElementsByName('zip-code')[0].value.trim();
    
    // Clear previous error messages
    document.getElementById('nameError').textContent = '';
    document.getElementById('phoneError').textContent = '';
    document.getElementById('zipError').textContent = '';

    let isValid = true;

    // Validate name (letters and spaces only)
    if (!/^[A-Za-z\s]+$/.test(name)) {
        document.getElementById('nameError').textContent = 'Name can only contain letters and spaces';
        isValid = false;
    }

    // Validate phone (11 digits)
    if (!/^\d{11}$/.test(phone)) {
        document.getElementById('phoneError').textContent = 'Phone number must be exactly 11 digits';
        isValid = false;
    }

    // Validate zip code (4 digits)
    if (!/^\d{4}$/.test(zipCode)) {
        document.getElementById('zipError').textContent = 'Zip code must be exactly 4 digits';
        isValid = false;
    }

    return isValid;
}
