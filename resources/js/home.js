function updateClock() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, "0");
    const minutes = String(now.getMinutes()).padStart(2, "0");
    const seconds = String(now.getSeconds()).padStart(2, "0");
    const timeString = `${hours}:${minutes}:${seconds}`;
    document.getElementById("clock").textContent = timeString;

    const days = [
        "Minggu",
        "Senin",
        "Selasa",
        "Rabu",
        "Kamis",
        "Jum`at",
        "Sabtu",
    ];

    const months = [
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember",
    ];

    const day = days[now.getDay()];
    const date = now.getDate();
    const month = months[now.getMonth()]; // Get the month name from the array
    const year = now.getFullYear();
    const dateString = `${day}, ${date} ${month} ${year}`;
    document.getElementById("date").textContent = dateString;
}

setInterval(updateClock, 1000); // Update the clock every second
updateClock(); // Initial call to set the clock immediately
