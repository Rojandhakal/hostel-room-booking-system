<?php
// includes/footer.php
?>
    </div> <!-- Close container -->

    <!-- Footer -->
    <footer class="footer mt-5 py-3 bg-light">
        <div class="container text-center">
            <span class="text-muted">© <?php echo date('Y'); ?> Hostel Booking System - Fullstack Assignment</span>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="/hostel-booking/assets/js/main.js"></script>
    
    <script>
        // Initialize date pickers
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr(".datepicker", {
                dateFormat: "Y-m-d",
                minDate: "today"
            });
            
            // Make check-out date depend on check-in
            const checkIn = document.getElementById('check_in');
            const checkOut = document.getElementById('check_out');
            
            if (checkIn && checkOut) {
                checkIn.addEventListener('change', function() {
                    flatpickr(checkOut, {
                        dateFormat: "Y-m-d",
                        minDate: this.value
                    });
                });
            }
        });
    </script>
</body>
</html>