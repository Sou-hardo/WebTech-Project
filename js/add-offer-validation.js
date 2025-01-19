function validateForm() {
    const startDate = new Date(document.getElementsByName('start_date')[0].value);
    const expiryDate = new Date(document.getElementsByName('expiry_date')[0].value);
    const promoCode = document.getElementsByName('promo_code')[0].value.trim();
    
    // Clear previous error messages
    document.getElementById('startDateError').textContent = '';
    document.getElementById('expiryDateError').textContent = '';
    document.getElementById('promoCodeError').textContent = '';

    let isValid = true;

    // Check dates
    if (startDate > expiryDate) {
        document.getElementById('startDateError').textContent = 'Start date cannot be after expiry date';
        isValid = false;
    }

    // Enhanced promo code validation
    if (promoCode.length === 0) {
        document.getElementById('promoCodeError').textContent = 'Promo code is required';
        isValid = false;
    } else if (promoCode.length > 20) {
        document.getElementById('promoCodeError').textContent = 'Promo code must be 20 characters or less';
        isValid = false;
    } else if (!/^[A-Za-z0-9]+$/.test(promoCode)) {
        document.getElementById('promoCodeError').textContent = 'Only contain letters and numbers';
        isValid = false;
    }

    return isValid;
}