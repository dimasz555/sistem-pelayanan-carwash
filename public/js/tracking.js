document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("trackingForm");
    const trackBtn = document.getElementById("trackBtn");
    const trackBtnText = document.getElementById("trackBtnText");
    const trackBtnLoading = document.getElementById("trackBtnLoading");
    const loadingState = document.getElementById("loadingState");
    const trackingResult = document.getElementById("trackingResult");
    const errorMessage = document.getElementById("errorMessage");

    const token = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    form.addEventListener("submit", function (e) {
        e.preventDefault();
        trackOrder();
    });

    function trackOrder() {
        const invoice = document.getElementById("invoice").value.trim();
        if (!invoice) {
            alert("Mohon masukkan nomor invoice");
            return;
        }

        setLoadingState(true);
        hideAllResults();

        fetch("/tracking/search", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
            },
            body: JSON.stringify({ invoice }),
        })
            .then((response) => response.json())
            .then((data) => {
                setLoadingState(false);
                if (data.success) {
                    displayTrackingResult(data.data);
                } else {
                    showError(data.message || "Invoice tidak ditemukan.");
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                setLoadingState(false);
                showError(
                    "Terjadi kesalahan saat mencari data. Silakan coba lagi."
                );
            });
    }

    function setLoadingState(loading) {
        if (loading) {
            trackBtn.disabled = true;
            trackBtnText.classList.add("hidden");
            trackBtnLoading.classList.remove("hidden");
            loadingState.classList.remove("hidden");
        } else {
            trackBtn.disabled = false;
            trackBtnText.classList.remove("hidden");
            trackBtnLoading.classList.add("hidden");
            loadingState.classList.add("hidden");
        }
    }

    function hideAllResults() {
        trackingResult.classList.add("hidden");
        errorMessage.classList.add("hidden");
    }

    function showError(message) {
        document.getElementById("errorText").textContent = message;
        errorMessage.classList.remove("hidden");
    }

    function displayTrackingResult(data) {
        // Update order info
        document.getElementById("invoiceNumber").textContent = data.invoice;
        document.getElementById("orderDate").textContent = data.date;
        document.getElementById("vehicleInfo").textContent = data.vehicle;
        document.getElementById("serviceType").textContent = data.service;

        // Check if isPaid element exists before setting it
        const isPaidElement = document.getElementById("isPaid");
        if (isPaidElement && data.isPaid) {
            isPaidElement.textContent = data.isPaid;
        }

        document.getElementById("totalPrice").textContent = data.totalPrice;
        document.getElementById("customerName").textContent =
            data.customer.name;
        document.getElementById("customerPhone").textContent =
            data.customer.phone;

        // Clear existing steps
        const stepsContainer = document.getElementById("trackingSteps");
        stepsContainer.innerHTML = "";

        let completedSteps = 0;

        // Create tracking steps
        data.steps.forEach((step, index) => {
            const isLastStep = index === data.steps.length - 1;
            const stepElement = createStepElement(step, index, isLastStep);
            stepsContainer.appendChild(stepElement);

            // Count progress
            if (step.status === "completed") {
                completedSteps++;
            } else if (step.status === "current") {
                completedSteps += 0.5;
            }
        });

        // Show result
        trackingResult.classList.remove("hidden");

        // Animate progress line - tunggu sampai elemen fully rendered
        setTimeout(() => {
            updateProgressLine(data, completedSteps);
        }, 600); // Increased delay untuk memastikan rendering selesai
    }

    // Alternative sederhana - ubah bagian updateProgressLine menjadi:

    function updateProgressLine(data, completedSteps) {
        const progressLine = document.getElementById("progressLine");
        const progressBackground =
            document.getElementById("progressBackground");

        if (progressLine && progressBackground) {
            if (data.is_completed) {
                // Gunakan persentase yang disesuaikan dengan screen size
                let progressPercentage;

                if (window.innerWidth < 640) {
                    // Mobile
                    progressPercentage = 85; // Lebih tinggi untuk mobile
                } else if (window.innerWidth < 1024) {
                    // Tablet
                    progressPercentage = 80;
                } else {
                    // Desktop
                    progressPercentage = 80;
                }

                progressLine.style.height = progressPercentage + "%";
                progressBackground.style.height = progressPercentage + "%";
            } else {
                // Normal calculation untuk yang belum completed
                const progressPercentage =
                    (completedSteps / data.steps.length) * 100;
                progressLine.style.height = progressPercentage + "%";
                progressBackground.style.height = ""; // Reset to default
            }
        }
    }

    function createStepElement(step, index, isLastStep) {
        const div = document.createElement("div");
        div.className = "relative flex items-start animate-fade-in";
        div.style.animationDelay = `${index * 0.1}s`;

        let statusClasses = "";
        let iconClasses = "";
        let contentClasses = "";

        switch (step.status) {
            case "completed":
                statusClasses = "bg-green-500 border-green-500 text-white";
                iconClasses = "text-green-600";
                contentClasses = "text-gray-900";
                break;
            case "current":
                statusClasses =
                    "bg-blue-500 border-blue-500 text-white animate-pulse";
                iconClasses = "text-blue-600";
                contentClasses = "text-gray-900 font-medium";
                break;
            default:
                statusClasses = "bg-gray-200 border-gray-300 text-gray-400";
                iconClasses = "text-gray-400";
                contentClasses = "text-gray-500";
        }

        div.innerHTML = `
            <div class="relative z-10 flex items-center justify-center w-12 h-12 rounded-full border-4 ${statusClasses} shadow-lg">
                <span class="text-lg">${step.icon}</span>
            </div>
            <div class="ml-4 flex-1 ${isLastStep ? "pb-0" : "pb-6"}">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <h4 class="text-lg font-semibold ${contentClasses}">${
            step.title
        }</h4>
                    ${
                        step.time
                            ? `
                        <div class="text-sm ${iconClasses} mt-1 sm:mt-0">
                            🕐 ${step.time} • ${step.date}
                        </div>
                    `
                            : ""
                    }
                </div>
                <p class="text-sm ${contentClasses} mt-1">${
            step.description
        }</p>
                ${
                    step.status === "current"
                        ? `
                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        ⚡ Sedang Berlangsung
                    </div>
                `
                        : ""
                }
            </div>
        `;

        return div;
    }

    // Allow enter key to trigger search
    document
        .getElementById("invoice")
        .addEventListener("keypress", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                trackOrder();
            }
        });

    // Handle window resize untuk responsive
    let resizeTimeout;
    window.addEventListener("resize", function () {
        // Debounce resize event
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function () {
            // Re-calculate progress line jika tracking result sedang ditampilkan
            if (!trackingResult.classList.contains("hidden")) {
                const lastSearchData = window.lastTrackingData;
                if (lastSearchData) {
                    let completedSteps = 0;
                    lastSearchData.steps.forEach((step) => {
                        if (step.status === "completed") {
                            completedSteps++;
                        } else if (step.status === "current") {
                            completedSteps += 0.5;
                        }
                    });
                    updateProgressLine(lastSearchData, completedSteps);
                }
            }
        }, 250);
    });

    // Store last search data untuk resize handler
    const originalDisplayTrackingResult = displayTrackingResult;
    displayTrackingResult = function (data) {
        window.lastTrackingData = data; // Store untuk resize handler
        return originalDisplayTrackingResult(data);
    };
});
