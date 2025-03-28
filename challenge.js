document.addEventListener("DOMContentLoaded", function () {
    const challenges = [
        "Drink 8 glasses of water today 💧",
        "Do 15 minutes of stretching 🧘",
        "Take a 10-minute walk 🚶‍♂️",
        "Avoid sugary drinks today 🚫🥤",
        "Eat one extra serving of vegetables 🥦",
        "Practice deep breathing for 5 minutes 😌",
        "Do 20 push-ups 💪",
        "Sleep for at least 7 hours 💤",
        "Meditate for 10 minutes 🧘‍♂️",
        "Write down 3 things you're grateful for ✍️"
    ];

    const challengeText = document.getElementById("challengeText");
    const completeBtn = document.getElementById("completeBtn");
    const refreshBtn = document.getElementById("refreshBtn");
    const statusMsg = document.getElementById("statusMsg");
    const shareBtn = document.getElementById("shareBtn");
    const streakCounter = document.getElementById("streak");

    // Load streak from local storage
    let streak = localStorage.getItem("streak") ? parseInt(localStorage.getItem("streak")) : 0;
    streakCounter.textContent = streak;

    // Function to select a random challenge
    function getNewChallenge() {
        let randomChallenge = challenges[Math.floor(Math.random() * challenges.length)];
        challengeText.textContent = randomChallenge;
        statusMsg.classList.add("hidden");
        shareBtn.classList.add("hidden");
        completeBtn.disabled = false;
        completeBtn.classList.remove("bg-gray-400", "cursor-not-allowed");
    }

    getNewChallenge(); // Load initial challenge

    // Mark challenge as completed
    completeBtn.addEventListener("click", function () {
        statusMsg.textContent = "✅ Challenge Completed! Well done!";
        statusMsg.classList.remove("hidden");
        completeBtn.disabled = true;
        completeBtn.classList.add("bg-gray-400", "cursor-not-allowed");

        // Update streak
        streak++;
        localStorage.setItem("streak", streak);
        streakCounter.textContent = streak;

        // Show share button
        shareBtn.classList.remove("hidden");
    });

    // Refresh to get a new challenge
    refreshBtn.addEventListener("click", function () {
        getNewChallenge();
    });

    // Share challenge
    shareBtn.addEventListener("click", function () {
        const challenge = challengeText.textContent;
        const shareData = {
            title: "Daily Health Challenge",
            text: `Today's challenge: ${challenge}`,
            url: window.location.href
        };

        if (navigator.share) {
            navigator.share(shareData)
                .then(() => console.log("Challenge Shared!"))
                .catch((error) => console.log("Error sharing:", error));
        } else {
            alert("Sharing not supported on this browser.");
        }
    });
});
