function addBusiness() {
    const formData = {
        category_id: document.getElementById('category_id').value,
        name: document.getElementById('name').value,
        description: document.getElementById('description').value,
        contact_phone: document.getElementById('contact_phone').value,
        address: document.getElementById('address').value,
        website: document.getElementById('website').value,
    };

    fetch('add_business.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData),
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message || data.error);
    });
}

function getBusinesses() {
    fetch('get_businesses.php')
        .then(response => response.json())
        .then(data => {
            const tableDiv = document.getElementById('businessesTable');
            tableDiv.innerHTML = ''; // Clear previous table

            if (data.error) {
                tableDiv.innerHTML = `<p>${data.error}</p>`;
                return;
            }

            if (data.length === 0) {
                tableDiv.innerHTML = '<p>No businesses found.</p>';
                return;
            }

            const table = document.createElement('table');
            const thead = document.createElement('thead');
            const tbody = document.createElement('tbody');

            // Table Header
            const headerRow = document.createElement('tr');
            for (const key in data[0]) {
                const th = document.createElement('th');
                th.textContent = key;
                headerRow.appendChild(th);
            }
            thead.appendChild(headerRow);
            table.appendChild(thead);

            // Table Rows
            data.forEach(business => {
                const row = document.createElement('tr');
                for (const key in business) {
                    const cell = document.createElement('td');
                    cell.textContent = business[key];
                    row.appendChild(cell);
                }
                tbody.appendChild(row);
            });
            table.appendChild(tbody);
            tableDiv.appendChild(table);
        });
}


function getBusiness() {
    const businessId = document.getElementById('business_id').value;

    fetch(`get_business.php?id=${businessId}`)
        .then(response => response.json())
        .then(data => {
            const businessDetailsDiv = document.getElementById('businessDetails');
            if (data.error) {
                businessDetailsDiv.innerHTML = `<p>${data.error}</p>`;
                return;
            }

            let detailsHtml = '<ul>';
            for (const key in data) {
                detailsHtml += `<li><b>${key}:</b> ${data[key]}</li>`;
            }
            detailsHtml += '</ul>';
            businessDetailsDiv.innerHTML = detailsHtml;
        });
}
