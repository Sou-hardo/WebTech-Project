function validateForm() {
    let isValid = true;
    const mobileNumber = document.getElementById("mobile-number").value;
    const pin = document.getElementById("pin").value;
    const mobileError = document.getElementById("mobile-error");
    const pinError = document.getElementById("pin-error");

    // Reset error messages
    mobileError.style.display = "none";
    pinError.style.display = "none";

    // Validate mobile number
    if (!/^\d{11}$/.test(mobileNumber)) {
        mobileError.textContent = "Mobile number must be 11 digits";
        mobileError.style.display = "block";
        isValid = false;
    }

    // Validate PIN
    if (!/^\d{4,5}$/.test(pin)) {
        pinError.textContent = "PIN must be 4 or 5 digits";
        pinError.style.display = "block";
        isValid = false;
    }

    return isValid;
}