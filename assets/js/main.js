// Main JavaScript for Hostel Booking System

// Real-time availability check
function checkAvailability() {
    const roomId = document.getElementById('room_id').value;
    const checkIn = document.getElementById('check_in').value;
    const checkOut = document.getElementById('check_out').value;
    
    if (!roomId || !checkIn || !checkOut) {
        alert('Please fill in all fields');
        return;
    }
    
    // Show loading
    const resultDiv = document.getElementById('availability-result');
    resultDiv.innerHTML = '<div class="text-center"><div class="spinner-border text-primary"></div> Checking...</div>';
    
    // Send AJAX request
    fetch('/hostel-booking/ajax/check_availability.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `room_id=${roomId}&check_in=${checkIn}&check_out=${checkOut}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.available) {
            resultDiv.innerHTML = `
                <div class="alert alert-success">
                    <h5><i class="bi bi-check-circle"></i> Room Available!</h5>
                    <p>This room is available for your selected dates.</p>
                    ${data.price ? `<p><strong>Total Price:</strong> $${data.price}</p>` : ''}
                    <button class="btn btn-success" onclick="proceedToBooking()">Proceed to Booking</button>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <h5><i class="bi bi-x-circle"></i> Room Not Available</h5>
                    <p>This room is not available for your selected dates.</p>
                    <button class="btn btn-primary" onclick="suggestAlternativeRooms()">Find Alternative Rooms</button>
                </div>
            `;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `<div class="alert alert-danger">Error checking availability. Please try again.</div>`;
        console.error('Error:', error);
    });
}

// Auto-suggest alternative rooms
function suggestAlternativeRooms() {
    const checkIn = document.getElementById('check_in').value;
    const checkOut = document.getElementById('check_out').value;
    
    window.location.href = `/hostel-booking/search.php?check_in=${checkIn}&check_out=${checkOut}`;
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return false;
    }
    return true;
}

// Room search autocomplete
function initRoomSearch() {
    const searchInput = document.getElementById('room-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value;
            if (query.length > 2) {
                // You can implement autocomplete here
                console.log('Searching for:', query);
            }
        });
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    initRoomSearch();
    
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
    
    // Confirm delete actions
    const deleteLinks = document.querySelectorAll('a[onclick*="confirm"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this?')) {
                e.preventDefault();
            }
        });
    });
});