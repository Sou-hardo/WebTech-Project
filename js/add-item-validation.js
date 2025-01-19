function validateForm() {
    const name = document.getElementsByName('name')[0].value.trim();
    const category = document.getElementsByName('category')[0].value.trim();
    
    // Clear previous error messages
    document.getElementById('nameError').textContent = '';
    document.getElementById('categoryError').textContent = '';

    let isValid = true;

    // Validate name (required, letters and spaces only)
    if (name.length === 0) {
        document.getElementById('nameError').textContent = 'Name is required';
        isValid = false;
    } else if (!/^[A-Za-z\s]+$/.test(name)) {
        document.getElementById('nameError').textContent = 'Name can only contain letters and spaces';
        isValid = false;
    }

    // Validate category (optional, but if provided, letters and spaces only)
    if (category.length > 0 && !/^[A-Za-z\s]+$/.test(category)) {
        document.getElementById('categoryError').textContent = 'Category can only contain letters and spaces';
        isValid = false;
    }

    return isValid;
}
