document.addEventListener("DOMContentLoaded", function () {
    const tabs = document.querySelectorAll(".tab-btn");
    const contents = document.querySelectorAll(".tab-content");

    function switchTab(tabElement) {
        tabs.forEach((t) => {
            t.classList.remove("border-blue-500", "text-white");
            t.classList.add("text-gray-400", "border-transparent");
        });

        contents.forEach((c) => c.classList.add("hidden"));

        tabElement.classList.remove("text-gray-400", "border-transparent");
        tabElement.classList.add("border-blue-500", "text-white");

        const tabId = tabElement.getAttribute("data-tab");
        document.getElementById(tabId).classList.remove("hidden");

        updateEmptyStateMessages();
    }

    function updateEmptyStateMessages() {
        const activeTab = document.querySelector(".tab-content:not(.hidden)");
        if (!activeTab) return;

        const hasVisibleTasks =
            activeTab.querySelector('a[href*="/tasks/"]') !== null ||
            activeTab.querySelector('a[href*="/test-tasks/"]') !== null;
        const emptyMessage = activeTab.querySelector(".empty-state-message");

        if (emptyMessage) {
            emptyMessage.style.display = hasVisibleTasks ? "none" : "block";
        }
    }

    tabs.forEach((tab) => {
        tab.addEventListener("click", function () {
            switchTab(this);
        });
    });

    switchTab(tabs[0]);
});
