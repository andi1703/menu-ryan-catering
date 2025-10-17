document.addEventListener('DOMContentLoaded', function() {
    // Function to fetch material requirements
    function fetchMaterialRequirements() {
        fetch('/api/material-requirements')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayMaterialRequirements(data.data);
                } else {
                    console.error(data.message);
                }
            })
            .catch(error => console.error('Error fetching material requirements:', error));
    }

    // Function to display material requirements in a table
    function displayMaterialRequirements(requirements) {
        const tableBody = document.getElementById('material-requirements-table-body');
        tableBody.innerHTML = '';

        requirements.forEach(requirement => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${requirement.material_name}</td>
                <td>${requirement.quantity}</td>
                <td>${requirement.unit}</td>
                <td>${requirement.cost}</td>
            `;
            tableBody.appendChild(row);
        });
    }

    // Event listener for generating the report
    document.getElementById('generate-report-btn').addEventListener('click', function() {
        fetchMaterialRequirements();
    });
});