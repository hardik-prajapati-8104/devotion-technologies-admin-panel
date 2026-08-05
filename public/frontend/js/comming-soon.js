document.addEventListener("DOMContentLoaded", function () {

    const countdown = document.querySelector(".countdown__module");

    if (!countdown) return;

    const targetDate = new Date(countdown.dataset.date).getTime();

    function updateCountdown() {

        const now = new Date().getTime();

        const distance = targetDate - now;

        if (distance <= 0) {

            document.getElementById("days").innerHTML = "00";
            document.getElementById("hours").innerHTML = "00";
            document.getElementById("minutes").innerHTML = "00";
            document.getElementById("seconds").innerHTML = "00";

            clearInterval(timer);

            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));

        const hours = Math.floor(
            (distance % (1000 * 60 * 60 * 24)) /
            (1000 * 60 * 60)
        );

        const minutes = Math.floor(
            (distance % (1000 * 60 * 60)) /
            (1000 * 60)
        );

        const seconds = Math.floor(
            (distance % (1000 * 60)) /
            1000
        );

        document.getElementById("days").innerHTML = String(days).padStart(2, "0");
        document.getElementById("hours").innerHTML = String(hours).padStart(2, "0");
        document.getElementById("minutes").innerHTML = String(minutes).padStart(2, "0");
        document.getElementById("seconds").innerHTML = String(seconds).padStart(2, "0");
    }

    updateCountdown();

    const timer = setInterval(updateCountdown, 1000);

});