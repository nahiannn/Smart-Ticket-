<form action="confirm_booking.php" method="POST">

    <label for="user_select">Select User:</label>
    <select name="user_id" id="user_select" required>
        <option value="1">User One</option>
        <option value="2">User Two</option>
    </select>

    <input type="hidden" name="showtime_id" id="selected_showtime_id" value="">

    <input type="hidden" name="selected_seats" id="selected_seats_input" value="">

    <button type="submit">Confirm Booking</button>

</form>