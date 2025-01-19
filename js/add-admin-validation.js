document.addEventListener('DOMContentLoaded', function() {
    const addAdminForm = document.getElementById('addAdminForm');
    const nameInput = document.getElementById('name_add');

    function validateName(name) {
        return !/\d/.test(name); // Returns false if name contains digits
    }

    addAdminForm.addEventListener('submit', function(event) {
        const name = nameInput.value.trim();
        
        if (!validateName(name)) {
            event.preventDefault();
            alert('Name cannot contain digits');
            nameInput.focus();
        }
    });
});
